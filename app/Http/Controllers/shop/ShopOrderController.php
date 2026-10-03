<?php

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use App\Models\Fruit;
use App\Models\Order;
use App\Models\Shop;
use App\Models\SystemSetting;
use App\Models\Unit;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShopOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user->hasRole('shop-manager') && $user->shop_id, 403);

        $shop = Shop::query()
            ->whereKey($user->shop_id)
            ->where('status', 'active')
            ->firstOrFail();

        $orderDate = $this->deliveryDate();
        $order = Order::query()
            ->with(['items.fruit', 'items.unit', 'items.boxConfiguration'])
            ->where('shop_id', $shop->id)
            ->whereDate('order_date', $orderDate)
            ->first();
        $initialItems = $order && $order->status === 'draft'
            ? $order->items->map(fn ($item) => [
                'fruit_id' => $item->fruit_id,
                'unit_id' => $item->unit_id,
                'box_configuration_id' => $item->box_configuration_id,
                'quantity' => (float) $item->quantity,
            ])->values()
            : [];

        $fruits = Fruit::query()
            ->with([
                'defaultUnit',
                'boxConfigurations' => fn ($query) => $query->where('status', true)->orderByDesc('is_default'),
                'prices' => fn ($query) => $query->where('is_active', true)
                    ->whereDate('effective_from', '<=', $orderDate)
                    ->where(fn ($price) => $price->whereNull('effective_to')->orWhereDate('effective_to', '>=', $orderDate))
                    ->orderByDesc('effective_from'),
            ])
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $units = Unit::query()
            ->where('status', true)
            ->where('is_order_unit', true)
            ->whereIn('code', ['KG', 'BOX'])
            ->orderBy('name')
            ->get();

        $catalog = $fruits->map(function (Fruit $fruit) use ($units): array {
            $availableUnits = $units->filter(function (Unit $unit) use ($fruit): bool {
                return $unit->code === 'KG' ? $fruit->allow_kg : $fruit->allow_box;
            })->map(function (Unit $unit) use ($fruit): array {
                $boxes = $unit->code === 'BOX'
                    ? $fruit->boxConfigurations->map(function ($box) use ($fruit, $unit): ?array {
                        $price = $fruit->prices->first(fn ($candidate) =>
                            (int) $candidate->unit_id === (int) $unit->id
                            && (int) $candidate->box_configuration_id === (int) $box->id
                        );
                        return $price ? [
                            'id' => $box->id,
                            'name' => $box->name,
                            'weight_kg' => (float) $box->weight_kg,
                            'price' => (float) $price->price,
                            'currency' => $price->currency,
                        ] : null;
                    })->filter()->values()->all()
                    : [];

                $price = $unit->code === 'KG'
                    ? $fruit->prices->first(fn ($candidate) =>
                        (int) $candidate->unit_id === (int) $unit->id
                        && $candidate->box_configuration_id === null
                    )
                    : null;

                if (($unit->code === 'KG' && !$price) || ($unit->code === 'BOX' && !$boxes)) {
                    return null;
                }

                return [
                    'id' => $unit->id,
                    'code' => $unit->code,
                    'name' => $unit->name,
                    'symbol' => $unit->symbol,
                    'price' => $price ? (float) $price->price : null,
                    'currency' => $price?->currency,
                    'boxes' => $boxes,
                ];
            })->filter()->values()->all();

            return [
                'id' => $fruit->id,
                'name' => $fruit->display_name ?: $fruit->name,
                'code' => $fruit->code,
                'units' => $availableUnits,
            ];
        })->filter(fn ($fruit) => count($fruit['units']) > 0)->values();

        $currency = SystemSetting::query()->where('key', 'currency')->value('value') ?: 'EUR';

        return view('shop.orders.index', compact('shop', 'order', 'orderDate', 'catalog', 'currency', 'initialItems'));
    }

    public function saveDraft(Request $request)
    {
        return $this->persistOrder($request, false);
    }

    public function submit(Request $request)
    {
        return $this->persistOrder($request, true);
    }

    private function persistOrder(Request $request, bool $submit)
    {
        $user = $request->user();
        abort_unless($user->hasRole('shop-manager') && $user->shop_id, 403);

        $shop = Shop::query()
            ->whereKey($user->shop_id)
            ->where('status', 'active')
            ->firstOrFail();
        $orderDate = $this->deliveryDate();

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => [$submit ? 'required' : 'nullable', 'array', 'max:100'],
            'items.*.fruit_id' => ['required', 'integer', 'distinct', 'exists:fruits,id'],
            'items.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'items.*.box_configuration_id' => ['nullable', 'integer', 'exists:fruit_box_configurations,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'decimal:0,3'],
        ]);

        if ($submit && empty($data['items'])) {
            throw ValidationException::withMessages([
                'items' => 'Add at least one fruit before submitting the order.',
            ]);
        }

        DB::transaction(function () use ($data, $shop, $user, $orderDate, $submit): void {
            Shop::query()->whereKey($shop->id)->lockForUpdate()->firstOrFail();
            $order = Order::query()
                ->where('shop_id', $shop->id)
                ->whereDate('order_date', $orderDate)
                ->lockForUpdate()
                ->first();

            if ($order && $order->status !== 'draft') {
                throw ValidationException::withMessages([
                    'order' => 'This order has already been submitted and can no longer be changed.',
                ]);
            }

            if (!$order) {
                $order = new Order([
                    'shop_id' => $shop->id,
                    'order_date' => $orderDate,
                    'status' => 'draft',
                    'created_by' => $user->id,
                ]);
                $order->save();
            }

            $rows = $data['items'] ?? [];
            $fruitIds = collect($rows)->pluck('fruit_id')->unique();
            $fruits = Fruit::query()
                ->with([
                    'boxConfigurations' => fn ($query) => $query->where('status', true),
                    'prices' => fn ($query) => $query->where('is_active', true)
                        ->whereDate('effective_from', '<=', $orderDate)
                        ->where(fn ($price) => $price->whereNull('effective_to')->orWhereDate('effective_to', '>=', $orderDate))
                        ->orderByDesc('effective_from'),
                ])
                ->where('status', true)
                ->whereIn('id', $fruitIds)
                ->get()
                ->keyBy('id');
            $units = Unit::query()
                ->where('status', true)
                ->where('is_order_unit', true)
                ->whereIn('code', ['KG', 'BOX'])
                ->whereIn('id', collect($rows)->pluck('unit_id')->unique())
                ->get()
                ->keyBy('id');

            $itemData = [];
            foreach ($rows as $index => $row) {
                $fruit = $fruits->get((int) $row['fruit_id']);
                $unit = $units->get((int) $row['unit_id']);
                if (!$fruit || !$unit) {
                    throw ValidationException::withMessages([
                        "items.{$index}.fruit_id" => 'Choose an active fruit and order unit.',
                    ]);
                }

                if ($unit->code === 'KG' && !$fruit->allow_kg) {
                    throw ValidationException::withMessages([
                        "items.{$index}.unit_id" => 'This fruit cannot be ordered by weight.',
                    ]);
                }

                if ($unit->code === 'BOX' && !$fruit->allow_box) {
                    throw ValidationException::withMessages([
                        "items.{$index}.unit_id" => 'This fruit cannot be ordered by box.',
                    ]);
                }

                $boxId = $row['box_configuration_id'] ?? null;
                $box = null;
                if ($unit->code === 'BOX') {
                    $box = $boxId ? $fruit->boxConfigurations->firstWhere('id', (int) $boxId) : null;
                    if (!$box) {
                        throw ValidationException::withMessages([
                            "items.{$index}.box_configuration_id" => 'Select an active box configuration for this fruit.',
                        ]);
                    }
                } elseif ($boxId) {
                    throw ValidationException::withMessages([
                        "items.{$index}.box_configuration_id" => 'Box configuration is only used for box orders.',
                    ]);
                }

                $price = $fruit->prices->first(function ($candidate) use ($unit, $box): bool {
                    return (int) $candidate->unit_id === (int) $unit->id
                        && ($box
                            ? (int) $candidate->box_configuration_id === (int) $box->id
                            : $candidate->box_configuration_id === null);
                });
                if (!$price) {
                    throw ValidationException::withMessages([
                        "items.{$index}.unit_id" => "There is no active price for {$fruit->display_name}. Contact the warehouse.",
                    ]);
                }

                $quantity = (float) $row['quantity'];
                $unitWeightKg = $box ? (float) $box->weight_kg : ($unit->is_weight_unit ? 1.0 : null);
                $itemData[] = [
                    'fruit_id' => $fruit->id,
                    'unit_id' => $unit->id,
                    'box_configuration_id' => $box?->id,
                    'quantity' => $quantity,
                    'unit_weight_kg' => $unitWeightKg,
                    'converted_kg' => $unitWeightKg === null ? null : round($quantity * $unitWeightKg, 3),
                    'unit_price' => $price->price,
                    'line_total' => round($quantity * (float) $price->price, 2),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $order->items()->delete();
            if ($itemData) {
                $order->items()->createMany($itemData);
            }

            $order->notes = $data['notes'] ?? null;
            if ($submit) {
                $order->status = 'submitted';
                $order->submitted_at = now();
            }
            $order->save();
        });

        return response()->json([
            'status' => 'success',
            'message' => $submit ? 'Your order was sent to the warehouse.' : 'Your draft order was saved.',
            'redirect' => route('shop.orders.index'),
        ]);
    }

    private function deliveryDate(): string
    {
        $days = (int) (SystemSetting::query()->where('key', 'order_day_offset')->value('value') ?: 1);
        $timezone = SystemSetting::query()->where('key', 'timezone')->value('value') ?: config('app.timezone');

        return CarbonImmutable::today($timezone)->addDays(max(1, $days))->toDateString();
    }
}

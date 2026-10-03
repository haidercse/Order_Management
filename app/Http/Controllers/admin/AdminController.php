<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Fruit;
use App\Models\Order;
use App\Models\Shop;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $submittedOrders = Order::query()
            ->whereNotNull('submitted_at')
            ->whereDate('submitted_at', $today);

        $todayOrdersCount = (clone $submittedOrders)->count();
        $activeShopsCount = Shop::query()->where('status', 'active')->count();
        $orderValue = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNotNull('orders.submitted_at')
            ->whereDate('orders.submitted_at', $today)
            ->sum(DB::raw('COALESCE(order_items.line_total, order_items.quantity * order_items.unit_price)'));

        $demandByFruit = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereNotNull('orders.submitted_at')
            ->whereDate('orders.submitted_at', $today)
            ->select('order_items.fruit_id')
            ->selectRaw('SUM(COALESCE(order_items.converted_kg, order_items.quantity)) AS demand_kg')
            ->groupBy('order_items.fruit_id')
            ->get()
            ->keyBy('fruit_id');

        $stockByFruit = DB::table('inventory_stocks')
            ->where('status', 'active')
            ->select('fruit_id')
            ->selectRaw('SUM(quantity_kg - reserved_kg) AS available_kg')
            ->groupBy('fruit_id')
            ->pluck('available_kg', 'fruit_id');

        $fruits = Fruit::query()
            ->with([
                'defaultUnit',
                'boxConfigurations' => fn ($query) => $query->where('is_default', true),
            ])
            ->whereIn('id', $demandByFruit->keys())
            ->get()
            ->keyBy('id');

        $fruitDemand = $demandByFruit->map(function ($demand) use ($fruits, $stockByFruit) {
            $fruit = $fruits->get($demand->fruit_id);
            if (!$fruit) {
                return null;
            }

            $box = $fruit->boxConfigurations->first();
            $boxWeightKg = $box ? (float) $box->weight_kg : 0;
            $unitLabel = $boxWeightKg > 0 ? 'caja' : ($fruit->defaultUnit->symbol ?? 'kg');
            $demandKg = (float) $demand->demand_kg;
            $availableKg = max(0, (float) ($stockByFruit[$fruit->id] ?? 0));
            $divisor = $boxWeightKg > 0 ? $boxWeightKg : 1;
            $demandQuantity = $demandKg / $divisor;
            $availableQuantity = $availableKg / $divisor;

            return [
                'name' => $fruit->display_name ?: $fruit->name,
                'unit' => $unitLabel,
                'demand' => $demandQuantity,
                'available' => $availableQuantity,
                'difference' => $availableQuantity - $demandQuantity,
                'shortage' => $demandKg > $availableKg,
            ];
        })->filter()->values();

        $shortageCount = $fruitDemand->where('shortage', true)->count();
        $demandSummary = $fruitDemand
            ->groupBy('unit')
            ->map(fn ($items) => $items->sum('demand'));
        $topDemandedFruits = $fruitDemand
            ->sortByDesc('demand')
            ->take(5)
            ->values();

        $orders = (clone $submittedOrders)
            ->with(['shop', 'items.fruit', 'items.unit'])
            ->withCount('items')
            ->orderByDesc('submitted_at')
            ->limit(10)
            ->get();

        $currency = SystemSetting::query()
            ->where('key', 'currency')
            ->value('value') ?: 'EUR';

        $data = compact(
            'todayOrdersCount',
            'activeShopsCount',
            'orderValue',
            'currency',
            'fruitDemand',
            'demandSummary',
            'shortageCount',
            'topDemandedFruits',
            'orders'
        );

        if ($request->ajax()) {
            return response()->json([
                'html' => view('backend.pages.dashboard.partials.overview', $data)->render(),
                'refreshed_at' => now()->format('H:i:s'),
            ]);
        }

        return view('backend.pages.dashboard.index', $data);
    }
}

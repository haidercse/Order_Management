<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Fruit;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $stockQuery = InventoryStock::query()->with(['fruit', 'unit', 'boxConfiguration']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $stockQuery->whereHas('fruit', function ($fruit) use ($search) {
                $fruit->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('fruit_id')) {
            $stockQuery->where('fruit_id', $request->integer('fruit_id'));
        }

        if ($request->filled('status')) {
            $stockQuery->where('status', $request->string('status')->toString());
        }

        $stocks = $stockQuery->orderBy('fruit_id')->orderBy('unit_id')->paginate(15)->withQueryString();

        $movementQuery = InventoryTransaction::query()->with(['fruit', 'unit', 'boxConfiguration', 'creator', 'referenceOrder.shop'])
            ->latest('id');
        if ($request->filled('fruit_id')) {
            $movementQuery->where('fruit_id', $request->integer('fruit_id'));
        }
        $movements = $movementQuery->limit(20)->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('backend.pages.inventory.partials.stocks', compact('stocks'))->render(),
            ]);
        }

        $fruits = Fruit::query()
            ->with(['defaultUnit', 'boxConfigurations' => fn ($query) => $query->where('status', true)->orderByDesc('is_default')])
            ->where('status', true)
            ->orderBy('name')
            ->get();
        $units = Unit::query()->where('is_order_unit', true)->where('status', true)->orderBy('name')->get();
        $filters = $request->only(['search', 'fruit_id', 'status']);

        return view('backend.pages.inventory.index', compact('stocks', 'movements', 'fruits', 'units', 'filters'));
    }

    public function storeMovement(Request $request)
    {
        $request->merge([
            'box_configuration_id' => $request->filled('box_configuration_id') ? $request->input('box_configuration_id') : null,
            'unit_price' => $request->filled('unit_price') ? $request->input('unit_price') : null,
        ]);

        $data = $request->validate([
            'fruit_id' => ['required', 'integer', 'exists:fruits,id'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'box_configuration_id' => ['nullable', 'integer', 'exists:fruit_box_configurations,id'],
            'type' => ['required', 'in:purchase,issue,return,damage,adjustment'],
            'direction' => ['nullable', 'required_if:type,adjustment', 'in:in,out'],
            'quantity' => ['required', 'numeric', 'gt:0', 'decimal:0,3'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $fruit = Fruit::findOrFail($data['fruit_id']);
        $unit = Unit::query()->where('is_order_unit', true)->findOrFail($data['unit_id']);
        $box = null;

        if ($unit->code === 'BOX') {
            if (!$data['box_configuration_id']) {
                throw ValidationException::withMessages(['box_configuration_id' => 'Select a box configuration for BOX stock.']);
            }
            $box = $fruit->boxConfigurations()->whereKey($data['box_configuration_id'])->where('status', true)->first();
            if (!$box) {
                throw ValidationException::withMessages(['box_configuration_id' => 'Choose an active box configuration belonging to this fruit.']);
            }
        } elseif ($data['box_configuration_id']) {
            throw ValidationException::withMessages(['box_configuration_id' => 'Box configuration can only be selected for the BOX unit.']);
        }

        if ($unit->code !== 'BOX' && !$unit->is_weight_unit) {
            throw ValidationException::withMessages(['unit_id' => 'Inventory can only be tracked in kilograms or configured box units.']);
        }

        $factorKg = $box ? (float) $box->weight_kg : 1.0;
        $quantity = (float) $data['quantity'];
        $quantityKg = round($quantity * $factorKg, 3);
        $addsStock = in_array($data['type'], ['purchase', 'return'], true)
            || ($data['type'] === 'adjustment' && $data['direction'] === 'in');
        $storedType = $data['type'];
        $notes = $data['notes'] ?? null;
        if ($data['type'] === 'adjustment') {
            $notes = trim('Adjustment ' . ($addsStock ? '(increase)' : '(decrease)') . '. ' . ($notes ?? ''));
        }

        DB::transaction(function () use ($data, $fruit, $unit, $box, $addsStock, $quantity, $quantityKg, $storedType, $notes): void {
            $stockQuery = InventoryStock::query()
                ->where('fruit_id', $fruit->id)
                ->where('unit_id', $unit->id);
            $box
                ? $stockQuery->where('box_configuration_id', $box->id)
                : $stockQuery->whereNull('box_configuration_id');

            $stock = $stockQuery->lockForUpdate()->first();
            if (!$stock && !$addsStock) {
                throw ValidationException::withMessages(['quantity' => 'There is no stock row for this item; record an incoming purchase first.']);
            }

            if (!$stock) {
                $stock = InventoryStock::create([
                    'fruit_id' => $fruit->id,
                    'unit_id' => $unit->id,
                    'box_configuration_id' => $box?->id,
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'quantity_kg' => 0,
                    'reserved_kg' => 0,
                    'status' => 'active',
                ]);
            }

            if (!$addsStock && ((float) $stock->quantity - (float) $stock->reserved_quantity) < $quantity) {
                throw ValidationException::withMessages(['quantity' => 'The requested outgoing quantity exceeds the unreserved stock.']);
            }

            if ($addsStock) {
                $stock->quantity = (float) $stock->quantity + $quantity;
                $stock->quantity_kg = (float) $stock->quantity_kg + $quantityKg;
            } else {
                $stock->quantity = (float) $stock->quantity - $quantity;
                $stock->quantity_kg = (float) $stock->quantity_kg - $quantityKg;
            }
            $stock->status = 'active';
            $stock->save();

            $price = $data['unit_price'] === null ? null : (float) $data['unit_price'];
            InventoryTransaction::create([
                'fruit_id' => $fruit->id,
                'unit_id' => $unit->id,
                'box_configuration_id' => $box?->id,
                'type' => $storedType,
                'quantity' => $quantity,
                'quantity_kg' => $quantityKg,
                'unit_price' => $price,
                'total_price' => $price === null ? null : round($price * $quantity, 2),
                'created_by' => auth()->id(),
                'notes' => $notes,
            ]);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Inventory movement recorded successfully.',
        ]);
    }
}

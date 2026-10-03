<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Fruit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShortageController extends Controller
{
    public function index(Request $request)
    {
        $demand = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', ['submitted', 'prepared', 'ready'])
            ->select('order_items.fruit_id')
            ->selectRaw('SUM(COALESCE(order_items.converted_kg, order_items.quantity)) AS demand_kg')
            ->groupBy('order_items.fruit_id')
            ->get()
            ->keyBy('fruit_id');

        $stocks = DB::table('inventory_stocks')
            ->where('status', 'active')
            ->select('fruit_id')
            ->selectRaw('SUM(quantity_kg - reserved_kg) AS available_kg')
            ->groupBy('fruit_id')
            ->pluck('available_kg', 'fruit_id');

        $fruits = Fruit::query()
            ->with(['defaultUnit', 'boxConfigurations' => fn ($query) => $query->where('is_default', true)])
            ->whereIn('id', $demand->keys())
            ->get()
            ->keyBy('id');

        $items = $demand->map(function ($row) use ($fruits, $stocks) {
            $fruit = $fruits->get($row->fruit_id);
            if (!$fruit) {
                return null;
            }

            $box = $fruit->boxConfigurations->first();
            $boxWeight = $box ? (float) $box->weight_kg : 0;
            $divisor = $boxWeight > 0 ? $boxWeight : 1;
            $demandKg = (float) $row->demand_kg;
            $availableKg = max(0, (float) ($stocks[$fruit->id] ?? 0));

            return [
                'fruit' => $fruit,
                'demand' => $demandKg / $divisor,
                'available' => $availableKg / $divisor,
                'shortage' => max(0, ($demandKg - $availableKg) / $divisor),
                'unit' => $boxWeight > 0 ? 'caja' : ($fruit->defaultUnit->symbol ?? 'kg'),
            ];
        })->filter()->sortByDesc('shortage')->values();

        $items = $request->boolean('show_all')
            ? $items
            : $items->filter(fn ($item) => $item['shortage'] > 0)->values();

        return view('backend.pages.shortage.index', [
            'items' => $items,
            'shortageCount' => $items->where('shortage', '>', 0)->count(),
            'showAll' => $request->boolean('show_all'),
        ]);
    }
}

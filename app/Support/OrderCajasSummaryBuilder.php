<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderCajasSummaryBuilder
{
    public function build(string $date): Collection
    {
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('fruits', 'fruits.id', '=', 'order_items.fruit_id')
            ->join('units', 'units.id', '=', 'order_items.unit_id')
            ->whereDate('orders.order_date', $date)
            ->whereNotNull('orders.submitted_at')
            ->where('units.code', 'BOX')
            ->select('fruits.id as fruit_id')
            ->selectRaw('COALESCE(fruits.display_name, fruits.name) AS name')
            ->selectRaw('SUM(order_items.quantity) AS cajas')
            ->groupBy('fruits.id', 'fruits.display_name', 'fruits.name')
            ->orderByRaw('COALESCE(fruits.display_name, fruits.name)')
            ->get();
    }
}

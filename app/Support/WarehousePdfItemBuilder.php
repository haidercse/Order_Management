<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Collection;

class WarehousePdfItemBuilder
{
    public function build(Collection $orders): Collection
    {
        return $orders
            ->flatMap(fn (Order $order) => $order->items)
            ->groupBy(fn ($item) => $item->fruit_id.':'.($item->box_configuration_id ?? 'default'))
            ->map(function (Collection $items): array {
                $firstItem = $items->first();
                $fruit = $firstItem->fruit;
                $box = $firstItem->boxConfiguration
                    ?? $fruit->boxConfigurations->firstWhere('is_default', true)
                    ?? $fruit->boxConfigurations->first();
                $boxWeight = (float) ($box?->weight_kg ?? $firstItem->unit_weight_kg ?? 0);
                $tare = round($items->sum(fn ($item) => (float) $item->quantity * (float) (
                    $item->unit_tare_kg ?? $box?->tare_weight_kg ?? 0
                )), 3);
                $boxPrice = $fruit->prices->first(fn ($price) => $price->unit?->code === 'BOX'
                    && $box
                    && (int) $price->box_configuration_id === (int) $box->id
                );
                $allWeightsSaved = $items->every(fn ($item) => $item->warehouse_weight_kg !== null);
                $totalWeight = $allWeightsSaved
                    ? $items->sum(fn ($item) => (float) $item->warehouse_weight_kg)
                    : null;
                $netWeight = $totalWeight !== null ? round($totalWeight - $tare, 3) : null;
                $pricePerKg = $boxPrice && $boxWeight > 0
                    ? round((float) $boxPrice->price / $boxWeight, 2)
                    : null;

                return [
                    'quantity' => $items->sum(fn ($item) => (float) $item->quantity),
                    'name' => $fruit->display_name ?: $fruit->name,
                    'box_name' => $box?->name,
                    'box_weight' => $boxWeight > 0 ? $boxWeight : null,
                    'total_weight' => $totalWeight,
                    'tare' => $tare,
                    'net_weight' => $netWeight,
                    'price' => $pricePerKg,
                    'total' => $netWeight !== null && $pricePerKg !== null
                        ? round($netWeight * $pricePerKg, 2)
                        : null,
                ];
            })
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }
}

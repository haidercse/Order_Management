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
            ->groupBy(fn ($item) => implode(':', [
                $item->fruit_id,
                $item->box_configuration_id ?? 'default',
                (float) $item->unit_weight_kg > 0
                    ? number_format((float) $item->unit_weight_kg, 3, '.', '')
                    : 'scale',
            ]))
            ->map(function (Collection $items): array {
                $firstItem = $items->first();
                $fruit = $firstItem->fruit;
                $box = $firstItem->boxConfiguration
                    ?? $fruit->boxConfigurations->firstWhere('is_default', true)
                    ?? $fruit->boxConfigurations->first();
                $boxWeight = (float) $firstItem->unit_weight_kg > 0
                    ? (float) $firstItem->unit_weight_kg
                    : null;
                $tarePerBox = (float) ($firstItem->unit_tare_kg ?? $box?->tare_weight_kg ?? 0);
                $totalTare = round($items->sum(fn ($item) => (float) $item->quantity * (float) (
                    $item->unit_tare_kg ?? $box?->tare_weight_kg ?? 0
                )), 3, PHP_ROUND_HALF_UP);
                $boxPrice = $fruit->prices->first(fn ($price) => $price->unit?->code === 'BOX'
                    && $box
                    && (int) $price->box_configuration_id === (int) $box->id
                );
                $kilogramPrice = $fruit->prices->first(
                    fn ($price) => $price->unit?->code === 'KG' && $price->box_configuration_id === null
                );
                $allWeightsAvailable = $items->every(fn ($item) => $item->warehouse_weight_kg !== null
                    || (float) $item->unit_weight_kg > 0);
                $totalWeight = $allWeightsAvailable
                    ? round($items->sum(fn ($item) => $item->warehouse_weight_kg !== null
                        ? (float) $item->warehouse_weight_kg
                        : (float) $item->quantity * (float) $item->unit_weight_kg), 3, PHP_ROUND_HALF_UP)
                    : null;
                $netWeight = $totalWeight !== null
                    ? round($totalWeight - $totalTare, 3, PHP_ROUND_HALF_UP)
                    : null;
                $lineTotals = $netWeight === null
                    ? collect()
                    : $items->map(function ($item) use ($kilogramPrice, $box, $boxPrice, $boxWeight) {
                        $itemWeight = $item->warehouse_weight_kg !== null
                            ? (float) $item->warehouse_weight_kg
                            : (float) $item->quantity * (float) $item->unit_weight_kg;
                        $itemTare = (float) $item->quantity * (float) (
                            $item->unit_tare_kg ?? $box?->tare_weight_kg ?? 0
                        );
                        $itemNet = round($itemWeight - $itemTare, 3, PHP_ROUND_HALF_UP);
                        $itemKilo = (float) $item->unit_weight_kg > 0
                            ? (float) $item->unit_weight_kg
                            : null;
                        if ($itemKilo !== null) {
                            $pricePerKg = $item->unit_price !== null
                                ? (float) $item->unit_price / $itemKilo
                                : ($boxPrice && $boxWeight > 0 ? (float) $boxPrice->price / $boxWeight : null);
                        } else {
                            $pricePerKg = $kilogramPrice ? (float) $kilogramPrice->price : null;
                        }

                        return $pricePerKg === null
                            ? null
                            : round($itemNet * $pricePerKg, 2, PHP_ROUND_HALF_UP);
                    });
                $total = $lineTotals->isNotEmpty() && $lineTotals->every(fn ($lineTotal) => $lineTotal !== null)
                    ? round($lineTotals->sum(), 2, PHP_ROUND_HALF_UP)
                    : null;
                $pricePerKg = $total !== null && $netWeight > 0
                    ? round($total / $netWeight, 2, PHP_ROUND_HALF_UP)
                    : ($boxWeight > 0 && $boxPrice
                        ? round((float) $boxPrice->price / $boxWeight, 2, PHP_ROUND_HALF_UP)
                        : ($kilogramPrice ? (float) $kilogramPrice->price : null));

                return [
                    'quantity' => round($items->sum(fn ($item) => (float) $item->quantity), 3, PHP_ROUND_HALF_UP),
                    'name' => $fruit->display_name ?: $fruit->name,
                    'box_name' => $box?->name,
                    'box_weight' => $boxWeight,
                    'total_weight' => $totalWeight,
                    'tare' => $tarePerBox,
                    'total_tare' => $totalTare,
                    'net_weight' => $netWeight,
                    'price' => $pricePerKg,
                    'total' => $total,
                ];
            })
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }
}

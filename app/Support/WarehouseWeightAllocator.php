<?php

namespace App\Support;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class WarehouseWeightAllocator
{
    /**
     * Distribute an aggregate weight in grams by item quantity without losing precision.
     *
     * @return array<int, int> Item ID mapped to allocated grams.
     */
    public function distribute(Collection $items, int $totalGrams): array
    {
        $totalQuantityUnits = $items->sum(
            fn ($item) => (int) round((float) $item->quantity * 1000)
        );

        if (
            $items->isEmpty()
            || $totalQuantityUnits <= 0
            || $totalGrams < $items->count()
        ) {
            throw new InvalidArgumentException('Each shop order item must receive at least one gram.');
        }

        $allocations = $items->map(function ($item) use ($totalGrams, $totalQuantityUnits): array {
            $quantityUnits = (int) round((float) $item->quantity * 1000);
            $weightedGrams = $totalGrams * $quantityUnits;

            return [
                'id' => (int) $item->id,
                'grams' => intdiv($weightedGrams, $totalQuantityUnits),
                'remainder' => $weightedGrams % $totalQuantityUnits,
            ];
        })->all();

        $remainingGrams = $totalGrams - array_sum(array_column($allocations, 'grams'));
        $allocationOrder = $allocations;
        usort($allocationOrder, fn ($left, $right) =>
            ($right['remainder'] <=> $left['remainder']) ?: ($left['id'] <=> $right['id'])
        );

        $bonusItemIds = array_fill_keys(
            array_column(array_slice($allocationOrder, 0, $remainingGrams), 'id'),
            true
        );

        $result = [];
        foreach ($allocations as $allocation) {
            $result[$allocation['id']] = $allocation['grams'] + (isset($bonusItemIds[$allocation['id']]) ? 1 : 0);
        }

        return $result;
    }
}

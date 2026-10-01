<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $stockPlan = [
            ['PATATA AGRIA', 'BOX', 30],
            ['TOMATE RAMA', 'BOX', 240],
            ['MA GOLDEN C', 'BOX', 240],
            ['PEPINO', 'BOX', 240],
            ['CEBOLLA B', 'BOX', 240],
            ['NARANJA C', 'BOX', 520],
            ['PLATANO M B-', 'BOX', 450],
            ['ZANAHORIA', 'KG', 650],
        ];

        $warehouseManagerId = DB::table('users')
            ->where('email', 'warehouse.manager@gmail.com')
            ->value('id');

        foreach ($stockPlan as [$fruitName, $unitCode, $quantity]) {
            $fruit = DB::table('fruits')->where('name', $fruitName)->first();
            $unitId = DB::table('units')->where('code', $unitCode)->value('id');
            if (!$fruit || !$unitId) {
                continue;
            }

            $configuration = $unitCode === 'BOX'
                ? DB::table('fruit_box_configurations')
                    ->where('fruit_id', $fruit->id)
                    ->where('is_default', true)
                    ->first()
                : null;
            if ($unitCode === 'BOX' && !$configuration) {
                continue;
            }

            $configurationId = $configuration?->id;
            $unitWeightKg = $configuration ? (float) $configuration->weight_kg : 1.0;
            $quantityKg = $quantity * $unitWeightKg;
            $stockQuery = DB::table('inventory_stocks')
                ->where('fruit_id', $fruit->id)
                ->where('unit_id', $unitId);
            $configuration
                ? $stockQuery->where('box_configuration_id', $configurationId)
                : $stockQuery->whereNull('box_configuration_id');
            $stockId = $stockQuery->value('id');

            $stockValues = [
                'quantity' => $quantity,
                'reserved_quantity' => 0,
                'quantity_kg' => $quantityKg,
                'reserved_kg' => 0,
                'status' => 'active',
                'updated_at' => now(),
            ];

            if ($stockId) {
                DB::table('inventory_stocks')->where('id', $stockId)->update($stockValues);
            } else {
                DB::table('inventory_stocks')->insert(array_merge([
                    'fruit_id' => $fruit->id,
                    'unit_id' => $unitId,
                    'box_configuration_id' => $configurationId,
                ], $stockValues, [
                    'created_at' => now(),
                ]));
            }

            $priceQuery = DB::table('fruit_prices')
                ->where('fruit_id', $fruit->id)
                ->where('unit_id', $unitId)
                ->where('is_active', true);
            $configuration
                ? $priceQuery->where('box_configuration_id', $configurationId)
                : $priceQuery->whereNull('box_configuration_id');
            $price = $priceQuery->orderByDesc('effective_from')->value('price') ?? 0;
            $notes = 'Demo opening inventory balance';

            $transactionQuery = DB::table('inventory_transactions')
                ->where('fruit_id', $fruit->id)
                ->where('unit_id', $unitId)
                ->where('type', 'adjustment')
                ->where('notes', $notes);
            $configuration
                ? $transactionQuery->where('box_configuration_id', $configurationId)
                : $transactionQuery->whereNull('box_configuration_id');
            $transactionId = $transactionQuery->value('id');

            $transactionValues = [
                'quantity' => $quantity,
                'quantity_kg' => $quantityKg,
                'unit_price' => $price,
                'total_price' => round($quantity * (float) $price, 2),
                'reference_order_id' => null,
                'created_by' => $warehouseManagerId,
                'updated_at' => now(),
            ];

            if ($transactionId) {
                DB::table('inventory_transactions')
                    ->where('id', $transactionId)
                    ->update($transactionValues);
            } else {
                DB::table('inventory_transactions')->insert(array_merge([
                    'fruit_id' => $fruit->id,
                    'unit_id' => $unitId,
                    'box_configuration_id' => $configurationId,
                    'type' => 'adjustment',
                    'notes' => $notes,
                ], $transactionValues, [
                    'created_at' => now(),
                ]));
            }
        }
    }
}

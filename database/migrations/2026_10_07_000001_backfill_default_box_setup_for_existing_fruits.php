<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $fruits = DB::table('fruits')
            ->where('allow_box', true)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('fruit_box_configurations')
                    ->whereColumn('fruit_box_configurations.fruit_id', 'fruits.id');
            })
            ->get(['id']);

        if ($fruits->isEmpty()) {
            return;
        }

        $boxUnitId = DB::table('units')
            ->where('code', 'BOX')
            ->where('is_order_unit', true)
            ->value('id');

        if (! $boxUnitId) {
            throw new RuntimeException('Cannot backfill fruit box setup because the BOX order unit is missing.');
        }

        DB::transaction(function () use ($fruits, $boxUnitId): void {
            foreach ($fruits as $fruit) {
                $now = now();
                $boxId = DB::table('fruit_box_configurations')->insertGetId([
                    'fruit_id' => $fruit->id,
                    'name' => 'Caja estándar',
                    'code' => 'AUTO-STD',
                    'weight_kg' => 10,
                    'is_default' => true,
                    'status' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('fruit_prices')->insert([
                    'fruit_id' => $fruit->id,
                    'unit_id' => $boxUnitId,
                    'box_configuration_id' => $boxId,
                    'price' => 0,
                    'currency' => 'EUR',
                    'effective_from' => $now->toDateString(),
                    'is_active' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });
    }

    public function down(): void
    {
        $boxIds = DB::table('fruit_box_configurations')
            ->where('code', 'AUTO-STD')
            ->pluck('id');

        DB::table('fruit_prices')->whereIn('box_configuration_id', $boxIds)->delete();
        DB::table('fruit_box_configurations')->whereIn('id', $boxIds)->delete();
    }
};

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $boxUnit = DB::table('units')->where('code', 'BOX')->value('id');
        $kgUnit = DB::table('units')->where('code', 'KG')->value('id');

        // TEST SCENARIO:
        // PATATA AGRIA = 30 cajas available.
        // Demo orders below can request 10+ boxes, so warehouse can test
        // "available / required / remaining / purchase needed".
        $potatoId = DB::table('fruits')->where('name', 'PATATA AGRIA')->value('id');

        if ($potatoId) {
            $boxId = DB::table('fruit_box_configurations')
                ->where('fruit_id', $potatoId)
                ->where('is_default', true)
                ->value('id');

            DB::table('inventory_stocks')->updateOrInsert(
                [
                    'fruit_id' => $potatoId,
                    'unit_id' => $boxUnit,
                    'box_configuration_id' => $boxId,
                ],
                [
                    'quantity' => 30,
                    'reserved_quantity' => 0,
                    'quantity_kg' => 300,
                    'reserved_kg' => 0,
                    'status' => 'active',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // A second KG-based test stock.
        $carrotId = DB::table('fruits')->where('name', 'ZANAHORIA')->value('id');

        if ($carrotId) {
            DB::table('inventory_stocks')->updateOrInsert(
                [
                    'fruit_id' => $carrotId,
                    'unit_id' => $kgUnit,
                    'box_configuration_id' => null,
                ],
                [
                    'quantity' => 50,
                    'reserved_quantity' => 0,
                    'quantity_kg' => 50,
                    'reserved_kg' => 0,
                    'status' => 'active',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}

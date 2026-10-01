<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'warehouse_name' => ['Central Produce Warehouse', 'string', 'Display name used for warehouse documents.'],
            'currency' => ['EUR', 'string', 'Currency used for catalog and order prices.'],
            'timezone' => ['Europe/Madrid', 'string', 'Local timezone for warehouse operations.'],
            'order_cutoff_time' => ['20:00', 'time', 'Local time when shops should submit next-day orders.'],
            'order_day_offset' => ['1', 'integer', 'Number of days between order submission and delivery.'],
        ];

        foreach ($settings as $key => [$value, $type, $description]) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => $type,
                    'description' => $description,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}

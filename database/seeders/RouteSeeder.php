<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('routes')->insert([
            [
                'from_city' => 'Maijdee',
                'to_city' => 'Dhaka',
                'distance_km' => 180,
                'approx_duration_minutes' => 240,
                'status' => 'active'
            ],
            [
                'from_city' => 'Dhaka',
                'to_city' => 'Maijdee',
                'distance_km' => 180,
                'approx_duration_minutes' => 240,
                'status' => 'active'
            ],
        ]);
    }

}

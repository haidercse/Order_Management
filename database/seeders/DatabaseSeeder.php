<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionGroupSeeder::class,
            RolePermissionSeeder::class,
            MenuSeeder::class,
            SuperAdminSeeder::class,
            WarehouseManagerSeeder::class,
            ShopSeeder::class,
            FruitMasterSeeder::class,
            InventorySeeder::class,
        ]);
    }
}

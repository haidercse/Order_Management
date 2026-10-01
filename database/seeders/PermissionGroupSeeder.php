<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'Menu Management'],
            ['name' => 'Role Management'],
            ['name' => 'Permission Management'],
            ['name' => 'Shop Management'],
            ['name' => 'User Management'],
            ['name' => 'Fruit Management'],
            ['name' => 'Order Management'],
            ['name' => 'Inventory Management'],
            ['name' => 'Report Management'],
        ];

        foreach ($groups as $group) {
            DB::table('permission_groups')->updateOrInsert(
                ['name' => $group['name']],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}

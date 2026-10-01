<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class WarehouseManagerSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'warehouse-manager')->where('guard_name', 'web')->firstOrFail();

        $user = User::updateOrCreate(
            ['email' => 'warehouse.manager@gmail.com'],
            [
                'name' => 'Warehouse Manager',
                'password' => bcrypt('12345678'),
                'shop_id' => null,
            ]
        );

        $user->syncRoles([$role]);
    }
}

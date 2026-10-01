<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'shop-manager')->where('guard_name', 'web')->firstOrFail();

        for ($i = 1; $i <= 85; $i++) {
            $code = 'SHOP-' . str_pad((string)$i, 3, '0', STR_PAD_LEFT);

            $shopId = DB::table('shops')->updateOrInsert(
                ['code' => $code],
                [
                    'name' => 'Fruit Shop ' . str_pad((string)$i, 2, '0', STR_PAD_LEFT),
                    'manager_name' => 'Shop Manager ' . str_pad((string)$i, 2, '0', STR_PAD_LEFT),
                    'phone' => null,
                    'email' => 'shop' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '@example.com',
                    'address' => null,
                    'city' => null,
                    'status' => 'active',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $shopId = DB::table('shops')->where('code', $code)->value('id');

            $user = User::updateOrCreate(
                ['email' => 'shop' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . '@example.com'],
                [
                    'name' => 'Shop Manager ' . str_pad((string)$i, 2, '0', STR_PAD_LEFT),
                    'password' => bcrypt('12345678'),
                    'shop_id' => $shopId,
                ]
            );

            $user->syncRoles([$role]);
        }
    }
}

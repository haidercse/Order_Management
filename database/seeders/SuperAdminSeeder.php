<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'super.admin@gmail.com',
            'password' => bcrypt('12345678')
        ]);

        $role = Role::firstOrCreate(['name' => 'super-admin']);
        $user->assignRole($role);
    }
}
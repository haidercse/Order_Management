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
        $shopNames = [
            'La Huerta', 'Frutas del Turia', 'El Mercado', 'La Cosecha', 'Fruteria Central',
            'Verde y Fresco', 'La Mandarina', 'El Buen Precio', 'Frutas Levante', 'La Parada',
            'Hermanos Garcia', 'La Despensa', 'Frutas Selectas', 'El Campesino', 'Huerta Viva',
            'Frutas Sol', 'La Esquina Verde',
        ];
        $areas = [
            'Ciutat Vella', 'L Eixample', 'Extramurs', 'Campanar', 'La Saidia',
            'El Pla del Real', 'Olivereta', 'Patraix', 'Jesus', 'Quatre Carreres',
            'Poblats Maritims', 'Camins al Grau', 'Algiros', 'Benimaclet',
            'Rascanya', 'Benicalap', 'Mercado Central',
        ];
        $managerNames = [
            'Carlos Navarro', 'Maria Lopez', 'Javier Garcia', 'Lucia Martinez',
            'Antonio Sanchez', 'Carmen Torres', 'Manuel Ruiz', 'Elena Gomez',
            'Pablo Fernandez', 'Isabel Romero', 'David Martin', 'Marta Diaz',
            'Jose Alvarez', 'Sara Moreno', 'Miguel Ortega', 'Ana Castillo',
            'Rafael Molina',
        ];
        $streets = [
            'Calle de la Paz', 'Carrer de Colon', 'Avenida del Puerto',
            'Calle de Xativa', 'Carrer de Quart', 'Avenida de Peris y Valero',
            'Calle de Sagunto', 'Carrer de Sueca', 'Avenida del Cid',
        ];

        for ($i = 1; $i <= 85; $i++) {
            $number = str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $code = 'SHOP-' . $number;
            $areaIndex = ($i - 1) % count($areas);
            $area = $areas[$areaIndex];
            $shopEmail = 'shop.' . strtolower($number) . '@example.test';
            $managerEmail = 'manager.' . strtolower($number) . '@example.test';
            $managerName = $managerNames[($i - 1) % count($managerNames)];

            DB::table('shops')->updateOrInsert(
                ['code' => $code],
                [
                    'name' => $shopNames[($i - 1) % count($shopNames)] . ' - ' . $area . ' ' . $number,
                    'manager_name' => $managerName,
                    'phone' => '+34 960 000 ' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'email' => $shopEmail,
                    'address' => $streets[($i - 1) % count($streets)] . ' ' . (10 + $i) . ', ' . $area,
                    'city' => 'Valencia',
                    'status' => 'active',
                    'deleted_at' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $shopId = DB::table('shops')->where('code', $code)->value('id');

            $user = User::updateOrCreate(
                ['email' => $managerEmail],
                [
                    'name' => $managerName,
                    'password' => bcrypt('12345678'),
                    'shop_id' => $shopId,
                ]
            );

            $user->syncRoles([$role]);
        }
    }
}

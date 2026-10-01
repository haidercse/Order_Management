<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FruitMasterSeeder extends Seeder
{
    public function run(): void
    {
        $kgUnit = DB::table('units')->where('code', 'KG')->value('id');
        $boxUnit = DB::table('units')->where('code', 'BOX')->value('id');

        DB::table('units')->updateOrInsert(
            ['code' => 'KG'],
            ['name' => 'Kilogram', 'symbol' => 'kg', 'is_weight_unit' => true, 'is_order_unit' => true, 'status' => true, 'updated_at' => now(), 'created_at' => now()]
        );
        DB::table('units')->updateOrInsert(
            ['code' => 'BOX'],
            ['name' => 'Caja / Box', 'symbol' => 'caja', 'is_weight_unit' => false, 'is_order_unit' => true, 'status' => true, 'updated_at' => now(), 'created_at' => now()]
        );
        DB::table('units')->updateOrInsert(
            ['code' => 'TRAY'],
            ['name' => 'Bandeja / Tray', 'symbol' => 'tray', 'is_weight_unit' => false, 'is_order_unit' => true, 'status' => true, 'updated_at' => now(), 'created_at' => now()]
        );
        DB::table('units')->updateOrInsert(
            ['code' => 'UNIT'],
            ['name' => 'Unit', 'symbol' => 'unit', 'is_weight_unit' => false, 'is_order_unit' => true, 'status' => true, 'updated_at' => now(), 'created_at' => now()]
        );

        $kgUnit = DB::table('units')->where('code', 'KG')->value('id');
        $boxUnit = DB::table('units')->where('code', 'BOX')->value('id');

        $categoryNames = [
            'Fruit' => 'FRUIT',
            'Vegetable' => 'VEGETABLE',
            'Leafy & Herb' => 'LEAFY_HERB',
            'Other Produce' => 'OTHER',
        ];

        foreach ($categoryNames as $name => $code) {
            DB::table('categories')->updateOrInsert(
                ['code' => $code],
                ['name' => $name, 'status' => true, 'sort_order' => 1, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        $vegetableWords = ['PATATA','CEBOLLA','AJO','PEPINO','TOMATE','PIMIENTO','PIMI','BERENJ','BROCOLI','COL ','CALAB','ZANAHORIA','NABO','RABANO','PUERRO','APIO','ESPARRAG','ESPINACA','LECHUGA','JUDIA','MAIZ','HABAS','OKRA','PAK CHOI','LOMBARDA','CHIRIVIA','HINOJO','YUCA','YAME','BATATA','REPOLLO','REMOLACHA'];
        $leafyWords = ['ACELGAS','ALBAHACA','CILANTRO','RUKULA','HIERBABUENA','ENELDO','CANONIGOS','ENDIVIAS','ESCAROLA','GRELOS','LECHUGA'];
        $otherWords = ['BOLSA','BANDEJA','GUANTES','SOPA JULIANA','ROLLO DE MAC','REMO COCIDO','REMO CRUDO','NUECES','SETAS','CHAMPI','CASTAÑA'];

        $fruitNames = [
            "ACELGAS",
            "AGUACATE",
            "AGUACATE D",
            "AGUACATE OFF",
            "AJETE",
            "AJO",
            "AJO BUTI",
            "AJO PELADO",
            "ALBAHACA",
            "ALBARICOQUE",
            "ALCACHOFAS",
            "ALOVERA",
            "APIO",
            "ARANDANOS",
            "BANANAS C",
            "BANANAS B",
            "BATATA",
            "BATATA ROJA",
            "BERENJENA",
            "BERENJENA LA",
            "BERENJEN PEQ",
            "BOLSA B",
            "BOLSA RO",
            "BOLSA V",
            "BROCOLI",
            "CALABACIN",
            "CALABAZA",
            "CALABAZA AUY",
            "CALABAZA PEQ",
            "CANARIAS",
            "CANONIGOS",
            "CASTAÑA",
            "CEBOLLA B",
            "CEBOLLAS DUL",
            "CEBOLLAS G",
            "CEBOLLA ROJA",
            "CEBOLLATA",
            "CEBOLLA FINA",
            "CEBOLLA A",
            "CEREZAS",
            "CHAMPIÑON",
            "CHAMPI BAN",
            "CHAMPI COR",
            "CHIRIMOLLA",
            "CHIRIVIA",
            "CIDRA",
            "CILANTRO",
            "CIRUELAS AMA",
            "CIRUELA CLAU",
            "CIRUELAS N",
            "CIRUELAS R",
            "COCO",
            "COL CHINA",
            "COL DE BRUSE",
            "COLIFLOR",
            "DATIL 1C",
            "EDO",
            "ENDIVIAS",
            "ENELDO",
            "ENSALADA",
            "ESCAROLA",
            "ESPARRAGOS C",
            "ESPARRAGOS B",
            "ESPINACA",
            "ESPINACA MAN",
            "FRAMBUESA",
            "FRESAS BAN",
            "FRESAS C",
            "FRESAS B",
            "GOYAVA",
            "GRANADA",
            "GRANADILLA",
            "GRELOS",
            "GUANTES",
            "GUINDIAS",
            "GUINEO VERDE",
            "HABAS",
            "HIERBABUENA",
            "HIGOS B",
            "HIGOS CHUM",
            "HIGOS N",
            "HIGO SECO",
            "HINOJO",
            "JENGIBRE",
            "JUDIA BOBBY",
            "JUDIA LARGA",
            "JUDIA VERD C",
            "JUDIA VER OFF",
            "KAKI B-",
            "KIWI B-",
            "KIWI GOLDEN",
            "KIWI ZESPRI",
            "KOROLA",
            "LECHUGA BAT",
            "LECHUGA COG",
            "LECHUGA ISA",
            "LECHUGA LAR",
            "LEGUSTAN",
            "LIMA C",
            "LIMA OFF",
            "LIMON",
            "LIMON BOL",
            "LOMBARDA",
            "MAIZ COCIDO",
            "MAIZ VERDE S",
            "MANDARINA C",
            "MANDARI NOR",
            "MANDARI OFF",
            "MANDARIN RAMA",
            "MANGO C",
            "MANGO OFF",
            "MA DONCELLA",
            "MANDANZA FUJ",
            "MA GOLDEN C",
            "M GOLDEN OFF",
            "MAN GRAN C",
            "MAN GRAN OFF",
            "MAN KANZI",
            "MAN PERLIM",
            "MAN PINK",
            "M REINETA C",
            "M REINETA OFF",
            "MAN ROYEL C",
            "MA ROYEL OFF",
            "MAN STARKING",
            "MAN VALVEN",
            "MELOCOTON C",
            "MELOCOTON B",
            "MELOCOTON R",
            "MELON C",
            "MELON OFF",
            "MELON CANTA",
            "MELON GALIA",
            "MEMBRILLO",
            "MORA",
            "NABO",
            "NABO RAMA/CHINA",
            "NARANJA C",
            "NARANJA OFF",
            "NARANJ ZUMO",
            "NARANJA B",
            "NARANJ BOL C",
            "NARANJ BOL B",
            "NECTARINA",
            "NISCALOS",
            "NISPERO",
            "NUECES",
            "OKRA",
            "PAK CHOI",
            "PAPAYA C",
            "PAPAYA OFF",
            "PARAGUAYA",
            "PATATA AGRIA",
            "PATATA BOLSA",
            "PATATA LAVA",
            "PATATA ROJA",
            "PATATA SUCIA",
            "PEPINO",
            "PERA AGUA",
            "PERA CHINA",
            "PERA C",
            "PERA CON OFF",
            "PERA ERCOLIN",
            "PERA CONFERENCIA OFF",
            "PERIJO",
            "PICANTE AFRI",
            "PICANTE BAN",
            "PICOTAS",
            "PIMIENTA ITA C",
            "PIMIENTO ITA B",
            "PIMITA ROJO",
            "PIMI PADRON",
            "PIMI ROJO C",
            "PIMI ROJO OFF",
            "PIMI VERDE C",
            "PIMI VERDE OFF",
            "PIÑA C",
            "PIÑA DEL MON",
            "PIÑA OFF",
            "PITHAYA",
            "PLATANO M B-",
            "PLATANO V B-",
            "POMELO",
            "POMELO CHIN",
            "PUERRO",
            "RABANO",
            "RAIZ DE APIO",
            "REMO COCIDO",
            "REMO CRUDO",
            "REPOLLO",
            "ROLLO DE MAC",
            "RUKULA",
            "SANDIA B",
            "SANDIA F",
            "SANDIA MAR",
            "SANDIA NEGRA",
            "SANDIA PLASE",
            "SANDIA ROLLO G P",
            "SETAS BANDO",
            "SETAS S",
            "SOPA JULIANA",
            "TOMATE CHER",
            "TOMATE ENSA",
            "TOMATE KUMA",
            "TOMATE OFF",
            "TOMATE PERA",
            "TOMATE RAF",
            "TOMATE RAMA",
            "TOMATE ROSA",
            "UVAS BANDEJA",
            "UVAS BLANC",
            "UVAS NEGR C",
            "UVAS PEQ",
            "YAME",
            "YUCA C B-",
            "ZANAHORIA",
            "BANDEJA P M G"
];

        foreach ($fruitNames as $index => $name) {
            $upper = mb_strtoupper($name);
            $category = 'FRUIT';

            foreach ($otherWords as $word) {
                if (mb_strpos($upper, $word) !== false) { $category = 'OTHER'; break; }
            }
            foreach ($vegetableWords as $word) {
                if (mb_strpos($upper, $word) !== false) { $category = 'VEGETABLE'; break; }
            }
            foreach ($leafyWords as $word) {
                if (mb_strpos($upper, $word) !== false) { $category = 'LEAFY_HERB'; break; }
            }

            $categoryId = DB::table('categories')->where('code', $category)->value('id');
            $code = 'FR-' . str_pad((string)($index + 1), 4, '0', STR_PAD_LEFT);

            DB::table('fruits')->updateOrInsert(
                ['code' => $code],
                [
                    'category_id' => $categoryId,
                    'name' => $name,
                    'display_name' => $name,
                    'default_unit_id' => $kgUnit,
                    'allow_kg' => true,
                    'allow_box' => true,
                    'status' => true,
                    'sort_order' => $index + 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $fruitId = DB::table('fruits')->where('code', $code)->value('id');

            // TEST DATA ONLY: one example box configuration per product.
            // Replace weights with the real box/caja weights before production.
            DB::table('fruit_box_configurations')->updateOrInsert(
                ['fruit_id' => $fruitId, 'code' => 'STD'],
                [
                    'name' => 'Standard Test Caja',
                    'weight_kg' => 10.000,
                    'is_default' => true,
                    'status' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $boxConfigId = DB::table('fruit_box_configurations')
                ->where('fruit_id', $fruitId)->where('code', 'STD')->value('id');

            // TEST DATA ONLY: prices are placeholders so PDF/Excel totals can be tested.
            DB::table('fruit_prices')->updateOrInsert(
                [
                    'fruit_id' => $fruitId,
                    'unit_id' => $kgUnit,
                    'box_configuration_id' => null,
                    'effective_from' => '2026-01-01',
                ],
                [
                    'price' => 1.00,
                    'currency' => 'EUR',
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            DB::table('fruit_prices')->updateOrInsert(
                [
                    'fruit_id' => $fruitId,
                    'unit_id' => $boxUnit,
                    'box_configuration_id' => $boxConfigId,
                    'effective_from' => '2026-01-01',
                ],
                [
                    'price' => 10.00,
                    'currency' => 'EUR',
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}

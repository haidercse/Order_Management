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
            ['name' => 'Kilogramo', 'symbol' => 'kg', 'is_weight_unit' => true, 'is_order_unit' => true, 'status' => true, 'updated_at' => now(), 'created_at' => now()]
        );
        DB::table('units')->updateOrInsert(
            ['code' => 'BOX'],
            ['name' => 'Caja', 'symbol' => 'caja', 'is_weight_unit' => false, 'is_order_unit' => true, 'status' => true, 'updated_at' => now(), 'created_at' => now()]
        );
        DB::table('units')->updateOrInsert(
            ['code' => 'TRAY'],
            ['name' => 'Bandeja', 'symbol' => 'bandeja', 'is_weight_unit' => false, 'is_order_unit' => true, 'status' => true, 'updated_at' => now(), 'created_at' => now()]
        );
        DB::table('units')->updateOrInsert(
            ['code' => 'UNIT'],
            ['name' => 'Unidad', 'symbol' => 'ud', 'is_weight_unit' => false, 'is_order_unit' => true, 'status' => true, 'updated_at' => now(), 'created_at' => now()]
        );

        $kgUnit = DB::table('units')->where('code', 'KG')->value('id');
        $boxUnit = DB::table('units')->where('code', 'BOX')->value('id');

        $categoryNames = [
            'Frutas' => 'FRUIT',
            'Verduras' => 'VEGETABLE',
            'Hojas y hierbas' => 'LEAFY_HERB',
            'Otros productos' => 'OTHER',
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

        $productProfiles = [
            'PATATA AGRIA' => ['VEGETABLE', 20.000, 0.82],
            'PATATA ROJA' => ['VEGETABLE', 20.000, 0.95],
            'TOMATE RAMA' => ['VEGETABLE', 6.000, 1.45],
            'NARANJA C' => ['FRUIT', 10.000, 0.88],
            'PLATANO M B-' => ['FRUIT', 18.000, 1.35],
            'ZANAHORIA' => ['VEGETABLE', 10.000, 0.78],
            'CEBOLLA B' => ['VEGETABLE', 15.000, 0.72],
            'AGUACATE' => ['FRUIT', 4.000, 3.20],
            'LIMON' => ['FRUIT', 8.000, 1.10],
            'MA GOLDEN C' => ['FRUIT', 13.000, 1.28],
            'PEPINO' => ['VEGETABLE', 6.000, 1.05],
            'LECHUGA BAT' => ['LEAFY_HERB', 4.000, 0.82],
            'FRESAS B' => ['FRUIT', 2.000, 3.75],
            'PIMIENTO ITA B' => ['VEGETABLE', 5.000, 1.85],
            'BROCOLI' => ['VEGETABLE', 6.000, 1.55],
            'CALABACIN' => ['VEGETABLE', 7.000, 1.12],
            'PERA C' => ['FRUIT', 10.000, 1.48],
            'UVAS BLANC' => ['FRUIT', 5.000, 2.35],
            'PIÑA C' => ['FRUIT', 12.000, 1.25],
            'BATATA' => ['VEGETABLE', 10.000, 1.05],
            'CHAMPIÑON' => ['VEGETABLE', 3.000, 2.40],
            'AJO' => ['VEGETABLE', 5.000, 2.60],
            'APIO' => ['VEGETABLE', 5.000, 1.10],
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

            $profile = $productProfiles[$name] ?? null;
            if ($profile) {
                $category = $profile[0];
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

            DB::table('fruit_box_configurations')->updateOrInsert(
                ['fruit_id' => $fruitId, 'code' => 'STD'],
                [
                    'name' => 'Caja estándar',
                    'weight_kg' => 10.000,
                    'is_default' => true,
                    'status' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $boxConfigId = DB::table('fruit_box_configurations')
                ->where('fruit_id', $fruitId)->where('code', 'STD')->value('id');

            $boxWeightKg = $profile[1] ?? match ($category) {
                'FRUIT' => 8.000,
                'VEGETABLE' => 10.000,
                'LEAFY_HERB' => 3.000,
                default => 5.000,
            };
            $basePricePerKg = $profile[2] ?? match ($category) {
                'FRUIT' => 2.10,
                'VEGETABLE' => 1.35,
                'LEAFY_HERB' => 2.40,
                default => 2.75,
            };
            $pricePerKg = round($basePricePerKg + (($index % 7) * 0.07), 2);

            DB::table('fruit_box_configurations')
                ->where('id', $boxConfigId)
                ->update(['weight_kg' => $boxWeightKg, 'updated_at' => now()]);

            $this->upsertPrice(
                [
                    'fruit_id' => $fruitId,
                    'unit_id' => $kgUnit,
                    'box_configuration_id' => null,
                    'effective_from' => '2026-01-01',
                ],
                [
                    'price' => $pricePerKg,
                    'currency' => 'EUR',
                    'is_active' => true,
                ]
            );

            $this->upsertPrice(
                [
                    'fruit_id' => $fruitId,
                    'unit_id' => $boxUnit,
                    'box_configuration_id' => $boxConfigId,
                    'effective_from' => '2026-01-01',
                ],
                [
                    'price' => round($pricePerKg * $boxWeightKg, 2),
                    'currency' => 'EUR',
                    'is_active' => true,
                ]
            );
        }
    }

    private function upsertPrice(array $identity, array $values): void
    {
        $query = DB::table('fruit_prices');
        foreach ($identity as $column => $value) {
            $value === null
                ? $query->whereNull($column)
                : $query->where($column, $value);
        }

        $priceId = $query->value('id');
        $values['updated_at'] = now();

        if ($priceId) {
            DB::table('fruit_prices')->where('id', $priceId)->update($values);
            return;
        }

        DB::table('fruit_prices')->insert(array_merge($identity, $values, [
            'created_at' => now(),
        ]));
    }
}

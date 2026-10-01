<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DailyOrderSeeder extends Seeder
{
    public function run(): void
    {
        $orderDate = today()->addDay()->toDateString();
        $specialProducts = [
            'PATATA AGRIA',
            'TOMATE RAMA',
            'MA GOLDEN C',
            'PEPINO',
            'CEBOLLA B',
        ];

        DB::transaction(function () use ($orderDate, $specialProducts): void {
            for ($shopNumber = 1; $shopNumber <= 85; $shopNumber++) {
                $shopCode = 'SHOP-' . str_pad((string) $shopNumber, 3, '0', STR_PAD_LEFT);
                $shop = DB::table('shops')->where('code', $shopCode)->first();
                if (!$shop) {
                    continue;
                }

                $managerId = DB::table('users')->where('shop_id', $shop->id)->value('id');
                $orderAttributes = [
                    'shop_id' => $shop->id,
                    'order_date' => $orderDate,
                ];

                DB::table('orders')->updateOrInsert($orderAttributes, [
                    'status' => 'submitted',
                    'created_by' => $managerId,
                    'submitted_at' => now()->subMinutes($shopNumber),
                    'prepared_at' => null,
                    'ready_at' => null,
                    'sent_at' => null,
                    'notes' => 'Demo order for next-day warehouse planning.',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]);

                $orderId = DB::table('orders')
                    ->where('shop_id', $shop->id)
                    ->where('order_date', $orderDate)
                    ->value('id');

                $lines = [
                    ['fruit' => 'NARANJA C', 'unit' => 'BOX', 'quantity' => 3 + ($shopNumber % 4)],
                    ['fruit' => 'PLATANO M B-', 'unit' => 'BOX', 'quantity' => 3 + ($shopNumber % 3)],
                    [
                        'fruit' => $specialProducts[($shopNumber - 1) % count($specialProducts)],
                        'unit' => 'BOX',
                        'quantity' => 9 + ($shopNumber % 6),
                    ],
                ];

                if ($shopNumber % 3 === 0) {
                    $lines[] = [
                        'fruit' => 'ZANAHORIA',
                        'unit' => 'KG',
                        'quantity' => 8 + ($shopNumber % 13),
                    ];
                }

                foreach ($lines as $line) {
                    $fruit = DB::table('fruits')->where('name', $line['fruit'])->first();
                    $unitId = DB::table('units')->where('code', $line['unit'])->value('id');
                    if (!$fruit || !$unitId) {
                        throw new RuntimeException('Missing seeded product or unit for ' . $line['fruit']);
                    }

                    $configuration = null;
                    if ($line['unit'] === 'BOX') {
                        $configuration = DB::table('fruit_box_configurations')
                            ->where('fruit_id', $fruit->id)
                            ->where('is_default', true)
                            ->first();
                        if (!$configuration) {
                            throw new RuntimeException('Missing default caja configuration for ' . $line['fruit']);
                        }
                    }

                    $priceQuery = DB::table('fruit_prices')
                        ->where('fruit_id', $fruit->id)
                        ->where('unit_id', $unitId)
                        ->where('is_active', true)
                        ->whereDate('effective_from', '<=', $orderDate)
                        ->where(function ($query) use ($orderDate): void {
                            $query->whereNull('effective_to')
                                ->orWhereDate('effective_to', '>=', $orderDate);
                        });

                    $configuration
                        ? $priceQuery->where('box_configuration_id', $configuration->id)
                        : $priceQuery->whereNull('box_configuration_id');

                    $price = $priceQuery->orderByDesc('effective_from')->first();
                    if (!$price) {
                        throw new RuntimeException('Missing active price for ' . $line['fruit']);
                    }

                    $unitWeightKg = $configuration ? (float) $configuration->weight_kg : null;
                    $convertedKg = $configuration
                        ? $line['quantity'] * $unitWeightKg
                        : $line['quantity'];
                    $itemIdentity = [
                        'order_id' => $orderId,
                        'fruit_id' => $fruit->id,
                        'unit_id' => $unitId,
                        'box_configuration_id' => $configuration?->id,
                    ];

                    $itemQuery = DB::table('order_items')
                        ->where('order_id', $orderId)
                        ->where('fruit_id', $fruit->id)
                        ->where('unit_id', $unitId);
                    $configuration
                        ? $itemQuery->where('box_configuration_id', $configuration->id)
                        : $itemQuery->whereNull('box_configuration_id');
                    $itemId = $itemQuery->value('id');

                    $itemValues = [
                        'quantity' => $line['quantity'],
                        'unit_weight_kg' => $unitWeightKg,
                        'converted_kg' => $convertedKg,
                        'unit_price' => $price->price,
                        'line_total' => round($line['quantity'] * (float) $price->price, 2),
                        'notes' => null,
                        'updated_at' => now(),
                    ];

                    if ($itemId) {
                        DB::table('order_items')->where('id', $itemId)->update($itemValues);
                    } else {
                        DB::table('order_items')->insert(array_merge($itemIdentity, $itemValues, [
                            'created_at' => now(),
                        ]));
                    }
                }
            }
        });
    }
}

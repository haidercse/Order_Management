<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Fruit;
use App\Models\FruitBoxConfiguration;
use App\Models\FruitPrice;
use App\Models\Shop;
use App\Models\SystemSetting;
use App\Models\Unit;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShopOrderSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_can_save_an_order_with_more_than_one_hundred_fruits(): void
    {
        $shop = Shop::create(['code' => 'SHOP-101', 'name' => 'Large Order Shop']);
        $user = User::factory()->create(['shop_id' => $shop->id]);
        $user->assignRole(Role::create(['name' => 'shop-manager', 'guard_name' => 'web']));

        SystemSetting::create([
            'key' => 'timezone',
            'value' => config('app.timezone'),
            'type' => 'string',
        ]);
        SystemSetting::create([
            'key' => 'order_day_offset',
            'value' => '1',
            'type' => 'integer',
        ]);

        $category = Category::create(['name' => 'Fruit', 'code' => 'FRUIT']);
        $unit = Unit::create([
            'name' => 'Caja',
            'code' => 'BOX',
            'symbol' => 'Cj',
            'is_weight_unit' => false,
            'is_order_unit' => true,
            'status' => true,
        ]);
        $effectiveFrom = CarbonImmutable::today(config('app.timezone'))->addDay()->toDateString();
        $items = [];

        for ($number = 1; $number <= 101; $number++) {
            $fruit = Fruit::create([
                'category_id' => $category->id,
                'code' => sprintf('FRUIT-%03d', $number),
                'name' => sprintf('Fruit %03d', $number),
                'default_unit_id' => $unit->id,
                'allow_kg' => false,
                'allow_box' => true,
                'status' => true,
            ]);
            $box = FruitBoxConfiguration::create([
                'fruit_id' => $fruit->id,
                'name' => 'Standard box',
                'code' => 'STANDARD',
                'weight_kg' => 1,
                'is_default' => true,
                'status' => true,
            ]);
            FruitPrice::create([
                'fruit_id' => $fruit->id,
                'unit_id' => $unit->id,
                'box_configuration_id' => $box->id,
                'price' => 1,
                'currency' => 'EUR',
                'effective_from' => $effectiveFrom,
                'is_active' => true,
            ]);

            $items[] = [
                'fruit_id' => $fruit->id,
                'unit_id' => $unit->id,
                'box_configuration_id' => $box->id,
                'quantity' => 1,
            ];
        }

        $response = $this->actingAs($user)
            ->postJson(route('shop.orders.draft'), ['items' => $items]);

        $response->assertOk()->assertJsonPath('status', 'success');
        $this->assertDatabaseCount('order_items', 101);
    }
}

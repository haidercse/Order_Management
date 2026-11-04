<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Shop;
use App\Models\SystemSetting;
use App\Models\User;
use App\Support\ShopOrderHistory;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShopOrderHistoryQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_is_limited_to_the_shop_date_range_and_submitted_orders(): void
    {
        $shop = Shop::create(['code' => 'SHOP-1', 'name' => 'Shop One']);
        $otherShop = Shop::create(['code' => 'SHOP-2', 'name' => 'Shop Two']);

        $included = Order::create([
            'shop_id' => $shop->id,
            'order_date' => '2026-10-05',
            'status' => 'submitted',
        ]);
        Order::create([
            'shop_id' => $shop->id,
            'order_date' => '2026-10-06',
            'status' => 'draft',
        ]);
        Order::create([
            'shop_id' => $otherShop->id,
            'order_date' => '2026-10-05',
            'status' => 'submitted',
        ]);
        Order::create([
            'shop_id' => $shop->id,
            'order_date' => '2026-10-08',
            'status' => 'sent',
        ]);

        $orders = (new ShopOrderHistory())->forShopBetween(
            $shop,
            CarbonImmutable::parse('2026-10-01'),
            CarbonImmutable::parse('2026-10-07')
        )->get();

        $this->assertSame([$included->id], $orders->modelKeys());
    }

    public function test_history_data_endpoint_returns_json_for_the_authenticated_shops_orders(): void
    {
        $shop = Shop::create(['code' => 'SHOP-3', 'name' => 'History Shop']);
        $otherShop = Shop::create(['code' => 'SHOP-4', 'name' => 'Other Shop']);
        $user = User::factory()->create(['shop_id' => $shop->id]);
        $user->assignRole(Role::create(['name' => 'shop-manager', 'guard_name' => 'web']));
        SystemSetting::create([
            'key' => 'timezone',
            'value' => config('app.timezone'),
            'type' => 'string',
        ]);
        $today = CarbonImmutable::today(config('app.timezone'))->toDateString();

        $included = Order::create([
            'shop_id' => $shop->id,
            'order_date' => $today,
            'status' => 'submitted',
        ]);
        Order::create([
            'shop_id' => $otherShop->id,
            'order_date' => $today,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('shop.orders.history.data', ['history' => 'today']));
        $response
            ->assertOk()
            ->assertJsonPath('history', 'today')
            ->assertJsonPath('from', $today)
            ->assertJsonPath('to', $today)
            ->assertJsonPath('order_count', 1)
            ->assertJsonPath('orders.0.id', $included->id);
    }
}

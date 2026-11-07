<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Fruit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop;
use App\Models\Unit;
use App\Models\User;
use App\Support\OrderCajasSummaryBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class OrderCajasPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_combines_submitted_caja_orders_across_shops_only_for_the_selected_date(): void
    {
        [$date] = $this->createOrders();

        $items = app(OrderCajasSummaryBuilder::class)->build($date);

        $this->assertCount(2, $items);
        $this->assertSame('Apple', $items[0]->name);
        $this->assertEquals(5.5, (float) $items[0]->cajas);
        $this->assertSame('Orange', $items[1]->name);
        $this->assertEquals(4, (float) $items[1]->cajas);
    }

    public function test_fruit_cajas_pdf_download_has_only_fruit_and_cajas_columns(): void
    {
        [$date] = $this->createOrders();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $role = Role::create(['name' => 'warehouse-manager', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'order.prepare', 'guard_name' => 'web']));
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)
            ->get(route('admin.orders.fruit-cajas-pdf', ['date' => $date]));

        $response->assertOk()->assertDownload("fruit-cajas-{$date}.pdf");
        $this->assertStringStartsWith('%PDF', $response->getContent());

        $html = view('backend.pages.orders.fruit-cajas-pdf', [
            'date' => $date,
            'shopCount' => 2,
            'items' => app(OrderCajasSummaryBuilder::class)->build($date),
        ])->render();
        $this->assertSame(4, substr_count($html, '<th>Fruit / Cajas</th>'));
        $this->assertStringContainsString('Apple', $html);
        $this->assertStringContainsString('cajas', $html);
        $this->assertStringNotContainsString('Price', $html);
        $this->assertStringNotContainsString('PRECIO', $html);
    }

    public function test_fruit_cajas_pdf_places_four_fruits_on_each_row(): void
    {
        $items = collect(range(1, 9))->map(fn (int $number) => (object) [
            'name' => "Fruit {$number}",
            'cajas' => $number,
        ]);

        $html = view('backend.pages.orders.fruit-cajas-pdf', [
            'date' => '2026-10-07',
            'shopCount' => 1,
            'items' => $items,
        ])->render();

        $this->assertSame(4, substr_count($html, '<th>Fruit / Cajas</th>'));
        $this->assertSame(4, substr_count($html, '<tr>'));
        $this->assertStringContainsString('Fruit 1', $html);
        $this->assertStringContainsString('Fruit 4', $html);
        $this->assertStringContainsString('Fruit 9', $html);
        $this->assertSame(3, substr_count($html, '<td></td>'));
    }

    public function test_fruit_cajas_excel_download_contains_fruit_and_cajas_for_the_selected_date(): void
    {
        [$date] = $this->createOrders();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $role = Role::create(['name' => 'warehouse-manager', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'order.prepare', 'guard_name' => 'web']));
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)
            ->get(route('admin.orders.fruit-cajas-excel', ['date' => $date]));

        $response->assertOk()->assertDownload("fruit-cajas-{$date}.csv");
        $csv = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBFFruit,Cajas", $csv);
        $this->assertStringContainsString('Apple,5.5', $csv);
        $this->assertStringContainsString('Orange,4', $csv);
    }

    private function createOrders(): array
    {
        $date = '2026-10-07';
        $caja = Unit::create([
            'name' => 'Caja',
            'code' => 'BOX',
            'symbol' => 'caja',
        ]);
        $kilogram = Unit::create([
            'name' => 'Kilogram',
            'code' => 'KG',
            'symbol' => 'kg',
            'is_weight_unit' => true,
        ]);
        $category = Category::create(['name' => 'Fruit', 'code' => 'FRUIT']);
        $apple = Fruit::create([
            'category_id' => $category->id,
            'code' => 'APPLE',
            'name' => 'Apple',
            'default_unit_id' => $caja->id,
        ]);
        $orange = Fruit::create([
            'category_id' => $category->id,
            'code' => 'ORANGE',
            'name' => 'Orange',
            'default_unit_id' => $caja->id,
        ]);

        $shops = collect([
            Shop::create(['code' => 'SHOP-1', 'name' => 'Shop One']),
            Shop::create(['code' => 'SHOP-2', 'name' => 'Shop Two']),
        ]);

        foreach ($shops as $index => $shop) {
            $order = Order::create([
                'shop_id' => $shop->id,
                'order_date' => $date,
                'status' => $index === 0 ? 'prepared' : 'sent',
                'submitted_at' => "{$date} 10:00:00",
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'fruit_id' => $apple->id,
                'unit_id' => $caja->id,
                'quantity' => $index === 0 ? 2.5 : 3,
            ]);

            if ($index === 0) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'fruit_id' => $orange->id,
                    'unit_id' => $caja->id,
                    'quantity' => 4,
                ]);
                OrderItem::create([
                    'order_id' => $order->id,
                    'fruit_id' => $apple->id,
                    'unit_id' => $kilogram->id,
                    'quantity' => 99,
                ]);
            }
        }

        $draftShop = Shop::create(['code' => 'SHOP-DRAFT', 'name' => 'Draft Shop']);
        $draft = Order::create([
            'shop_id' => $draftShop->id,
            'order_date' => $date,
            'status' => 'draft',
        ]);
        OrderItem::create([
            'order_id' => $draft->id,
            'fruit_id' => $orange->id,
            'unit_id' => $caja->id,
            'quantity' => 100,
        ]);

        return [$date];
    }
}

<?php

namespace Tests\Feature;

use App\Models\Fruit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class OrderPdfTemplatesTest extends TestCase
{
    public function test_shop_picking_sheet_uses_the_shop_order_and_leaves_warehouse_weight_blank(): void
    {
        [$shop, $order, $fruit, $item] = $this->makeOrderData();
        $item->warehouse_weight_kg = '12.345';

        $html = view('test3_1st', [
            'shop' => $shop,
            'order' => $order,
            'itemsByFruit' => collect([$fruit->id => $item]),
            'fruitRows' => collect([collect([$fruit, null, null, null])]),
        ])->render();

        $this->assertStringContainsString('TEST-SHOP', $html);
        $this->assertStringContainsString('AGUACATE C', $html);
        $this->assertStringContainsString('WEIGHT KG', $html);
        $this->assertStringContainsString('CAJAS', $html);
        $this->assertStringContainsString('3', $html);
        $this->assertStringNotContainsString('12.345', $html);
    }

    public function test_shop_order_history_pdf_lists_ordered_fruit_and_selected_dates(): void
    {
        [$shop, $order, , $item] = $this->makeOrderData();
        $order->status = 'submitted';
        $order->setRelation('items', collect([$item]));

        $html = view('shop.orders.history-pdf', [
            'shop' => $shop,
            'orders' => collect([$order]),
            'from' => CarbonImmutable::parse('2026-10-01'),
            'to' => CarbonImmutable::parse('2026-10-07'),
        ])->render();

        $this->assertStringContainsString('Shop Order History', $html);
        $this->assertStringContainsString('01 Oct 2026', $html);
        $this->assertStringContainsString('07 Oct 2026', $html);
        $this->assertStringContainsString('AGUACATE C', $html);
        $this->assertStringContainsString('3', $html);
        $this->assertStringStartsWith('%PDF', \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4')->output());
    }

    public function test_shop_order_page_places_history_table_and_download_inside_a_modal(): void
    {
        [$shop, $order, , $item] = $this->makeOrderData();
        $order->status = 'submitted';
        $order->submitted_at = '2026-10-07 10:30:00';
        $order->setRelation('items', collect([$item]));

        $html = view('shop.orders.index', [
            'shop' => $shop,
            'order' => $order,
            'orderDate' => '2026-10-08',
            'catalog' => collect(),
            'initialItems' => collect(),
            'unavailableFruitCount' => 0,
            'historyFrom' => CarbonImmutable::parse('2026-10-01'),
            'historyTo' => CarbonImmutable::parse('2026-10-07'),
            'historyPreset' => 'custom',
            'historyOrders' => collect([$order]),
        ])->render();

        $this->assertStringContainsString('<dialog class="order-history-dialog"', $html);
        $this->assertStringContainsString('<th>Date</th>', $html);
        $this->assertStringContainsString('<th>Order</th>', $html);
        $this->assertStringContainsString('<th>Status</th>', $html);
        $this->assertStringContainsString('<th>Fruit</th>', $html);
        $this->assertStringContainsString('<th class="history-quantity">Cajas</th>', $html);
        $this->assertStringContainsString('Download PDF', $html);
        $this->assertStringContainsString('AGUACATE C', $html);
    }

    public function test_draft_shop_order_page_renders_mobile_order_preview_controls(): void
    {
        [$shop] = $this->makeOrderData();

        $html = view('shop.orders.index', [
            'shop' => $shop,
            'order' => null,
            'orderDate' => '2026-10-08',
            'catalog' => collect(),
            'initialItems' => collect(),
            'unavailableFruitCount' => 0,
            'historyFrom' => CarbonImmutable::parse('2026-10-01'),
            'historyTo' => CarbonImmutable::parse('2026-10-07'),
            'historyPreset' => 'custom',
            'historyOrders' => collect(),
        ])->render();

        $this->assertStringContainsString('id="openMobileOrder"', $html);
        $this->assertStringContainsString('id="closeMobileOrder"', $html);
        $this->assertStringContainsString('id="mobilePreviewEmpty"', $html);
        $this->assertStringContainsString('data-quantity-step=', $html);
        $this->assertStringContainsString('88dvh', $html);
        $this->assertStringContainsString('data-letter="ALL"', $html);
        $this->assertStringContainsString('id="orderHistoryModal"', $html);
        $this->assertStringContainsString('id="historyTableBody"', $html);
    }

    public function test_final_warehouse_pdf_shows_calculated_weights_and_whole_currency_grand_total(): void
    {
        [$shop, $order, , $item] = $this->makeOrderData();
        $item->warehouse_weight_kg = '12.345';
        $order->setRelation('items', collect([$item]));

        $html = view('test2', [
            'shop' => $shop,
            'order' => $order,
            'currency' => 'EUR',
            'pdfItems' => collect([
                [
                    'quantity' => 8,
                    'name' => 'BANANAS B',
                    'box_weight' => 18,
                    'total_weight' => 144,
                    'tare' => 0,
                    'net_weight' => 144,
                    'price' => 1.05,
                    'total' => 151.20,
                ],
                [
                    'quantity' => 4,
                    'name' => 'AGUACATE OFF',
                    'box_weight' => null,
                    'total_weight' => 5.5,
                    'tare' => 0.3,
                    'net_weight' => 4.3,
                    'price' => 2.5,
                    'total' => 10.75,
                ],
                [
                    'quantity' => 1,
                    'name' => 'OTHER FRUIT',
                    'box_weight' => null,
                    'total_weight' => 1877.68,
                    'tare' => 0,
                    'net_weight' => 1877.68,
                    'price' => 1,
                    'total' => 1877.68,
                ],
            ]),
        ])->render();

        $this->assertStringContainsString('TEST-SHOP', $html);
        $this->assertStringContainsString('KILO / BOX', $html);
        $this->assertStringContainsString('TARA / BOX', $html);
        $this->assertStringContainsString('144.000', $html);
        $this->assertStringContainsString('4.300', $html);
        $this->assertStringContainsString('€', $html);
        $this->assertStringContainsString('2,040', $html);
        $this->assertStringContainsString('10.75', $html);
        $this->assertStringStartsWith('%PDF', \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'landscape')->output());
    }

    public function test_consolidated_warehouse_pdf_shows_the_combined_quantity_and_saved_weight(): void
    {
        $html = view('backend.pages.orders.warehouse-pdf', [
            'date' => '2026-10-07',
            'warehouseName' => 'Central Warehouse',
            'orders' => collect([new Order, new Order]),
            'items' => collect([
                [
                    'name' => 'AGUACATE C',
                    'quantity' => 12,
                    'shop_count' => 2,
                    'weight' => 47.250,
                ],
            ]),
            'pdfItems' => collect([
                [
                    'quantity' => 12,
                    'name' => 'AGUACATE C',
                    'box_name' => 'Caja estándar',
                    'box_weight' => 4,
                    'total_weight' => 47.250,
                    'tare' => 1.250,
                    'total_tare' => 1.250,
                    'net_weight' => 46.000,
                    'price' => 6.70,
                    'total' => 308.20,
                ],
            ]),
            'allWeightsSaved' => true,
        ])->render();

        $this->assertStringContainsString('BISMILLAHIR RAHMANIR RAHEEM', $html);
        $this->assertStringContainsString('WAREHOUSE : Central Warehouse', $html);
        $this->assertStringContainsString('T KILO', $html);
        $this->assertStringContainsString('TARA', $html);
        $this->assertStringContainsString('AGUACATE C', $html);
        $this->assertStringContainsString('12', $html);
        $this->assertStringContainsString('47.250', $html);
        $this->assertStringContainsString('1.250', $html);
        $this->assertStringContainsString('46.000', $html);
        $this->assertStringContainsString('2', $html);
        $this->assertStringContainsString('6.70', $html);
        $this->assertStringContainsString('308.20', $html);
        $this->assertStringStartsWith('%PDF', \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4')->output());
    }

    private function makeOrderData(): array
    {
        $shop = new Shop;
        $shop->name = 'Test Shop';
        $shop->code = 'TEST-SHOP';

        $order = new Order;
        $order->order_date = '2026-10-07';
        $order->id = 17;

        $fruit = new Fruit;
        $fruit->id = 42;
        $fruit->name = 'AGUACATE C';
        $fruit->display_name = 'AGUACATE C';

        $item = new OrderItem([
            'quantity' => 3,
            'unit_weight_kg' => 4,
            'converted_kg' => 12,
            'warehouse_weight_kg' => null,
            'unit_price' => 6.70,
            'line_total' => 20.10,
        ]);
        $item->setRelation('fruit', $fruit);

        return [$shop, $order, $fruit, $item];
    }
}

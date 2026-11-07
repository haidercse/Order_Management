<?php

namespace Tests\Feature;

use App\Models\Fruit;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop;
use Tests\TestCase;

class AdminOrderDetailsTest extends TestCase
{
    public function test_order_details_only_show_the_simple_scale_weight_table_and_offer_pdf_after_weights_are_saved(): void
    {
        $order = $this->makeOrderWithScaleWeights(['12.500', null]);

        $html = view('backend.pages.orders.partials.details', compact('order'))->render();

        $this->assertStringContainsString('<th>Fruit</th>', $html);
        $this->assertStringContainsString('<th>Cajas</th>', $html);
        $this->assertStringContainsString('<th>Scale weight (kg)</th>', $html);
        $this->assertSame(3, substr_count($html, '<th>'));
        $this->assertStringNotContainsString('Box</th>', $html);
        $this->assertStringNotContainsString('KILO / box', $html);
        $this->assertStringNotContainsString('T KILO', $html);
        $this->assertStringNotContainsString('TARA / box', $html);
        $this->assertStringContainsString('name="weights[9]"', $html);
        $this->assertStringContainsString('name="weights[10]"', $html);
        $this->assertSame(2, substr_count($html, 'type="number"'));
        $this->assertStringNotContainsString('Fixed weight', $html);
        $this->assertStringContainsString('Save all scale weights to generate the PDF.', $html);
        $this->assertStringNotContainsString('>Generate PDF</a>', $html);
        $this->assertStringContainsString('>Delivery PDF</a>', $html);

        $order = $this->makeOrderWithScaleWeights(['12.500', '8.250']);

        $html = view('backend.pages.orders.partials.details', compact('order'))->render();

        $this->assertStringContainsString('>Generate PDF</a>', $html);
        $this->assertStringContainsString('>Delivery PDF</a>', $html);
        $this->assertStringNotContainsString('Save all scale weights to generate the PDF.', $html);
    }

    private function makeOrderWithScaleWeights(array $weights): Order
    {
        $shop = new Shop(['name' => 'Test Shop', 'code' => 'TEST']);
        $order = new Order(['status' => 'submitted', 'order_date' => '2026-10-07']);
        $order->id = 17;
        $order->setRelation('shop', $shop);

        $fruit = new Fruit(['name' => 'Apple', 'display_name' => 'Apple']);
        $items = collect($weights)->values()->map(function (?string $weight, int $index) use ($fruit): OrderItem {
            $item = new OrderItem([
                'quantity' => 3,
                'unit_weight_kg' => $index === 0 ? 12 : null,
                'warehouse_weight_kg' => $weight,
            ]);
            $item->id = 9 + $index;
            $item->setRelation('fruit', $fruit);

            return $item;
        });
        $order->setRelation('items', $items);

        return $order;
    }
}

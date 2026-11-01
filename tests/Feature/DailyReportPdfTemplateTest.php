<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DailyReportPdfTemplateTest extends TestCase
{
    public function test_daily_report_pdf_uses_the_pdf_style_layout_and_keeps_report_data(): void
    {
        $shop = new Shop;
        $shop->name = 'Test Shop';
        $shop->code = 'TEST-SHOP';

        $order = new Order;
        $order->id = 17;
        $order->status = 'submitted';
        $order->submitted_at = Carbon::parse('2026-10-07 09:30:00');
        $order->report_value = 20.10;
        $order->setRelation('shop', $shop);
        $order->setRelation('items', collect([new OrderItem]));

        $html = view('backend.pages.reports.pdf', [
            'date' => '2026-10-07',
            'warehouseName' => 'Test Warehouse',
            'currency' => 'EUR',
            'items' => collect([[
                'name' => 'Dragon fruit',
                'code' => '1000000000001',
                'demand' => 2,
                'demand_kg' => 20,
                'demand_boxes' => 2,
                'available' => 1,
                'unit' => 'caja',
                'price_per_kg' => 2.50,
                'price_per_box' => 25,
                'value' => 50,
            ]]),
            'orders' => collect([$order]),
            'statusLabels' => ['submitted' => 'Submitted'],
            'orderCount' => 1,
            'shopCount' => 1,
            'totalValue' => 50,
            'shortageCount' => 1,
        ])->render();

        $this->assertStringContainsString('BISMILLAHIR RAHMANIR RAHEEM', $html);
        $this->assertStringContainsString('font-family: Arial, sans-serif', $html);
        $this->assertStringContainsString('font-size: 13px', $html);
        $this->assertStringContainsString('border: 1px solid #333', $html);
        $this->assertStringContainsString('WAREHOUSE : Test Warehouse', $html);
        $this->assertStringContainsString('FECHA : 07/10/2026', $html);
        $this->assertStringContainsString('Dragon fruit (1000000000001)', $html);
        $this->assertStringContainsString('TEST-SHOP', $html);
        $this->assertStringContainsString('50.00', $html);
        $this->assertStringStartsWith('%PDF', Pdf::loadHTML($html)->setPaper('a4')->output());
    }
}

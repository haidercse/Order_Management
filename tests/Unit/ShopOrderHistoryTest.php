<?php

namespace Tests\Unit;

use App\Support\ShopOrderHistory;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class ShopOrderHistoryTest extends TestCase
{
    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_it_resolves_today_yesterday_last_seven_days_and_last_month(): void
    {
        $today = CarbonImmutable::parse('2026-11-04 15:30:00', 'Europe/Madrid');
        $history = new ShopOrderHistory();

        [$from, $to] = $history->dateRange('today', null, null, 'Europe/Madrid', $today);
        $this->assertSame('2026-11-04 00:00:00', $from->format('Y-m-d H:i:s'));
        $this->assertSame('2026-11-04 23:59:59', $to->format('Y-m-d H:i:s'));

        [$from, $to] = $history->dateRange('yesterday', null, null, 'Europe/Madrid', $today);
        $this->assertSame('2026-11-03', $from->toDateString());
        $this->assertSame('2026-11-03', $to->toDateString());

        [$from, $to] = $history->dateRange('last_7_days', null, null, 'Europe/Madrid', $today);
        $this->assertSame('2026-10-29', $from->toDateString());
        $this->assertSame('2026-11-04', $to->toDateString());

        [$from, $to] = $history->dateRange('last_month', null, null, 'Europe/Madrid', $today);
        $this->assertSame('2026-10-01', $from->toDateString());
        $this->assertSame('2026-10-31', $to->toDateString());
    }

    public function test_it_resolves_custom_date_ranges_and_rejects_reversed_dates(): void
    {
        $history = new ShopOrderHistory();
        [$from, $to] = $history->dateRange('custom', '2026-10-02', '2026-10-08', 'Europe/Madrid');

        $this->assertSame('2026-10-02', $from->toDateString());
        $this->assertSame('2026-10-08', $to->toDateString());

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $history->dateRange('custom', '2026-10-08', '2026-10-02', 'Europe/Madrid');
    }
}

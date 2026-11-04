<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Shop;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class ShopOrderHistory
{
    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    public function dateRange(
        string $preset,
        ?string $from,
        ?string $to,
        string $timezone,
        ?CarbonImmutable $today = null
    ): array {
        $today ??= CarbonImmutable::today($timezone);

        [$start, $end] = match ($preset) {
            'today' => [$today, $today],
            'yesterday' => [$today->subDay(), $today->subDay()],
            'last_month' => [$today->startOfMonth()->subMonth(), $today->startOfMonth()->subDay()],
            'custom' => [
                $from ? CarbonImmutable::createFromFormat('!Y-m-d', $from, $timezone) : null,
                $to ? CarbonImmutable::createFromFormat('!Y-m-d', $to, $timezone) : null,
            ],
            default => [$today->subDays(6), $today],
        };

        if (! $start || ! $end || $end->lessThan($start)) {
            throw ValidationException::withMessages([
                'to' => 'Choose a valid date range with an end date on or after the start date.',
            ]);
        }

        return [$start->startOfDay(), $end->endOfDay()];
    }

    public function forShopBetween(Shop $shop, CarbonImmutable $from, CarbonImmutable $to): Builder
    {
        return Order::query()
            ->with(['items.fruit'])
            ->where('shop_id', $shop->id)
            ->where('status', '!=', 'draft')
            ->whereDate('order_date', '>=', $from->toDateString())
            ->whereDate('order_date', '<=', $to->toDateString())
            ->orderByDesc('order_date')
            ->orderByDesc('id');
    }
}

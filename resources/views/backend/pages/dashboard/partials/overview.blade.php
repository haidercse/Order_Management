<div class="dash-heading d-flex flex-wrap justify-content-between align-items-end">
    <div>
        <h2>Business overview</h2>
        <div class="dash-subtitle">{{ now()->format('l, d M Y') }} · Daily orders, demand and warehouse availability</div>
    </div>
    <small id="dashboard-refresh-status" class="dash-muted">Updates every 30 seconds</small>
</div>

<div class="row">
    <div class="col-12 col-sm-6 col-xl-3">
        <section class="dash-kpi">
            <div class="dash-kpi-body">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="dash-kpi-label">Today's submitted orders</span>
                    <span class="dash-kpi-icon"><i class="fa fa-shopping-basket"></i></span>
                </div>
                <div class="dash-kpi-value">{{ number_format($todayOrdersCount) }} <span class="font-weight-normal text-muted">/ {{ number_format($activeShopsCount) }}</span></div>
                <div class="dash-kpi-note">Orders submitted / active shops</div>
            </div>
        </section>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <section class="dash-kpi">
            <div class="dash-kpi-body">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="dash-kpi-label">Total demand</span>
                    <span class="dash-kpi-icon"><i class="fa fa-cubes"></i></span>
                </div>
                <div class="dash-kpi-value">
                    @php
                        $primaryDemandUnit = $demandSummary->has('caja')
                            ? 'caja'
                            : ($demandSummary->keys()->first() ?: 'caja');
                    @endphp
                    {{ number_format((float) ($demandSummary[$primaryDemandUnit] ?? 0), 1) }}
                    <span class="font-weight-normal text-muted">{{ $primaryDemandUnit ?: 'caja' }}</span>
                </div>
                <div class="dash-kpi-note">
                    @forelse ($demandSummary->except($primaryDemandUnit) as $unit => $quantity)
                        {{ number_format($quantity, 1) }} {{ $unit }} ·
                    @empty
                        No additional unit quantities
                    @endforelse
                </div>
            </div>
        </section>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <section class="dash-kpi">
            <div class="dash-kpi-body">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="dash-kpi-label">Today's order value</span>
                    <span class="dash-kpi-icon"><i class="fa fa-money"></i></span>
                </div>
                <div class="dash-kpi-value">{{ $currency }} {{ number_format((float) $orderValue, 2) }}</div>
                <div class="dash-kpi-note">Based on the saved order-line price totals</div>
            </div>
        </section>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <section class="dash-kpi">
            <div class="dash-kpi-body">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="dash-kpi-label">Warehouse shortage alerts</span>
                    <span class="dash-kpi-icon dash-kpi-alert-icon"><i class="fa fa-exclamation-triangle"></i></span>
                </div>
                <div class="dash-kpi-value">{{ number_format($shortageCount) }}</div>
                <div class="dash-kpi-note">Fruit items where submitted demand exceeds available stock</div>
            </div>
        </section>
    </div>
</div>

<section class="dash-section">
    <div class="dash-section-heading">
        <div>
            <h3>Warehouse availability &amp; shortage</h3>
            <small class="dash-muted">Demand and unreserved stock are compared in caja equivalents where configured, otherwise in the fruit's default unit.</small>
        </div>
        <span class="dash-status {{ $shortageCount ? 'status-submitted' : 'status-ready' }}">
            {{ $shortageCount ? $shortageCount . ' item(s) to review' : 'Stock covers demand' }}
        </span>
    </div>
    @if ($shortageCount)
        <div class="dash-alert-box">
            <strong><i class="fa fa-exclamation-circle mr-1"></i> Purchase attention required.</strong>
            {{ $shortageCount }} fruit item(s) have more submitted demand than available warehouse stock.
        </div>
    @else
        <div class="dash-alert-box is-clear">
            <strong><i class="fa fa-check-circle mr-1"></i> No shortage detected.</strong>
            Current available stock covers the submitted demand.
        </div>
    @endif
    <div class="table-responsive dash-section-content">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Fruit item</th>
                    <th>Total demand</th>
                    <th>WH stock available</th>
                    <th>Shortage / excess</th>
                    <th>Action required</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fruitDemand->sortBy(fn ($item) => [$item['shortage'] ? 0 : 1, -$item['demand']]) as $item)
                    <tr>
                        <td class="font-weight-bold">{{ $item['name'] }}</td>
                        <td class="dash-number">{{ number_format($item['demand'], 2) }} {{ $item['unit'] }}</td>
                        <td class="dash-number">{{ number_format($item['available'], 2) }} {{ $item['unit'] }}</td>
                        <td class="dash-number {{ $item['shortage'] ? 'text-danger font-weight-bold' : 'text-success' }}">
                            {{ $item['difference'] > 0 ? '+' : '' }}{{ number_format($item['difference'], 2) }} {{ $item['unit'] }}
                        </td>
                        <td>
                            @if ($item['shortage'])
                                <span class="text-danger font-weight-bold">Need to buy {{ number_format(abs($item['difference']), 2) }} {{ $item['unit'] }}</span>
                            @else
                                <span class="text-success font-weight-bold">Sufficient stock</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="dash-empty">No submitted order demand recorded today.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<div class="row">
    <div class="col-12 col-xl-8">
        <section class="dash-section">
            <div class="dash-section-heading">
                <h3>Today's shop orders</h3>
                <span class="dash-muted">{{ number_format($todayOrdersCount) }} submitted</span>
            </div>
            <div class="table-responsive dash-section-content">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Shop</th>
                            <th>Submitted</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $displayStatus = $order->sent_at ? 'Delivered' : ucfirst(str_replace('_', ' ', $order->status));
                                $statusClass = strtolower(str_replace(' ', '-', $displayStatus));
                            @endphp
                            <tr>
                                <td>
                                    <span class="font-weight-bold">{{ $order->shop->name ?? 'Unknown shop' }}</span>
                                    @if ($order->shop?->code)
                                        <small class="d-block dash-muted">{{ $order->shop->code }}</small>
                                    @endif
                                </td>
                                <td class="dash-number">{{ $order->submitted_at?->format('H:i') ?? '—' }}</td>
                                <td class="dash-number">{{ number_format($order->items_count) }}</td>
                                <td><span class="dash-status status-{{ $statusClass }}">{{ $displayStatus }}</span></td>
                                <td>
                                    <details class="dash-details">
                                        <summary>View details</summary>
                                        @if ($order->items->isNotEmpty())
                                            <ul>
                                                @foreach ($order->items as $item)
                                                    <li>
                                                        {{ $item->fruit->display_name ?? $item->fruit->name ?? 'Fruit' }}:
                                                        {{ number_format((float) $item->quantity, 2) }}
                                                        {{ $item->unit->symbol ?? $item->unit->name ?? '' }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <small class="d-block dash-muted mt-2">No order items.</small>
                                        @endif
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="dash-empty">No shops have submitted an order today.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="col-12 col-xl-4">
        <section class="dash-section">
            <div class="dash-section-heading">
                <h3>Top demanded fruits</h3>
                <small class="dash-muted">By quantity</small>
            </div>
            <div class="dash-section-content">
                @forelse ($topDemandedFruits as $index => $fruit)
                    <div class="d-flex justify-content-between align-items-center py-3 {{ $index ? 'border-top' : '' }}">
                        <div class="d-flex align-items-center">
                            <span class="dash-kpi-icon">{{ $index + 1 }}</span>
                            <span class="font-weight-bold ml-3">{{ $fruit['name'] }}</span>
                        </div>
                        <span class="font-weight-bold dash-number">{{ number_format($fruit['demand'], 2) }} <small class="dash-muted">{{ $fruit['unit'] }}</small></span>
                    </div>
                @empty
                    <div class="dash-empty">No fruit demand recorded today.</div>
                @endforelse
            </div>
        </section>
    </div>
</div>

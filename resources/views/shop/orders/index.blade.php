<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dhaka Frutas — Shop Order</title>
    <link rel="stylesheet" href="{{ asset('admin/assets/css/styles.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .shop-orders-page { background: #f4f7f4; font-family: Inter, "Segoe UI", system-ui, sans-serif; -webkit-font-smoothing: antialiased; }
        .shop-orders-page button, .shop-orders-page input { touch-action: manipulation; }

        /* Header */
        .shop-orders-page .topbar { border-bottom: 0; background: #15803d; box-shadow: 0 2px 8px rgba(0,0,0,.15); }
        .shop-orders-page .topbar-inner { max-width: 1024px; min-height: 52px; padding: 8px 12px; }
        .shop-orders-page .brand { font-size: 16px; font-weight: 800; letter-spacing: -.02em; line-height: 1.1; }
        .shop-orders-page .brand small { margin-top: 3px; color: #d1fae5; font-size: 11px; font-weight: 500; }
        .shop-orders-page .logout { min-height: 0; padding: 6px 12px; border-radius: 8px; background: #f59e0b; color: #fff; font-size: 12px; font-weight: 700; }
        .shop-orders-page .logout:hover { background: #d97706; color: #fff; }
        .shop-orders-page .page { max-width: 1024px; padding: 12px 10px 40px; }

        /* Cards */
        .shop-orders-page .order-summary-card,
        .shop-orders-page .choose-card,
        .shop-orders-page #orderPreview,
        .shop-orders-page .order-history-launcher { margin-bottom: 12px; padding: 12px; border: 1px solid #d1fae5; border-radius: 12px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.05); }
        .shop-orders-page .order-summary-card h1 { margin: 0; font-size: 14px; font-weight: 700; }
        .shop-orders-page .order-summary-card .order-meta { flex-wrap: nowrap; align-items: center; }
        .shop-orders-page .title-row { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
        .shop-orders-page .pill { padding: 3px 9px; font-size: 10px; font-weight: 700; }
        .shop-orders-page .pill.draft { border: 1px solid #e5e7eb; border-radius: 6px; background: #f3f4f6; color: #4b5563; }
        .shop-orders-page .pill.count { padding: 4px 10px; background: #fef3c7; color: #92400e; font-size: 12px; }
        .shop-orders-page .order-shop-hint { margin: 2px 0 0; font-size: 11px; }

        /* Choose fruits + alphabet (sticky) */
        .shop-orders-page .choose-card { position: sticky; top: 52px; z-index: 20; padding: 10px; }
        .shop-orders-page .choose-head { display: flex; align-items: center; justify-content: space-between; margin: 0 4px 8px; }
        .shop-orders-page .choose-head strong { color: #374151; font-size: 12px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .shop-orders-page .choose-head span { color: #047857; font-size: 11px; font-weight: 500; }
        .shop-orders-page .alphabet-nav { position: static; gap: 4px; margin: 0; padding: 2px 0; overflow-x: auto; border: 0; border-radius: 0; background: transparent; box-shadow: none; scrollbar-width: none; }
        .shop-orders-page .alphabet-nav::-webkit-scrollbar { display: none; }
        .shop-orders-page .letter-button { flex: 0 0 auto; min-width: 32px; min-height: 30px; padding: 0 10px; border: 1px solid #e5e7eb; border-radius: 8px; color: #4b5563; font-size: 12px; font-weight: 700; }
        .shop-orders-page .letter-button.active { border-color: #16a34a; background: #16a34a; color: #fff; }
        .shop-orders-page .letter-button:disabled { opacity: .4; }

        /* Compact fruit grid */
        .shop-orders-page .fruit-section { margin-bottom: 12px; padding: 0; border: 0; background: transparent; box-shadow: none; }
        .shop-orders-page .fruit-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 6px; }
        @media (min-width: 640px) { .shop-orders-page .fruit-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (min-width: 1024px) { .shop-orders-page .fruit-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
        .shop-orders-page .fruit-card { justify-content: space-between; gap: 4px; padding: 8px; border: 1px solid #e5e7eb; border-radius: 8px; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.04); transition: border-color .15s, background-color .15s; }
        .shop-orders-page .fruit-card.is-selected { border-color: #34d399; background: #f3fcf8; }
        .shop-orders-page .fruit-info { flex: 1; min-width: 0; padding-right: 2px; }
        .shop-orders-page .fruit-name { overflow: hidden; color: #1f2937; font-size: 11px; font-weight: 700; letter-spacing: -.01em; text-overflow: ellipsis; text-transform: uppercase; white-space: nowrap; }
        .shop-orders-page .fruit-unavailable { display: block; margin-top: 2px; font-size: 10px; white-space: normal; }
        .shop-orders-page .fruit-card select { max-width: 100%; min-height: 26px; margin-top: 4px; padding: 2px 4px; font-size: 10px; }
        .shop-orders-page .fruit-quantity-controls { display: flex; flex: 0 0 auto; align-items: center; gap: 4px; padding: 2px; border: 1px solid #e5e7eb; border-radius: 6px; background: #f9fafb; }
        .shop-orders-page .fruit-quantity-step { display: inline-flex; width: 28px; min-width: 28px; height: 28px; align-items: center; justify-content: center; padding: 0; border: 1px solid #e5e7eb; border-radius: 5px; background: #fff; color: #374151; cursor: pointer; font-size: 14px; font-weight: 800; }
        .shop-orders-page .fruit-quantity-step:active { transform: scale(.95); }
        .shop-orders-page .fruit-quantity-step.add { border-color: #16a34a; background: #16a34a; color: #fff; }
        .shop-orders-page .fruit-input { width: 30px; min-height: 28px; margin: 0; padding: 0; border: 0; border-radius: 0; background: transparent; font-size: 12px; font-weight: 800; -moz-appearance: textfield; }
        .shop-orders-page .fruit-input::-webkit-outer-spin-button, .shop-orders-page .fruit-input::-webkit-inner-spin-button { margin: 0; -webkit-appearance: none; }
        .shop-orders-page .fruit-input:focus { outline: 2px solid rgba(22,163,74,.3); }
        .shop-orders-page .fruit-card.has-box-choice { flex-wrap: wrap; }

        /* Order preview */
        .shop-orders-page #orderPreview { padding: 16px; }
        .shop-orders-page #orderPreview > .order-meta { padding-bottom: 12px; border-bottom: 1px solid #e5e7eb; }
        .shop-orders-page #orderPreview h2 { margin: 0; font-size: 14px; font-weight: 700; }
        .shop-orders-page #orderPreview .hint { font-size: 12px; }
        .shop-orders-page .preview-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin: 12px 0; }
        .shop-orders-page .preview-order-item { flex-direction: row; align-items: center; justify-content: space-between; gap: 8px; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f3fcf8; }
        .shop-orders-page .preview-fruit { min-width: 0; font-size: 12px; text-transform: uppercase; }
        .shop-orders-page .preview-fruit small { display: block; margin-top: 2px; color: #047857; font-size: 11px; font-weight: 600; text-transform: none; }
        .shop-orders-page .preview-controls { flex: 0 0 auto; align-items: center; gap: 6px; }
        .shop-orders-page .preview-stepper { display: flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 6px; background: #fff; padding: 2px; }
        .shop-orders-page .preview-stepper .quantity-button { min-width: 26px; min-height: 26px; padding: 0 6px; border: 0; background: transparent; color: #4b5563; }
        .shop-orders-page .preview-stepper b { padding: 0 8px; font-size: 12px; }
        .shop-orders-page .quantity-button.remove { border: 0; background: transparent; min-height: 26px; }
        .shop-orders-page #emptyOrder { grid-column: 1 / -1; padding: 16px; font-size: 12px; }
        .shop-orders-page .notes label { font-size: 11px; font-weight: 600; }
        .shop-orders-page .notes textarea { min-height: 60px; background: #f9fafb; font-size: 12px; }
        .shop-orders-page #orderPreview .actions .btn { border-radius: 8px; font-size: 12px; font-weight: 700; }
        .shop-orders-page #orderPreview .actions .btn.light { border-color: #d1d5db; background: #f3f4f6; color: #374151; }
        .shop-orders-page #orderPreview .actions .btn.orange { background: #f59e0b; color: #fff; }
        .shop-orders-page #orderPreview .actions .btn.orange:hover { background: #d97706; }
        .shop-orders-page .mobile-order-bar, .shop-orders-page .order-preview-backdrop, .shop-orders-page .mobile-preview-close { display: none; }

        @media (max-width: 767px) {
            .shop-orders-page.has-draft-order .page { padding-bottom: 100px; }
            .shop-orders-page .mobile-order-bar { position: fixed; right: 0; bottom: 0; left: 0; z-index: 40; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 12px calc(10px + env(safe-area-inset-bottom)); border-top: 1px solid #a7f3d0; background: rgba(255,255,255,.95); box-shadow: 0 -4px 16px rgba(0,0,0,.08); backdrop-filter: blur(10px); }
            .shop-orders-page .mobile-order-info { display: flex; align-items: center; gap: 8px; min-width: 0; }
            .shop-orders-page .cart-icon { position: relative; padding: 8px; border-radius: 8px; background: #d1fae5; color: #065f46; line-height: 0; }
            .shop-orders-page .cart-icon svg { width: 20px; height: 20px; }
            .shop-orders-page #mobileOrderCount { position: absolute; top: -6px; right: -6px; display: flex; width: 16px; height: 16px; align-items: center; justify-content: center; border-radius: 50%; background: #f59e0b; color: #fff; font-size: 10px; font-weight: 800; line-height: 1; }
            .shop-orders-page #mobileOrderQuantity { display: block; color: #1f2937; font-size: 12px; font-weight: 700; }
            .shop-orders-page .mobile-order-info em { display: block; color: #047857; font-size: 10px; font-style: normal; font-weight: 500; text-decoration: underline; }
            .shop-orders-page .mobile-order-open { display: inline-flex; align-items: center; gap: 4px; min-height: 40px; padding: 0 16px; border: 0; border-radius: 12px; background: #f59e0b; color: #fff; box-shadow: 0 2px 6px rgba(217,119,6,.25); cursor: pointer; font-size: 12px; font-weight: 700; }
            .shop-orders-page .mobile-order-open:active { transform: scale(.95); }
            .shop-orders-page .order-preview-backdrop { position: fixed; inset: 0; z-index: 50; display: block; border: 0; background: rgba(0,0,0,.5); opacity: 0; pointer-events: none; visibility: hidden; transition: opacity .3s, visibility .3s; }
            .shop-orders-page.order-preview-open { overflow: hidden; }
            .shop-orders-page.order-preview-open .order-preview-backdrop { opacity: 1; pointer-events: auto; visibility: visible; }
            .shop-orders-page #orderForm #orderPreview { position: fixed; right: 0; bottom: 0; left: 0; z-index: 51; display: flex; max-height: 85vh; max-height: 85dvh; flex-direction: column; margin: 0; padding: 0; overflow-y: auto; overscroll-behavior: contain; border: 0; border-radius: 16px 16px 0 0; background: #fff; box-shadow: 0 -12px 40px rgba(0,0,0,.2); transform: translateY(105%); visibility: hidden; transition: transform .3s ease-out, visibility .3s; }
            .shop-orders-page.order-preview-open #orderForm #orderPreview { transform: translateY(0); visibility: visible; }
            .shop-orders-page #orderPreview > .order-meta { flex-wrap: nowrap; padding: 12px; background: #f9fafb; }
            .shop-orders-page #orderPreview h2 { font-size: 12px; text-transform: uppercase; letter-spacing: .04em; }
            .shop-orders-page #orderPreview .order-meta .hint { display: none; }
            .shop-orders-page #orderPreview .mobile-preview-close { display: inline-flex; border: 0; background: transparent; color: #9ca3af; cursor: pointer; font-size: 24px; line-height: 1; }
            .shop-orders-page .preview-list { grid-template-columns: 1fr; gap: 8px; margin: 0; padding: 12px; }
            .shop-orders-page .preview-order-item { background: #fff; }
            .shop-orders-page #orderPreview .notes { margin: 0; padding: 0 12px; }
            .shop-orders-page #orderPreview .actions { position: sticky; bottom: 0; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin: 12px 0 0; padding: 12px 12px calc(12px + env(safe-area-inset-bottom)); border-top: 1px solid #e5e7eb; background: #f9fafb; }
            .shop-orders-page #orderPreview .actions .btn { width: 100%; min-height: 40px; }
            .shop-orders-page #orderPreview #submitOrderBtn { grid-column: 1 / -1; min-height: 44px; text-transform: uppercase; letter-spacing: .04em; }
        }
        @media (prefers-reduced-motion: reduce) {
            .shop-orders-page #orderForm #orderPreview, .shop-orders-page .order-preview-backdrop { transition: none; }
        }
    </style>
</head>
<body class="shop-orders-page @if (!$order || $order->status === 'draft') has-draft-order @endif">
<header class="topbar">
    <div class="topbar-inner">
        <div class="brand">Dhaka Frutas <small>{{ $shop->name }} · Shop order</small></div>
        <form method="POST" action="{{ route('shop.logout') }}">
            @csrf
            <button class="btn logout" type="submit">Log out</button>
        </form>
    </div>
</header>

<main class="page">
    <div id="feedback" aria-live="polite"></div>

    @if (!$catalog->count())
        <div class="banner warning">No active fruits configured for Caja orders are available. Please contact the warehouse.</div>
    @elseif ($unavailableFruitCount > 0)
        <div class="banner warning">{{ $unavailableFruitCount }} fruit(s) are shown but cannot be ordered until the warehouse adds an active Caja configuration and price for {{ $orderDate }}.</div>
    @endif

    <section class="order-summary-card">
        <div class="order-meta">
            <div>
                <div class="title-row">
                    <h1>Order for {{ \Illuminate\Support\Carbon::parse($orderDate)->format('D, d M Y') }}</h1>
                    @if ($order)
                        <span class="pill {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    @else
                        <span class="pill draft">Not submitted</span>
                    @endif
                </div>
                <p class="hint order-shop-hint">Your account only shows orders for {{ $shop->name }}.</p>
            </div>
            @if (!$order || $order->status === 'draft')
                <span class="pill count">{{ $catalog->count() }} fruits available</span>
            @endif
        </div>
    </section>

    @if ($order && $order->status !== 'draft')
        <section class="card" id="orderPreview">
            <div class="order-meta">
                <div>
                    <h2 class="mb-1">Order preview</h2>
                    <p class="hint mb-0">Order #{{ $order->id }} · Submitted {{ $order->submitted_at?->format('d M Y, H:i') }}</p>
                </div>
                <a class="btn" href="{{ route('shop.orders.pdf', $order->id) }}">Download order PDF</a>
            </div>
            <div class="banner mt-3">Your order has been sent to the warehouse. Review the items below or download a copy for your records.</div>
            <div class="preview-list">
                @foreach ($order->items as $item)
                    <div class="preview-order-item">
                        <span class="fruit-name preview-fruit">{{ $item->fruit->display_name ?: $item->fruit->name }}</span>
                        <span class="pill preview-quantity">{{ (int) $item->quantity }} Cajas</span>
                    </div>
                @endforeach
            </div>
            @if ($order->notes)<p class="hint mt-3 mb-0"><strong>Note:</strong> {{ $order->notes }}</p>@endif
        </section>
    @else
        <form id="orderForm">
            @csrf
            <div class="choose-card">
                <div class="choose-head">
                    <strong>Choose your fruits</strong>
                    <span id="chooseCount">0 items selected</span>
                </div>
                <nav class="alphabet-nav" id="alphabetNav" aria-label="Filter fruits by first letter"></nav>
            </div>

            <section class="fruit-section">
                <div class="fruit-grid" id="fruitList"></div>
                <div id="emptyLetter" class="empty" hidden>No fruits start with this letter.</div>
            </section>

            <section id="orderPreview" aria-labelledby="orderPreviewTitle">
                <div class="order-meta">
                    <div><h2 id="orderPreviewTitle" class="mb-1">Your order preview</h2><span class="hint">Your selected fruits and Caja amounts appear here as you enter them.</span></div>
                    <span class="pill count" id="lineCount">0 items</span>
                    <button class="mobile-preview-close" type="button" id="closeMobileOrder" aria-label="Close order preview">×</button>
                </div>
                <div class="preview-list" id="orderLines"></div>
                <div id="emptyOrder" class="empty">No fruits selected yet. Enter a Caja quantity beside a fruit above.</div>
                <div class="notes">
                    <label for="orderNotes">Note for the warehouse (optional)</label>
                    <textarea id="orderNotes" rows="2" maxlength="2000" placeholder="Delivery or preparation notes">{{ $order?->notes }}</textarea>
                </div>
                <div class="actions">
                    <button class="btn light" type="button" id="saveDraftBtn">Save order</button>
                    <button class="btn light" type="button" id="downloadPdfBtn">Download PDF</button>
                    <button class="btn orange" type="button" id="submitOrderBtn">Submit to warehouse</button>
                </div>
            </section>
        </form>
    @endif

    @if (!$order || $order->status === 'draft')
        <div class="mobile-order-bar">
            <div class="mobile-order-info" aria-live="polite" onclick="document.getElementById('openMobileOrder').click()">
                <div class="cart-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span id="mobileOrderCount">0</span>
                </div>
                <div>
                    <span id="mobileOrderQuantity">0 Cajas Selected</span>
                    <em>Tap to view &amp; submit</em>
                </div>
            </div>
            <button class="mobile-order-open" type="button" id="openMobileOrder" aria-controls="orderPreview" aria-expanded="false">
                View Order <span aria-hidden="true">↑</span>
            </button>
        </div>
        <button class="order-preview-backdrop" type="button" id="closeMobileOrderBackdrop" aria-label="Close order preview"></button>
    @endif

    <section class="order-history-launcher">
        <div class="order-meta">
            <div>
                <h2 class="mb-1" style="font-size:14px">Order history</h2>
                <p class="hint mb-0" id="historyLauncherSummary">Showing {{ $historyFrom->format('d M Y') }} – {{ $historyTo->format('d M Y') }} · {{ $historyOrders->count() }} order{{ $historyOrders->count() === 1 ? '' : 's' }}</p>
            </div>
            <button class="btn" type="button" id="openOrderHistory" style="background:#16a34a;font-size:12px">View order history</button>
        </div>
    </section>

    <dialog class="order-history-dialog" id="orderHistoryModal" aria-labelledby="orderHistoryTitle">
        <div class="order-history-modal">
            <div class="order-meta order-history-heading">
                <div>
                    <h2 id="orderHistoryTitle" class="mb-1">Order history</h2>
                    <p class="hint mb-0">Review or download submitted orders by date.</p>
                </div>
                <button class="btn light close-order-history" type="button" aria-label="Close order history">Close</button>
            </div>

            <nav class="history-filters" aria-label="Filter order history">
                @foreach (['today' => 'Today', 'yesterday' => 'Yesterday', 'last_7_days' => 'Last 7 days', 'last_month' => 'Last month'] as $preset => $label)
                    <a class="btn light {{ $historyPreset === $preset ? 'active' : '' }}" href="{{ route('shop.orders.index', ['history' => $preset]) }}" data-history-preset="{{ $preset }}" @if ($historyPreset === $preset) aria-current="date" @endif>{{ $label }}</a>
                @endforeach
            </nav>

            <form class="history-date-form" method="GET" action="{{ route('shop.orders.index') }}" id="historyDateForm">
                <input type="hidden" name="history" value="custom">
                <div>
                    <label for="historyFrom">From</label>
                    <input id="historyFrom" name="from" type="date" value="{{ $historyFrom->toDateString() }}" required>
                </div>
                <div>
                    <label for="historyTo">To</label>
                    <input id="historyTo" name="to" type="date" value="{{ $historyTo->toDateString() }}" required>
                </div>
                <button class="btn light" type="submit">Show orders</button>
                <a class="btn history-download" id="historyDownload" href="{{ route('shop.orders.history.pdf', ['history' => 'custom', 'from' => $historyFrom->toDateString(), 'to' => $historyTo->toDateString()]) }}">Download PDF</a>
            </form>

            <p class="hint history-result-summary" id="historyResultSummary" aria-live="polite">
                {{ $historyFrom->format('d M Y') }} – {{ $historyTo->format('d M Y') }}
                · {{ $historyOrders->count() }} order{{ $historyOrders->count() === 1 ? '' : 's' }}
            </p>

            <div class="banner error history-error" id="historyError" role="alert" hidden></div>
            <div class="empty" id="historyEmpty" @if ($historyOrders->isNotEmpty()) hidden @endif>No submitted orders found for these dates.</div>
            <div class="table-wrap history-table-wrap" id="historyTableWrap" @if ($historyOrders->isEmpty()) hidden @endif>
                <table class="order-items history-table">
                    <thead>
                        <tr><th>Date</th><th>Order</th><th>Status</th><th>Fruit</th><th class="history-quantity">Cajas</th></tr>
                    </thead>
                    <tbody id="historyTableBody">
                        @foreach ($historyOrders as $historyOrder)
                            @forelse ($historyOrder->items as $item)
                                <tr>
                                    <td>{{ $historyOrder->order_date->format('d M Y') }}</td>
                                    <td>#{{ $historyOrder->id }}</td>
                                    <td>{{ ucfirst($historyOrder->status) }}</td>
                                    <td>{{ $item->fruit->display_name ?: $item->fruit->name }}</td>
                                    <td class="history-quantity">{{ (int) $item->quantity }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td>{{ $historyOrder->order_date->format('d M Y') }}</td>
                                    <td>#{{ $historyOrder->id }}</td>
                                    <td>{{ ucfirst($historyOrder->status) }}</td>
                                    <td colspan="2" class="muted">No fruit items</td>
                                </tr>
                            @endforelse
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </dialog>
</main>

<script>
(() => {
    const catalog = @json($catalog);
    const initialItems = @json($initialItems);
    const draftUrl = @json(route('shop.orders.draft'));
    const submitUrl = @json(route('shop.orders.submit'));
    const historyDataUrl = @json(route('shop.orders.history.data'));
    const historyPdfUrl = @json(route('shop.orders.history.pdf'));
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const quantities = new Map(initialItems.map(line => [Number(line.fruit_id), String(Math.trunc(Number(line.quantity)))]));
    const boxSelections = new Map(initialItems.map(line => [Number(line.fruit_id), Number(line.box_configuration_id)]));
    let activeLetter = null;

    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);
    const fruitFor = id => catalog.find(fruit => Number(fruit.id) === Number(id));
    const cajaFor = fruit => fruit?.units[0];
    const boxFor = (fruit, id) => cajaFor(fruit)?.boxes.find(box => Number(box.id) === Number(id));
    const selectedBoxFor = fruit => boxFor(fruit, boxSelections.get(Number(fruit.id))) || cajaFor(fruit)?.boxes[0];
    const orderLines = () => catalog
        .map(fruit => ({
            fruit,
            quantity: Number(quantities.get(Number(fruit.id)) || 0),
            box: selectedBoxFor(fruit),
        }))
        .filter(line => line.quantity > 0 && line.box);
    const firstLetter = name => String(name || '').trim().charAt(0).normalize('NFD').replace(/[\u0300-\u036f]/g, '').toUpperCase();
    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');

    function renderAlphabet() {
        const presentLetters = new Set(catalog.map(fruit => firstLetter(fruit.name)));
        if (!activeLetter || (activeLetter !== 'ALL' && !presentLetters.has(activeLetter))) {
            activeLetter = letters.find(letter => presentLetters.has(letter)) || 'A';
        }
        document.getElementById('alphabetNav').innerHTML = [
            `<button class="letter-button all-letter ${activeLetter === 'ALL' ? 'active' : ''}" type="button" data-letter="ALL" aria-pressed="${activeLetter === 'ALL'}">ALL</button>`,
            ...letters.map(letter =>
                `<button class="letter-button ${letter === activeLetter ? 'active' : ''}" type="button" data-letter="${letter}" aria-pressed="${letter === activeLetter}" ${presentLetters.has(letter) ? '' : 'disabled'}>${letter}</button>`
            ),
        ].join('');
        renderFruits();
    }

    function renderFruits() {
        const fruits = activeLetter === 'ALL'
            ? catalog
            : catalog.filter(fruit => firstLetter(fruit.name) === activeLetter);
        const list = document.getElementById('fruitList');
        document.getElementById('emptyLetter').hidden = fruits.length > 0;
        const emptyLetter = document.getElementById('emptyLetter');
        emptyLetter.textContent = activeLetter === 'ALL'
            ? 'No fruits are available.'
            : `No fruits start with "${activeLetter}".`;
        list.innerHTML = fruits.map(fruit => {
            const unit = cajaFor(fruit);
            if (!unit) {
                return `<article class="fruit-card">
                    <div class="fruit-info">
                        <span class="fruit-name" title="${escapeHtml(fruit.name)}">${escapeHtml(fruit.name)}</span>
                        <span class="fruit-unavailable">Not orderable yet — warehouse setup required</span>
                    </div>
                </article>`;
            }
            const selectedBox = selectedBoxFor(fruit);
            const quantity = quantities.get(Number(fruit.id)) || '';
            const choices = unit.boxes.length > 1
                ? `<label class="sr-only" for="box-choice-${fruit.id}">Caja type for ${escapeHtml(fruit.name)}</label>
                    <select id="box-choice-${fruit.id}" data-box-choice="${fruit.id}" aria-label="Caja type for ${escapeHtml(fruit.name)}">
                        ${unit.boxes.map(box => `<option value="${box.id}" ${Number(box.id) === Number(selectedBox.id) ? 'selected' : ''}>${escapeHtml(box.name)}</option>`).join('')}
                    </select>`
                : '';
            return `<article class="fruit-card ${Number(quantity) > 0 ? 'is-selected' : ''}">
                <div class="fruit-info"><span class="fruit-name" title="${escapeHtml(fruit.name)}">${escapeHtml(fruit.name)}</span>${choices}</div>
                <label class="sr-only" for="fruit-quantity-${fruit.id}">Quantity in Cajas for ${escapeHtml(fruit.name)}</label>
                <div class="fruit-quantity-controls">
                    <button class="fruit-quantity-step" type="button" data-quantity-step="-1" data-fruit-id="${fruit.id}" aria-label="Decrease ${escapeHtml(fruit.name)} by one Caja">−</button>
                    <input class="fruit-input" id="fruit-quantity-${fruit.id}" data-fruit-quantity="${fruit.id}" type="number" min="0" step="1" inputmode="numeric" value="${escapeHtml(quantity)}" placeholder="0" aria-label="Cajas of ${escapeHtml(fruit.name)}">
                    <button class="fruit-quantity-step add" type="button" data-quantity-step="1" data-fruit-id="${fruit.id}" aria-label="Increase ${escapeHtml(fruit.name)} by one Caja">+</button>
                </div>
            </article>`;
        }).join('');
    }

    function renderOrderPreview() {
        const list = document.getElementById('orderLines');
        if (!list) return;

        const lines = orderLines();
        document.getElementById('emptyOrder').hidden = lines.length > 0;
        const itemsLabel = `${lines.length} item${lines.length === 1 ? '' : 's'}`;
        document.getElementById('lineCount').textContent = itemsLabel;
        document.getElementById('chooseCount').textContent = `${itemsLabel} selected`;
        const selectedQuantity = lines.reduce((total, line) => total + line.quantity, 0);
        const mobileOrderCount = document.getElementById('mobileOrderCount');
        const mobileOrderQuantity = document.getElementById('mobileOrderQuantity');
        if (mobileOrderCount) {
            mobileOrderCount.textContent = lines.length;
            mobileOrderQuantity.textContent = `${selectedQuantity} Caja${selectedQuantity === 1 ? '' : 's'} Selected`;
        }
        list.innerHTML = lines.map(({ fruit, quantity }) => `<div class="preview-order-item">
                <span class="fruit-name preview-fruit">${escapeHtml(fruit.name)}<small>${escapeHtml(quantity)} Cajas</small></span>
                <div class="preview-controls">
                    <div class="preview-stepper">
                        <button class="quantity-button" type="button" data-preview-action="decrease" data-fruit-id="${fruit.id}" aria-label="Decrease ${escapeHtml(fruit.name)} by one Caja">−</button>
                        <b>${escapeHtml(quantity)}</b>
                        <button class="quantity-button" type="button" data-preview-action="increase" data-fruit-id="${fruit.id}" aria-label="Increase ${escapeHtml(fruit.name)} by one Caja">+</button>
                    </div>
                    <button class="quantity-button remove" type="button" data-preview-action="remove" data-fruit-id="${fruit.id}" aria-label="Remove ${escapeHtml(fruit.name)} from the order">Remove</button>
                </div>
            </div>`).join('');
    }

    function setQuantity(fruitId, quantity) {
        if (quantity > 0) {
            quantities.set(fruitId, String(quantity));
        } else {
            quantities.delete(fruitId);
        }
        const input = document.getElementById(`fruit-quantity-${fruitId}`);
        if (input) input.value = quantity > 0 ? String(quantity) : '';
        input?.closest('.fruit-card')?.classList.toggle('is-selected', quantity > 0);
        renderOrderPreview();
    }

    const mobileOrderButton = document.getElementById('openMobileOrder');
    const closeMobileOrderButton = document.getElementById('closeMobileOrder');
    const closeMobileOrderBackdrop = document.getElementById('closeMobileOrderBackdrop');
    const orderPreview = document.getElementById('orderPreview');

    function closeMobileOrderPreview(restoreFocus = true) {
        document.body.classList.remove('order-preview-open');
        mobileOrderButton?.setAttribute('aria-expanded', 'false');
        orderPreview?.removeAttribute('role');
        orderPreview?.removeAttribute('aria-modal');
        if (restoreFocus) mobileOrderButton?.focus({ preventScroll: true });
    }

    function openMobileOrderPreview() {
        if (!window.matchMedia('(max-width: 767px)').matches) {
            orderPreview?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            return;
        }
        document.body.classList.add('order-preview-open');
        mobileOrderButton?.setAttribute('aria-expanded', 'true');
        orderPreview?.setAttribute('role', 'dialog');
        orderPreview?.setAttribute('aria-modal', 'true');
        closeMobileOrderButton?.focus({ preventScroll: true });
    }

    mobileOrderButton?.addEventListener('click', openMobileOrderPreview);
    closeMobileOrderButton?.addEventListener('click', () => closeMobileOrderPreview());
    closeMobileOrderBackdrop?.addEventListener('click', () => closeMobileOrderPreview());
    window.addEventListener('keydown', event => {
        if (!document.body.classList.contains('order-preview-open')) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            closeMobileOrderPreview();
            return;
        }
        if (event.key !== 'Tab' || !orderPreview) return;
        const focusable = [...orderPreview.querySelectorAll('button:not(:disabled), input:not(:disabled), textarea:not(:disabled), a[href]')]
            .filter(element => element.getClientRects().length > 0);
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last?.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first?.focus();
        }
    });
    window.addEventListener('resize', () => {
        if (document.body.classList.contains('order-preview-open') && !window.matchMedia('(max-width: 767px)').matches) {
            closeMobileOrderPreview(false);
        }
    });

    function showMessage(message, isError = false) {
        const feedback = document.getElementById('feedback');
        feedback.innerHTML = `<div class="banner ${isError ? 'error' : ''}" role="${isError ? 'alert' : 'status'}">${escapeHtml(message)}</div>`;
        feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function postOrder(url, action) {
        const lines = orderLines();
        if (action !== 'save' && !lines.length) {
            showMessage(action === 'download' ? 'Enter a Caja quantity before downloading the PDF.' : 'Enter a Caja quantity before submitting.', true);
            return;
        }

        const buttons = [...document.querySelectorAll('#saveDraftBtn, #downloadPdfBtn, #submitOrderBtn')];
        buttons.forEach(button => button.disabled = true);
        const payload = {
            _token: csrf,
            notes: document.getElementById('orderNotes').value,
            items: lines.map(line => ({
                fruit_id: line.fruit.id,
                unit_id: cajaFor(line.fruit).id,
                box_configuration_id: line.box.id,
                quantity: line.quantity,
            })),
        };

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(payload),
        }).then(async response => {
            const result = await response.json();
            if (!response.ok) {
                const messages = result.errors ? Object.values(result.errors).flat().join(' ') : (result.message || 'Unable to save the order.');
                throw new Error(messages);
            }
            if (action === 'download') {
                if (!result.pdf_url) {
                    throw new Error('The order was saved, but the PDF download link was not returned.');
                }
                window.location.assign(result.pdf_url);
            } else {
                if (action === 'submit') {
                    sessionStorage.setItem('shop-order-success', result.message);
                }
                window.location.assign(result.redirect || @json(route('shop.orders.index')));
            }
        }).catch(error => showMessage(error.message, true))
            .finally(() => buttons.forEach(button => button.disabled = false));
    }

    document.getElementById('alphabetNav')?.addEventListener('click', event => {
        const button = event.target.closest('[data-letter]');
        if (!button || button.disabled) return;
        activeLetter = button.dataset.letter;
        renderAlphabet();
        document.getElementById('fruitList').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    document.getElementById('fruitList')?.addEventListener('keydown', event => {
        if (
            event.target.matches('[data-fruit-quantity]')
            && !event.ctrlKey && !event.metaKey && !event.altKey
            && event.key.length === 1 && !/[0-9]/.test(event.key)
        ) {
            event.preventDefault();
        }
    });
    document.getElementById('fruitList')?.addEventListener('click', event => {
        const button = event.target.closest('[data-quantity-step]');
        if (!button) return;
        const fruitId = Number(button.dataset.fruitId);
        const quantity = Number(quantities.get(fruitId) || 0);
        setQuantity(fruitId, quantity + Number(button.dataset.quantityStep));
    });
    document.getElementById('fruitList')?.addEventListener('input', event => {
        const input = event.target.closest('[data-fruit-quantity]');
        if (!input) return;
        const fruitId = Number(input.dataset.fruitQuantity);
        const previousValue = quantities.get(fruitId) || '';
        if (input.value !== '' && !/^\d+$/.test(input.value)) {
            input.value = previousValue;
            showMessage('Enter a whole number of Cajas (1, 2, 3...).', true);
            return;
        }
        if (input.value === '') {
            quantities.delete(fruitId);
        } else {
            quantities.set(fruitId, input.value);
        }
        input.closest('.fruit-card')?.classList.toggle('is-selected', Number(input.value) > 0);
        renderOrderPreview();
    });
    document.getElementById('fruitList')?.addEventListener('change', event => {
        const select = event.target.closest('[data-box-choice]');
        if (!select) return;
        boxSelections.set(Number(select.dataset.boxChoice), Number(select.value));
        renderOrderPreview();
    });
    document.getElementById('orderLines')?.addEventListener('click', event => {
        const button = event.target.closest('[data-preview-action]');
        if (!button) return;
        const fruitId = Number(button.dataset.fruitId);
        const quantity = Number(quantities.get(fruitId) || 0);
        if (button.dataset.previewAction === 'increase') {
            setQuantity(fruitId, quantity + 1);
        } else if (button.dataset.previewAction === 'decrease') {
            setQuantity(fruitId, quantity - 1);
        } else if (button.dataset.previewAction === 'remove') {
            setQuantity(fruitId, 0);
        }
    });
    document.getElementById('saveDraftBtn')?.addEventListener('click', () => postOrder(draftUrl, 'save'));
    document.getElementById('downloadPdfBtn')?.addEventListener('click', () => postOrder(draftUrl, 'download'));
    document.getElementById('submitOrderBtn')?.addEventListener('click', () => {
        if (window.confirm('এই অর্ডারটি গুদামে জমা দেবেন? একবার জমা দেওয়ার পর এটি আর দেওয়া করা যাবে না।')) postOrder(submitUrl, 'submit');
    });

    if (document.getElementById('orderLines')) {
        renderAlphabet();
        renderOrderPreview();
    }
    const historyModal = document.getElementById('orderHistoryModal');
    document.getElementById('openOrderHistory')?.addEventListener('click', () => historyModal.showModal());
    historyModal?.querySelector('.close-order-history')?.addEventListener('click', () => historyModal.close());
    historyModal?.addEventListener('click', event => {
        if (event.target === historyModal) historyModal.close();
    });
    const historyDateForm = document.getElementById('historyDateForm');
    const historyFilters = historyModal?.querySelector('.history-filters');
    const historyStatus = document.getElementById('historyResultSummary');
    const historyError = document.getElementById('historyError');
    let activeHistoryRequest = null;

    function appendHistoryCell(row, value, className = '') {
        const cell = document.createElement('td');
        cell.textContent = value;
        if (className) cell.className = className;
        row.appendChild(cell);
    }

    function updateOrderHistory(data) {
        const body = document.getElementById('historyTableBody');
        const rows = [];
        data.orders.forEach(order => {
            if (!order.items.length) {
                const row = document.createElement('tr');
                appendHistoryCell(row, order.date);
                appendHistoryCell(row, `#${order.id}`);
                appendHistoryCell(row, order.status);
                const emptyCell = document.createElement('td');
                emptyCell.colSpan = 2;
                emptyCell.className = 'muted';
                emptyCell.textContent = 'No fruit items';
                row.appendChild(emptyCell);
                rows.push(row);
                return;
            }
            order.items.forEach(item => {
                const row = document.createElement('tr');
                appendHistoryCell(row, order.date);
                appendHistoryCell(row, `#${order.id}`);
                appendHistoryCell(row, order.status);
                appendHistoryCell(row, item.fruit);
                appendHistoryCell(row, String(item.quantity), 'history-quantity');
                rows.push(row);
            });
        });
        body.replaceChildren(...rows);

        const hasOrders = data.orders.length > 0;
        document.getElementById('historyEmpty').hidden = hasOrders;
        document.getElementById('historyTableWrap').hidden = !hasOrders;
        const countLabel = `${data.order_count} order${data.order_count === 1 ? '' : 's'}`;
        historyStatus.textContent = `${data.from_label} – ${data.to_label} · ${countLabel}`;
        document.getElementById('historyLauncherSummary').textContent = `Showing ${data.from_label} – ${data.to_label} · ${countLabel}`;
        document.getElementById('historyFrom').value = data.from;
        document.getElementById('historyTo').value = data.to;

        const downloadQuery = new URLSearchParams({ history: 'custom', from: data.from, to: data.to });
        document.getElementById('historyDownload').href = `${historyPdfUrl}?${downloadQuery.toString()}`;
        historyFilters.querySelectorAll('[data-history-preset]').forEach(button => {
            const selected = button.dataset.historyPreset === data.history;
            button.classList.toggle('active', selected);
            if (selected) button.setAttribute('aria-current', 'date');
            else button.removeAttribute('aria-current');
        });

        const url = new URL(window.location.href);
        url.search = new URLSearchParams({
            history: data.history,
            from: data.from,
            to: data.to,
        }).toString();
        window.history.replaceState({}, '', url);
    }

    async function loadOrderHistory(parameters) {
        activeHistoryRequest?.abort();
        const requestController = new AbortController();
        activeHistoryRequest = requestController;
        const controls = [...historyModal.querySelectorAll('.history-filters a, .history-date-form input, .history-date-form button, #historyDownload')];
        controls.forEach(control => {
            control.setAttribute('aria-disabled', 'true');
            if ('disabled' in control) control.disabled = true;
        });
        historyModal.setAttribute('aria-busy', 'true');
        historyError.hidden = true;
        historyStatus.textContent = 'Loading orders...';

        try {
            const response = await fetch(`${historyDataUrl}?${parameters.toString()}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                signal: requestController.signal,
            });
            const data = await response.json();
            if (!response.ok) {
                const message = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Unable to load order history.');
                throw new Error(message);
            }
            updateOrderHistory(data);
        } catch (error) {
            if (error.name === 'AbortError') return;
            historyStatus.textContent = 'Could not load the selected orders.';
            historyError.textContent = error.message;
            historyError.hidden = false;
        } finally {
            if (activeHistoryRequest === requestController) {
                controls.forEach(control => {
                    control.removeAttribute('aria-disabled');
                    if ('disabled' in control) control.disabled = false;
                });
                historyModal.removeAttribute('aria-busy');
            }
        }
    }

    historyFilters?.addEventListener('click', event => {
        const button = event.target.closest('[data-history-preset]');
        if (!button || button.getAttribute('aria-disabled') === 'true') return;
        event.preventDefault();
        loadOrderHistory(new URLSearchParams({ history: button.dataset.historyPreset }));
    });
    document.getElementById('historyDownload')?.addEventListener('click', event => {
        if (event.currentTarget.getAttribute('aria-disabled') === 'true') event.preventDefault();
    });
    historyDateForm?.addEventListener('submit', event => {
        event.preventDefault();
        if (!historyDateForm.reportValidity()) return;
        loadOrderHistory(new URLSearchParams(new FormData(historyDateForm)));
    });
    const successMessage = sessionStorage.getItem('shop-order-success');
    if (successMessage) {
        sessionStorage.removeItem('shop-order-success');
        showMessage(successMessage);
    }
})();
</script>
</body>
</html>
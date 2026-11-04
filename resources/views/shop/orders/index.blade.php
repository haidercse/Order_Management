<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dhaka Frutas — Shop Order</title>
    <link rel="stylesheet" href="{{ asset('admin/assets/css/styles.css') }}">
</head>
<body>
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

    <section class="card order-summary-card">
        <div class="order-meta">
            <div>
                <h1>Order for {{ \Illuminate\Support\Carbon::parse($orderDate)->format('D, d M Y') }}</h1>
                <p class="hint order-shop-hint">Your account only shows orders for {{ $shop->name }}.</p>
            </div>
            @if ($order)
                <span class="pill {{ $order->status }}">{{ ucfirst($order->status) }}</span>
            @else
                <span class="pill draft">Not submitted</span>
            @endif
        </div>
        @if (!$order || $order->status === 'draft')
            <div class="order-meta order-summary-selection">
                <h2 class="mb-1">Choose your fruits</h2>
                <span class="pill">{{ $catalog->count() }} fruits</span>
            </div>
        @endif
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
            <div class="table-wrap">
                <table class="order-items preview-order-items">
                    <thead><tr><th colspan="2">Fruit / Cajas</th><th colspan="2">Fruit / Cajas</th></tr></thead>
                    <tbody>
                        @foreach ($order->items->chunk(2) as $items)
                            <tr>
                                @foreach ($items as $item)
                                    <td colspan="2">
                                        <div class="preview-order-item">
                                            <span class="fruit-name preview-fruit">{{ $item->fruit->display_name ?: $item->fruit->name }}</span>
                                            <span class="pill preview-quantity">{{ (int) $item->quantity }} Cajas</span>
                                        </div>
                                    </td>
                                @endforeach
                                @if ($items->count() === 1)<td colspan="2"></td>@endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($order->notes)<p class="hint mt-3 mb-0"><strong>Note:</strong> {{ $order->notes }}</p>@endif
        </section>
    @else
        <form id="orderForm">
            @csrf
            <nav class="alphabet-nav" id="alphabetNav" aria-label="Filter fruits by first letter"></nav>
            <section class="card">
                <div class="fruit-grid" id="fruitList"></div>
                <div id="emptyLetter" class="empty" hidden>No fruits start with this letter.</div>
            </section>

            <section class="card" id="orderPreview">
                <div class="order-meta">
                    <div><h2 class="mb-1">Your order preview</h2><span class="hint">Your selected fruits and Caja amounts appear here as you enter them.</span></div>
                    <span class="pill" id="lineCount">0 items</span>
                </div>
                <div class="table-wrap">
                    <table class="order-items preview-order-items">
                        <thead><tr><th colspan="2">Fruit / Cajas</th><th colspan="2">Fruit / Cajas</th></tr></thead>
                        <tbody id="orderLines"></tbody>
                    </table>
                </div>
                <div id="emptyOrder" class="empty">No fruits selected yet. Enter a Caja quantity beside a fruit above.</div>
                <div class="notes">
                    <label for="orderNotes">Note for the warehouse (optional)</label>
                    <textarea id="orderNotes" rows="2" maxlength="2000" placeholder="Delivery or preparation notes">{{ $order?->notes }}</textarea>
                </div>
                <div class="actions">
                    <button class="btn light" type="button" id="saveDraftBtn">Save order</button>
                    <button class="btn light" type="button" id="previewOrderBtn">Preview order</button>
                    <button class="btn light" type="button" id="downloadPdfBtn">Download PDF</button>
                    <button class="btn orange" type="button" id="submitOrderBtn">Submit to warehouse</button>
                </div>
            </section>
        </form>
    @endif

    <section class="card order-history-launcher">
        <div class="order-meta">
            <div>
                <h2 class="mb-1">Order history</h2>
                <p class="hint mb-0" id="historyLauncherSummary">Showing {{ $historyFrom->format('d M Y') }} – {{ $historyTo->format('d M Y') }} · {{ $historyOrders->count() }} order{{ $historyOrders->count() === 1 ? '' : 's' }}</p>
            </div>
            <button class="btn" type="button" id="openOrderHistory">View order history</button>
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
        if (!activeLetter || !presentLetters.has(activeLetter)) {
            activeLetter = letters.find(letter => presentLetters.has(letter)) || 'A';
        }
        document.getElementById('alphabetNav').innerHTML = letters.map(letter =>
            `<button class="letter-button ${letter === activeLetter ? 'active' : ''}" type="button" data-letter="${letter}" aria-pressed="${letter === activeLetter}" ${presentLetters.has(letter) ? '' : 'disabled'}>${letter}</button>`
        ).join('');
        renderFruits();
    }

    function renderFruits() {
        const fruits = catalog.filter(fruit => firstLetter(fruit.name) === activeLetter);
        const list = document.getElementById('fruitList');
        document.getElementById('emptyLetter').hidden = fruits.length > 0;
        list.innerHTML = fruits.map(fruit => {
            const unit = cajaFor(fruit);
            if (!unit) {
                return `<article class="fruit-card">
                    <div class="fruit-info">
                        <span class="fruit-name">${escapeHtml(fruit.name)}</span>
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
            return `<article class="fruit-card">
                <div class="fruit-info"><span class="fruit-name">${escapeHtml(fruit.name)}</span>${unit.boxes.length === 1 ? choices : ''}</div>
                ${unit.boxes.length > 1 ? choices : ''}
                <label class="sr-only" for="fruit-quantity-${fruit.id}">Quantity in Cajas for ${escapeHtml(fruit.name)}</label>
                <input class="fruit-input" id="fruit-quantity-${fruit.id}" data-fruit-quantity="${fruit.id}" type="number" min="0" step="1" inputmode="numeric" value="${escapeHtml(quantity)}" placeholder="0" aria-label="Cajas of ${escapeHtml(fruit.name)}">
            </article>`;
        }).join('');
    }

    function renderOrderPreview() {
        const list = document.getElementById('orderLines');
        if (!list) return;

        const lines = orderLines();
        document.getElementById('emptyOrder').hidden = lines.length > 0;
        document.getElementById('lineCount').textContent = `${lines.length} item${lines.length === 1 ? '' : 's'}`;
        const itemMarkup = ({ fruit, quantity }) => `<td colspan="2">
            <div class="preview-order-item">
                <span class="fruit-name preview-fruit">${escapeHtml(fruit.name)}</span>
                <div class="preview-controls">
                    <button class="quantity-button" type="button" data-preview-action="decrease" data-fruit-id="${fruit.id}" aria-label="Decrease ${escapeHtml(fruit.name)} by one Caja">−</button>
                    <span class="pill preview-quantity">${escapeHtml(quantity)} Cajas</span>
                    <button class="quantity-button" type="button" data-preview-action="increase" data-fruit-id="${fruit.id}" aria-label="Increase ${escapeHtml(fruit.name)} by one Caja">+</button>
                    <button class="quantity-button remove" type="button" data-preview-action="remove" data-fruit-id="${fruit.id}" aria-label="Remove ${escapeHtml(fruit.name)} from the order">Remove</button>
                </div>
            </div>
        </td>`;
        const rows = [];
        for (let index = 0; index < lines.length; index += 2) {
            rows.push(`<tr>${itemMarkup(lines[index])}${lines[index + 1] ? itemMarkup(lines[index + 1]) : '<td colspan="2"></td>'}</tr>`);
        }
        list.innerHTML = rows.join('');
    }

    function setQuantity(fruitId, quantity) {
        if (quantity > 0) {
            quantities.set(fruitId, String(quantity));
        } else {
            quantities.delete(fruitId);
        }
        const input = document.getElementById(`fruit-quantity-${fruitId}`);
        if (input) input.value = quantity > 0 ? String(quantity) : '';
        renderOrderPreview();
    }

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

        const buttons = [...document.querySelectorAll('#saveDraftBtn, #previewOrderBtn, #downloadPdfBtn, #submitOrderBtn')];
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
    document.getElementById('previewOrderBtn')?.addEventListener('click', () => {
        document.getElementById('orderPreview').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    document.getElementById('downloadPdfBtn')?.addEventListener('click', () => postOrder(draftUrl, 'download'));
    document.getElementById('submitOrderBtn')?.addEventListener('click', () => {
        if (window.confirm('এই অর্ডারটি গুদামে জমা দেবেন? একবার জমা দেওয়ার পর এটি আর দেওয়া করা যাবে না।')) postOrder(submitUrl, 'submit');
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

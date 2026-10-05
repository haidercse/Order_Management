<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FreshLink — Shop Order</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f4faf1;
            --card: #fff;
            --ink: #17301f;
            --muted: #5f7565;
            --line: #dbe8d5;
            --green: #2f9e4f;
            --green-dark: #1e7a38;
            --green-light: #e2f4e5;
            --orange: #ffb56b;
            --orange-light: #fff0de;
            --red: #c0392b;
        }

        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--ink); font: 16px/1.45 "Trebuchet MS", "Segoe UI", system-ui, sans-serif; }
        button, input, select, textarea { color: inherit; font: inherit; }
        .topbar { position: sticky; top: 0; z-index: 5; min-height: 64px; background: var(--green); border-bottom: 4px solid var(--orange); color: #fff; }
        .topbar-inner { display: flex; min-height: 60px; align-items: center; gap: 12px; max-width: 980px; margin: auto; padding: 7px 16px; }
        .brand { flex: 1; font-size: 18px; font-weight: 800; }
        .brand small { display: block; font-size: 12px; font-weight: 500; opacity: .9; }
        .page { width: min(980px, 100%); margin: 0 auto; padding: 20px 16px 40px; }
        .card { margin-bottom: 16px; padding: 18px; border: 1px solid var(--line); border-radius: 14px; background: var(--card); box-shadow: 0 4px 16px rgba(23, 48, 31, .04); }
        h1, h2, p { margin-top: 0; }
        h1 { margin-bottom: 5px; font-size: 24px; }
        h2 { margin-bottom: 14px; font-size: 18px; }
        .muted, .hint { color: var(--muted); }
        .hint { font-size: 13px; }
        .order-meta { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .pill { display: inline-block; padding: 5px 11px; border-radius: 999px; background: var(--orange-light); color: #8c510c; font-size: 12px; font-weight: 800; }
        .pill.prepared, .pill.ready { background: var(--green-light); color: var(--green-dark); }
        .pill.sent { background: var(--green); color: #fff; }
        .pill.draft { background: #eef1f4; color: #5d6875; }
        .alphabet-nav { position: sticky; top: 64px; z-index: 4; display: flex; gap: 5px; overflow-x: auto; margin: -4px 0 16px; padding: 10px; border: 1px solid var(--line); border-radius: 12px; background: rgba(255, 255, 255, .97); box-shadow: 0 4px 14px rgba(23, 48, 31, .08); scrollbar-width: thin; }
        .letter-button { flex: 0 0 36px; min-height: 38px; border: 1px solid var(--line); border-radius: 9px; background: #fff; color: var(--green-dark); cursor: pointer; font-weight: 800; }
        .letter-button.active { border-color: var(--green); background: var(--green); color: #fff; }
        .letter-button:disabled { color: #aab6aa; cursor: default; opacity: .55; }
        .fruit-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .fruit-card { display: flex; min-width: 0; align-items: center; gap: 10px; padding: 12px; border: 1px solid var(--line); border-radius: 11px; background: #fff; }
        .fruit-info { flex: 1; min-width: 0; }
        .fruit-name { display: block; overflow-wrap: anywhere; font-weight: 800; }
        .fruit-card select { width: 100%; max-width: 170px; min-height: 38px; padding: 6px 8px; border: 1px solid var(--line); border-radius: 8px; background: #fff; font-size: 13px; }
        .fruit-input { width: 92px; min-height: 44px; text-align: center; font-size: 17px; font-weight: 800; }
        .sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; clip-path: inset(50%); }
        .btn { display: inline-flex; min-height: 42px; align-items: center; justify-content: center; padding: 9px 15px; border: 0; border-radius: 9px; background: var(--green); color: #fff; cursor: pointer; font-weight: 750; text-decoration: none; }
        .btn:hover { background: var(--green-dark); color: #fff; }
        .btn:disabled { cursor: not-allowed; opacity: .55; }
        .btn.light { border: 1px solid currentColor; background: transparent; color: inherit; }
        .btn.orange { background: var(--orange); color: #3b2400; }
        .line-list { margin: 0; padding: 0; list-style: none; }
        .order-line { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 9px; padding: 14px 16px; border: 1px solid var(--line); border-radius: 11px; background: #fbfdf9; }
        .preview-fruit { font-size: 15px; }
        .preview-quantity { padding: 7px 13px; background: var(--green-light); color: var(--green-dark); font-size: 14px; }
        label { display: block; margin-bottom: 5px; color: var(--muted); font-size: 12px; font-weight: 700; }
        input, select, textarea { width: 100%; min-height: 42px; padding: 9px 11px; border: 1px solid var(--line); border-radius: 9px; background: var(--card); }
        input:focus, select:focus, textarea:focus, button:focus-visible, a:focus-visible { outline: 3px solid rgba(47, 158, 79, .25); outline-offset: 2px; }
        .empty { padding: 26px 10px; color: var(--muted); text-align: center; }
        .actions { display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap; margin-top: 16px; }
        .banner { margin-bottom: 16px; padding: 12px 14px; border-left: 5px solid var(--green); border-radius: 9px; background: var(--green-light); }
        .banner.error { border-color: var(--red); background: #fff0ed; color: #8d261e; }
        .banner.warning { border-color: var(--orange); background: var(--orange-light); }
        .notes { margin-top: 15px; }
        .order-items { width: 100%; border-collapse: collapse; }
        .order-items th, .order-items td { padding: 10px; border-bottom: 1px solid var(--line); text-align: left; }
        .order-items th { color: var(--muted); font-size: 12px; text-transform: uppercase; }
        .order-items td:last-child, .order-items th:last-child { text-align: right; }
        .logout { min-height: 36px; padding: 6px 11px; background: var(--orange); color: #3b2400; }
        .logout:hover { background: #ffa445; color: #3b2400; }
        @media (max-width: 700px) {
            .page { padding: 14px 12px 32px; }
            .card { padding: 15px; }
            .fruit-grid { grid-template-columns: 1fr; }
            .order-line { gap: 8px; padding: 12px; }
            .preview-fruit { font-size: 14px; }
            .preview-quantity { flex: 0 0 auto; }
            .table-wrap { overflow-x: auto; }
        }
        @media (max-width: 420px) {
            h1 { font-size: 21px; }
            .alphabet-nav { margin-right: -4px; margin-left: -4px; }
            .fruit-card { flex-wrap: wrap; }
            .fruit-card select { max-width: none; }
            .fruit-card .btn { width: 100%; }
            .actions .btn { width: 100%; }
        }
    </style>
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <div class="brand">FreshLink <small>{{ $shop->name }} · Shop order</small></div>
        <form method="POST" action="{{ route('shop.logout') }}">
            @csrf
            <button class="btn logout" type="submit">Log out</button>
        </form>
    </div>
</header>

<main class="page">
    <div id="feedback" aria-live="polite"></div>

    @if (!$catalog->count())
        <div class="banner warning">No fruits with active Caja prices are available for {{ $orderDate }}. Please contact the warehouse.</div>
    @endif

    <section class="card">
        <div class="order-meta">
            <div>
                <h1>Order for {{ \Illuminate\Support\Carbon::parse($orderDate)->format('D, d M Y') }}</h1>
                <p class="muted mb-0">Choose fruits by letter and enter the number of Cajas your shop needs.</p>
            </div>
            @if ($order)
                <span class="pill {{ $order->status }}">{{ ucfirst($order->status) }}</span>
            @else
                <span class="pill draft">Not submitted</span>
            @endif
        </div>
        <p class="hint mt-3 mb-0">Your account only shows orders for {{ $shop->name }}. Other shops' orders are private.</p>
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
                <table class="order-items">
                    <thead><tr><th>Fruit</th><th>Quantity</th></tr></thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->fruit->display_name ?: $item->fruit->name }}</td>
                                <td>{{ (int) $item->quantity }} Cajas</td>
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
            <section class="card">
                <div class="order-meta">
                    <div>
                        <h2 class="mb-1">Choose your fruits</h2>
                        <p class="hint mb-0">Tap a letter to see fruits starting with it. All quantities are ordered in Caja.</p>
                    </div>
                    <span class="pill" id="fruitCount">{{ $catalog->count() }} fruits</span>
                </div>
            </section>
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
                <ul class="line-list" id="orderLines"></ul>
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
</main>

<script>
(() => {
    const catalog = @json($catalog);
    const initialItems = @json($initialItems);
    const draftUrl = @json(route('shop.orders.draft'));
    const submitUrl = @json(route('shop.orders.submit'));
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
        list.innerHTML = lines.map(({ fruit, quantity }) => {
            return `<li class="order-line">
                <span class="fruit-name preview-fruit">${escapeHtml(fruit.name)}</span>
                <span class="pill preview-quantity">${escapeHtml(quantity)} Cajas</span>
            </li>`;
        }).join('');
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
    document.getElementById('saveDraftBtn')?.addEventListener('click', () => postOrder(draftUrl, 'save'));
    document.getElementById('previewOrderBtn')?.addEventListener('click', () => {
        document.getElementById('orderPreview').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    document.getElementById('downloadPdfBtn')?.addEventListener('click', () => postOrder(draftUrl, 'download'));
    document.getElementById('submitOrderBtn')?.addEventListener('click', () => {
        if (window.confirm('Submit this order to the warehouse? It cannot be edited after submission.')) postOrder(submitUrl, 'submit');
    });

    if (document.getElementById('orderLines')) {
        renderAlphabet();
        renderOrderPreview();
    }
    const successMessage = sessionStorage.getItem('shop-order-success');
    if (successMessage) {
        sessionStorage.removeItem('shop-order-success');
        showMessage(successMessage);
    }
})();
</script>
</body>
</html>

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
            --blue-light: #e7eefd;
            --blue: #1d4fbf;
        }

        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--ink); font: 16px/1.45 "Trebuchet MS", "Segoe UI", system-ui, sans-serif; }
        button, input, select, textarea { color: inherit; font: inherit; }
        .topbar { position: sticky; top: 0; z-index: 3; background: var(--green); border-bottom: 4px solid var(--orange); color: #fff; }
        .topbar-inner { display: flex; align-items: center; gap: 12px; max-width: 980px; margin: auto; padding: 12px 16px; }
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
        .controls { display: grid; grid-template-columns: minmax(200px, 2fr) minmax(130px, 1fr) minmax(150px, 1fr) auto; align-items: end; gap: 10px; }
        label { display: block; margin-bottom: 5px; color: var(--muted); font-size: 13px; font-weight: 600; }
        input, select, textarea { width: 100%; min-height: 42px; padding: 9px 11px; border: 1px solid var(--line); border-radius: 9px; background: var(--card); }
        input:focus, select:focus, textarea:focus { outline: 3px solid rgba(47, 158, 79, .2); border-color: var(--green); }
        .btn { display: inline-flex; min-height: 42px; align-items: center; justify-content: center; padding: 9px 15px; border: 0; border-radius: 9px; background: var(--green); color: #fff; cursor: pointer; font-weight: 750; text-decoration: none; }
        .btn:hover { background: var(--green-dark); color: #fff; }
        .btn:disabled { cursor: not-allowed; opacity: .55; }
        .btn.light { border: 1px solid currentColor; background: transparent; color: inherit; }
        .btn.orange { background: var(--orange); color: #3b2400; }
        .btn.remove { min-width: 42px; background: #fff0ed; color: var(--red); }
        .line-list { margin: 0; padding: 0; list-style: none; }
        .order-line { display: grid; grid-template-columns: minmax(170px, 2fr) minmax(120px, 1fr) minmax(145px, 1.3fr) minmax(115px, .8fr) minmax(105px, .8fr) 42px; align-items: center; gap: 9px; padding: 12px 0; border-bottom: 1px solid var(--line); }
        .line-heading { display: grid; grid-template-columns: minmax(170px, 2fr) minmax(120px, 1fr) minmax(145px, 1.3fr) minmax(115px, .8fr) minmax(105px, .8fr) 42px; gap: 9px; padding: 0 0 8px; color: var(--muted); font-size: 11px; font-weight: 800; text-transform: uppercase; }
        .line-price { color: var(--muted); font-size: 13px; }
        .line-total { text-align: right; font-size: 14px; font-weight: 750; white-space: nowrap; }
        .empty { padding: 26px 10px; color: var(--muted); text-align: center; }
        .total-row { display: flex; justify-content: space-between; gap: 12px; margin-top: 15px; padding-top: 14px; border-top: 1px solid var(--line); font-size: 17px; font-weight: 800; }
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
        @media (max-width: 760px) {
            .line-heading { display: none; }
            .order-line { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) 42px; gap: 9px; }
            .order-line .fruit-field { grid-column: 1 / 3; }
            .order-line .unit-field { grid-column: 1; }
            .order-line .box-field { grid-column: 2; }
            .order-line .quantity-field { grid-column: 1; }
            .order-line .price-field { grid-column: 2; }
            .order-line .remove { grid-column: 3; grid-row: 2; }
            .controls { grid-template-columns: 1fr 1fr; }
            .controls .fruit-picker { grid-column: 1 / 3; }
            .controls .btn { grid-column: 1 / 3; }
            .line-total { text-align: left; }
            .table-wrap { overflow-x: auto; }
        }
        @media (max-width: 420px) {
            .order-line { grid-template-columns: minmax(0, 1fr) 42px; }
            .order-line .fruit-field, .order-line .unit-field, .order-line .box-field,
            .order-line .quantity-field, .order-line .price-field { grid-column: 1; }
            .order-line .remove { grid-column: 2; grid-row: 1; }
            .actions .btn { width: 100%; }
        }
    </style>
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <div class="brand">🍎 FreshLink <small>{{ $shop->name }} · Shop order</small></div>
        <form method="POST" action="{{ route('shop.logout') }}">
            @csrf
            <button class="btn logout" type="submit">Log out</button>
        </form>
    </div>
</header>

<main class="page">
    <div id="feedback"></div>

    @if (!$catalog->count())
        <div class="banner warning">No fruits with active KG/Caja prices are available for {{ $orderDate }}. Please contact the warehouse.</div>
    @endif

    <section class="card">
        <div class="order-meta">
            <div>
                <h1>Order for {{ \Illuminate\Support\Carbon::parse($orderDate)->format('D, d M Y') }}</h1>
                <p class="muted mb-0">Add the fruits and quantities your shop needs. Prices are shown before you send the order.</p>
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
        <section class="card">
            <h2>Order sent to warehouse</h2>
            <div class="banner">
                Order #{{ $order->id }} was submitted {{ $order->submitted_at?->format('d M Y, H:i') }}.
                The warehouse will prepare your order and update its status here.
            </div>
            <div class="table-wrap">
                <table class="order-items">
                    <thead><tr><th>Fruit</th><th>Quantity</th><th>Price / unit</th><th>Line total</th></tr></thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->fruit->display_name ?: $item->fruit->name }}</td>
                                <td>
                                    {{ number_format((float) $item->quantity, 3) }} {{ $item->unit->symbol }}
                                    @if ($item->boxConfiguration)<small class="d-block muted">{{ $item->boxConfiguration->name }} ({{ number_format((float) $item->boxConfiguration->weight_kg, 3) }} kg)</small>@endif
                                </td>
                                <td>{{ $item->unit_price !== null ? number_format((float) $item->unit_price, 2) . ' ' . $currency : '—' }}</td>
                                <td>{{ $item->line_total !== null ? number_format((float) $item->line_total, 2) : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr><td colspan="3">Estimated total</td><td>{{ $currency }} {{ number_format($order->items->sum('line_total'), 2) }}</td></tr></tfoot>
                </table>
            </div>
            @if ($order->notes)<p class="hint mt-3 mb-0"><strong>Note:</strong> {{ $order->notes }}</p>@endif
        </section>
    @else
        <form id="orderForm">
            @csrf
            <section class="card">
                <h2>Add fruit to your order</h2>
                <div class="controls">
                    <div class="fruit-picker">
                        <label for="fruitPicker">Fruit</label>
                        <select id="fruitPicker">
                            @foreach ($catalog as $fruit)
                                <option value="{{ $fruit['id'] }}">{{ $fruit['name'] }} ({{ $fruit['code'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="unitPicker">Order unit</label>
                        <select id="unitPicker"></select>
                    </div>
                    <div>
                        <label for="boxPicker">Box / Caja</label>
                        <select id="boxPicker"></select>
                    </div>
                    <button class="btn" type="button" id="addFruitBtn">＋ Add fruit</button>
                </div>
            </section>

            <section class="card">
                <div class="order-meta">
                    <div><h2 class="mb-1">Your order list</h2><span class="hint">Quantities can be entered in kilograms or Caja.</span></div>
                    <span class="pill" id="lineCount">0 items</span>
                </div>
                <div class="line-heading mt-3"><span>Fruit</span><span>Unit</span><span>Box</span><span>Quantity</span><span class="text-right">Estimate</span><span></span></div>
                <ul class="line-list" id="orderLines"></ul>
                <div id="emptyOrder" class="empty">🧺<br>Your list is empty. Add the fruits your shop needs.</div>
                <div class="total-row"><span>Estimated total</span><span id="orderTotal">—</span></div>
                <div class="notes">
                    <label for="orderNotes">Note for the warehouse (optional)</label>
                    <textarea id="orderNotes" rows="2" maxlength="2000" placeholder="Delivery or preparation notes">{{ $order?->notes }}</textarea>
                </div>
                <div class="actions">
                    <button class="btn light" type="button" id="saveDraftBtn">Save draft</button>
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
    const currency = @json($currency);
    const draftUrl = @json(route('shop.orders.draft'));
    const submitUrl = @json(route('shop.orders.submit'));
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    let lines = initialItems;

    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);
    const fruitFor = id => catalog.find(fruit => Number(fruit.id) === Number(id));
    const unitFor = (fruit, id) => fruit?.units.find(unit => Number(unit.id) === Number(id));
    const boxFor = (unit, id) => unit?.boxes.find(box => Number(box.id) === Number(id));
    const money = amount => `${currency} ${Number(amount || 0).toFixed(2)}`;
    const option = (value, label, selected, disabled = false) => `<option value="${escapeHtml(value)}" ${selected ? 'selected' : ''} ${disabled ? 'disabled' : ''}>${escapeHtml(label)}</option>`;

    function syncPickers() {
        const fruit = fruitFor(document.getElementById('fruitPicker').value);
        const unitSelect = document.getElementById('unitPicker');
        unitSelect.innerHTML = fruit ? fruit.units.map((unit, index) => option(unit.id, `${unit.name} (${unit.symbol})`, index === 0)).join('') : '';
        syncBoxPicker();
    }

    function syncBoxPicker(selectedId = null) {
        const fruit = fruitFor(document.getElementById('fruitPicker').value);
        const unit = unitFor(fruit, document.getElementById('unitPicker').value);
        const boxPicker = document.getElementById('boxPicker');
        const boxField = boxPicker.closest('div');
        if (!unit || unit.code !== 'BOX') {
            boxField.hidden = true;
            boxPicker.innerHTML = '';
            return;
        }

        boxField.hidden = false;
        boxPicker.innerHTML = unit.boxes.map((box, index) =>
            option(box.id, `${box.name} · ${box.weight_kg} kg · ${money(box.price)}`, selectedId ? Number(selectedId) === Number(box.id) : index === 0)
        ).join('');
    }

    function renderLines() {
        const list = document.getElementById('orderLines');
        const empty = document.getElementById('emptyOrder');
        const lineCount = document.getElementById('lineCount');
        const totalOutput = document.getElementById('orderTotal');
        if (!list) return;

        empty.hidden = lines.length > 0;
        lineCount.textContent = `${lines.length} item${lines.length === 1 ? '' : 's'}`;
        list.innerHTML = lines.map((line, index) => {
            const fruit = fruitFor(line.fruit_id);
            const unit = unitFor(fruit, line.unit_id);
            const box = boxFor(unit, line.box_configuration_id);
            const price = box ? box.price : unit?.price;
            const lineTotal = Number(line.quantity || 0) * Number(price || 0);
            const fruitOptions = catalog.map(item => {
                const alreadyUsed = lines.some((other, otherIndex) => otherIndex !== index && Number(other.fruit_id) === Number(item.id));
                return option(item.id, `${item.name} (${item.code})`, Number(item.id) === Number(line.fruit_id), alreadyUsed);
            });
            const unitOptions = (fruit?.units || []).map(item => option(item.id, `${item.name} (${item.symbol})`, Number(item.id) === Number(line.unit_id)));
            const boxOptions = (unit?.boxes || []).map(item => option(item.id, `${item.name} · ${item.weight_kg} kg`, Number(item.id) === Number(line.box_configuration_id)));
            const needsBox = unit?.code === 'BOX';

            return `<li class="order-line" data-index="${index}">
                <div class="fruit-field"><label>Fruit</label><select data-field="fruit_id">${fruitOptions.join('')}</select></div>
                <div class="unit-field"><label>Unit</label><select data-field="unit_id">${unitOptions.join('')}</select></div>
                <div class="box-field" ${needsBox ? '' : 'hidden'}><label>Box / Caja</label><select data-field="box_configuration_id">${boxOptions.join('')}</select></div>
                <div class="quantity-field"><label>Quantity</label><input data-field="quantity" type="number" min="0.001" step="0.001" value="${escapeHtml(line.quantity)}" inputmode="decimal"></div>
                <div class="price-field"><label>Est. line total</label><div class="line-total">${money(lineTotal)}<small class="d-block line-price">${price ? `${money(price)} / ${escapeHtml(unit.symbol)}` : 'Price unavailable'}</small></div></div>
                <button class="btn remove" type="button" data-remove="${index}" aria-label="Remove fruit">×</button>
            </li>`;
        }).join('');

        totalOutput.textContent = money(lines.reduce((sum, line) => {
            const fruit = fruitFor(line.fruit_id);
            const unit = unitFor(fruit, line.unit_id);
            return sum + Number(line.quantity || 0) * Number(boxFor(unit, line.box_configuration_id)?.price ?? unit?.price ?? 0);
        }, 0));
    }

    function showMessage(message, isError = false) {
        const feedback = document.getElementById('feedback');
        feedback.innerHTML = `<div class="banner ${isError ? 'error' : ''}" role="status">${escapeHtml(message)}</div>`;
        feedback.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function postOrder(url, submit) {
        if (submit && !lines.length) {
            showMessage('Add at least one fruit before submitting.', true);
            return;
        }

        const buttons = [...document.querySelectorAll('#saveDraftBtn, #submitOrderBtn')];
        buttons.forEach(button => button.disabled = true);
        const payload = {
            _token: csrf,
            notes: document.getElementById('orderNotes').value,
            items: lines.map(line => ({
                fruit_id: line.fruit_id,
                unit_id: line.unit_id,
                box_configuration_id: line.box_configuration_id || '',
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
            window.location.reload();
        }).catch(error => showMessage(error.message, true))
            .finally(() => buttons.forEach(button => button.disabled = false));
    }

    document.getElementById('fruitPicker')?.addEventListener('change', syncPickers);
    document.getElementById('unitPicker')?.addEventListener('change', () => syncBoxPicker());
    document.getElementById('addFruitBtn')?.addEventListener('click', () => {
        const fruit = fruitFor(document.getElementById('fruitPicker').value);
        const unit = unitFor(fruit, document.getElementById('unitPicker').value);
        const box = unit?.code === 'BOX' ? unit.boxes.find(item => Number(item.id) === Number(document.getElementById('boxPicker').value)) : null;
        if (!fruit || !unit || lines.some(line => Number(line.fruit_id) === Number(fruit.id)) || (unit.code === 'BOX' && !box)) {
            showMessage('Choose a fruit and a valid order unit. A fruit can only be added once; change its unit on the order row.', true);
            return;
        }
        lines.push({ fruit_id: fruit.id, unit_id: unit.id, box_configuration_id: box?.id || null, quantity: '' });
        renderLines();
    });
    document.getElementById('orderLines')?.addEventListener('change', event => {
        const row = event.target.closest('.order-line');
        if (!row) return;
        const index = Number(row.dataset.index);
        const field = event.target.dataset.field;
        if (field === 'fruit_id') {
            const fruit = fruitFor(event.target.value);
            const unit = fruit?.units[0];
            lines[index] = {
                fruit_id: fruit?.id,
                unit_id: unit?.id,
                box_configuration_id: unit?.code === 'BOX' ? unit.boxes[0]?.id : null,
                quantity: lines[index].quantity,
            };
        } else if (field === 'unit_id') {
            const unit = unitFor(fruitFor(lines[index].fruit_id), event.target.value);
            lines[index].unit_id = unit?.id;
            lines[index].box_configuration_id = unit?.code === 'BOX' ? unit.boxes[0]?.id : null;
        } else if (field === 'box_configuration_id') {
            lines[index].box_configuration_id = event.target.value;
        }
        renderLines();
    });
    document.getElementById('orderLines')?.addEventListener('input', event => {
        if (event.target.dataset.field !== 'quantity') return;
        const row = event.target.closest('.order-line');
        lines[Number(row.dataset.index)].quantity = event.target.value;
        renderLines();
        const input = document.querySelector(`.order-line[data-index="${row.dataset.index}"] [data-field="quantity"]`);
        input?.focus();
    });
    document.getElementById('orderLines')?.addEventListener('click', event => {
        const remove = event.target.closest('[data-remove]');
        if (!remove) return;
        lines.splice(Number(remove.dataset.remove), 1);
        renderLines();
    });
    document.getElementById('saveDraftBtn')?.addEventListener('click', () => postOrder(draftUrl, false));
    document.getElementById('submitOrderBtn')?.addEventListener('click', () => {
        if (window.confirm('Submit this order to the warehouse? It cannot be edited after submission.')) postOrder(submitUrl, true);
    });

    if (document.getElementById('orderLines')) {
        syncPickers();
        renderLines();
    }
})();
</script>
</body>
</html>

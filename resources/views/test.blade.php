<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frutería Wholesale Order & Invoice System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Courier+Prime:wght@400;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }

        .mono {
            font-family: 'Courier Prime', monospace;
        }

        /* Standard Print Rules for Exact PDF Generation */
        @media print {
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .no-print {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            .page-break {
                page-break-before: always;
                break-before: page;
            }

            @page {
                size: A4 portrait;
                margin: 10mm 12mm 10mm 12mm;
            }

            .printable-paper {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        /* Custom Table Grid Borders matching Spanish Wholesale Sheet */
        .sheet-border {
            border: 1.5px solid #1e293b;
        }
        .sheet-border th, .sheet-border td {
            border: 1px solid #334155;
        }

        .invoice-table th, .invoice-table td {
            border: 1px solid #000;
            padding: 2px 4px;
            font-size: 11px;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 flex flex-col">

    <header class="no-print bg-slate-900 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">🍎</span>
                <div>
                    <h1 class="font-bold text-lg leading-tight">Gestión Frutería Mayorista</h1>
                    <p class="text-xs text-slate-400">Order Sheet & Invoice Generator</p>
                </div>
            </div>

            <!-- View Switcher Tabs -->
            <div class="flex bg-slate-800 p-1 rounded-lg border border-slate-700">
                <button id="btn-mode-customer" onclick="switchView('customer')" class="px-4 py-1.5 text-xs sm:text-sm font-semibold rounded-md transition-all bg-emerald-600 text-white shadow">
                    1. Hoja de Pedido (Cliente)
                </button>
                <button id="btn-mode-warehouse" onclick="switchView('warehouse')" class="px-4 py-1.5 text-xs sm:text-sm font-semibold rounded-md transition-all text-slate-300 hover:text-white">
                    2. Entrada Almacén (Pesaje)
                </button>
                <button id="btn-mode-invoice" onclick="switchView('invoice')" class="px-4 py-1.5 text-xs sm:text-sm font-semibold rounded-md transition-all text-slate-300 hover:text-white">
                    3. Factura Final (PDF)
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
                <button onclick="loadSampleData()" class="px-3 py-1.5 text-xs font-medium bg-slate-700 hover:bg-slate-600 text-slate-200 rounded transition">
                    Cargar Ejemplo
                </button>
                <button onclick="window.print()" class="px-4 py-1.5 text-xs sm:text-sm font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 rounded shadow flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 002-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Imprimir / Guardar PDF
                </button>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto p-4 sm:p-6">

        <section id="view-customer" class="space-y-4">
            <div class="no-print bg-white p-4 rounded-xl shadow-sm border border-slate-200 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <h2 class="font-bold text-slate-900 text-lg">1. Hoja de Toma de Pedidos (Customer Sheet)</h2>
                    <p class="text-xs text-slate-500">Seleccione la cantidad o cajas requeridas para cada producto.</p>
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-xs font-semibold text-slate-700">Cliente / Tienda:</label>
                    <input type="text" id="cust-store-input" value="TIENDA : ENTREVIAS" oninput="syncHeaders()" class="border rounded px-2 py-1 text-sm bg-slate-50 focus:bg-white border-slate-300">
                    
                    <label class="text-xs font-semibold text-slate-700">Fecha:</label>
                    <input type="date" id="cust-date-input" oninput="syncHeaders()" class="border rounded px-2 py-1 text-sm bg-slate-50 focus:bg-white border-slate-300">
                </div>
            </div>

            <!-- Customer Sheet Printable Template (4 Columns Grid) -->
            <div class="printable-paper bg-white p-6 sm:p-8 rounded-xl shadow-md border border-slate-200 mx-auto max-w-[210mm]">
                <div class="flex justify-between items-end border-b-2 border-slate-900 pb-2 mb-4">
                    <div>
                        <span class="text-xs font-bold text-slate-500 block">NOMBRE DEL CLIENTE / TIENDA:</span>
                        <span id="disp-cust-name" class="text-lg font-bold uppercase text-slate-900">TIENDA : ENTREVIAS</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-slate-500 block">FECHA:</span>
                        <span id="disp-cust-date" class="text-base font-bold text-slate-900">30/09/2026</span>
                    </div>
                </div>

                <!-- 4 Columns Product List Grid -->
                <div id="customer-grid-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 text-xs">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>
        </section>

        <section id="view-warehouse" class="hidden space-y-4">
            <div class="no-print bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <h2 class="font-bold text-slate-900 text-lg">2. Panel del Encargado de Almacén (Pesaje y Precios)</h2>
                        <p class="text-xs text-slate-500">Ingrese Gross Weight (T KILO), Tare (TARA), and Unit Price (PRECIO) for ordered items.</p>
                    </div>
                    <button onclick="clearAllData()" class="text-xs text-red-600 hover:underline">
                        Limpiar Todo
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 uppercase font-bold border-b border-slate-300">
                            <tr>
                                <th class="p-2">Cajas/No</th>
                                <th class="p-2">Producto</th>
                                <th class="p-2 w-20">Kilo Base</th>
                                <th class="p-2 w-28">T KILO (Bruto)</th>
                                <th class="p-2 w-24">TARA (Caja)</th>
                                <th class="p-2 w-24">NETTO (Real)</th>
                                <th class="p-2 w-24">PRECIO (€)</th>
                                <th class="p-2 w-28 text-right">TOTAL (€)</th>
                            </tr>
                        </thead>
                        <tbody id="warehouse-table-body" class="divide-y divide-slate-200">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="view-invoice" class="hidden space-y-4">
            <div class="no-print bg-amber-50 border border-amber-200 p-3 rounded-lg text-xs text-amber-800 flex justify-between items-center">
                <span>📄 Vista previa exacta de la factura para descargar en PDF o Imprimir.</span>
                <button onclick="window.print()" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-3 py-1 rounded shadow">
                    Imprimir / Exportar a PDF
                </button>
            </div>

            <!-- Page Container matching 1st and 3rd Images -->
            <div id="invoice-pdf-pages" class="space-y-8">
                <!-- Page 1 -->
                <div class="printable-paper bg-white p-8 sm:p-10 rounded-xl shadow-lg border border-slate-300 mx-auto max-w-[210mm] min-h-[297mm] flex flex-col justify-between">
                    <div>
                        <!-- Bismillah Header -->
                        <div class="text-center font-bold text-sm tracking-wider uppercase mb-3 text-slate-900">
                            BISMILLAHIR RAHMANIR RAHEEM
                        </div>

                        <!-- Store Header -->
                        <div class="text-center font-bold text-xs sm:text-sm leading-snug mb-4 uppercase text-slate-900">
                            <div id="inv-header-store">TIENDA : ENTREVIAS</div>
                            <div>FECHA : <span id="inv-header-date">30/09/2026</span></div>
                        </div>

                        <!-- Invoice Main Table -->
                        <table class="invoice-table w-full mono border-collapse text-left">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th class="w-[6%] text-center">NO</th>
                                    <th class="w-[38%]">NAME</th>
                                    <th class="w-[8%] text-center">KILO</th>
                                    <th class="w-[10%] text-right">T KILO</th>
                                    <th class="w-[8%] text-right">TARA</th>
                                    <th class="w-[10%] text-right">NETTO</th>
                                    <th class="w-[10%] text-right">PRECIO</th>
                                    <th class="w-[10%] text-right">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody id="invoice-body-page-1">
                                <!-- JS Populated -->
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right text-[10px] text-slate-400 mt-4">Página 1</div>
                </div>

                <!-- Page 2 (Dynamic if item count > threshold or for exact summary match) -->
                <div id="invoice-page-2" class="printable-paper bg-white p-8 sm:p-10 rounded-xl shadow-lg border border-slate-300 mx-auto max-w-[210mm] min-h-[297mm] flex flex-col justify-between page-break">
                    <div>
                        <!-- Bismillah Header -->
                        <div class="text-center font-bold text-sm tracking-wider uppercase mb-4 text-slate-900">
                            BISMILLAHIR RAHMANIR RAHEEM
                        </div>

                        <table class="invoice-table w-full mono border-collapse text-left">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th class="w-[6%] text-center">NO</th>
                                    <th class="w-[38%]">NAME</th>
                                    <th class="w-[8%] text-center">KILO</th>
                                    <th class="w-[10%] text-right">T KILO</th>
                                    <th class="w-[8%] text-right">TARA</th>
                                    <th class="w-[10%] text-right">NETTO</th>
                                    <th class="w-[10%] text-right">PRECIO</th>
                                    <th class="w-[10%] text-right">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody id="invoice-body-page-2">
                                <!-- JS Populated -->
                            </tbody>
                            <tfoot id="invoice-footer-summary">
                                <tr class="font-bold text-red-600">
                                    <td></td>
                                    <td>GENERO</td>
                                    <td class="text-center">1</td>
                                    <td class="text-right">1</td>
                                    <td></td>
                                    <td class="text-right">1</td>
                                    <td class="text-right">173</td>
                                    <td class="text-right">173.00</td>
                                </tr>
                                <tr class="font-bold">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">0</td>
                                    <td></td>
                                    <td class="text-right">0</td>
                                    <td></td>
                                    <td class="text-right">0.00</td>
                                </tr>
                                <tr class="font-bold border-t-2 border-black">
                                    <td colspan="2" class="text-center uppercase" id="inv-footer-store">ENTREVIAS</td>
                                    <td colspan="4"></td>
                                    <td class="text-right font-bold text-sm">TOTAL</td>
                                    <td class="text-right font-bold text-sm" id="inv-grand-total">€ 2,040.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="text-right text-[10px] text-slate-400 mt-4">Página 2</div>
                </div>
            </div>
        </section>

    </main>

    <script>
        // Master Product List matching the entire Spanish Wholesale Catalog (Image 2)
        const MASTER_CATALOG = [
            "ACELGAS", "AGUACATE C", "AGUACATE D", "AGUACATE OFF", "AJETE", "AJO", "AJO BUTI", "AJO PELADO",
            "ALBAHACA", "ALBARICOQUE", "ALCACHOFAS", "ALOVERA", "APIO", "ARANDANOS", "BANANAS C", "BANANAS B",
            "BATATA", "BATATA ROJA", "BERENJENA", "BERENJENA LA", "BERENJEN PEQ", "BOLSA B", "BOLSA RO", "BOLSA V",
            "BROCOLI", "CALABACIN", "CALABAZA", "CALABAZA AUY", "CALABAZA PEQ", "CANARIAS", "CANONIGOS", "CASTAÑA",
            "CEBOLLAS B", "CEBOLLA BUTI", "CEBOLLAS DUL", "CEBOLLAS G", "CEBOLLA ROJA", "CEBOLLATA", "CEBOLLA FINA",
            "CEBOLLATA LA", "CEREZAS", "CHAMPIÑON", "CHAMPI BAN", "CHAMPI COR", "CHIRIMOLLA", "CHIRIVIA", "CIDRA",
            "CILANTRO", "CIRUELAS AMA", "CIRUELA CLAU", "CIRUELAS N", "CIRUELAS R", "COCO", "COL CHINA", "COL DE BRUSE",
            "COLIFLOR", "DATIL 1€", "EDO", "ENDIVIAS", "ENELDO", "ENSALADA", "ESCAROLA", "ESPARRAGOS C", "ESPARRAGOS B",
            "ESPINACA", "ESPINACA MAN", "FRAMBUESA", "FRESAS BAN", "FRESAS C", "FRESAS B", "GOYAVA", "GRANADA", "GRANADILLA",
            "GRELOS", "GUANTES", "GUINDIAS", "GUINEO VERDE", "HABAS", "HIERBABUENA", "HIGOS B", "HIGOS CHUM", "HIGOS N",
            "HIGOS SECO", "HINOJO", "JENGIBRE", "JUDIA BOBBY", "JUDIA LARGA", "JUDIA VERD C", "JUDIA VER OFF", "KAKI B-",
            "KIWI B-", "KIWI GOLDEN", "KIWI ZESPRI", "KOROLA", "LECHUGA BAT", "LECHUGA COG", "LECHUGA ISA", "LECHUGA LAR",
            "LEGUSTAN", "LIMA C", "LIMA OFF", "LIMON", "LIMON BOL", "LOMBARDA", "MAIZ COCIDO", "MAIZ VERDE", "MANDARINA C",
            "MANDARI NOR", "MANDARI OFF", "MANDAR ORRI", "MANDAR RAMA", "MANGO C", "MANGO OFF", "MA DONCELLA", "MANZANA FUJI",
            "MA GOLDEN C", "M GOLDEN OFF", "MAN GRAN C", "MAN GRAN OFF", "MAN KANZI", "MAN PERLIM", "MAN PINK", "MA REINETA C",
            "M REINETA OFF", "MAN ROYEL C", "MA ROYEL OFF", "MAN STARKING", "MAN VALVEN", "MELOCOTON C", "MELOCOTON B",
            "MELOCOTON R", "MELON C", "MELON OFF", "MELON CANTA", "MELON GALIA", "MENBRILLO", "MORA", "NABO", "NABO RAMA/CHINA",
            "NARANJA C", "NARANJA OFF", "NARANJ ZUMO", "NARANJA B", "NARANJ BOL C", "NARANJ BOL B", "NECTARINA", "NISCALOS",
            "NISPERO", "NUECES", "OKRA", "PAK CHOI", "PAPAYA C", "PAPAYA OFF", "PARAGUAYA", "PATATA AGRIA", "PATATA BOLSA",
            "PATATA LAVA", "PATATA ROJA", "PATATA SUCIA", "PEPINO", "PERA AGUA", "PERA CHINA", "PERA CON C", "PERA CON OFF",
            "PERA ERCOLIN", "PERA LIMO", "PEREJIL", "PICANTE AFRI", "PICANTE BAN", "PICOTAS", "PIMIENT ITA C", "PIMIENT ITA B",
            "PIMI ITA ROJO", "PIMI PADRON", "PIMI ROJO C", "PIMI ROJO OFF", "PIMI VERDE C", "PIM VERDE OFF", "PIÑA C", "PIÑA DEL MON",
            "PIÑA OFF", "PITHAYA", "PLATANO M", "PLATANO V", "POMELO", "POMELO CHIN", "PUERRO", "RABANO", "RAIZ DE APIO",
            "REMO COCIDO", "REMO CRUDO", "REPOLLO", "ROLLO DE MAC", "RUKULA", "SANDIA B", "SANDIA F", "SANDIA MAR", "SANDIA NEGRA",
            "SANDIA PLASE", "SANDIA ROLLO G", "SETAS BAND", "SETAS S", "SOPA JULIANA", "TOMATE CHER", "TOMATE ENSA", "TOMATE KUMA",
            "TOMATE OFF", "TOMATE PERA", "TOMATE RAF", "TOMATE RAMA", "TOMATE ROSA", "UVAS BANDEJA", "UVAS BLAN C", "UVAS NEGR C",
            "UVAS PEQ", "YAME", "YUCA C", "ZANAHORIA", "BANDEJA P"
        ];

        // State Store
        let orderState = {};

        // Initialize App
        window.addEventListener('DOMContentLoaded', () => {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('cust-date-input').value = today;
            syncHeaders();
            renderCustomerGrid();
        });

        function switchView(mode) {
            document.getElementById('view-customer').classList.add('hidden');
            document.getElementById('view-warehouse').classList.add('hidden');
            document.getElementById('view-invoice').classList.add('hidden');

            document.getElementById('btn-mode-customer').className = "px-4 py-1.5 text-xs sm:text-sm font-semibold rounded-md transition-all text-slate-300 hover:text-white";
            document.getElementById('btn-mode-warehouse').className = "px-4 py-1.5 text-xs sm:text-sm font-semibold rounded-md transition-all text-slate-300 hover:text-white";
            document.getElementById('btn-mode-invoice').className = "px-4 py-1.5 text-xs sm:text-sm font-semibold rounded-md transition-all text-slate-300 hover:text-white";

            if (mode === 'customer') {
                document.getElementById('view-customer').classList.remove('hidden');
                document.getElementById('btn-mode-customer').className = "px-4 py-1.5 text-xs sm:text-sm font-semibold rounded-md transition-all bg-emerald-600 text-white shadow";
            } else if (mode === 'warehouse') {
                renderWarehouseTable();
                document.getElementById('view-warehouse').classList.remove('hidden');
                document.getElementById('btn-mode-warehouse').className = "px-4 py-1.5 text-xs sm:text-sm font-semibold rounded-md transition-all bg-blue-600 text-white shadow";
            } else if (mode === 'invoice') {
                renderInvoicePages();
                document.getElementById('view-invoice').classList.remove('hidden');
                document.getElementById('btn-mode-invoice').className = "px-4 py-1.5 text-xs sm:text-sm font-semibold rounded-md transition-all bg-amber-600 text-white shadow";
            }
        }

        // Sync Headers Across Views
        function syncHeaders() {
            const store = document.getElementById('cust-store-input').value || 'TIENDA : ENTREVIAS';
            const dateVal = document.getElementById('cust-date-input').value;
            const dateObj = dateVal ? new Date(dateVal) : new Date();
            const formattedDate = `${String(dateObj.getDate()).padStart(2,'0')}/${String(dateObj.getMonth()+1).padStart(2,'0')}/${dateObj.getFullYear()}`;

            document.getElementById('disp-cust-name').innerText = store;
            document.getElementById('disp-cust-date').innerText = formattedDate;

            document.getElementById('inv-header-store').innerText = store;
            document.getElementById('inv-header-date').innerText = formattedDate;
            document.getElementById('inv-footer-store').innerText = store.replace('TIENDA :', '').trim();
        }

        function renderCustomerGrid() {
            const container = document.getElementById('customer-grid-container');
            container.innerHTML = '';

            // Render into 4 equal columns matching Image 2
            const itemsPerCol = Math.ceil(MASTER_CATALOG.length / 4);

            for (let col = 0; col < 4; col++) {
                const colDiv = document.createElement('div');
                colDiv.className = "border border-slate-300 rounded overflow-hidden divide-y divide-slate-200 bg-white";

                const colItems = MASTER_CATALOG.slice(col * itemsPerCol, (col + 1) * itemsPerCol);

                colItems.forEach(item => {
                    const row = document.createElement('div');
                    row.className = "flex items-center justify-between p-1 hover:bg-slate-50 transition";
                    
                    const val = orderState[item] ? orderState[item].qty || '' : '';

                    row.innerHTML = `
                        <span class="font-bold text-[10px] sm:text-xs text-slate-800 truncate pr-1">${item}</span>
                        <input type="text" placeholder="-" value="${val}" 
                            onchange="updateOrderQty('${item}', this.value)"
                            class="w-10 h-6 text-center text-xs font-bold bg-slate-100 focus:bg-amber-100 border border-slate-300 rounded focus:outline-none focus:ring-1 focus:ring-amber-500">
                    `;
                    colDiv.appendChild(row);
                });

                container.appendChild(colDiv);
            }
        }

        function updateOrderQty(item, val) {
            if (!orderState[item]) {
                orderState[item] = { item: item, qty: '', kilo: '', tKilo: '', tara: '', precio: '' };
            }
            orderState[item].qty = val;
            if (!val.trim()) {
                delete orderState[item];
            }
        }

        function renderWarehouseTable() {
            const tbody = document.getElementById('warehouse-table-body');
            tbody.innerHTML = '';

            const activeItems = Object.keys(orderState);

            if (activeItems.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-slate-400">No hay productos seleccionados. Seleccione cantidades en la 'Hoja de Pedido'.</td></tr>`;
                return;
            }

            activeItems.forEach(key => {
                const data = orderState[key];
                const tr = document.createElement('tr');
                tr.className = "hover:bg-slate-50";

                const tKilo = parseFloat(data.tKilo) || 0;
                const tara = parseFloat(data.tara) || 0;
                const netto = Math.max(0, tKilo - tara);
                const precio = parseFloat(data.precio) || 0;

                const rowTotal = netto > 0 ? (netto * precio) : ((parseFloat(data.qty) || 0) * precio);

                tr.innerHTML = `
                    <td class="p-2 font-bold text-slate-900">${data.qty || '-'}</td>
                    <td class="p-2 font-semibold uppercase text-slate-800">${data.item}</td>
                    <td class="p-2"><input type="number" step="0.1" value="${data.kilo || ''}" onchange="updateWarehouseData('${key}', 'kilo', this.value)" class="w-16 p-1 border rounded text-xs"></td>
                    <td class="p-2"><input type="number" step="0.1" value="${data.tKilo || ''}" onchange="updateWarehouseData('${key}', 'tKilo', this.value)" class="w-20 p-1 border rounded text-xs font-bold bg-amber-50"></td>
                    <td class="p-2"><input type="number" step="0.1" value="${data.tara || ''}" onchange="updateWarehouseData('${key}', 'tara', this.value)" class="w-16 p-1 border rounded text-xs"></td>
                    <td class="p-2 font-bold text-slate-700">${netto > 0 ? netto.toFixed(1) : '-'}</td>
                    <td class="p-2"><input type="number" step="0.01" value="${data.precio || ''}" onchange="updateWarehouseData('${key}', 'precio', this.value)" class="w-20 p-1 border rounded text-xs font-bold"></td>
                    <td class="p-2 text-right font-bold text-emerald-700">€ ${rowTotal.toFixed(2)}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function updateWarehouseData(key, field, val) {
            if (orderState[key]) {
                orderState[key][field] = val;
            }
            renderWarehouseTable();
        }

        function renderInvoicePages() {
            const page1Body = document.getElementById('invoice-body-page-1');
            const page2Body = document.getElementById('invoice-body-page-2');

            page1Body.innerHTML = '';
            page2Body.innerHTML = '';

            const items = Object.values(orderState);
            let grandTotal = 0;

            const renderRowHtml = (data) => {
                const tKilo = parseFloat(data.tKilo) || 0;
                const tara = parseFloat(data.tara) || 0;
                const netto = Math.max(0, tKilo - tara);
                const precio = parseFloat(data.precio) || 0;
                const total = netto > 0 ? (netto * precio) : ((parseFloat(data.qty) || 0) * precio);

                grandTotal += total;

                return `
                    <tr>
                        <td class="text-center font-bold">${data.qty || ''}</td>
                        <td class="font-bold uppercase">${data.item}</td>
                        <td class="text-center">${data.kilo || ''}</td>
                        <td class="text-right">${tKilo ? tKilo.toFixed(1) : ''}</td>
                        <td class="text-right">${tara ? tara.toFixed(1) : ''}</td>
                        <td class="text-right font-bold">${netto ? netto.toFixed(1) : ''}</td>
                        <td class="text-right">${precio ? precio.toFixed(2) : ''}</td>
                        <td class="text-right font-bold">${total ? total.toFixed(2) : '0.00'}</td>
                    </tr>
                `;
            };

            // Split into Page 1 and Page 2 (50 items max per page)
            const page1Items = items.slice(0, 50);
            const page2Items = items.slice(50);

            page1Items.forEach(item => {
                page1Body.innerHTML += renderRowHtml(item);
            });

            page2Items.forEach(item => {
                page2Body.innerHTML += renderRowHtml(item);
            });

            document.getElementById('inv-grand-total').innerText = `€ ${grandTotal.toLocaleString('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }

        function loadSampleData() {
            orderState = {
                "ACELGAS": { item: "ACELGAS", qty: "1", kilo: "", tKilo: "12.2", tara: "1", precio: "0.8" },
                "AGUACATE C": { item: "AGUACATE C", qty: "3", kilo: "1", tKilo: "3", tara: "", precio: "15" },
                "AGUACATE DOMINICANO": { item: "AGUACATE DOMINICANO", qty: "1", kilo: "1", tKilo: "1", tara: "", precio: "19" },
                "AGUACATE OFF": { item: "AGUACATE OFF", qty: "4", kilo: "", tKilo: "41.3", tara: "0.3", precio: "2.5" },
                "AJO": { item: "AJO", qty: "1", kilo: "5", tKilo: "5", tara: "", precio: "2.4" },
                "ALBAHACA": { item: "ALBAHACA", qty: "5", kilo: "1", tKilo: "5", tara: "", precio: "1.25" },
                "APIO": { item: "APIO", qty: "3", kilo: "", tKilo: "18.2", tara: "0.4", precio: "0.85" },
                "ARANDANOS": { item: "ARANDANOS", qty: "1", kilo: "12", tKilo: "12", tara: "", precio: "1.65" },
                "BANANAS B": { item: "BANANAS B", qty: "8", kilo: "18", tKilo: "144", tara: "", precio: "1.05" },
                "BATATA": { item: "BATATA", qty: "1", kilo: "", tKilo: "14.1", tara: "0.6", precio: "0.55" },
                "PERA CON C": { item: "PERA CON C", qty: "1", kilo: "", tKilo: "7.8", tara: "0.5", precio: "0.95" },
                "PLATANO M": { item: "PLATANO M", qty: "6", kilo: "22", tKilo: "132", tara: "", precio: "1.3" },
                "ZANAHORIA": { item: "ZANAHORIA", qty: "6", kilo: "5", tKilo: "30", tara: "", precio: "0.6" }
            };

            renderCustomerGrid();
            syncHeaders();
            alert("Ejemplo cargado con éxito.");
        }

        function clearAllData() {
            if (confirm("¿Desea borrar todos los datos del pedido?")) {
                orderState = {};
                renderCustomerGrid();
                renderWarehouseTable();
                renderInvoicePages();
            }
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dhaka Frutas - Shop Order</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                        },
                        amberBrand: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom Scrollbar for smooth touch scroll */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        /* Mobile touch responsiveness */
        button, input {
            touch-action: manipulation;
        }
    </style>
</head>
<body class="bg-[#f4f7f4] text-gray-800 font-sans antialiased pb-28 md:pb-12">

    <!-- Top Navigation Header -->
    <header class="bg-brand-700 text-white sticky top-0 z-30 shadow-md">
        <div class="max-w-5xl mx-auto px-3 py-2.5 flex items-center justify-between">
            <div>
                <h1 class="text-base font-extrabold tracking-tight">Dhaka Frutas</h1>
                <p class="text-[11px] text-emerald-100 font-medium leading-none mt-0.5">RASEL BHAI - MAA FRUTAS • Shop order</p>
            </div>
            <button class="bg-amberBrand-500 hover:bg-amberBrand-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm transition active:scale-95">
                Log out
            </button>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-5xl mx-auto px-2.5 pt-3 space-y-3">

        <!-- Date & Order Info Card -->
        <div class="bg-white rounded-xl p-3 shadow-sm border border-emerald-100/60 flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-sm font-bold text-gray-800">Order for Thu, 05 Nov 2026</h2>
                    <span class="bg-gray-100 text-gray-600 text-[10px] font-semibold px-2 py-0.5 rounded-md border">Not submitted</span>
                </div>
                <p class="text-[11px] text-gray-500 mt-0.5">Your account only shows orders for RASEL BHAI • MAA FRUTAS</p>
            </div>
            <div class="hidden sm:block text-right">
                <span id="total-fruits-badge" class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-1 rounded-full">269 fruits available</span>
            </div>
        </div>

        <!-- Section Header + Alphabet Quick Jump Filter -->
        <div class="bg-white rounded-xl p-2.5 shadow-sm border border-emerald-100/60 sticky top-[49px] z-20">
            <div class="flex items-center justify-between mb-2 px-1">
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">Choose your fruits</span>
                <span class="text-[11px] font-medium text-emerald-700 sm:hidden"><span id="active-count-mobile">2</span> items selected</span>
            </div>

            <!-- Alphabet Scroll Bar -->
            <div id="alphabet-filter" class="flex space-x-1 overflow-x-auto no-scrollbar py-0.5">
                <!-- JS Populated A-Z buttons -->
            </div>
        </div>

        <!-- Compact 2-Column Fruit List Grid (Mobile First UI) -->
        <div id="fruit-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-1.5">
            <!-- Dynamic Items injected via JS -->
        </div>

        <!-- Desktop Order Preview Section (Visible on larger screens) -->
        <section class="hidden md:block bg-white rounded-xl p-4 shadow-sm border border-emerald-100 mt-6">
            <div class="flex items-center justify-between pb-3 border-b mb-3">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Your order preview</h3>
                    <p class="text-xs text-gray-500">Your selected fruits and Caja amounts appear here as you enter them.</p>
                </div>
                <span id="desktop-item-count" class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-1 rounded-full">2 items</span>
            </div>

            <!-- Preview Items Grid -->
            <div id="desktop-preview-list" class="grid grid-cols-2 gap-3 mb-4">
                <!-- Injected via JS -->
            </div>

            <!-- Notes & Desktop Actions -->
            <div class="space-y-3 pt-2 border-t">
                <div>
                    <label class="text-[11px] font-semibold text-gray-600 block mb-1">Note for the warehouse (optional)</label>
                    <textarea id="desktop-notes" rows="2" placeholder="Delivery or preparation notes..." class="w-full text-xs p-2.5 border rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-gray-50"></textarea>
                </div>
                <div class="flex items-center justify-end space-x-2">
                    <button onclick="saveOrder()" class="px-3.5 py-2 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 border">Save order</button>
                    <button onclick="downloadPDF()" class="px-3.5 py-2 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 border">Download PDF</button>
                    <button onclick="submitOrder()" class="px-5 py-2 text-xs font-bold text-white bg-amberBrand-500 hover:bg-amberBrand-600 rounded-lg shadow-sm">Submit to warehouse</button>
                </div>
            </div>
        </section>

    </main>

    <!-- Mobile Bottom Floating Action Bar -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-emerald-200 p-2.5 z-40 shadow-lg">
        <div class="flex items-center justify-between max-w-5xl mx-auto">
            <div onclick="toggleMobileCartDrawer()" class="cursor-pointer flex items-center space-x-2">
                <div class="relative bg-emerald-100 p-2 rounded-lg text-emerald-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span id="mobile-badge-count" class="absolute -top-1.5 -right-1.5 bg-amberBrand-500 text-white text-[10px] font-black w-4 h-4 rounded-full flex items-center justify-center">2</span>
                </div>
                <div>
                    <p id="mobile-bottom-sum" class="text-xs font-bold text-gray-800">9 Cajas Selected</p>
                    <p class="text-[10px] text-emerald-700 font-medium underline">Tap to view & submit</p>
                </div>
            </div>
            <button onclick="toggleMobileCartDrawer()" class="bg-amberBrand-500 hover:bg-amberBrand-600 active:scale-95 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow transition flex items-center space-x-1">
                <span>View Order</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            </button>
        </div>
    </div>

    <!-- Mobile Drawer Modal for Order Preview -->
    <div id="mobile-drawer-backdrop" onclick="toggleMobileCartDrawer()" class="fixed inset-0 bg-black/50 z-50 opacity-0 pointer-events-none transition-opacity duration-300 md:hidden"></div>
    <div id="mobile-drawer" class="fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl z-50 transform translate-y-full transition-transform duration-300 ease-out max-h-[85vh] flex flex-col md:hidden">
        
        <!-- Drawer Header -->
        <div class="p-3 border-b flex items-center justify-between bg-gray-50 rounded-t-2xl">
            <div class="flex items-center space-x-2">
                <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wide">Order Preview</h3>
                <span id="drawer-item-count" class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">2 items</span>
            </div>
            <button onclick="toggleMobileCartDrawer()" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Selected Items Scrollable List -->
        <div id="drawer-items-list" class="p-3 overflow-y-auto space-y-2 flex-1">
            <!-- Dynamic Selected Items injected via JS -->
        </div>

        <!-- Drawer Footer & Actions -->
        <div class="p-3 border-t bg-gray-50 space-y-2.5">
            <div>
                <label class="text-[10px] font-bold text-gray-600 block mb-1">Note for warehouse (optional)</label>
                <textarea id="mobile-notes" rows="2" placeholder="Delivery notes..." class="w-full text-xs p-2 border rounded-lg focus:ring-1 focus:ring-emerald-500 bg-white"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-2">
                <button onclick="saveOrder()" class="py-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-lg active:bg-gray-100">Save Order</button>
                <button onclick="downloadPDF()" class="py-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-lg active:bg-gray-100">Download PDF</button>
            </div>
            
            <button onclick="submitOrder()" class="w-full py-3 text-xs font-extrabold text-white bg-amberBrand-500 active:bg-amberBrand-600 rounded-xl shadow-md uppercase tracking-wider">
                Submit to warehouse
            </button>
        </div>
    </div>

    <!-- Toast Notification Box (Replaces alert) -->
    <div id="toast" class="fixed top-4 right-4 bg-gray-900 text-white text-xs px-4 py-2.5 rounded-xl shadow-xl z-50 opacity-0 pointer-events-none transition-all duration-300">
        Notification message
    </div>

    <!-- JavaScript Application Logic -->
    <script>
        // Sample Fruits Data
        const sampleFruits = [
            { id: 1, name: "ACELGAS", qty: 0, letter: "A" },
            { id: 2, name: "AGUACATE", qty: 0, letter: "A" },
            { id: 3, name: "AGUACATE C", qty: 0, letter: "A" },
            { id: 4, name: "AGUACATE D", qty: 0, letter: "A" },
            { id: 5, name: "AGUACATE DOMINICANO", qty: 1, letter: "A" },
            { id: 6, name: "AGUACATE OFF", qty: 8, letter: "A" },
            { id: 7, name: "AJETE", qty: 0, letter: "A" },
            { id: 8, name: "AJO", qty: 0, letter: "A" },
            { id: 9, name: "AJO BUTI", qty: 0, letter: "A" },
            { id: 10, name: "AJO PELADO", qty: 0, letter: "A" },
            { id: 11, name: "ALBAHACA", qty: 0, letter: "A" },
            { id: 12, name: "ALBARICOQUE", qty: 0, letter: "A" },
            { id: 13, name: "ALCACHOFAS", qty: 0, letter: "A" },
            { id: 14, name: "ALOVERA", qty: 0, letter: "A" },
            { id: 15, name: "APIO", qty: 3, letter: "A" },
            { id: 16, name: "ARANDANOS", qty: 2, letter: "A" },
            { id: 17, name: "BANANO", qty: 0, letter: "B" },
            { id: 18, name: "BERENJENA", qty: 0, letter: "B" },
            { id: 19, name: "CALABACIN", qty: 0, letter: "C" },
            { id: 20, name: "CEBOLLA", qty: 0, letter: "C" },
            { id: 21, name: "DURAZNO", qty: 0, letter: "D" },
            { id: 22, name: "FESA", qty: 0, letter: "F" },
            { id: 23, name: "GRANADA", qty: 0, letter: "G" },
            { id: 24, name: "HIJO", qty: 0, letter: "H" },
            { id: 25, name: "KIWI", qty: 0, letter: "K" },
            { id: 26, name: "LIMON", qty: 0, letter: "L" },
            { id: 27, name: "MANZANA", qty: 0, letter: "M" },
            { id: 28, name: "NARANJA", qty: 0, letter: "N" }
        ];

        let activeLetter = 'A';

        // Initialize UI
        window.onload = function() {
            renderAlphabetBar();
            renderFruitGrid();
            updateOrderPreview();
        };

        // Render A-Z Filter Buttons
        function renderAlphabetBar() {
            const alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ".split('');
            const container = document.getElementById('alphabet-filter');
            
            let html = `<button onclick="filterLetter('ALL')" class="px-2.5 py-1 text-xs font-bold rounded-lg border transition ${activeLetter === 'ALL' ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'}">ALL</button>`;
            
            alphabet.forEach(letter => {
                const isSelected = activeLetter === letter;
                const hasItems = sampleFruits.some(f => f.letter === letter);
                
                html += `
                    <button 
                        onclick="filterLetter('${letter}')" 
                        class="px-2.5 py-1 text-xs font-bold rounded-lg border transition ${isSelected ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'} ${!hasItems ? 'opacity-40 cursor-not-allowed' : ''}">
                        ${letter}
                    </button>
                `;
            });
            container.innerHTML = html;
        }

        // Filter Grid by Selected Letter
        function filterLetter(letter) {
            activeLetter = letter;
            renderAlphabetBar();
            renderFruitGrid();
        }

        // Render Fruits Grid (Compact Mobile 2-Columns Layout)
        function renderFruitGrid() {
            const container = document.getElementById('fruit-grid');
            const filtered = activeLetter === 'ALL' 
                ? sampleFruits 
                : sampleFruits.filter(f => f.letter === activeLetter);

            if (filtered.length === 0) {
                container.innerHTML = `<div class="col-span-full py-8 text-center text-xs text-gray-400 font-medium">No fruits starting with "${activeLetter}"</div>`;
                return;
            }

            let html = '';
            filtered.forEach(fruit => {
                const hasQty = fruit.qty > 0;
                html += `
                    <div class="bg-white rounded-lg p-2 border ${hasQty ? 'border-emerald-400 bg-emerald-50/20' : 'border-gray-200'} shadow-sm flex items-center justify-between transition-all">
                        <!-- Left: Item Name -->
                        <div class="pr-1 flex-1 min-w-0">
                            <span class="text-[11px] font-bold text-gray-800 uppercase tracking-tight block truncate" title="${fruit.name}">
                                ${fruit.name}
                            </span>
                        </div>

                        <!-- Right: Quantity Controls -->
                        <div class="flex items-center space-x-1 bg-gray-50 rounded-md p-0.5 border border-gray-200 shrink-0">
                            <button 
                                onclick="updateQty(${fruit.id}, -1)" 
                                class="w-6 h-6 flex items-center justify-center bg-white text-gray-700 font-extrabold rounded border shadow-2xs hover:bg-gray-100 active:scale-95 text-xs">
                                -
                            </button>
                            <input 
                                type="number" 
                                value="${fruit.qty}" 
                                readonly 
                                class="w-6 text-center text-xs font-extrabold bg-transparent text-gray-800 focus:outline-none">
                            <button 
                                onclick="updateQty(${fruit.id}, 1)" 
                                class="w-6 h-6 flex items-center justify-center bg-brand-600 text-white font-extrabold rounded shadow-2xs hover:bg-brand-700 active:scale-95 text-xs">
                                +
                            </button>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        // Handle Quantity Increase/Decrease
        function updateQty(id, change) {
            const fruit = sampleFruits.find(f => f.id === id);
            if (fruit) {
                fruit.qty = Math.max(0, fruit.qty + change);
                renderFruitGrid();
                updateOrderPreview();
            }
        }

        // Update Desktop & Mobile Order Preview Components
        function updateOrderPreview() {
            const selectedItems = sampleFruits.filter(f => f.qty > 0);
            const totalItemsCount = selectedItems.length;
            const totalCajasSum = selectedItems.reduce((sum, item) => sum + item.qty, 0);

            // Update Badges & Counters
            document.getElementById('active-count-mobile').innerText = totalItemsCount;
            document.getElementById('mobile-badge-count').innerText = totalItemsCount;
            document.getElementById('desktop-item-count').innerText = `${totalItemsCount} items`;
            document.getElementById('drawer-item-count').innerText = `${totalItemsCount} items`;
            document.getElementById('mobile-bottom-sum').innerText = `${totalCajasSum} Cajas Selected`;

            // Desktop List HTML
            const desktopList = document.getElementById('desktop-preview-list');
            // Drawer List HTML
            const drawerList = document.getElementById('drawer-items-list');

            if (selectedItems.length === 0) {
                const emptyState = `<p class="col-span-full py-4 text-center text-xs text-gray-400">No items added to order yet.</p>`;
                desktopList.innerHTML = emptyState;
                drawerList.innerHTML = emptyState;
                return;
            }

            let desktopHTML = '';
            let drawerHTML = '';

            selectedItems.forEach(item => {
                // Desktop Preview Item Component
                desktopHTML += `
                    <div class="p-2.5 rounded-lg border bg-emerald-50/30 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-800 uppercase">${item.name}</p>
                            <span class="text-[11px] font-semibold text-emerald-700">${item.qty} Cajas</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center border rounded bg-white p-0.5">
                                <button onclick="updateQty(${item.id}, -1)" class="px-2 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded">-</button>
                                <span class="px-2 text-xs font-bold">${item.qty}</span>
                                <button onclick="updateQty(${item.id}, 1)" class="px-2 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded">+</button>
                            </div>
                            <button onclick="updateQty(${item.id}, -${item.qty})" class="text-xs text-red-500 hover:text-red-700 font-medium">Remove</button>
                        </div>
                    </div>
                `;

                // Mobile Drawer Item Component
                drawerHTML += `
                    <div class="p-2 bg-white rounded-lg border flex items-center justify-between shadow-2xs">
                        <div>
                            <p class="text-xs font-bold text-gray-800 uppercase">${item.name}</p>
                            <span class="text-[10px] text-emerald-600 font-bold">${item.qty} Cajas</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center border rounded bg-gray-50 p-0.5">
                                <button onclick="updateQty(${item.id}, -1)" class="w-5 h-5 flex items-center justify-center font-bold text-gray-600">-</button>
                                <span class="px-2 text-xs font-bold">${item.qty}</span>
                                <button onclick="updateQty(${item.id}, 1)" class="w-5 h-5 flex items-center justify-center font-bold text-gray-600">+</button>
                            </div>
                            <button onclick="updateQty(${item.id}, -${item.qty})" class="text-xs text-red-500 font-medium">Remove</button>
                        </div>
                    </div>
                `;
            });

            desktopList.innerHTML = desktopHTML;
            drawerList.innerHTML = drawerHTML;
        }

        // Toggle Mobile Bottom Drawer Sheet
        function toggleMobileCartDrawer() {
            const drawer = document.getElementById('mobile-drawer');
            const backdrop = document.getElementById('mobile-drawer-backdrop');
            
            const isOpen = !drawer.classList.contains('translate-y-full');

            if (isOpen) {
                drawer.classList.add('translate-y-full');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
            } else {
                drawer.classList.remove('translate-y-full');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
            }
        }

        // Helper Toast Notification Function
        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.innerText = message;
            toast.classList.remove('opacity-0', 'pointer-events-none');
            setTimeout(() => {
                toast.classList.add('opacity-0', 'pointer-events-none');
            }, 2500);
        }

        // Action Handlers
        function saveOrder() {
            showToast("Order saved as draft!");
        }

        function downloadPDF() {
            showToast("Downloading order PDF...");
        }

        function submitOrder() {
            const selectedItems = sampleFruits.filter(f => f.qty > 0);
            if (selectedItems.length === 0) {
                showToast("Please select at least 1 fruit before submitting.");
                return;
            }
            showToast("Order successfully submitted to warehouse!");
            if (!document.getElementById('mobile-drawer').classList.contains('translate-y-full')) {
                toggleMobileCartDrawer();
            }
        }
    </script>
</body>
</html>
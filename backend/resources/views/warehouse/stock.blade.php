<x-layouts::app title="Stock Management - WMS Portal">
    <div x-data="stockPage()">
        <div class="mb-6 flex items-center gap-2 text-on-surface-variant font-body-sm">
            <span class="cursor-pointer hover:text-primary transition-colors">Inventory</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="font-medium text-primary">Stock Management</span>
        </div>

        <div class="mb-8">
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Stock Management</h2>
            <p class="font-body-sm text-on-surface-variant mt-2">Monitor inventory levels, adjust quantities, and manage stock movements.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-primary text-on-primary rounded-2xl p-5 shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">inventory_2</span>
                    </div>
                    <span class="font-label-caps text-label-caps bg-white/20 px-2 py-1 rounded-full">+12%</span>
                </div>
                <p class="font-headline-lg text-headline-lg">1,248</p>
                <p class="font-label-caps text-label-caps opacity-80 mt-1">Total Items in Stock</p>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-secondary text-2xl">warning</span>
                    </div>
                    <span class="font-label-caps text-label-caps bg-secondary-container/20 text-secondary px-2 py-1 rounded-full">Alert</span>
                </div>
                <p class="font-headline-lg text-headline-lg text-on-surface">23</p>
                <p class="font-label-caps text-label-caps text-on-surface-variant mt-1">Low Stock Items</p>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-error-container/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-error text-2xl">error</span>
                    </div>
                    <span class="font-label-caps text-label-caps bg-error-container/20 text-error px-2 py-1 rounded-full">Critical</span>
                </div>
                <p class="font-headline-lg text-headline-lg text-on-surface">5</p>
                <p class="font-label-caps text-label-caps text-on-surface-variant mt-1">Out of Stock</p>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-6 form-card">
            <div class="flex flex-col sm:flex-row gap-4 sm:items-end">
                <div class="flex-1">
                    <input x-model="searchQuery" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="Search by product name, SKU, or serial number..." type="text" />
                </div>
                <div class="sm:w-48">
                    <select x-model="filterCategory" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg font-body-sm">
                        <option value="">All Categories</option>
                        <option value="electronics">Industrial Electronics</option>
                        <option value="mechanical">Mechanical Parts</option>
                        <option value="safety">Safety Equipment</option>
                    </select>
                </div>
                <button class="btn-wh-primary h-12 px-4 gap-2" @click="drawerOpen = true">
                    <span class="material-symbols-outlined text-xl">tune</span>
                    Filters
                </button>
            </div>
        </div>

        <div class="md:hidden space-y-3">
            <template x-for="item in filteredStock" :key="item.id">
                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 form-card">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-body-sm font-semibold text-on-surface truncate" x-text="item.name"></p>
                            <p class="font-data-mono text-data-mono text-on-surface-variant" x-text="item.sku"></p>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] uppercase tracking-wider font-semibold shrink-0 ml-3" :class="getStatusBadge(item.qty)" x-text="getStatusLabel(item.qty)"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-outline-variant">
                        <div>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Serial</p>
                            <p class="font-data-mono text-data-mono text-on-surface" x-text="item.serialNo"></p>
                        </div>
                        <div class="text-right">
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Qty</p>
                            <p class="font-headline-md text-headline-md" :class="getQtyColor(item.qty)" x-text="item.qty"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="hidden md:block bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden form-card">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-surface-container border-b border-outline-variant">
                            <th class="text-left px-5 py-3 font-label-caps text-label-caps text-on-surface-variant">Product</th>
                            <th class="text-left px-5 py-3 font-label-caps text-label-caps text-on-surface-variant">SKU</th>
                            <th class="text-left px-5 py-3 font-label-caps text-label-caps text-on-surface-variant">Category</th>
                            <th class="text-center px-5 py-3 font-label-caps text-label-caps text-on-surface-variant">Qty</th>
                            <th class="text-left px-5 py-3 font-label-caps text-label-caps text-on-surface-variant">Status</th>
                            <th class="text-right px-5 py-3 font-label-caps text-label-caps text-on-surface-variant">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in filteredStock" :key="item.id">
                            <tr class="border-b border-outline-variant/30 hover:bg-surface-container-low/50 transition-colors">
                                <td class="px-5 py-4">
                                    <div>
                                        <p class="font-body-sm font-semibold text-on-surface" x-text="item.name"></p>
                                        <p class="font-data-mono text-data-mono text-on-surface-variant" x-text="item.serialNo"></p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 font-data-mono text-data-mono text-on-surface" x-text="item.sku"></td>
                                <td class="px-5 py-4 text-body-sm text-on-surface-variant" x-text="item.category"></td>
                                <td class="px-5 py-4 text-center font-data-mono text-data-mono" :class="getQtyColor(item.qty)" x-text="item.qty"></td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] uppercase tracking-wider font-semibold" :class="getStatusBadge(item.qty)" x-text="getStatusLabel(item.qty)"></span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button class="p-2 hover:bg-surface-container rounded-full transition-colors text-on-surface-variant">
                                        <span class="material-symbols-outlined text-sm">more_vert</span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Filter Drawer --}}
        <div
            class="fixed inset-0 z-50"
            x-show="drawerOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
        >
            <div class="absolute inset-0 bg-black/40" @click="drawerOpen = false"></div>
            <div class="absolute right-0 top-0 h-full w-80 bg-surface-container-lowest shadow-2xl flex flex-col">
                <div class="flex items-center justify-between px-5 h-16 border-b border-outline-variant">
                    <h2 class="font-headline-md text-headline-md text-on-surface">Filters</h2>
                    <button class="p-2 hover:bg-surface-container rounded-full transition-colors" @click="drawerOpen = false">
                        <span class="material-symbols-outlined text-on-surface-variant">close</span>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-5 space-y-4">
                    <div>
                        <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Stock Status</label>
                        <select x-model="advancedFilters.status" class="w-full h-10 px-3 bg-white border border-outline-variant rounded font-body-sm">
                            <option value="">All Status</option>
                            <option value="in-stock">In Stock</option>
                            <option value="low-stock">Low Stock</option>
                            <option value="out-of-stock">Out of Stock</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Min Quantity</label>
                        <input x-model="advancedFilters.minQty" type="number" class="w-full h-10 px-3 bg-white border border-outline-variant rounded font-body-sm" placeholder="0" />
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Max Quantity</label>
                        <input x-model="advancedFilters.maxQty" type="number" class="w-full h-10 px-3 bg-white border border-outline-variant rounded font-body-sm" placeholder="No limit" />
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Storage Zone</label>
                        <select x-model="advancedFilters.location" class="w-full h-10 px-3 bg-white border border-outline-variant rounded font-body-sm">
                            <option value="">All Zones</option>
                            <option value="zone-a">Zone A</option>
                            <option value="zone-b">Zone B</option>
                            <option value="zone-c">Zone C</option>
                            <option value="hazmat">HAZMAT</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" x-model="advancedFilters.lowStockOnly" class="w-4 h-4" id="lowStock" />
                        <label for="lowStock" class="font-body-sm text-body-sm text-on-surface-variant">Low Stock Alert Only</label>
                    </div>
                </div>
                <div class="p-5 border-t border-outline-variant flex gap-3">
                    <button class="flex-1 h-10 border border-outline-variant rounded-lg font-body-sm text-on-surface-variant hover:bg-surface-container" @click="resetFilters()">Reset</button>
                    <button class="flex-1 h-10 bg-primary text-on-primary rounded-lg font-body-sm font-semibold" @click="drawerOpen = false">Apply</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function stockPage() {
            return {
                searchQuery: '',
                filterCategory: '',
                drawerOpen: false,
                advancedFilters: { status: '', minQty: '', maxQty: '', location: '', lowStockOnly: false },
                stockItems: [
                    { id: 1, name: 'Industrial Servo Motor', sku: 'SRV-001', serialNo: 'SN-44821', category: 'Industrial Electronics', qty: 145, location: 'zone-a' },
                    { id: 2, name: 'Hydraulic Pump Assembly', sku: 'HYD-023', serialNo: 'SN-77234', category: 'Mechanical Parts', qty: 8, location: 'zone-b' },
                    { id: 3, name: 'Safety Relief Valve', sku: 'SRV-089', serialNo: 'SN-11098', category: 'Safety Equipment', qty: 0, location: 'zone-a' },
                    { id: 4, name: 'Control Board PCB-X4', sku: 'PCB-X4', serialNo: 'SN-55612', category: 'Industrial Electronics', qty: 67, location: 'zone-c' },
                    { id: 5, name: 'Steel Gear Module 4', sku: 'GRM-M4', serialNo: 'SN-99341', category: 'Mechanical Parts', qty: 3, location: 'zone-b' },
                    { id: 6, name: 'Emergency Stop Button', sku: 'EST-001', serialNo: 'SN-22190', category: 'Safety Equipment', qty: 234, location: 'zone-a' },
                ],
                get filteredStock() {
                    return this.stockItems.filter(item => {
                        const q = this.searchQuery.toLowerCase();
                        const matchSearch = !q || item.name.toLowerCase().includes(q) || item.sku.toLowerCase().includes(q) || item.serialNo.toLowerCase().includes(q);
                        const matchCat = !this.filterCategory || item.category.toLowerCase().includes(this.filterCategory);
                        const af = this.advancedFilters;
                        const matchStatus = !af.status || (af.status === 'in-stock' && item.qty > 10) || (af.status === 'low-stock' && item.qty > 0 && item.qty <= 10) || (af.status === 'out-of-stock' && item.qty === 0);
                        const matchMin = !af.minQty || item.qty >= Number(af.minQty);
                        const matchMax = !af.maxQty || item.qty <= Number(af.maxQty);
                        const matchLoc = !af.location || item.location === af.location;
                        const matchLow = !af.lowStockOnly || (item.qty > 0 && item.qty <= 10);
                        return matchSearch && matchCat && matchStatus && matchMin && matchMax && matchLoc && matchLow;
                    });
                },
                getQtyColor(qty) { return qty === 0 ? 'text-error' : qty <= 10 ? 'text-secondary' : 'text-on-surface'; },
                getStatusBadge(qty) { return qty === 0 ? 'bg-error-container text-on-error-container' : qty <= 10 ? 'bg-secondary-fixed text-on-secondary-fixed-variant' : 'bg-primary-fixed text-on-primary-fixed-variant'; },
                getStatusLabel(qty) { return qty === 0 ? 'Out of Stock' : qty <= 10 ? 'Low Stock' : 'In Stock'; },
                resetFilters() { this.advancedFilters = { status: '', minQty: '', maxQty: '', location: '', lowStockOnly: false }; },
            }
        }
    </script>
    @endpush
</x-layouts::app>

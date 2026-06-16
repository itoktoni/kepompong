<x-layouts::app title="Prepare Barang - WMS Portal">
    <div x-data="prepareBarangPage()">
        <style>[x-cloak] { display: none !important; }</style>

        <div class="mb-6 flex items-center gap-2 text-on-surface-variant font-body-sm">
            <span class="cursor-pointer hover:text-primary transition-colors">Operations</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="font-medium text-primary">Prepare Barang</span>
        </div>

        <div class="mb-8">
            <p class="font-label-caps text-label-caps text-secondary uppercase tracking-widest mb-1">Picking Operations</p>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Prepare Barang</h2>
            <p class="font-body-sm text-on-surface-variant mt-2">Search and select products to prepare for outbound operations.</p>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-6 form-card">
            <div class="flex gap-3 items-center">
                <div class="flex-1 relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                    <input x-model="searchQuery" type="text" class="w-full h-12 pl-10 pr-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="Search product name or SKU..." />
                </div>
                <button class="w-12 h-12 bg-primary-container/10 border border-outline-variant rounded-lg flex items-center justify-center hover:bg-primary-container/20 transition-colors">
                    <span class="material-symbols-outlined text-primary">qr_code_scanner</span>
                </button>
            </div>
        </div>

        <div x-show="selectedProduct" x-cloak class="bg-primary-container/5 border border-primary/20 rounded-xl p-5 mb-6 form-card">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="font-body-lg text-body-lg font-semibold text-on-surface" x-text="selectedProduct?.name"></p>
                    <span class="font-label-caps text-label-caps bg-primary-container/20 text-primary px-2 py-0.5 rounded-full" x-text="selectedProduct?.sku"></span>
                </div>
                <button @click="selectedProduct = null" class="p-1 hover:bg-surface-container rounded-full transition-colors">
                    <span class="material-symbols-outlined text-on-surface-variant">close</span>
                </button>
            </div>
            <div class="grid grid-cols-3 gap-3 pt-3 border-t border-primary/10">
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant">Total Qty</p>
                    <p class="font-data-mono text-data-mono text-on-surface font-bold" x-text="selectedProduct?.totalQty"></p>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant">Category</p>
                    <p class="font-body-sm text-body-sm text-on-surface" x-text="selectedProduct?.category"></p>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant">Location</p>
                    <p class="font-data-mono text-data-mono text-on-surface" x-text="selectedProduct?.location"></p>
                </div>
            </div>
        </div>

        <div x-show="selectedProduct" x-cloak class="space-y-4 mb-8">
            <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-xl">location_on</span>
                Rack Suggestions
                <span class="font-label-caps text-label-caps text-on-surface-variant ml-auto">Sorted by Qty</span>
            </h3>
            <template x-for="(rack, index) in rackSuggestions" :key="rack.id">
                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 form-card">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-primary-container/10 flex items-center justify-center">
                                <span class="font-data-mono text-data-mono text-primary font-bold" x-text="index + 1"></span>
                            </div>
                            <div>
                                <p class="font-data-mono text-data-mono text-on-surface font-bold" x-text="rack.rackId"></p>
                                <p class="font-label-caps text-label-caps text-on-surface-variant" x-text="'Level ' + rack.level"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="font-data-mono text-data-mono text-on-surface font-bold" x-text="rack.qty + ' pcs'"></p>
                                <p class="font-label-caps text-label-caps text-on-surface-variant">Available</p>
                            </div>
                            <button @click="pickFromRack(rack)" class="btn-wh-primary h-10 px-4 gap-1">
                                <span class="material-symbols-outlined text-lg">hand_package</span>
                                Pick
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="selectedProduct" x-cloak class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-xl">info</span>
                FEFO Operational Info
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-outline-variant/50">
                    <span class="font-label-caps text-label-caps text-on-surface-variant">Strategy</span>
                    <span class="font-data-mono text-data-mono text-on-surface">FEFO (First Expired, First Out)</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-outline-variant/50">
                    <span class="font-label-caps text-label-caps text-on-surface-variant">Nearest Expiry</span>
                    <span class="font-data-mono text-data-mono text-error">Jul 15, 2024</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="font-label-caps text-label-caps text-on-surface-variant">Furthest Expiry</span>
                    <span class="font-data-mono text-data-mono text-on-surface">Dec 30, 2024</span>
                </div>
            </div>
        </div>

        <div x-show="!selectedProduct" x-cloak class="flex flex-col items-center justify-center py-20 text-center">
            <span class="material-symbols-outlined text-6xl text-outline mb-4">search</span>
            <p class="font-body-lg text-body-lg text-on-surface-variant mb-2">Search for a product</p>
            <p class="font-body-sm text-body-sm text-outline">Use the search bar or scan a barcode to find a product to prepare.</p>
        </div>

        <template x-if="searchQuery && !selectedProduct">
            <div class="space-y-2 mt-4">
                <template x-for="product in searchResults" :key="product.id">
                    <button @click="selectProduct(product)" class="w-full bg-surface-container-lowest border border-outline-variant rounded-xl p-4 form-card hover:shadow-md transition-all text-left">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-body-sm font-semibold text-on-surface" x-text="product.name"></p>
                                <span class="font-label-caps text-label-caps bg-primary-container/20 text-primary px-2 py-0.5 rounded-full" x-text="product.sku"></span>
                            </div>
                            <span class="material-symbols-outlined text-on-surface-variant">chevron_right</span>
                        </div>
                    </button>
                </template>
            </div>
        </template>
    </div>

    @push('scripts')
    <script>
        function prepareBarangPage() {
            return {
                searchQuery: '',
                selectedProduct: null,
                rackSuggestions: [],
                allProducts: [
                    { id: 1, name: 'Industrial Servo Motor X200', sku: 'SKU-BRG-2024-X9', category: 'Electronics', totalQty: 145, location: 'Zone A' },
                    { id: 2, name: 'Hydraulic Pump Assembly', sku: 'SKU-HYD-023', category: 'Mechanical', totalQty: 80, location: 'Zone B' },
                    { id: 3, name: 'Control Board PCB-X4', sku: 'SKU-PCB-X4', category: 'Electronics', totalQty: 67, location: 'Zone C' },
                ],
                get searchResults() {
                    if (!this.searchQuery) return [];
                    const q = this.searchQuery.toLowerCase();
                    return this.allProducts.filter(p => p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));
                },
                selectProduct(product) {
                    this.selectedProduct = product;
                    this.searchQuery = '';
                    this.rackSuggestions = [
                        { id: 1, rackId: 'A1-04-B', level: 'L3', qty: 45 },
                        { id: 2, rackId: 'A2-01-A', level: 'L2', qty: 38 },
                        { id: 3, rackId: 'B1-07-C', level: 'L1', qty: 32 },
                        { id: 4, rackId: 'C3-02-B', level: 'L4', qty: 30 },
                    ];
                },
                pickFromRack(rack) {
                    alert('Picking ' + this.selectedProduct.name + ' from ' + rack.rackId);
                },
            }
        }
    </script>
    @endpush
</x-layouts::app>

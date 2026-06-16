<x-layouts::app title="Work Orders - WMS Portal">
    <div x-data="workOrdersPage()">
        <style>[x-cloak] { display: none !important; }</style>

        <div class="mb-6 flex items-center gap-2 text-on-surface-variant font-body-sm">
            <span class="cursor-pointer hover:text-primary transition-colors">Operations</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="font-medium text-primary">Work Orders</span>
        </div>

        <div class="mb-8">
            <p class="font-label-caps text-label-caps text-secondary uppercase tracking-widest mb-1">Production Support</p>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Work Orders</h2>
            <p class="font-body-sm text-on-surface-variant mt-2">Browse and manage products associated with production work orders.</p>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-6 form-card">
            <div class="flex flex-col sm:flex-row gap-4 sm:items-center">
                <div class="flex-1 relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                    <input x-model="searchQuery" type="text" class="w-full h-12 pl-10 pr-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="Search by product name, SKU, or work order..." />
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mb-6">
            <template x-for="cat in categories" :key="cat.value">
                <button @click="activeCategory = cat.value" :class="activeCategory === cat.value ? 'bg-primary text-on-primary' : 'bg-surface-container-lowest text-on-surface-variant border border-outline-variant hover:bg-surface-container'" class="px-4 py-2 rounded-full font-body-sm transition-all" x-text="cat.label"></button>
            </template>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="product in filteredProducts" :key="product.id">
                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 form-card hover:shadow-md transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-body-lg text-body-lg font-semibold text-on-surface" x-text="product.name"></p>
                            <p class="font-data-mono text-data-mono text-on-surface-variant" x-text="product.id"></p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <span class="font-label-caps text-label-caps bg-primary-container/20 text-primary px-2 py-0.5 rounded-full" x-text="product.sku"></span>
                            <span class="font-label-caps text-label-caps bg-secondary-container/20 text-secondary px-2 py-0.5 rounded-full" x-text="product.category"></span>
                        </div>
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mb-4" x-text="product.description"></p>
                    <div class="flex items-center justify-between pt-3 border-t border-outline-variant">
                        <span class="font-data-mono text-data-mono text-on-surface font-bold" x-text="'Rp ' + product.price.toLocaleString()"></span>
                        <div class="flex items-center gap-2">
                            <div class="h-8 flex items-end gap-px">
                                <template x-for="(bar, i) in product.barcode" :key="i">
                                    <div class="bg-on-surface" :style="'width:' + bar.w + 'px; height:' + bar.h + 'px;'"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    @push('scripts')
    <script>
        function workOrdersPage() {
            return {
                searchQuery: '',
                activeCategory: 'all',
                categories: [
                    { label: 'All Products', value: 'all' },
                    { label: 'Electronics', value: 'electronics' },
                    { label: 'Heavy Machinery', value: 'machinery' },
                    { label: 'Perishables', value: 'perishables' },
                    { label: 'Raw Materials', value: 'raw' },
                ],
                products: [
                    {
                        id: 'WO-PRD-001',
                        name: 'Copper Wire 2.5mm Roll',
                        sku: 'SKU-CW-25',
                        category: 'Raw Materials',
                        catValue: 'raw',
                        description: 'High-purity copper wire for electrical assembly. 100m per roll, 2.5mm gauge.',
                        price: 850000,
                        barcode: [
                            { w: 2, h: 32 }, { w: 1, h: 32 }, { w: 3, h: 32 }, { w: 1, h: 32 }, { w: 2, h: 32 },
                            { w: 1, h: 32 }, { w: 3, h: 32 }, { w: 2, h: 32 }, { w: 1, h: 32 }, { w: 2, h: 32 },
                            { w: 3, h: 32 }, { w: 1, h: 32 }, { w: 2, h: 32 }, { w: 1, h: 32 }, { w: 3, h: 32 },
                            { w: 2, h: 32 }, { w: 1, h: 32 }, { w: 2, h: 32 }, { w: 1, h: 32 }, { w: 3, h: 32 },
                        ],
                    },
                    {
                        id: 'WO-PRD-002',
                        name: 'Power Supply Unit 500W',
                        sku: 'SKU-PSU-500',
                        category: 'Electronics',
                        catValue: 'electronics',
                        description: 'Industrial-grade 500W PSU for production line equipment. 24V DC output.',
                        price: 2750000,
                        barcode: [
                            { w: 1, h: 32 }, { w: 3, h: 32 }, { w: 1, h: 32 }, { w: 2, h: 32 }, { w: 3, h: 32 },
                            { w: 2, h: 32 }, { w: 1, h: 32 }, { w: 3, h: 32 }, { w: 1, h: 32 }, { w: 2, h: 32 },
                            { w: 1, h: 32 }, { w: 3, h: 32 }, { w: 2, h: 32 }, { w: 1, h: 32 }, { w: 2, h: 32 },
                            { w: 3, h: 32 }, { w: 1, h: 32 }, { w: 2, h: 32 }, { w: 3, h: 32 }, { w: 1, h: 32 },
                        ],
                    },
                    {
                        id: 'WO-PRD-003',
                        name: 'Pneumatic Cylinder 50mm',
                        sku: 'SKU-PNC-50',
                        category: 'Heavy Machinery',
                        catValue: 'machinery',
                        description: 'Double-acting pneumatic cylinder. 50mm bore, 200mm stroke. For assembly jigs.',
                        price: 1200000,
                        barcode: [
                            { w: 3, h: 32 }, { w: 1, h: 32 }, { w: 2, h: 32 }, { w: 1, h: 32 }, { w: 3, h: 32 },
                            { w: 1, h: 32 }, { w: 2, h: 32 }, { w: 3, h: 32 }, { w: 1, h: 32 }, { w: 2, h: 32 },
                            { w: 3, h: 32 }, { w: 2, h: 32 }, { w: 1, h: 32 }, { w: 3, h: 32 }, { w: 2, h: 32 },
                            { w: 1, h: 32 }, { w: 3, h: 32 }, { w: 2, h: 32 }, { w: 1, h: 32 }, { w: 2, h: 32 },
                        ],
                    },
                ],
                get filteredProducts() {
                    const q = this.searchQuery.toLowerCase();
                    return this.products.filter(p => {
                        const matchSearch = !q || p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q) || p.id.toLowerCase().includes(q);
                        const matchCat = this.activeCategory === 'all' || p.catValue === this.activeCategory;
                        return matchSearch && matchCat;
                    });
                },
            }
        }
    </script>
    @endpush
</x-layouts::app>

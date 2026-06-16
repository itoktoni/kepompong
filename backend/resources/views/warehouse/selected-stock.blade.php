<x-layouts::app title="Stock Detail - WMS Portal">
    <div x-data="selectedStockPage()">
        <style>[x-cloak] { display: none !important; }</style>

        <div class="mb-6 flex items-center gap-2 text-on-surface-variant font-body-sm">
            <span class="cursor-pointer hover:text-primary transition-colors">Inventory</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="cursor-pointer hover:text-primary transition-colors">Stock Management</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="font-medium text-primary">Stock Detail</span>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-6 form-card">
            <div class="flex gap-3 items-center">
                <div class="flex-1 relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                    <input x-model="searchQuery" type="text" class="w-full h-12 pl-10 pr-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="Search stock items..." />
                </div>
            </div>
        </div>

        <div class="bg-primary-container/5 border border-primary/20 rounded-xl p-6 mb-6 form-card">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="font-body-lg text-body-lg font-semibold text-on-surface">Arctic Chill — Organic Blueberry</p>
                    <span class="font-label-caps text-label-caps bg-primary-container/20 text-primary px-2 py-0.5 rounded-full">SKU-BLU-ORG-2024</span>
                </div>
                <span class="material-symbols-outlined text-primary text-3xl">inventory_2</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-primary/10">
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant">Real Stock</p>
                    <p class="font-data-mono text-data-mono text-on-surface text-xl font-bold">1,240</p>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant">Reserved (Sales)</p>
                    <p class="font-data-mono text-data-mono text-secondary text-xl font-bold">142</p>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant">Available</p>
                    <p class="font-data-mono text-data-mono text-green-600 text-xl font-bold">1,098</p>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant">Unit</p>
                    <p class="font-data-mono text-data-mono text-on-surface text-xl font-bold">pcs</p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-xl">location_on</span>
                Storage Locations
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <template x-for="loc in storageLocations" :key="loc.id">
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 form-card">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-data-mono text-data-mono text-on-surface font-bold" x-text="loc.rackId"></p>
                            <span class="font-label-caps text-label-caps bg-surface-container px-2 py-0.5 rounded-full text-on-surface-variant" x-text="'Level ' + loc.level"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="font-label-caps text-label-caps text-on-surface-variant">Qty</span>
                            <span class="font-data-mono text-data-mono text-on-surface" x-text="loc.qty + ' pcs'"></span>
                        </div>
                        <div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden mt-2">
                            <div class="h-full bg-primary rounded-full" :style="'width: ' + (loc.qty / loc.capacity * 100) + '%'"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden form-card">
            <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between">
                <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-on-surface-variant text-xl">table_chart</span>
                    Inventory List
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-surface-container border-b border-outline-variant">
                            <th class="text-left px-5 py-3 font-label-caps text-label-caps text-on-surface-variant">SKU</th>
                            <th class="text-left px-5 py-3 font-label-caps text-label-caps text-on-surface-variant">Batch</th>
                            <th class="text-left px-5 py-3 font-label-caps text-label-caps text-on-surface-variant">Rack</th>
                            <th class="text-center px-5 py-3 font-label-caps text-label-caps text-on-surface-variant">Qty</th>
                            <th class="text-left px-5 py-3 font-label-caps text-label-caps text-on-surface-variant">Expiry</th>
                            <th class="text-left px-5 py-3 font-label-caps text-label-caps text-on-surface-variant">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in inventoryList" :key="item.id">
                            <tr class="border-b border-outline-variant/30 hover:bg-surface-container-low/50 transition-colors">
                                <td class="px-5 py-4 font-data-mono text-data-mono text-on-surface" x-text="item.sku"></td>
                                <td class="px-5 py-4 font-data-mono text-data-mono text-on-surface-variant" x-text="item.batch"></td>
                                <td class="px-5 py-4 font-data-mono text-data-mono text-on-surface" x-text="item.rack"></td>
                                <td class="px-5 py-4 text-center font-data-mono text-data-mono text-on-surface" x-text="item.qty"></td>
                                <td class="px-5 py-4 font-data-mono text-data-mono text-on-surface-variant" x-text="item.expiry"></td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] uppercase tracking-wider font-semibold" :class="item.qty > 0 ? 'bg-green-50 text-green-700' : 'bg-error-container text-on-error-container'" x-text="item.qty > 0 ? 'In Stock' : 'Depleted'"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function selectedStockPage() {
            return {
                searchQuery: '',
                storageLocations: [
                    { id: 1, rackId: 'A1-04-B', level: 'L3', qty: 320, capacity: 500 },
                    { id: 2, rackId: 'A2-01-A', level: 'L2', qty: 280, capacity: 400 },
                    { id: 3, rackId: 'B1-07-C', level: 'L1', qty: 240, capacity: 350 },
                    { id: 4, rackId: 'B3-02-B', level: 'L4', qty: 200, capacity: 300 },
                    { id: 5, rackId: 'C1-05-A', level: 'L2', qty: 200, capacity: 250 },
                ],
                inventoryList: [
                    { id: 1, sku: 'SKU-BLU-ORG-2024', batch: 'BAT-2024-F06', rack: 'A1-04-B', qty: 320, expiry: 'Jul 15, 2024' },
                    { id: 2, sku: 'SKU-BLU-ORG-2024', batch: 'BAT-2024-F07', rack: 'A2-01-A', qty: 280, expiry: 'Aug 20, 2024' },
                    { id: 3, sku: 'SKU-BLU-ORG-2024', batch: 'BAT-2024-F08', rack: 'B1-07-C', qty: 240, expiry: 'Sep 10, 2024' },
                    { id: 4, sku: 'SKU-BLU-ORG-2024', batch: 'BAT-2024-F09', rack: 'B3-02-B', qty: 200, expiry: 'Oct 05, 2024' },
                    { id: 5, sku: 'SKU-BLU-ORG-2024', batch: 'BAT-2024-F10', rack: 'C1-05-A', qty: 200, expiry: 'Nov 30, 2024' },
                ],
            }
        }
    </script>
    @endpush
</x-layouts::app>

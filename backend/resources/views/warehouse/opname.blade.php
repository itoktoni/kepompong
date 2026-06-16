<x-layouts::app title="Stock Opname - WMS Portal">
    <div x-data="opnamePage()">
        <style>[x-cloak] { display: none !important; }</style>

        <div class="mb-6 flex items-center gap-2 text-on-surface-variant font-body-sm">
            <span class="cursor-pointer hover:text-primary transition-colors">Inventory</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="font-medium text-primary">Stock Opname</span>
        </div>

        <div class="mb-8">
            <p class="font-label-caps text-label-caps text-secondary uppercase tracking-widest mb-1">Inventory Audit</p>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Stock Opname</h2>
            <p class="font-body-sm text-on-surface-variant mt-2">Perform physical inventory counts and reconcile with system records.</p>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant uppercase">Current Progress</p>
                    <p class="font-headline-md text-headline-md text-on-surface">Rack A1-04-B</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-data-mono text-data-mono text-on-surface text-2xl font-bold" x-text="progress.current + '/' + progress.total"></span>
                    <span class="font-label-caps text-label-caps text-on-surface-variant">items counted</span>
                </div>
            </div>
            <div class="w-full h-3 bg-surface-container rounded-full overflow-hidden">
                <div class="h-full bg-primary rounded-full transition-all" :style="'width: ' + (progress.current / progress.total * 100) + '%'"></div>
            </div>
            <p class="font-label-caps text-label-caps text-secondary mt-2" x-text="Math.round(progress.current / progress.total * 100) + '% Complete'"></p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
                    </div>
                    <span class="font-label-caps text-label-caps bg-green-50 text-green-700 px-2 py-1 rounded-full">Matched</span>
                </div>
                <p class="font-headline-lg text-headline-lg text-on-surface">12</p>
                <p class="font-label-caps text-label-caps text-on-surface-variant mt-1">Items Matched</p>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-secondary text-2xl">pending_actions</span>
                    </div>
                    <span class="font-label-caps text-label-caps bg-secondary-container/20 text-secondary px-2 py-1 rounded-full">Pending</span>
                </div>
                <p class="font-headline-lg text-headline-lg text-on-surface">8</p>
                <p class="font-label-caps text-label-caps text-on-surface-variant mt-1">Items Pending</p>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden form-card">
            <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between">
                <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-on-surface-variant text-xl">fact_check</span>
                    Audit List
                </h3>
                <span class="font-label-caps text-label-caps bg-surface-container px-2 py-1 rounded-full text-on-surface-variant" x-text="auditItems.length + ' items'"></span>
            </div>
            <div class="divide-y divide-outline-variant/30">
                <template x-for="item in auditItems" :key="item.id">
                    <div class="flex items-center gap-4 px-5 py-4 hover:bg-surface-container-low/50 transition-colors">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="item.status === 'Matched' ? 'bg-green-50' : 'bg-secondary-container/10'">
                            <span class="material-symbols-outlined text-xl" :class="item.status === 'Matched' ? 'text-green-600' : 'text-secondary'" x-text="item.status === 'Matched' ? 'check_circle' : 'schedule'"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-body-sm font-semibold text-on-surface" x-text="item.name"></p>
                            <p class="font-data-mono text-data-mono text-on-surface-variant" x-text="item.sku"></p>
                        </div>
                        <div class="text-center shrink-0">
                            <p class="font-label-caps text-label-caps text-on-surface-variant">System</p>
                            <p class="font-data-mono text-data-mono text-on-surface" x-text="item.systemQty"></p>
                        </div>
                        <div class="text-center shrink-0">
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Physical</p>
                            <p class="font-data-mono text-data-mono text-on-surface" x-text="item.physicalQty ?? '—'"></p>
                        </div>
                        <span class="font-label-caps text-label-caps px-2 py-0.5 rounded-full shrink-0" :class="item.status === 'Matched' ? 'bg-green-50 text-green-700' : 'bg-secondary-container text-on-secondary-container'" x-text="item.status"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function opnamePage() {
            return {
                progress: { current: 12, total: 20 },
                auditItems: [
                    { id: 1, name: 'Industrial Servo Motor X200', sku: 'SKU-SRV-X200', systemQty: 145, physicalQty: 145, status: 'Matched' },
                    { id: 2, name: 'Hydraulic Pump Assembly', sku: 'SKU-HYD-023', systemQty: 8, physicalQty: 8, status: 'Matched' },
                    { id: 3, name: 'Control Board PCB-X4', sku: 'SKU-PCB-X4', systemQty: 67, physicalQty: 67, status: 'Matched' },
                    { id: 4, name: 'Emergency Stop Button', sku: 'SKU-EST-001', systemQty: 234, physicalQty: 234, status: 'Matched' },
                    { id: 5, name: 'Steel Gear Module 4', sku: 'SKU-GRM-M4', systemQty: 3, physicalQty: 3, status: 'Matched' },
                    { id: 6, name: 'Safety Relief Valve', sku: 'SKU-SRV-089', systemQty: 15, physicalQty: 15, status: 'Matched' },
                    { id: 7, name: 'Ball Bearing 6205ZZ', sku: 'SKU-BB-6205', systemQty: 500, physicalQty: 500, status: 'Matched' },
                    { id: 8, name: 'Conveyor Belt Roll 2M', sku: 'SKU-CBR-2M', systemQty: 22, physicalQty: 22, status: 'Matched' },
                    { id: 9, name: 'Pneumatic Cylinder 50mm', sku: 'SKU-PNC-50', systemQty: 40, physicalQty: 40, status: 'Matched' },
                    { id: 10, name: 'Thermal Fuse TF-240', sku: 'SKU-TF-240', systemQty: 180, physicalQty: 180, status: 'Matched' },
                    { id: 11, name: 'LED Panel Light 60W', sku: 'SKU-LED-60', systemQty: 95, physicalQty: 95, status: 'Matched' },
                    { id: 12, name: 'Stainless Steel Bolt M10', sku: 'SKU-SSB-M10', systemQty: 2000, physicalQty: 2000, status: 'Matched' },
                    { id: 13, name: 'Rubber Gasket Set', sku: 'SKU-RGS-01', systemQty: 75, physicalQty: null, status: 'Pending' },
                    { id: 14, name: 'Power Supply Unit 500W', sku: 'SKU-PSU-500', systemQty: 30, physicalQty: null, status: 'Pending' },
                    { id: 15, name: 'Aluminum Heat Sink', sku: 'SKU-AHS-01', systemQty: 120, physicalQty: null, status: 'Pending' },
                    { id: 16, name: 'Carbon Brush Set', sku: 'SKU-CBS-01', systemQty: 60, physicalQty: null, status: 'Pending' },
                    { id: 17, name: 'Timing Belt 5M', sku: 'SKU-TB-5M', systemQty: 45, physicalQty: null, status: 'Pending' },
                    { id: 18, name: 'Copper Wire 2.5mm', sku: 'SKU-CW-25', systemQty: 300, physicalQty: null, status: 'Pending' },
                    { id: 19, name: 'Hydraulic Oil Filter', sku: 'SKU-HOF-01', systemQty: 55, physicalQty: null, status: 'Pending' },
                    { id: 20, name: 'Proximity Sensor M18', sku: 'SKU-PS-M18', systemQty: 85, physicalQty: null, status: 'Pending' },
                ],
            }
        }
    </script>
    @endpush
</x-layouts::app>

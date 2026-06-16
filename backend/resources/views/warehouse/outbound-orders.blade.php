<x-layouts::app title="Outbound Orders - WMS Portal">
    <div x-data="outboundOrdersPage()">
        <style>[x-cloak] { display: none !important; }</style>

        <div class="mb-6 flex items-center gap-2 text-on-surface-variant font-body-sm">
            <span class="cursor-pointer hover:text-primary transition-colors">Operations</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="font-medium text-primary">Outbound Orders</span>
        </div>

        <div class="mb-8">
            <p class="font-label-caps text-label-caps text-secondary uppercase tracking-widest mb-1">Order Fulfillment</p>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Outbound Orders</h2>
            <p class="font-body-sm text-on-surface-variant mt-2">Manage sales orders, work orders, and outbound shipment consolidation.</p>
        </div>

        <div class="flex gap-1 bg-surface-container rounded-xl p-1 mb-8">
            <button @click="activeTab = 'sales'" :class="activeTab === 'sales' ? 'bg-surface-container-lowest shadow-sm text-on-surface' : 'text-on-surface-variant hover:text-on-surface'" class="flex-1 px-4 py-2.5 rounded-lg font-body-sm transition-all text-center">Sales Orders</button>
            <button @click="activeTab = 'work'" :class="activeTab === 'work' ? 'bg-surface-container-lowest shadow-sm text-on-surface' : 'text-on-surface-variant hover:text-on-surface'" class="flex-1 px-4 py-2.5 rounded-lg font-body-sm transition-all text-center">Work Orders</button>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-xl">inventory</span>
                    Consolidation Summary
                </h3>
                <span class="font-data-mono text-data-mono text-primary font-bold" x-text="consolidation.packed + '/' + consolidation.total"></span>
            </div>
            <div class="w-full h-2.5 bg-surface-container rounded-full overflow-hidden mb-2">
                <div class="h-full bg-primary rounded-full transition-all" :style="'width: ' + (consolidation.packed / consolidation.total * 100) + '%'"></div>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-label-caps text-label-caps text-on-surface-variant" x-text="Math.round(consolidation.packed / consolidation.total * 100) + '% Consolidated'"></span>
                <span class="font-label-caps text-label-caps text-secondary" x-text="(consolidation.total - consolidation.packed) + ' remaining'"></span>
            </div>
        </div>

        <div x-show="activeTab === 'sales'" x-cloak class="space-y-4">
            <template x-for="order in salesOrders" :key="order.id">
                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 form-card hover:shadow-md transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="font-body-sm font-semibold text-on-surface" x-text="order.orderNo"></p>
                            <p class="font-label-caps text-label-caps text-on-surface-variant" x-text="order.customer"></p>
                        </div>
                        <span class="font-label-caps text-label-caps px-3 py-1 rounded-full font-bold" :class="getOrderStatusBadge(order.status)" x-text="order.status"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 pt-3 border-t border-outline-variant">
                        <div>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Items</p>
                            <p class="font-data-mono text-data-mono text-on-surface" x-text="order.items"></p>
                        </div>
                        <div>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Priority</p>
                            <p class="font-body-sm text-body-sm" :class="order.priority === 'High' ? 'text-error' : 'text-on-surface'" x-text="order.priority"></p>
                        </div>
                        <div class="text-right">
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Ship By</p>
                            <p class="font-data-mono text-data-mono text-on-surface" x-text="order.shipBy"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="activeTab === 'work'" x-cloak>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="font-body-lg text-body-lg font-semibold text-on-surface">WO-2024-0187</p>
                        <p class="font-label-caps text-label-caps text-on-surface-variant">Production Line B — Assembly Required</p>
                    </div>
                    <span class="font-label-caps text-label-caps bg-primary-container/20 text-primary px-3 py-1 rounded-full font-bold">In Progress</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-outline-variant">
                    <div>
                        <p class="font-label-caps text-label-caps text-on-surface-variant">Total Items</p>
                        <p class="font-data-mono text-data-mono text-on-surface font-bold">24</p>
                    </div>
                    <div>
                        <p class="font-label-caps text-label-caps text-on-surface-variant">Picked</p>
                        <p class="font-data-mono text-data-mono text-green-600 font-bold">18</p>
                    </div>
                    <div>
                        <p class="font-label-caps text-label-caps text-on-surface-variant">Remaining</p>
                        <p class="font-data-mono text-data-mono text-secondary font-bold">6</p>
                    </div>
                    <div>
                        <p class="font-label-caps text-label-caps text-on-surface-variant">Due Date</p>
                        <p class="font-data-mono text-data-mono text-on-surface font-bold">Jun 15</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-outline-variant">
                    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">Notes</p>
                    <p class="font-body-sm text-body-sm text-on-surface">All components must be delivered to Assembly Bay 3. Priority: components SKU-401 and SKU-402 first.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function outboundOrdersPage() {
            return {
                activeTab: 'sales',
                consolidation: { packed: 18, total: 25 },
                salesOrders: [
                    { id: 1, orderNo: 'SO-2024-1201', customer: 'PT Maju Bersama', items: 8, status: 'Processing', priority: 'High', shipBy: 'Jun 12' },
                    { id: 2, orderNo: 'SO-2024-1200', customer: 'CV Teknik Jaya', items: 3, status: 'Ready', priority: 'Normal', shipBy: 'Jun 13' },
                    { id: 3, orderNo: 'SO-2024-1199', customer: 'PT Sinar Elektrik', items: 12, status: 'Shipped', priority: 'High', shipBy: 'Jun 10' },
                    { id: 4, orderNo: 'SO-2024-1198', customer: 'UD Makmur Sentosa', items: 5, status: 'Processing', priority: 'Normal', shipBy: 'Jun 14' },
                    { id: 5, orderNo: 'SO-2024-1197', customer: 'PT Baja Kencana', items: 15, status: 'Ready', priority: 'Low', shipBy: 'Jun 15' },
                ],
                getOrderStatusBadge(status) {
                    const map = {
                        'Processing': 'bg-secondary-container text-on-secondary-container',
                        'Ready': 'bg-primary-container text-on-primary-container',
                        'Shipped': 'bg-green-50 text-green-700',
                    };
                    return map[status] || 'bg-surface-container text-on-surface-variant';
                },
            }
        }
    </script>
    @endpush
</x-layouts::app>

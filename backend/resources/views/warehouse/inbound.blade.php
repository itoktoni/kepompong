<x-layouts::app title="Inbound Management - WMS Portal">
    <div x-data="inboundPage()">
        <style>[x-cloak] { display: none !important; }</style>

        <div class="mb-6 flex items-center gap-2 text-on-surface-variant font-body-sm">
            <span class="cursor-pointer hover:text-primary transition-colors">Operations</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="font-medium text-primary">Inbound Management</span>
        </div>

        <div class="mb-8">
            <p class="font-label-caps text-label-caps text-secondary uppercase tracking-widest mb-1">Receiving Operations</p>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Inbound Management</h2>
            <p class="font-body-sm text-on-surface-variant mt-2">Track and manage incoming shipments, purchase orders, and receiving operations.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-secondary text-2xl">pending_actions</span>
                    </div>
                    <span class="font-label-caps text-label-caps bg-secondary-container/20 text-secondary px-2 py-1 rounded-full">Pending</span>
                </div>
                <p class="font-headline-lg text-headline-lg text-on-surface">12</p>
                <p class="font-label-caps text-label-caps text-on-surface-variant mt-1">Pending Orders</p>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-container/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-2xl">local_shipping</span>
                    </div>
                    <span class="font-label-caps text-label-caps bg-primary-container/20 text-primary px-2 py-1 rounded-full">In Transit</span>
                </div>
                <p class="font-headline-lg text-headline-lg text-on-surface">8</p>
                <p class="font-label-caps text-label-caps text-on-surface-variant mt-1">In Transit</p>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
                    </div>
                    <span class="font-label-caps text-label-caps bg-green-50 text-green-700 px-2 py-1 rounded-full">Received</span>
                </div>
                <p class="font-headline-lg text-headline-lg text-on-surface">5</p>
                <p class="font-label-caps text-label-caps text-on-surface-variant mt-1">Received Today</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-6 form-card">
                    <div class="flex-1">
                        <input x-model="searchQuery" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="Search by PO number, supplier, or status..." type="text" />
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-on-surface-variant text-xl">list_alt</span>
                        Recent Inbound
                    </h3>
                    <template x-for="item in filteredInbound" :key="item.id">
                        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 form-card hover:shadow-md transition-all">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="font-body-sm font-semibold text-on-surface" x-text="item.poNumber"></p>
                                    <p class="font-label-caps text-label-caps text-on-surface-variant" x-text="item.supplier"></p>
                                </div>
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] uppercase tracking-wider font-bold" :class="getStatusBadge(item.status)" x-text="item.status"></span>
                            </div>
                            <div class="grid grid-cols-3 gap-3 pt-3 border-t border-outline-variant">
                                <div>
                                    <p class="font-label-caps text-label-caps text-on-surface-variant">Destination</p>
                                    <p class="font-data-mono text-data-mono text-on-surface" x-text="item.warehouse"></p>
                                </div>
                                <div>
                                    <p class="font-label-caps text-label-caps text-on-surface-variant">Qty</p>
                                    <p class="font-data-mono text-data-mono text-on-surface" x-text="item.qty"></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-label-caps text-label-caps text-on-surface-variant">ETA</p>
                                    <p class="font-data-mono text-data-mono text-on-surface" x-text="item.eta"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div>
                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card sticky top-4">
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">add_circle</span>
                        New Inbound
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">PO Number</label>
                            <input x-model="form.poNumber" type="text" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="e.g. PO-2024-001" />
                        </div>
                        <div>
                            <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Supplier</label>
                            <input x-model="form.supplier" type="text" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="Supplier name" />
                        </div>
                        <div>
                            <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Destination Warehouse</label>
                            <select x-model="form.warehouse" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg font-body-sm">
                                <option value="">Select Warehouse</option>
                                <option value="WH-001">WH-001 - Main Storage</option>
                                <option value="WH-002">WH-002 - Cold Storage</option>
                                <option value="WH-003">WH-003 - Hazmat Zone</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Expected Quantity</label>
                            <input x-model="form.qty" type="number" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="0" />
                        </div>
                        <div>
                            <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Notes</label>
                            <textarea x-model="form.notes" rows="3" class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm resize-none" placeholder="Additional notes..."></textarea>
                        </div>
                        <button @click="submitInbound()" class="btn-wh-primary w-full h-12 gap-2">
                            <span class="material-symbols-outlined text-xl">add</span>
                            Create Inbound Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function inboundPage() {
            return {
                searchQuery: '',
                form: { poNumber: '', supplier: '', warehouse: '', qty: '', notes: '' },
                inboundList: [
                    { id: 1, poNumber: 'PO-2024-0891', supplier: 'Acme Industrial Supplies', warehouse: 'WH-001', qty: 500, status: 'Pending', eta: 'Jun 12' },
                    { id: 2, poNumber: 'PO-2024-0890', supplier: 'GlobalTech Components', warehouse: 'WH-002', qty: 200, status: 'In Transit', eta: 'Jun 11' },
                    { id: 3, poNumber: 'PO-2024-0889', supplier: 'SteelWorks Ltd', warehouse: 'WH-001', qty: 150, status: 'Received', eta: 'Jun 10' },
                    { id: 4, poNumber: 'PO-2024-0888', supplier: 'SafetyFirst Corp', warehouse: 'WH-003', qty: 80, status: 'Pending', eta: 'Jun 14' },
                    { id: 5, poNumber: 'PO-2024-0887', supplier: 'MegaParts GmbH', warehouse: 'WH-001', qty: 320, status: 'In Transit', eta: 'Jun 11' },
                    { id: 6, poNumber: 'PO-2024-0886', supplier: 'Precision Tools Inc', warehouse: 'WH-002', qty: 95, status: 'Received', eta: 'Jun 10' },
                ],
                get filteredInbound() {
                    const q = this.searchQuery.toLowerCase();
                    return this.inboundList.filter(item => {
                        return !q || item.poNumber.toLowerCase().includes(q) || item.supplier.toLowerCase().includes(q) || item.status.toLowerCase().includes(q);
                    });
                },
                getStatusBadge(status) {
                    const map = {
                        'Pending': 'bg-secondary-container text-on-secondary-container',
                        'In Transit': 'bg-primary-container text-on-primary-container',
                        'Received': 'bg-green-50 text-green-700',
                    };
                    return map[status] || 'bg-surface-container text-on-surface-variant';
                },
                submitInbound() {
                    if (!this.form.poNumber || !this.form.supplier) {
                        alert('PO Number and Supplier are required.');
                        return;
                    }
                    alert('Inbound order created successfully!');
                },
            }
        }
    </script>
    @endpush
</x-layouts::app>

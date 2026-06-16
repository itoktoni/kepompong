<x-layouts::app title="Forklift Tasks - WMS Portal">
    <div x-data="forkliftPage()">
        <style>[x-cloak] { display: none !important; }</style>

        <div class="mb-6 flex items-center gap-2 text-on-surface-variant font-body-sm">
            <span class="cursor-pointer hover:text-primary transition-colors">Operations</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="font-medium text-primary">Forklift Tasks</span>
        </div>

        <div class="mb-8">
            <p class="font-label-caps text-label-caps text-secondary uppercase tracking-widest mb-1">Fleet Operations</p>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Forklift Tasks</h2>
            <p class="font-body-sm text-on-surface-variant mt-2">Manage forklift assignments, storage placement, and priority tasks.</p>
        </div>

        <div class="space-y-3 mb-8">
            <h3 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-error text-xl">priority_high</span>
                Task Queue
            </h3>
            <template x-for="task in tasks" :key="task.id">
                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 form-card" :class="task.urgency === 'URGENT' ? 'border-l-4 border-l-error' : ''">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="getUrgencyBg(task.urgency)">
                                <span class="material-symbols-outlined text-xl" :class="getUrgencyColor(task.urgency)" x-text="getUrgencyIcon(task.urgency)"></span>
                            </div>
                            <div>
                                <p class="font-body-sm font-semibold text-on-surface" x-text="task.sku"></p>
                                <p class="font-label-caps text-label-caps text-on-surface-variant" x-text="task.description"></p>
                            </div>
                        </div>
                        <span class="font-label-caps text-label-caps px-3 py-1 rounded-full font-bold" :class="getUrgencyBadge(task.urgency)" x-text="task.urgency"></span>
                    </div>
                </div>
            </template>
        </div>

        <div class="mb-8">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">grid_view</span>
                Available Storage Racks
            </h3>
            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2">
                <template x-for="rack in racks" :key="rack.id">
                    <div @click="selectedRack = rack.id" :class="selectedRack === rack.id ? 'bg-primary text-on-primary border-primary' : rack.available ? 'bg-surface-container-lowest text-on-surface-variant border-outline-variant hover:bg-surface-container cursor-pointer' : 'bg-surface-container text-outline border-outline-variant opacity-50 cursor-not-allowed'" class="border rounded-lg p-3 text-center transition-all">
                        <span class="material-symbols-outlined text-lg block mb-1" x-text="rack.available ? 'inventory_2' : 'block'"></span>
                        <p class="font-data-mono text-data-mono text-xs" x-text="rack.id"></p>
                    </div>
                </template>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-primary-container/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-2xl">qr_code_scanner</span>
                </div>
                <div class="flex-1">
                    <p class="font-body-lg text-body-lg font-semibold text-on-surface">Quick Scan</p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Scan item barcode to begin forklift assignment workflow.</p>
                </div>
                <button class="btn-wh-primary h-12 px-6 gap-2">
                    <span class="material-symbols-outlined text-xl">camera_alt</span>
                    Scan
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function forkliftPage() {
            return {
                selectedRack: null,
                tasks: [
                    { id: 1, sku: 'SKU-102', urgency: 'URGENT', description: 'Hydraulic Assembly — Dock 3 → Rack B2-05' },
                    { id: 2, sku: 'SKU-405', urgency: 'NORMAL', description: 'Control Board PCB — Zone A → Rack C1-02' },
                    { id: 3, sku: 'SKU-089', urgency: 'LOW', description: 'Safety Valve Set — Staging → Rack A3-07' },
                ],
                racks: [
                    { id: 'A1-01', available: true },
                    { id: 'A1-02', available: true },
                    { id: 'A1-03', available: false },
                    { id: 'A1-04', available: true },
                    { id: 'A2-01', available: true },
                    { id: 'A2-02', available: false },
                    { id: 'A2-03', available: true },
                    { id: 'A2-04', available: true },
                    { id: 'B1-01', available: true },
                    { id: 'B1-02', available: true },
                    { id: 'B1-03', available: true },
                    { id: 'B1-04', available: false },
                    { id: 'B2-01', available: false },
                    { id: 'B2-02', available: true },
                    { id: 'B2-03', available: true },
                    { id: 'B2-04', available: true },
                    { id: 'C1-01', available: true },
                    { id: 'C1-02', available: false },
                    { id: 'C1-03', available: true },
                    { id: 'C1-04', available: true },
                    { id: 'C2-01', available: true },
                    { id: 'C2-02', available: true },
                    { id: 'C2-03', available: false },
                    { id: 'C2-04', available: true },
                ],
                getUrgencyBg(urgency) {
                    const map = { 'URGENT': 'bg-error-container/10', 'NORMAL': 'bg-primary-container/10', 'LOW': 'bg-surface-container' };
                    return map[urgency] || 'bg-surface-container';
                },
                getUrgencyColor(urgency) {
                    const map = { 'URGENT': 'text-error', 'NORMAL': 'text-primary', 'LOW': 'text-on-surface-variant' };
                    return map[urgency] || 'text-on-surface-variant';
                },
                getUrgencyIcon(urgency) {
                    const map = { 'URGENT': 'priority_high', 'NORMAL': 'local_shipping', 'LOW': 'low_priority' };
                    return map[urgency] || 'local_shipping';
                },
                getUrgencyBadge(urgency) {
                    const map = { 'URGENT': 'bg-error-container text-on-error-container', 'NORMAL': 'bg-primary-container text-on-primary-container', 'LOW': 'bg-surface-container text-on-surface-variant' };
                    return map[urgency] || 'bg-surface-container text-on-surface-variant';
                },
            }
        }
    </script>
    @endpush
</x-layouts::app>

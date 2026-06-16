<x-layouts::app title="Split Barang - WMS Portal">
    <div x-data="splitBarangPage()">
        <style>[x-cloak] { display: none !important; }</style>

        <div class="mb-6 flex items-center gap-2 text-on-surface-variant font-body-sm">
            <span class="cursor-pointer hover:text-primary transition-colors">Operations</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="font-medium text-primary">Split Barang</span>
        </div>

        <div class="mb-8">
            <p class="font-label-caps text-label-caps text-secondary uppercase tracking-widest mb-1">Outbound Split</p>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Split Barang</h2>
            <p class="font-body-sm text-on-surface-variant mt-2">Split stock quantities from source inventory for outbound orders.</p>
        </div>

        <div class="bg-error-container/5 border border-error/20 rounded-xl p-5 mb-6 form-card">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-error-container/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-error text-2xl">priority_high</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        <p class="font-body-lg text-body-lg font-semibold text-on-surface">Urgent Request</p>
                        <span class="font-label-caps text-label-caps bg-error-container text-on-error-container px-2 py-0.5 rounded-full font-bold">URGENT</span>
                    </div>
                    <p class="font-data-mono text-data-mono text-on-surface-variant">SKU-99283-A</p>
                    <div class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-error/10">
                        <div>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Required</p>
                            <p class="font-data-mono text-data-mono text-error font-bold">50 pcs</p>
                        </div>
                        <div>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Order</p>
                            <p class="font-data-mono text-data-mono text-on-surface">SO-2024-1201</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 mb-6 form-card">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">inventory_2</span>
                Selected Stock Source
            </h3>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 rounded-xl bg-primary-container/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-2xl">warehouse</span>
                </div>
                <div class="flex-1">
                    <p class="font-body-lg text-body-lg font-semibold text-on-surface">Industrial Servo Motor X200</p>
                    <p class="font-data-mono text-data-mono text-on-surface-variant">ID-101 · A1-04-B</p>
                </div>
                <div class="text-right">
                    <p class="font-data-mono text-data-mono text-on-surface text-2xl font-bold">200</p>
                    <p class="font-label-caps text-label-caps text-on-surface-variant">Available</p>
                </div>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 mb-6 form-card">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-xl">call_split</span>
                Split Interface
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <div class="bg-surface-container rounded-xl p-5 text-center">
                    <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">Original</p>
                    <p class="font-headline-lg text-headline-lg text-on-surface" x-text="originalQty"></p>
                    <p class="font-label-caps text-label-caps text-on-surface-variant">pcs</p>
                </div>

                <div class="flex flex-col items-center gap-2">
                    <span class="material-symbols-outlined text-3xl text-on-surface-variant">arrow_forward</span>
                    <span class="font-label-caps text-label-caps text-on-surface-variant">SPLIT</span>
                </div>

                <div class="space-y-4">
                    <div class="bg-primary-container/5 border border-primary/20 rounded-xl p-4 text-center">
                        <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">Take</p>
                        <div class="flex items-center justify-center gap-3">
                            <button @click="takeQty = Math.max(10, takeQty - 10)" class="w-10 h-10 bg-surface-container-lowest border border-outline-variant rounded-lg flex items-center justify-center hover:bg-surface-container transition-colors">
                                <span class="material-symbols-outlined text-on-surface-variant">remove</span>
                            </button>
                            <span class="font-headline-lg text-headline-lg text-primary" x-text="takeQty"></span>
                            <button @click="takeQty = Math.min(originalQty - 10, takeQty + 10)" class="w-10 h-10 bg-surface-container-lowest border border-outline-variant rounded-lg flex items-center justify-center hover:bg-surface-container transition-colors">
                                <span class="material-symbols-outlined text-on-surface-variant">add</span>
                            </button>
                        </div>
                        <p class="font-label-caps text-label-caps text-on-surface-variant">pcs</p>
                    </div>
                    <div class="bg-surface-container rounded-xl p-4 text-center">
                        <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">Remaining</p>
                        <p class="font-headline-lg text-headline-lg text-on-surface" x-text="originalQty - takeQty"></p>
                        <p class="font-label-caps text-label-caps text-on-surface-variant">pcs</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-secondary-container/5 border border-secondary/20 rounded-xl p-4 mb-6 form-card">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-secondary text-xl mt-0.5">security</span>
                <div>
                    <p class="font-body-sm font-semibold text-on-surface mb-1">Security Protocol</p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Split operations are logged and audited. All quantity changes require supervisor approval for items exceeding 100 units. Ensure proper barcode scanning before execution.</p>
                </div>
            </div>
        </div>

        <button @click="executeSplit()" class="btn-wh-primary w-full h-14 gap-2 rounded-2xl shadow-lg">
            <span class="material-symbols-outlined text-xl">call_split</span>
            Execute Split
        </button>
    </div>

    @push('scripts')
    <script>
        function splitBarangPage() {
            return {
                originalQty: 200,
                takeQty: 50,
                executeSplit() {
                    if (this.takeQty <= 0 || this.takeQty >= this.originalQty) {
                        alert('Invalid split quantity.');
                        return;
                    }
                    alert('Split executed! Taking ' + this.takeQty + ' pcs, leaving ' + (this.originalQty - this.takeQty) + ' pcs.');
                },
            }
        }
    </script>
    @endpush
</x-layouts::app>

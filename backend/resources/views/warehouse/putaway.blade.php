<x-layouts::app title="Putaway - WMS Portal">
    <div x-data="putawayPage()">
        <style>[x-cloak] { display: none !important; }</style>

        <div class="mb-6 flex items-center gap-2 text-on-surface-variant font-body-sm">
            <span class="cursor-pointer hover:text-primary transition-colors">Operations</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="font-medium text-primary">Putaway</span>
        </div>

        <div class="mb-8">
            <p class="font-label-caps text-label-caps text-secondary uppercase tracking-widest mb-1">Storage Placement</p>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Putaway Process</h2>
            <p class="font-body-sm text-on-surface-variant mt-2">Scan, locate, and confirm storage placement for inbound items.</p>
        </div>

        <div class="flex items-center justify-center gap-0 mb-8">
            <template x-for="(stepItem, index) in steps" :key="stepItem.id">
                <div class="flex items-center">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all" :class="currentStep >= stepItem.id ? 'bg-primary border-primary text-on-primary' : 'bg-surface-container-lowest border-outline-variant text-on-surface-variant'">
                            <span x-show="currentStep > stepItem.id" class="material-symbols-outlined text-lg">check</span>
                            <span x-show="currentStep <= stepItem.id" class="font-data-mono text-data-mono font-bold" x-text="stepItem.id"></span>
                        </div>
                        <p class="font-label-caps text-label-caps mt-2" :class="currentStep >= stepItem.id ? 'text-primary' : 'text-on-surface-variant'" x-text="stepItem.label"></p>
                    </div>
                    <div x-show="index < steps.length - 1" class="w-16 h-0.5 mx-2 mb-6" :class="currentStep > stepItem.id ? 'bg-primary' : 'bg-outline-variant'"></div>
                </div>
            </template>
        </div>

        <div x-show="currentStep === 1" x-cloak>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card">
                <h3 class="font-headline-md text-headline-md text-on-surface mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-xl">qr_code_scanner</span>
                    Step 1: Scan Item
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Barcode Input</label>
                        <div class="flex gap-3">
                            <input x-model="barcodeInput" type="text" class="flex-1 h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="Scan or type barcode..." />
                            <button @click="verifyBarcode()" class="btn-wh-primary h-12 px-6 gap-2">
                                <span class="material-symbols-outlined text-xl">check_circle</span>
                                Verify
                            </button>
                        </div>
                    </div>

                    <div x-show="productVerified" x-cloak class="bg-primary-container/5 border border-primary/20 rounded-xl p-5">
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-xl bg-primary-container/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-primary text-2xl">inventory_2</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-body-lg text-body-lg font-semibold text-on-surface" x-text="product.name"></p>
                                <p class="font-data-mono text-data-mono text-on-surface-variant" x-text="product.sku"></p>
                                <div class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-primary/10">
                                    <div>
                                        <p class="font-label-caps text-label-caps text-on-surface-variant">Serial No</p>
                                        <p class="font-data-mono text-data-mono text-on-surface" x-text="product.serialNo"></p>
                                    </div>
                                    <div>
                                        <p class="font-label-caps text-label-caps text-on-surface-variant">Category</p>
                                        <p class="font-body-sm text-body-sm text-on-surface" x-text="product.category"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button x-show="productVerified" x-cloak @click="currentStep = 2" class="btn-wh-primary w-full h-12 gap-2">
                        Next: Select Rack Location
                        <span class="material-symbols-outlined text-xl">arrow_forward</span>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="currentStep === 2" x-cloak>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card">
                <h3 class="font-headline-md text-headline-md text-on-surface mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-xl">location_on</span>
                    Step 2: Rack Location
                </h3>
                <div class="space-y-4">
                    <div class="bg-secondary-container/5 border border-secondary/20 rounded-xl p-4">
                        <p class="font-label-caps text-label-caps text-on-surface-variant mb-2">Suggested Rack</p>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-secondary text-2xl">grid_view</span>
                            </div>
                            <div>
                                <p class="font-data-mono text-data-mono text-on-surface text-xl font-bold" x-text="suggestedRack.rackId"></p>
                                <p class="font-label-caps text-label-caps text-on-surface-variant" x-text="'Level ' + suggestedRack.level + ' · Capacity: ' + suggestedRack.capacity"></p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Quantity</p>
                            <p class="font-data-mono text-data-mono text-on-surface text-xl font-bold" x-text="product.qty + ' pcs'"></p>
                        </div>
                        <div>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Handling</p>
                            <p class="font-body-sm text-body-sm text-on-surface" x-text="product.handling"></p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button @click="currentStep = 1" class="btn-wh-outline flex-1 h-12 gap-2">
                            <span class="material-symbols-outlined text-xl">arrow_back</span>
                            Back
                        </button>
                        <button @click="currentStep = 3" class="btn-wh-primary flex-1 h-12 gap-2">
                            Confirm Location
                            <span class="material-symbols-outlined text-xl">arrow_forward</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="currentStep === 3" x-cloak>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card">
                <h3 class="font-headline-md text-headline-md text-on-surface mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-green-600 text-xl">task_alt</span>
                    Step 3: Confirm Putaway
                </h3>
                <div class="space-y-4">
                    <div class="bg-surface-container rounded-xl p-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="font-label-caps text-label-caps text-on-surface-variant">Product</p>
                                <p class="font-body-sm font-semibold text-on-surface" x-text="product.name"></p>
                            </div>
                            <div>
                                <p class="font-label-caps text-label-caps text-on-surface-variant">SKU</p>
                                <p class="font-data-mono text-data-mono text-on-surface" x-text="product.sku"></p>
                            </div>
                            <div>
                                <p class="font-label-caps text-label-caps text-on-surface-variant">Rack Location</p>
                                <p class="font-data-mono text-data-mono text-on-surface" x-text="suggestedRack.rackId"></p>
                            </div>
                            <div>
                                <p class="font-label-caps text-label-caps text-on-surface-variant">Quantity</p>
                                <p class="font-data-mono text-data-mono text-on-surface" x-text="product.qty + ' pcs'"></p>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button @click="currentStep = 1; productVerified = false; barcodeInput = ''" class="btn-wh-outline flex-1 h-12 gap-2">
                            <span class="material-symbols-outlined text-xl">flag</span>
                            Flag Issue
                        </button>
                        <button @click="confirmPutaway()" class="btn-wh-primary flex-1 h-12 gap-2">
                            <span class="material-symbols-outlined text-xl">check_circle</span>
                            Confirm Putaway
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function putawayPage() {
            return {
                currentStep: 1,
                barcodeInput: '',
                productVerified: false,
                steps: [
                    { id: 1, label: 'Scan Item' },
                    { id: 2, label: 'Rack Loc' },
                    { id: 3, label: 'Confirm' },
                ],
                product: {
                    name: 'Industrial Servo Motor X200',
                    sku: 'SKU-SRV-X200',
                    serialNo: 'SN-44821',
                    category: 'Industrial Electronics',
                    qty: 145,
                    handling: 'Fragile — Use care',
                },
                suggestedRack: {
                    rackId: 'A1-04-B',
                    level: 'L3',
                    capacity: '200 pcs',
                },
                verifyBarcode() {
                    if (this.barcodeInput.trim()) {
                        this.productVerified = true;
                    }
                },
                confirmPutaway() {
                    alert('Putaway confirmed! ' + this.product.name + ' placed at ' + this.suggestedRack.rackId);
                    this.currentStep = 1;
                    this.productVerified = false;
                    this.barcodeInput = '';
                },
            }
        }
    </script>
    @endpush
</x-layouts::app>

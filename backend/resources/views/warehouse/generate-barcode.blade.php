<x-layouts::app title="Generate Barcode - WMS Portal">
    <div x-data="generateBarcodePage()">
        <style>[x-cloak] { display: none !important; }</style>

        <div class="mb-6 flex items-center gap-2 text-on-surface-variant font-body-sm">
            <span class="cursor-pointer hover:text-primary transition-colors">Inventory</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="font-medium text-primary">Generate Barcode</span>
        </div>

        <div class="mb-8">
            <p class="font-label-caps text-label-caps text-secondary uppercase tracking-widest mb-1">Label Printing</p>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Barcode Generator</h2>
            <p class="font-body-sm text-on-surface-variant mt-2">Generate and print barcode labels for warehouse products.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card">
                <h3 class="font-headline-md text-headline-md text-on-surface mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-xl">qr_code_2</span>
                    Barcode Configuration
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Product Name</label>
                        <input x-model="form.productName" type="text" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="Enter product name" />
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">SKU Code</label>
                        <input x-model="form.skuCode" type="text" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all font-body-sm" placeholder="e.g. SKU-001" />
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Label Count</label>
                        <div class="flex items-center gap-3">
                            <button @click="form.labelCount = Math.max(1, form.labelCount - 1)" class="w-10 h-10 bg-surface-container border border-outline-variant rounded-lg flex items-center justify-center hover:bg-surface-container-high transition-colors">
                                <span class="material-symbols-outlined text-on-surface-variant">remove</span>
                            </button>
                            <input x-model.number="form.labelCount" type="number" min="1" class="flex-1 h-12 px-4 bg-white border border-outline-variant rounded-lg text-center font-data-mono text-data-mono focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all" />
                            <button @click="form.labelCount++" class="w-10 h-10 bg-surface-container border border-outline-variant rounded-lg flex items-center justify-center hover:bg-surface-container-high transition-colors">
                                <span class="material-symbols-outlined text-on-surface-variant">add</span>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Barcode Type</label>
                        <select x-model="form.barcodeType" class="w-full h-12 px-4 bg-white border border-outline-variant rounded-lg font-body-sm">
                            <option value="code128">Code 128</option>
                            <option value="code39">Code 39</option>
                            <option value="ean13">EAN-13</option>
                            <option value="ean8">EAN-8</option>
                            <option value="upca">UPC-A</option>
                        </select>
                    </div>
                    <button @click="generateBarcode()" class="btn-wh-primary w-full h-12 gap-2">
                        <span class="material-symbols-outlined text-xl">autorenew</span>
                        Generate Barcode
                    </button>
                </div>
            </div>

            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card">
                <h3 class="font-headline-md text-headline-md text-on-surface mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-xl">preview</span>
                    Preview
                </h3>

                <div x-show="generated" x-cloak class="space-y-4">
                    <div class="bg-white border-2 border-dashed border-outline-variant rounded-xl p-6 text-center">
                        <p class="font-body-sm font-semibold text-on-surface mb-1" x-text="form.productName || 'Product Name'"></p>
                        <p class="font-data-mono text-data-mono text-on-surface-variant mb-4" x-text="form.skuCode || 'SKU-000'"></p>
                        <div class="flex items-end justify-center gap-px mb-3">
                            <template x-for="(bar, i) in barcodePattern" :key="i">
                                <div class="bg-on-surface" :style="'width:' + bar.w + 'px; height:' + bar.h + 'px;'"></div>
                            </template>
                        </div>
                        <p class="font-data-mono text-data-mono text-xs text-on-surface-variant" x-text="form.skuCode || '0000000000'"></p>
                        <p class="font-label-caps text-label-caps text-outline mt-2" x-text="form.barcodeType.toUpperCase() + ' | Labels: ' + form.labelCount"></p>
                    </div>

                    <div class="flex gap-3">
                        <button class="btn-wh-outline flex-1 h-12 gap-2">
                            <span class="material-symbols-outlined text-xl">save</span>
                            Save PDF
                        </button>
                        <button class="btn-wh-primary flex-1 h-12 gap-2">
                            <span class="material-symbols-outlined text-xl">print</span>
                            Print Labels
                        </button>
                    </div>
                </div>

                <div x-show="!generated" x-cloak class="flex flex-col items-center justify-center py-16 text-center">
                    <span class="material-symbols-outlined text-5xl text-outline mb-4">qr_code_2</span>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Configure and generate a barcode to see the preview.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function generateBarcodePage() {
            return {
                form: {
                    productName: '',
                    skuCode: '',
                    labelCount: 1,
                    barcodeType: 'code128',
                },
                generated: false,
                barcodePattern: [],
                generateBarcode() {
                    if (!this.form.productName || !this.form.skuCode) {
                        alert('Product Name and SKU Code are required.');
                        return;
                    }
                    const pattern = [];
                    for (let i = 0; i < 30; i++) {
                        pattern.push({ w: Math.random() > 0.5 ? 3 : Math.random() > 0.5 ? 2 : 1, h: 48 + Math.floor(Math.random() * 8) });
                    }
                    this.barcodePattern = pattern;
                    this.generated = true;
                },
            }
        }
    </script>
    @endpush
</x-layouts::app>

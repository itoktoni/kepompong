<x-layouts::app title="Register Product - WMS Portal">
    <div x-data="registerPage()">
        <style>[x-cloak] { display: none !important; }</style>

        <x-breadcrumb :items="[['url' => '#', 'label' => 'Inventory'], ['url' => '', 'label' => 'Register New Product']]" />

        <div class="mb-8">
            <p class="font-label-caps text-label-caps text-secondary uppercase tracking-widest mb-1">Warehouse Intake</p>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Registration Desk</h2>
            <p class="font-body-sm text-on-surface-variant mt-2">Register new products into the warehouse inventory system.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card label="Product Information" icon="add_box">
                    @bind($model ?? null)
                        <x-input col="6" name="product_name" placeholder="Enter product name" />
                        <x-input col="3" name="sku_code" placeholder="e.g. SKU-001" />
                        <x-input col="3" name="serial_no" placeholder="e.g. SN-12345" />
                        <x-select col="6" name="category" :options="['electronics' => 'Industrial Electronics', 'mechanical' => 'Mechanical Parts', 'safety' => 'Safety Equipment', 'consumables' => 'Consumables', 'raw-materials' => 'Raw Materials']" placeholder="Select Category" />
                        <x-select col="6" name="tags" :options="['fragile' => 'Fragile', 'hazmat' => 'Hazardous', 'cold-chain' => 'Cold Chain', 'high-value' => 'High Value', 'oversized' => 'Oversized']" placeholder="Select Tags" />
                        <x-textarea col="12" name="notes" rows="4" placeholder="Additional notes about the product..." />
                    @endbind

                    <div class="col-span-12 flex items-center justify-between mt-2 pt-6 border-t border-outline-variant">
                        <div class="flex items-center gap-6">
                            <x-toggle name="active_status" label="Active Status" />
                            <x-checkbox name="expiration_control" label="Expiration Control" />
                        </div>
                        <x-button type="submit" icon="save" size="lg">Register Product</x-button>
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card label="File Upload" icon="cloud_upload">
                    <x-file col="12" name="attachments" />
                </x-card>

                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 form-card">
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-on-surface-variant text-xl">info</span>
                        System Metadata
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-outline-variant/50">
                            <span class="font-label-caps text-label-caps text-on-surface-variant">Created By</span>
                            <span class="font-data-mono text-data-mono text-on-surface">System Admin</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-outline-variant/50">
                            <span class="font-label-caps text-label-caps text-on-surface-variant">Date</span>
                            <span class="font-data-mono text-data-mono text-on-surface" x-text="new Date().toLocaleDateString()"></span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-outline-variant/50">
                            <span class="font-label-caps text-label-caps text-on-surface-variant">Facility</span>
                            <span class="font-data-mono text-data-mono text-on-surface">WH-001</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="font-label-caps text-label-caps text-on-surface-variant">Status</span>
                            <x-badge type="info">Draft</x-badge>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function registerPage() {
            return {
                submitForm() {
                    alert('Product registered successfully!');
                },
            }
        }
    </script>
    @endpush
</x-layouts::app>

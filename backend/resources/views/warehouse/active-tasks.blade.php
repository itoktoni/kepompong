<x-layouts::app title="Active Tasks - WMS Portal">
    <div x-data="activeTasksPage()">
        <style>[x-cloak] { display: none !important; }</style>

        <div class="mb-6 flex items-center gap-2 text-on-surface-variant font-body-sm">
            <span class="cursor-pointer hover:text-primary transition-colors">Operations</span>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
            <span class="font-medium text-primary">Active Tasks</span>
        </div>

        <div class="mb-8">
            <p class="font-label-caps text-label-caps text-secondary uppercase tracking-widest mb-1">Task Queue</p>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-surface">Active Tasks</h2>
            <p class="font-body-sm text-on-surface-variant mt-2">Monitor and manage ongoing warehouse operations tasks.</p>
        </div>

        <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
            <template x-for="tab in tabs" :key="tab.value">
                <button @click="activeTab = tab.value" :class="activeTab === tab.value ? 'bg-primary text-on-primary' : 'bg-surface-container-lowest text-on-surface-variant border border-outline-variant hover:bg-surface-container'" class="px-5 py-2.5 rounded-full font-body-sm transition-all whitespace-nowrap flex items-center gap-2" x-text="tab.label"></button>
            </template>
        </div>

        <div class="space-y-3 mb-8">
            <template x-for="task in filteredTasks" :key="task.id">
                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 form-card hover:shadow-md transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="task.type === 'Putaway' ? 'bg-primary-container/10' : 'bg-secondary-container/10'">
                                <span class="material-symbols-outlined text-xl" :class="task.type === 'Putaway' ? 'text-primary' : 'text-secondary'" x-text="task.type === 'Putaway' ? 'move_up' : 'hand_package'"></span>
                            </div>
                            <div>
                                <p class="font-body-sm font-semibold text-on-surface" x-text="task.taskId"></p>
                                <span class="font-label-caps text-label-caps px-2 py-0.5 rounded-full" :class="task.type === 'Putaway' ? 'bg-primary-container/20 text-primary' : 'bg-secondary-container/20 text-secondary'" x-text="task.type"></span>
                            </div>
                        </div>
                        <span class="font-label-caps text-label-caps bg-surface-container px-2 py-1 rounded-full text-on-surface-variant" x-text="task.time"></span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 pt-3 border-t border-outline-variant">
                        <div>
                            <p class="font-label-caps text-label-caps text-on-surface-variant" x-text="task.type === 'Putaway' ? 'Target Rack' : 'Source Rack'"></p>
                            <p class="font-data-mono text-data-mono text-on-surface" x-text="task.rack"></p>
                        </div>
                        <div>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Level</p>
                            <p class="font-data-mono text-data-mono text-on-surface" x-text="task.level"></p>
                        </div>
                        <div>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">SKU Name</p>
                            <p class="font-body-sm text-body-sm text-on-surface truncate" x-text="task.skuName"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="sticky bottom-4">
            <button class="btn-wh-primary w-full h-14 gap-2 rounded-2xl shadow-lg">
                <span class="material-symbols-outlined text-xl">qr_code_scanner</span>
                Scan Barcode
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        function activeTasksPage() {
            return {
                activeTab: 'all',
                tabs: [
                    { label: 'All Tasks', value: 'all' },
                    { label: 'Putaway', value: 'putaway' },
                    { label: 'Picking', value: 'picking' },
                ],
                tasks: [
                    { id: 1, taskId: 'TSK-2024-0451', type: 'Putaway', rack: 'A1-04-B', level: 'L3', skuName: 'Industrial Servo Motor X200', time: '14:30' },
                    { id: 2, taskId: 'TSK-2024-0450', type: 'Picking', rack: 'B2-07-A', level: 'L1', skuName: 'Hydraulic Pump Assembly', time: '14:22' },
                    { id: 3, taskId: 'TSK-2024-0449', type: 'Putaway', rack: 'C1-02-C', level: 'L2', skuName: 'Control Board PCB-X4', time: '14:15' },
                    { id: 4, taskId: 'TSK-2024-0448', type: 'Picking', rack: 'A3-01-B', level: 'L4', skuName: 'Steel Gear Module 4', time: '13:58' },
                    { id: 5, taskId: 'TSK-2024-0447', type: 'Putaway', rack: 'B1-05-A', level: 'L2', skuName: 'Emergency Stop Button', time: '13:42' },
                    { id: 6, taskId: 'TSK-2024-0446', type: 'Picking', rack: 'C2-03-A', level: 'L1', skuName: 'Safety Relief Valve', time: '13:30' },
                ],
                get filteredTasks() {
                    if (this.activeTab === 'all') return this.tasks;
                    return this.tasks.filter(t => t.type.toLowerCase() === this.activeTab);
                },
            }
        }
    </script>
    @endpush
</x-layouts::app>

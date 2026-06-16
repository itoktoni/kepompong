<x-layouts::app title="Dashboard - WMS Portal">
    <div>
        <section class="mb-8">
            <div class="grid grid-cols-4 gap-4">
                @php
                $quickActions = [
                    ['label' => 'Scan Inbound', 'icon' => 'qr_code_scanner', 'bgClass' => 'bg-primary-container/10', 'iconClass' => 'text-primary'],
                    ['label' => 'Picking', 'icon' => 'pan_tool_alt', 'bgClass' => 'bg-secondary-container/10', 'iconClass' => 'text-secondary'],
                    ['label' => 'Stock Opname', 'icon' => 'inventory', 'bgClass' => 'bg-tertiary-container/10', 'iconClass' => 'text-tertiary'],
                    ['label' => 'Relocation', 'icon' => 'sync_alt', 'bgClass' => 'bg-surface-variant', 'iconClass' => 'text-on-surface-variant'],
                ];
                @endphp
                @foreach($quickActions as $action)
                <button class="flex flex-col items-center gap-2 group bg-surface-container-lowest border border-outline-variant rounded-xl p-3 shadow-sm hover:shadow-md transition-all">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center group-active:scale-95 transition-transform border border-outline-variant shadow-sm {{ $action['bgClass'] }}">
                        <span class="material-symbols-outlined text-2xl {{ $action['iconClass'] }}">{{ $action['icon'] }}</span>
                    </div>
                    <span class="font-label-caps text-label-caps text-on-surface text-center">{{ $action['label'] }}</span>
                </button>
                @endforeach
            </div>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="widget-card">
                <h3 class="font-headline-md text-headline-md mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-xl">analytics</span>
                    Operational Metrics
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    @php
                    $metrics = [
                        ['label' => 'Inbound', 'value' => '482', 'change' => '+5%', 'changeClass' => 'text-green-600', 'valueClass' => 'text-primary'],
                        ['label' => 'Outbound', 'value' => '315', 'change' => '-2%', 'changeClass' => 'text-red-600', 'valueClass' => 'text-secondary'],
                        ['label' => 'Low Stock', 'value' => '12', 'icon' => 'warning', 'changeClass' => 'text-error', 'valueClass' => 'text-error'],
                        ['label' => 'Pending Splits', 'value' => '24', 'sub' => 'Active', 'valueClass' => 'text-on-surface'],
                    ];
                    @endphp
                    @foreach($metrics as $metric)
                    <div class="bg-surface-container-lowest border border-outline-variant p-3 rounded-lg">
                        <p class="font-label-caps text-label-caps text-on-surface-variant mb-1 uppercase">{{ $metric['label'] }}</p>
                        <div class="flex items-end justify-between">
                            <span class="font-headline-md text-headline-md {{ $metric['valueClass'] }}">{{ $metric['value'] }}</span>
                            @if(isset($metric['change']))
                                <span class="font-label-caps text-label-caps font-bold {{ $metric['changeClass'] }}">{{ $metric['change'] }}</span>
                            @elseif(isset($metric['icon']))
                                <span class="material-symbols-outlined text-sm {{ $metric['changeClass'] }}">{{ $metric['icon'] }}</span>
                            @elseif(isset($metric['sub']))
                                <span class="font-label-caps text-label-caps text-on-surface-variant">{{ $metric['sub'] }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="widget-card">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-secondary-container/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-secondary text-xl">warehouse</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-surface">Warehouse Occupancy</h3>
                </div>
                <div class="flex items-center gap-6">
                    <div class="relative w-28 h-28 shrink-0">
                        <svg class="w-full h-full transform -rotate-90">
                            <circle class="text-surface-container" cx="56" cy="56" fill="transparent" r="48" stroke="currentColor" stroke-width="10" />
                            <circle class="text-secondary" cx="56" cy="56" fill="transparent" r="48" stroke="currentColor" stroke-dasharray="301.6" stroke-dashoffset="96.5" stroke-linecap="round" stroke-width="10" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="font-headline-md text-headline-md text-on-surface font-bold">68%</span>
                            <span class="font-label-caps text-label-caps text-on-surface-variant">Used</span>
                        </div>
                    </div>
                    <div class="flex-1 space-y-3">
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Utilized</span>
                                <span class="font-data-mono text-data-mono text-on-surface font-bold">42,160</span>
                            </div>
                            <div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden">
                                <div class="h-full bg-secondary rounded-full" style="width: 68%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Available</span>
                                <span class="font-data-mono text-data-mono text-on-surface font-bold">19,840</span>
                            </div>
                            <div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden">
                                <div class="h-full bg-outline-variant rounded-full" style="width: 32%"></div>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-outline-variant">
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Status: Near Optimal</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="widget-card md:col-span-2">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-primary-container/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-xl">monitoring</span>
                        </div>
                        <h3 class="font-headline-md text-headline-md text-on-surface">Performance</h3>
                    </div>
                    <span class="font-label-caps text-label-caps text-on-surface-variant bg-surface-container px-2 py-1 rounded-full">LIVE</span>
                </div>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="font-body-sm text-body-sm text-on-surface-variant">Throughput Goal</span>
                            <span class="font-data-mono text-data-mono text-on-surface font-bold">796 / 850</span>
                        </div>
                        <div class="w-full h-2.5 bg-surface-container rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: 93%"></div>
                        </div>
                        <p class="font-label-caps text-label-caps text-secondary mt-1">93% Complete</p>
                    </div>
                    <div class="grid grid-cols-3 gap-3 pt-3 border-t border-outline-variant">
                        <div class="text-center p-2 bg-surface-container rounded-lg">
                            <span class="material-symbols-outlined text-green-600 text-lg mb-1 block">check_circle</span>
                            <p class="font-data-mono text-data-mono text-on-surface font-bold">0.04%</p>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Error Rate</p>
                        </div>
                        <div class="text-center p-2 bg-surface-container rounded-lg">
                            <span class="material-symbols-outlined text-primary text-lg mb-1 block">schedule</span>
                            <p class="font-data-mono text-data-mono text-on-surface font-bold">14m</p>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Avg Lead</p>
                        </div>
                        <div class="text-center p-2 bg-surface-container rounded-lg">
                            <span class="material-symbols-outlined text-secondary text-lg mb-1 block">local_shipping</span>
                            <p class="font-data-mono text-data-mono text-on-surface font-bold">88%</p>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">Fleet Util</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="widget-card md:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-headline-md text-headline-md flex items-center gap-2">
                        <span class="material-symbols-outlined text-on-surface-variant text-xl">history</span>
                        Recent Activity
                    </h3>
                    <button class="text-primary font-label-caps text-label-caps hover:underline">VIEW ALL</button>
                </div>
                <div class="space-y-0">
                    @php
                    $activities = [
                        ['icon' => 'login', 'iconBg' => 'bg-primary/5', 'iconColor' => 'text-primary', 'title' => '#WMS-9021 Pallet Intake', 'subtitle' => 'Aisle 4 · User: J. Doe', 'status' => 'COMPLETE', 'statusClass' => 'bg-green-50 text-green-700', 'time' => '14:22'],
                        ['icon' => 'logout', 'iconBg' => 'bg-secondary/5', 'iconColor' => 'text-secondary', 'title' => '#WMS-8842 Dock 7 Outbound', 'subtitle' => 'Priority Air · User: S. Lee', 'status' => 'PROCESSING', 'statusClass' => 'bg-blue-50 text-blue-700', 'time' => '14:15'],
                        ['icon' => 'sync', 'iconBg' => 'bg-tertiary/5', 'iconColor' => 'text-tertiary', 'title' => '#WMS-9104 Stock Relocation', 'subtitle' => 'Zone C to Zone B', 'status' => 'READY', 'statusClass' => 'bg-surface-container-high text-on-surface-variant', 'time' => '13:48'],
                    ];
                    @endphp
                    @foreach($activities as $activity)
                    <div class="flex items-center gap-4 py-3 border-b border-outline-variant last:border-0">
                        <div class="p-2 rounded-lg shrink-0 {{ $activity['iconBg'] }}">
                            <span class="material-symbols-outlined text-sm {{ $activity['iconColor'] }}">{{ $activity['icon'] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-body-sm text-body-sm font-semibold text-on-surface truncate">{{ $activity['title'] }}</p>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">{{ $activity['subtitle'] }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-label-caps text-[9px] font-bold px-2 py-0.5 rounded block mb-1 {{ $activity['statusClass'] }}">{{ $activity['status'] }}</span>
                            <p class="text-[9px] text-outline font-data-mono text-data-mono">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>

<x-layouts.app title="Monitor de PCs" moduleLabel="Activos" pageTitle="Monitor de PCs">

    @php
        $onlineCount  = $assets->filter(fn ($a) => $a->agentDevice?->isOnline())->count();
        $agentCount   = $assets->filter(fn ($a) => $a->agentDevice)->count();
        $criticalCount = $assets->filter(fn ($a) => data_get($a->agentDevice?->last_health_json, 'overallStatus') === 'critical')->count();
    @endphp

    <x-ui.page-header
        title="Monitor de PCs"
        description="Estado en tiempo real de computadoras y laptops con agente de monitoreo instalado."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('assets.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                Inventario completo
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 mb-6 sm:grid-cols-4">
        <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 shadow-sm">
            <p class="text-xs text-slate-500">Total PCs/Laptops</p>
            <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ $assets->total() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 px-4 py-3 shadow-sm">
            <p class="text-xs text-slate-500">Con agente</p>
            <p class="text-2xl font-bold text-slate-900 mt-0.5">{{ $agentCount }}</p>
        </div>
        <div class="bg-emerald-50 rounded-2xl border border-emerald-100 px-4 py-3 shadow-sm">
            <p class="text-xs text-emerald-600">En línea ahora</p>
            <p class="text-2xl font-bold text-emerald-700 mt-0.5">{{ $onlineCount }}</p>
        </div>
        <div class="bg-rose-50 rounded-2xl border border-rose-100 px-4 py-3 shadow-sm">
            <p class="text-xs text-rose-600">Estado crítico</p>
            <p class="text-2xl font-bold text-rose-700 mt-0.5">{{ $criticalCount }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('assets.pc-monitor') }}" class="flex flex-wrap items-end gap-3 mb-6">
        <div class="flex-1 min-w-[220px]">
            <x-ui.search-input name="search" :value="request('search')" placeholder="Buscar por nombre, código..." />
        </div>
        <div class="w-52">
            <select name="unit_id" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todas las unidades</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
        <x-ui.button type="submit" variant="secondary" size="sm">Filtrar</x-ui.button>
        @if (request()->hasAny(['search', 'unit_id']))
            <x-ui.button variant="ghost" size="sm" href="{{ route('assets.pc-monitor') }}">Limpiar</x-ui.button>
        @endif
    </form>

    {{-- PC Cards Grid --}}
    @if ($assets->isEmpty())
        <x-ui.empty-state
            title="Sin equipos registrados"
            description="No hay computadoras o laptops que coincidan con los filtros aplicados."
        />
    @else
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($assets as $asset)
        @php
            $agent      = $asset->agentDevice;
            $isOnline   = $agent?->isOnline() ?? false;
            $snap       = $agent?->last_snapshot_json ?? [];
            $health     = $agent?->last_health_json ?? [];
            $deviceData = data_get($snap, 'Device', data_get($snap, 'device', []));
            $software   = collect(data_get($snap, 'InstalledSoftware', data_get($snap, 'installedSoftware', [])) ?? []);
            $specs      = $asset->specs_json ?? [];

            $hostname       = data_get($specs, 'hostname') ?? data_get($deviceData, 'Hostname') ?? data_get($deviceData, 'hostname') ?? $asset->name;
            $cpu            = data_get($specs, 'cpu') ?? data_get($deviceData, 'ProcessorName') ?? data_get($deviceData, 'processorName');
            $ramGb          = data_get($specs, 'ram_gb') ?? data_get($deviceData, 'TotalMemoryGb') ?? data_get($deviceData, 'totalMemoryGb');
            $totalStorageGb = data_get($specs, 'total_storage_gb') ?? data_get($deviceData, 'TotalStorageGb') ?? data_get($deviceData, 'totalStorageGb');
            $freeStorageGb  = data_get($specs, 'free_storage_gb') ?? data_get($deviceData, 'FreeStorageGb') ?? data_get($deviceData, 'freeStorageGb');
            $storageUsed    = ($totalStorageGb > 0 && $freeStorageGb !== null) ? round((($totalStorageGb - $freeStorageGb) / $totalStorageGb) * 100) : null;
            $ramUsed        = data_get($health, 'memory.usedPercent');
            $os             = data_get($specs, 'operating_system') ?? data_get($deviceData, 'OperatingSystem') ?? data_get($deviceData, 'operatingSystem');
            $ipList         = collect(data_get($specs, 'ip_addresses') ?? data_get($deviceData, 'IpAddresses') ?? data_get($deviceData, 'ipAddresses') ?? [])->filter()->values();
            $healthStatus   = data_get($health, 'overallStatus', null);
            $warnings       = collect(data_get($health, 'warnings', []))->filter()->values();
            $softwareList   = $software->map(fn ($s) => data_get($s, 'Name') ?? data_get($s, 'name'))->filter()->sort()->values();

            $healthColor = match($healthStatus) {
                'ok'       => 'emerald',
                'warning'  => 'amber',
                'critical' => 'rose',
                default    => 'slate',
            };
            $agentStatusTone = $isOnline ? 'bg-emerald-500' : ($agent ? 'bg-slate-400' : 'bg-slate-200');
        @endphp

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col"
             x-data="{ showSoftware: false, softwareQ: '' }">

            {{-- Card Header --}}
            <div class="flex items-start gap-3 p-4 border-b border-slate-100">
                {{-- Icon + Online dot --}}
                <div class="relative shrink-0 mt-0.5">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
                        @if ($asset->asset_type->value === 'laptop')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25z"/>
                        </svg>
                        @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25z"/>
                        </svg>
                        @endif
                    </div>
                    @if ($agent)
                    <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                        @if ($isOnline)
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        @endif
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 border-2 border-white {{ $agentStatusTone }}"></span>
                    </span>
                    @endif
                </div>

                {{-- Title --}}
                <div class="flex-1 min-w-0">
                    <a href="{{ route('assets.show', $asset) }}"
                       class="font-semibold text-slate-900 hover:text-sigat-600 transition-colors text-sm leading-tight block truncate">
                        {{ $hostname }}
                    </a>
                    <p class="text-xs text-slate-400 mt-0.5 truncate">
                        {{ $asset->internal_code }}
                        @if ($asset->organizationalUnit)
                            · {{ $asset->organizationalUnit->name }}
                        @endif
                    </p>
                </div>

                {{-- Health/OS badge --}}
                <div class="shrink-0 flex flex-col items-end gap-1">
                    @if ($healthStatus)
                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                        @if($healthStatus === 'ok') bg-emerald-50 text-emerald-700
                        @elseif($healthStatus === 'warning') bg-amber-50 text-amber-700
                        @elseif($healthStatus === 'critical') bg-rose-50 text-rose-700
                        @else bg-slate-100 text-slate-600 @endif">
                        <span class="w-1.5 h-1.5 rounded-full
                            @if($healthStatus === 'ok') bg-emerald-500
                            @elseif($healthStatus === 'warning') bg-amber-500
                            @elseif($healthStatus === 'critical') bg-rose-500
                            @else bg-slate-400 @endif"></span>
                        {{ strtoupper($healthStatus) }}
                    </span>
                    @endif
                    @if ($os)
                    <span class="text-xs text-slate-400 truncate max-w-[100px]" title="{{ $os }}">{{ Str::limit($os, 12) }}</span>
                    @endif
                </div>
            </div>

            {{-- Hardware specs --}}
            <div class="p-4 space-y-3 flex-1">

                {{-- CPU --}}
                @if ($cpu)
                <div class="flex items-start gap-2 text-xs">
                    <span class="text-slate-400 shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21M12 6.75a5.25 5.25 0 100 10.5 5.25 5.25 0 000-10.5z"/></svg>
                    </span>
                    <span class="text-slate-600 line-clamp-2 leading-tight" title="{{ $cpu }}">{{ $cpu }}</span>
                </div>
                @endif

                {{-- RAM --}}
                @if ($ramGb)
                <div class="space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="8" width="20" height="8" rx="1" stroke-width="1.5"/><path d="M6 8V6m4 2V6m4 2V6m4 2V6M6 16v2m4-2v2m4-2v2m4-2v2" stroke-linecap="round" stroke-width="1.5"/></svg>
                            RAM
                        </span>
                        <span class="font-medium text-slate-700">
                            {{ $ramGb }} GB
                            @if ($ramUsed !== null)
                                <span class="text-slate-400">· {{ number_format($ramUsed, 0) }}%</span>
                            @endif
                        </span>
                    </div>
                    @if ($ramUsed !== null)
                    <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full transition-all
                            @if($ramUsed > 85) bg-rose-500 @elseif($ramUsed > 70) bg-amber-400 @else bg-emerald-500 @endif"
                             style="width: {{ min($ramUsed, 100) }}%"></div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Storage --}}
                @if ($totalStorageGb && $storageUsed !== null)
                <div class="space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>
                            Disco
                        </span>
                        <span class="font-medium text-slate-700">
                            {{ $freeStorageGb }} GB libre
                            <span class="text-slate-400">/ {{ $totalStorageGb }} GB</span>
                        </span>
                    </div>
                    <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full transition-all
                            @if($storageUsed > 85) bg-rose-500 @elseif($storageUsed > 70) bg-amber-400 @else bg-emerald-500 @endif"
                             style="width: {{ min($storageUsed, 100) }}%"></div>
                    </div>
                </div>
                @endif

                {{-- IPs --}}
                @if ($ipList->isNotEmpty())
                <div class="flex flex-wrap gap-1.5 pt-1">
                    @foreach ($ipList->take(3) as $ip)
                    <span class="font-mono text-xs bg-slate-50 border border-slate-200 text-slate-600 px-2 py-0.5 rounded-lg">{{ $ip }}</span>
                    @endforeach
                    @if ($ipList->count() > 3)
                    <span class="text-xs text-slate-400 py-0.5">+{{ $ipList->count() - 3 }}</span>
                    @endif
                </div>
                @endif

                {{-- Agent heartbeat --}}
                @if ($agent)
                <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-50">
                    <span class="text-slate-400">Último heartbeat</span>
                    <span class="{{ $isOnline ? 'text-emerald-600 font-medium' : 'text-slate-500' }}">
                        {{ $agent->last_heartbeat_at ? $agent->last_heartbeat_at->diffForHumans() : 'Nunca' }}
                    </span>
                </div>
                @endif

                {{-- Warnings --}}
                @if ($warnings->isNotEmpty())
                <div class="space-y-1 pt-1">
                    @foreach ($warnings->take(2) as $w)
                    <p class="text-xs text-amber-700 bg-amber-50 rounded-lg px-2 py-1">⚠ {{ $w }}</p>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Software footer --}}
            @if ($softwareList->isNotEmpty())
            <div class="border-t border-slate-100">
                <button @click="showSoftware = !showSoftware"
                        class="w-full flex items-center justify-between px-4 py-2.5 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-colors">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-sigat-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z"/></svg>
                        {{ $softwareList->count() }} programas instalados
                    </span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="showSoftware ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="showSoftware" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="px-4 pb-4">
                    <input type="text" x-model="softwareQ"
                           placeholder="Buscar programa..."
                           class="w-full text-xs rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 mb-2 focus:outline-none focus:border-sigat-400 focus:bg-white transition-colors">
                    <div class="max-h-48 overflow-y-auto space-y-0.5 pr-1">
                        @foreach ($softwareList as $sw)
                        <div x-show="!softwareQ || '{{ addslashes(strtolower($sw)) }}'.includes(softwareQ.toLowerCase())"
                             class="flex items-center gap-2 py-1 px-1.5 rounded-lg hover:bg-slate-50 group">
                            <span class="w-1.5 h-1.5 rounded-full bg-sigat-300 shrink-0"></span>
                            <span class="text-xs text-slate-600 group-hover:text-slate-900 truncate" title="{{ $sw }}">{{ $sw }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="border-t border-slate-100 px-4 py-2.5">
                <p class="text-xs text-slate-400">
                    @if ($agent)
                        Sin software en snapshot.
                    @else
                        Sin agente instalado.
                    @endif
                </p>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $assets->withQueryString()->links() }}
    </div>
    @endif

</x-layouts.app>

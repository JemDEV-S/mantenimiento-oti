<x-layouts.app title="Detalle Activo" moduleLabel="Activos" pageTitle="Detalle de Activo">

    @php
        $statusTone = match($asset->status->value) {
            'activo' => 'success', 'en_uso' => 'info', 'en_almacen' => 'neutral',
            'en_reparacion' => 'warning', 'dado_de_baja' => 'danger', 'extraviado' => 'danger',
            default => 'neutral',
        };
        $conditionTone = match($asset->condition->value) {
            'bueno' => 'success', 'regular' => 'warning', 'malo' => 'danger', 'obsoleto' => 'neutral',
            default => 'neutral',
        };

        $specs          = $asset->specs_json ?? [];
        $extra          = $asset->extra_json ?? [];
        $agentSnapshot  = $asset->agentDevice?->last_snapshot_json ?? [];
        $agentDeviceData = data_get($agentSnapshot, 'Device', data_get($agentSnapshot, 'device', []));
        $agentHealth    = data_get($extra, 'last_health', $asset->agentDevice?->last_health_json ?? []);
        $agentTone      = $asset->agentDevice ? match($asset->agentDevice->status->value) {
            'activo' => 'success', 'inactivo' => 'neutral', 'desconectado' => 'danger', default => 'neutral',
        } : 'neutral';
        $healthStatus   = data_get($agentHealth, 'overallStatus');
        $healthTone     = match($healthStatus) {
            'critical' => 'danger', 'warning' => 'warning', 'ok' => 'success', default => 'neutral',
        };

        // Software
        $allSoftware = collect(data_get($agentSnapshot, 'InstalledSoftware', data_get($agentSnapshot, 'installedSoftware', [])))
            ->map(fn ($item) => data_get($item, 'Name', data_get($item, 'name')))
            ->filter()->sort()->values();
        $topSoftware = $allSoftware->isEmpty()
            ? collect(data_get($specs, 'top_software', []))->filter()->values()
            : $allSoftware;

        // IPs y volúmenes
        $ipAddresses   = collect(data_get($specs, 'ip_addresses', data_get($agentDeviceData, 'IpAddresses', data_get($agentDeviceData, 'ipAddresses', []))))->filter()->values();
        $storageVolumes = data_get($specs, 'storage_volumes', data_get($agentDeviceData, 'StorageVolumes', data_get($agentDeviceData, 'storageVolumes', [])));

        // Métricas de storage/ram
        $totalStorageGb = data_get($specs, 'total_storage_gb') ?? data_get($agentDeviceData, 'TotalStorageGb') ?? data_get($agentDeviceData, 'totalStorageGb');
        $freeStorageGb  = data_get($specs, 'free_storage_gb') ?? data_get($agentDeviceData, 'FreeStorageGb') ?? data_get($agentDeviceData, 'freeStorageGb');
        $storageUsedPct = ($totalStorageGb > 0 && $freeStorageGb !== null) ? round((($totalStorageGb - $freeStorageGb) / $totalStorageGb) * 100) : null;
        $ramGb          = data_get($specs, 'ram_gb') ?? data_get($agentDeviceData, 'TotalMemoryGb') ?? data_get($agentDeviceData, 'totalMemoryGb');
        $ramUsedPct     = (float) (data_get($agentHealth, 'memory.usedPercent') ?? 0);

        $isOnline = $asset->agentDevice?->isOnline() ?? false;
    @endphp

    <x-ui.page-header
        :title="$asset->name"
        :description="$asset->asset_type->label() . ($asset->brand ? ' — ' . $asset->brand . ($asset->model ? ' ' . $asset->model : '') : '')"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('assets.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
            @can('asset-movement.create')
            <x-ui.button variant="secondary" size="sm" href="{{ route('asset-movements.create', ['asset_id' => $asset->id]) }}">Registrar movimiento</x-ui.button>
            @endcan
            @can('maintenance-case.create')
            <x-ui.button variant="secondary" size="sm" href="{{ route('maintenance-cases.create', ['asset_id' => $asset->id]) }}">Nuevo caso</x-ui.button>
            @endcan
            <a href="{{ route('assets.generate-ficha', $asset) }}" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                Ficha técnica
            </a>
            <a href="{{ route('assets.generate-historial', $asset) }}" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Historial
            </a>
            @can('asset.edit')
            <x-ui.button size="sm" href="{{ route('assets.edit', $asset) }}">Editar</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">

        {{-- ══ COLUMNA IZQUIERDA ══ --}}
        <div class="xl:col-span-1 space-y-4">

            {{-- Estado --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Estado del activo</h3>
                </x-slot:header>

                <div class="flex gap-2 mb-4">
                    <x-ui.badge :tone="$statusTone" :dot="true">{{ $asset->status->label() }}</x-ui.badge>
                    <x-ui.badge :tone="$conditionTone">{{ $asset->condition->label() }}</x-ui.badge>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Código interno</span>
                        <span class="font-mono font-semibold text-slate-800">{{ $asset->internal_code }}</span>
                    </div>
                    @if ($asset->patrimonial_code)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Código patrimonial</span>
                        <span class="font-mono font-medium text-slate-800">{{ $asset->patrimonial_code }}</span>
                    </div>
                    @endif
                    @if ($asset->serial_number)
                    <div class="flex justify-between">
                        <span class="text-slate-500">N.° de serie</span>
                        <span class="font-mono text-slate-700">{{ $asset->serial_number }}</span>
                    </div>
                    @endif
                    @if ($asset->purchase_date)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Fecha de compra</span>
                        <span class="text-slate-700">{{ $asset->purchase_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if ($asset->reference_value)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Valor referencial</span>
                        <span class="font-semibold text-slate-800">S/ {{ number_format($asset->reference_value, 2) }}</span>
                    </div>
                    @endif
                </div>
            </x-ui.card>

            {{-- Ubicación --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Ubicación</h3>
                </x-slot:header>

                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Unidad organizacional</p>
                        <p class="text-sm font-medium text-slate-800">{{ $asset->organizationalUnit?->name ?? '—' }}</p>
                        @if ($asset->organizationalUnit?->full_path)
                            <p class="text-xs text-slate-400 mt-0.5">{{ $asset->organizationalUnit->full_path }}</p>
                        @endif
                    </div>
                    @if ($asset->responsible)
                    <div class="pt-2 border-t border-slate-100">
                        <p class="text-xs text-slate-500 mb-2">Responsable</p>
                        <div class="flex items-center gap-2">
                            <x-ui.avatar :name="$asset->responsible->full_name" size="sm" />
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $asset->responsible->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $asset->responsible->position ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </x-ui.card>

            {{-- Agente de monitoreo --}}
            @if ($asset->agentDevice)
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Agente de monitoreo</h3>
                    <div class="flex items-center gap-2">
                        @if ($isOnline)
                        <span class="flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        @endif
                        <x-ui.badge :tone="$agentTone" :dot="!$isOnline">{{ $asset->agentDevice->status->label() }}</x-ui.badge>
                    </div>
                </x-slot:header>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">UUID</span>
                        <span class="font-mono text-xs text-slate-600 truncate max-w-[160px]">{{ $asset->agentDevice->uuid }}</span>
                    </div>
                    @if ($asset->agentDevice->last_heartbeat_at)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Último heartbeat</span>
                        <span class="{{ $isOnline ? 'text-emerald-600 font-medium' : 'text-slate-700' }}">
                            {{ $asset->agentDevice->last_heartbeat_at->diffForHumans() }}
                        </span>
                    </div>
                    @endif
                    @if ($asset->agentDevice->last_ip)
                    <div class="flex justify-between">
                        <span class="text-slate-500">IP actual</span>
                        <span class="font-mono text-slate-700">{{ $asset->agentDevice->last_ip }}</span>
                    </div>
                    @endif
                </div>
                @can('agent.view')
                <x-slot:footer>
                    <x-ui.button variant="ghost" size="xs" href="{{ route('agents.show', $asset->agentDevice) }}">
                        Ver monitor de signos vitales →
                    </x-ui.button>
                </x-slot:footer>
                @endcan
            </x-ui.card>
            @endif

            {{-- Salud del equipo (card compacto en sidebar) --}}
            @if ($agentHealth)
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Salud del equipo</h3>
                    <x-ui.badge :tone="$healthTone" :dot="true">{{ strtoupper($healthStatus ?? 'unknown') }}</x-ui.badge>
                </x-slot:header>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-slate-500">RAM usada</span>
                            <span class="font-semibold {{ $ramUsedPct > 85 ? 'text-rose-600' : ($ramUsedPct > 70 ? 'text-amber-600' : 'text-emerald-600') }}">
                                {{ $ramUsedPct > 0 ? number_format($ramUsedPct, 1) . '%' : '—' }}
                            </span>
                        </div>
                        @if ($ramUsedPct > 0)
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $ramUsedPct > 85 ? 'bg-rose-500' : ($ramUsedPct > 70 ? 'bg-amber-400' : 'bg-emerald-500') }}"
                                 style="width: {{ min($ramUsedPct, 100) }}%"></div>
                        </div>
                        @endif
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Uptime</span>
                        <span class="text-slate-700">{{ data_get($agentHealth, 'uptimeHours') ? number_format((float) data_get($agentHealth, 'uptimeHours'), 1) . ' h' : '—' }}</span>
                    </div>
                    @php $warnings = collect(data_get($agentHealth, 'warnings', []))->filter()->values(); @endphp
                    @foreach ($warnings->take(2) as $w)
                    <div class="flex items-start gap-2 bg-amber-50 border border-amber-100 rounded-xl px-2.5 py-1.5">
                        <span class="text-amber-500 text-xs shrink-0 mt-0.5">⚠</span>
                        <p class="text-xs text-amber-700">{{ $w }}</p>
                    </div>
                    @endforeach
                </div>
            </x-ui.card>
            @endif
        </div>

        {{-- ══ COLUMNA DERECHA ══ --}}
        <div class="xl:col-span-2 space-y-4">

            @if ($specs || $agentHealth)
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Datos técnicos sincronizados</h3>
                    @if (data_get($extra, 'collected_at_utc'))
                    <span class="text-xs text-slate-400">
                        {{ \Illuminate\Support\Carbon::parse(data_get($extra, 'collected_at_utc'))->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                    </span>
                    @endif
                </x-slot:header>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Hostname</span>
                            <span class="text-slate-700 text-right font-medium">{{ data_get($specs, 'hostname', data_get($agentDeviceData, 'Hostname', data_get($agentDeviceData, 'hostname'))) ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Sistema operativo</span>
                            <span class="text-slate-700 text-right text-xs">
                                {{ trim((data_get($specs, 'operating_system', data_get($agentDeviceData, 'OperatingSystem', data_get($agentDeviceData, 'operatingSystem'))) ?? '') . ' ' . (data_get($specs, 'os_version', data_get($agentDeviceData, 'OsVersion', data_get($agentDeviceData, 'osVersion'))) ?? '')) ?: '—' }}
                            </span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Arquitectura</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'os_architecture', data_get($agentDeviceData, 'OsArchitecture', data_get($agentDeviceData, 'osArchitecture'))) ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">CPU</span>
                            <span class="text-slate-700 text-right text-xs">{{ data_get($specs, 'cpu', data_get($agentDeviceData, 'ProcessorName', data_get($agentDeviceData, 'processorName'))) ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Dominio</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'domain') ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Usuario</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'user_name') ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Último arranque</span>
                            <span class="text-slate-700 text-right text-xs">
                                {{ data_get($specs, 'last_boot_time_utc') ? \Illuminate\Support\Carbon::parse(data_get($specs, 'last_boot_time_utc'))->timezone(config('app.timezone'))->format('d/m/Y H:i') : '—' }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Marca</span>
                            <span class="text-slate-700 text-right">{{ $asset->brand ?? data_get($agentDeviceData, 'Manufacturer', data_get($agentDeviceData, 'manufacturer')) ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Modelo</span>
                            <span class="text-slate-700 text-right">{{ $asset->model ?? data_get($agentDeviceData, 'Model', data_get($agentDeviceData, 'model')) ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Serie</span>
                            <span class="text-slate-700 text-right">{{ $asset->serial_number ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">BIOS</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'bios_version', data_get($agentDeviceData, 'BiosVersion', data_get($agentDeviceData, 'biosVersion'))) ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Placa madre</span>
                            <span class="text-slate-700 text-right text-xs">
                                {{ trim((data_get($specs, 'motherboard_manufacturer', data_get($agentDeviceData, 'MotherboardManufacturer', data_get($agentDeviceData, 'motherboardManufacturer'))) ?? '') . ' ' . (data_get($specs, 'motherboard_product', data_get($agentDeviceData, 'MotherboardProduct', data_get($agentDeviceData, 'motherboardProduct'))) ?? '')) ?: '—' }}
                            </span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Software detectado</span>
                            <span class="font-semibold text-sigat-600">{{ $allSoftware->count() ?: (data_get($specs, 'software_count') ?? '—') }}</span>
                        </div>
                    </div>
                </div>

                {{-- RAM y storage con barras visuales --}}
                @if ($ramGb || $totalStorageGb)
                <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 gap-4 md:grid-cols-2">
                    @if ($ramGb)
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="text-slate-500 font-medium">RAM</span>
                            <span class="text-slate-700">{{ $ramGb }} GB
                                @if ($ramUsedPct > 0)
                                <span class="font-semibold {{ $ramUsedPct > 85 ? 'text-rose-600' : ($ramUsedPct > 70 ? 'text-amber-600' : 'text-emerald-600') }}">· {{ number_format($ramUsedPct, 1) }}%</span>
                                @endif
                            </span>
                        </div>
                        @if ($ramUsedPct > 0)
                        <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all {{ $ramUsedPct > 85 ? 'bg-rose-500' : ($ramUsedPct > 70 ? 'bg-amber-400' : 'bg-emerald-500') }}"
                                 style="width: {{ min($ramUsedPct, 100) }}%"></div>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if ($totalStorageGb && $storageUsedPct !== null)
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="text-slate-500 font-medium">Disco</span>
                            <span class="text-slate-700">
                                {{ $freeStorageGb }} GB libre / {{ $totalStorageGb }} GB
                                <span class="font-semibold {{ $storageUsedPct > 85 ? 'text-rose-600' : ($storageUsedPct > 70 ? 'text-amber-600' : 'text-emerald-600') }}">· {{ $storageUsedPct }}%</span>
                            </span>
                        </div>
                        <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $storageUsedPct > 85 ? 'bg-rose-500' : ($storageUsedPct > 70 ? 'bg-amber-400' : 'bg-emerald-500') }}"
                                 style="width: {{ min($storageUsedPct, 100) }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- IPs y Volúmenes --}}
                <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold tracking-wide text-slate-400 uppercase mb-2">IPs detectadas</p>
                        @if ($ipAddresses->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach ($ipAddresses as $ip)
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-mono text-slate-700">{{ $ip }}</span>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-slate-400">Sin IPs registradas.</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs font-semibold tracking-wide text-slate-400 uppercase mb-2">Volúmenes</p>
                        @if (!empty($storageVolumes))
                        <div class="space-y-2">
                            @foreach ($storageVolumes as $vol)
                            @php $volUsed = (int) (data_get($vol, 'UsedPercent', data_get($vol, 'usedPercent', 0))); @endphp
                            <div class="rounded-xl border border-slate-200 p-3">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="font-medium text-slate-800 text-sm">
                                        {{ data_get($vol, 'Name', data_get($vol, 'name', '—')) }}
                                        @if (data_get($vol, 'Label', data_get($vol, 'label')))
                                        <span class="text-slate-400 font-normal">· {{ data_get($vol, 'Label', data_get($vol, 'label')) }}</span>
                                        @endif
                                    </p>
                                    <span class="text-xs font-semibold {{ $volUsed > 85 ? 'text-rose-600' : ($volUsed > 70 ? 'text-amber-600' : 'text-emerald-600') }}">{{ $volUsed }}%</span>
                                </div>
                                <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden mb-1.5">
                                    <div class="h-full rounded-full {{ $volUsed > 85 ? 'bg-rose-500' : ($volUsed > 70 ? 'bg-amber-400' : 'bg-emerald-500') }}"
                                         style="width: {{ min($volUsed, 100) }}%"></div>
                                </div>
                                <p class="text-xs text-slate-500">
                                    {{ data_get($vol, 'FileSystem', data_get($vol, 'fileSystem', '—')) }}
                                    · Libre {{ data_get($vol, 'FreeGb', data_get($vol, 'freeGb', '—')) }} GB
                                    / {{ data_get($vol, 'TotalGb', data_get($vol, 'totalGb', '—')) }} GB
                                </p>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-slate-400">Sin volúmenes.</p>
                        @endif
                    </div>
                </div>

                {{-- Software instalado (lista completa con búsqueda) --}}
                @if ($topSoftware->isNotEmpty())
                <div class="mt-4 pt-4 border-t border-slate-100" x-data="{ swQ: '' }">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold tracking-wide text-slate-400 uppercase">
                            Software instalado ({{ $topSoftware->count() }})
                        </p>
                        <input type="text" x-model="swQ" placeholder="Buscar programa..."
                               class="text-xs rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 focus:outline-none focus:border-sigat-400 focus:bg-white w-44 transition-colors">
                    </div>
                    <div class="max-h-56 overflow-y-auto grid grid-cols-1 gap-px sm:grid-cols-2 pr-1">
                        @foreach ($topSoftware as $sw)
                        <div x-show="!swQ || '{{ addslashes(strtolower($sw)) }}'.includes(swQ.toLowerCase())"
                             class="flex items-center gap-2 py-1 px-2 rounded-lg hover:bg-slate-50 group">
                            <span class="w-1.5 h-1.5 rounded-full bg-sigat-300 shrink-0"></span>
                            <span class="text-xs text-slate-600 group-hover:text-slate-900 truncate" title="{{ $sw }}">{{ $sw }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs text-slate-400">Sin programas detectados en el snapshot.</p>
                </div>
                @endif
            </x-ui.card>
            @endif

            {{-- Tabs: Historial, Casos, Documentos --}}
            <x-ui.tabs active="historial" :tabs="['historial' => 'Historial', 'casos' => 'Casos de mantenimiento', 'documentos' => 'Documentos']">

                <x-ui.tab-panel name="historial" x-show="activeTab === 'historial'">
                    @if ($asset->movements->isEmpty())
                        <x-ui.empty-state title="Sin movimientos" description="Este activo no tiene movimientos registrados." />
                    @else
                        <div class="space-y-2">
                            @foreach ($asset->movements as $movement)
                            @php
                                $movTone = match($movement->movement_type->value) {
                                    'asignacion' => 'success', 'traslado' => 'info', 'devolucion' => 'warning',
                                    'baja' => 'danger', 'ingreso' => 'primary', 'prestamo' => 'neutral', default => 'neutral',
                                };
                            @endphp
                            <div class="flex gap-3 p-3 rounded-xl bg-slate-50">
                                <x-ui.badge :tone="$movTone" size="sm">{{ $movement->movement_type->label() }}</x-ui.badge>
                                <div class="flex-1 min-w-0">
                                    @if ($movement->destination_unit)
                                        <p class="text-sm text-slate-700">→ {{ $movement->destination_unit }}</p>
                                    @endif
                                    @if ($movement->notes)
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $movement->notes }}</p>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-400 shrink-0">{{ $movement->created_at->format('d/m/Y') }}</span>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.tab-panel>

                <x-ui.tab-panel name="casos" x-show="activeTab === 'casos'">
                    @if ($asset->maintenanceCases->isEmpty())
                        <x-ui.empty-state title="Sin casos" description="Este activo no tiene casos de mantenimiento." />
                    @else
                        <div class="space-y-2">
                            @foreach ($asset->maintenanceCases as $case)
                            @php
                                $caseTone = match($case->status->value) {
                                    'pendiente' => 'neutral', 'en_progreso' => 'warning',
                                    'completado' => 'success', 'cancelado' => 'danger', default => 'neutral',
                                };
                            @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                                <div>
                                    <p class="text-sm font-medium text-slate-800">{{ $case->code }}</p>
                                    <p class="text-xs text-slate-400">{{ $case->maintenance_type->label() }} &middot; {{ $case->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-ui.badge :tone="$caseTone" size="sm">{{ $case->status->label() }}</x-ui.badge>
                                    @can('maintenance-case.view')
                                    <x-ui.button variant="ghost" size="xs" href="{{ route('maintenance-cases.show', $case) }}">Ver</x-ui.button>
                                    @endcan
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.tab-panel>

                <x-ui.tab-panel name="documentos" x-show="activeTab === 'documentos'">
                    @if ($asset->documents->isEmpty())
                        <x-ui.empty-state title="Sin documentos" description="No hay documentos asociados a este activo." />
                    @else
                        <div class="space-y-2">
                            @foreach ($asset->documents as $doc)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                                <div>
                                    <p class="text-sm font-medium text-slate-800">{{ $doc->title }}</p>
                                    <p class="text-xs text-slate-400">{{ $doc->document_type->label() }} &middot; {{ $doc->created_at->format('d/m/Y') }}</p>
                                </div>
                                @can('document.view')
                                <x-ui.button variant="ghost" size="xs" href="{{ route('documents.download', $doc) }}">Descargar</x-ui.button>
                                @endcan
                            </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.tab-panel>
            </x-ui.tabs>
        </div>
    </div>

</x-layouts.app>

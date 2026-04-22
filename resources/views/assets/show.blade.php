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

        $specs = $asset->specs_json ?? [];
        $extra = $asset->extra_json ?? [];
        $agentSnapshot = $asset->agentDevice?->last_snapshot_json ?? [];
        $agentDeviceData = data_get($agentSnapshot, 'Device', data_get($agentSnapshot, 'device', []));
        $agentHealth = data_get($extra, 'last_health', $asset->agentDevice?->last_health_json ?? []);
        $agentTone = $asset->agentDevice ? match($asset->agentDevice->status->value) {
            'activo' => 'success', 'inactivo' => 'neutral', 'desconectado' => 'danger', default => 'neutral',
        } : 'neutral';
        $healthTone = match(data_get($agentHealth, 'overallStatus')) {
            'critical' => 'danger',
            'warning' => 'warning',
            'ok' => 'success',
            default => 'neutral',
        };
        $topSoftware = collect(data_get($specs, 'top_software', []))->filter()->values();
        if ($topSoftware->isEmpty()) {
            $topSoftware = collect(data_get($agentSnapshot, 'InstalledSoftware', data_get($agentSnapshot, 'installedSoftware', [])))
                ->map(fn ($item) => data_get($item, 'Name', data_get($item, 'name')))
                ->filter()
                ->take(8)
                ->values();
        }
        $ipAddresses = data_get($specs, 'ip_addresses', data_get($agentDeviceData, 'IpAddresses', data_get($agentDeviceData, 'ipAddresses', [])));
        $storageVolumes = data_get($specs, 'storage_volumes', data_get($agentDeviceData, 'StorageVolumes', data_get($agentDeviceData, 'storageVolumes', [])));
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
            <a href="{{ route('assets.generate-ficha', $asset) }}"
               target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                Ficha técnica
            </a>
            <a href="{{ route('assets.generate-historial', $asset) }}"
               target="_blank"
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

        <div class="xl:col-span-1 space-y-4">

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Estado del activo</h3>
                </x-slot:header>

                <div class="flex gap-2 mb-4">
                    <x-ui.badge :tone="$statusTone" :dot="true">{{ $asset->status->label() }}</x-ui.badge>
                    <x-ui.badge :tone="$conditionTone">{{ $asset->condition->label() }}</x-ui.badge>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Código interno</span>
                        <span class="font-mono font-semibold text-slate-800">{{ $asset->internal_code }}</span>
                    </div>
                    @if ($asset->patrimonial_code)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Código patrimonial</span>
                        <span class="font-mono font-medium text-slate-800">{{ $asset->patrimonial_code }}</span>
                    </div>
                    @endif
                    @if ($asset->serial_number)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">N.° de serie</span>
                        <span class="font-mono text-slate-700">{{ $asset->serial_number }}</span>
                    </div>
                    @endif
                    @if ($asset->purchase_date)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Fecha de compra</span>
                        <span class="text-slate-700">{{ $asset->purchase_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if ($asset->reference_value)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Valor referencial</span>
                        <span class="font-semibold text-slate-800">S/ {{ number_format($asset->reference_value, 2) }}</span>
                    </div>
                    @endif
                </div>
            </x-ui.card>

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

            @if ($asset->agentDevice)
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Agente de monitoreo</h3>
                    <x-ui.badge :tone="$agentTone" :dot="true">{{ $asset->agentDevice->status->label() }}</x-ui.badge>
                </x-slot:header>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">UUID</span>
                        <span class="font-mono text-xs text-slate-600 truncate max-w-[160px]">{{ $asset->agentDevice->uuid }}</span>
                    </div>
                    @if ($asset->agentDevice->last_heartbeat_at)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Último heartbeat</span>
                        <span class="text-slate-700">{{ $asset->agentDevice->last_heartbeat_at->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>
                @can('agent.view')
                <x-slot:footer>
                    <x-ui.button variant="ghost" size="xs" href="{{ route('agents.show', $asset->agentDevice) }}">Ver detalles del agente</x-ui.button>
                </x-slot:footer>
                @endcan
            </x-ui.card>
            @endif
        </div>

        <div class="xl:col-span-2 space-y-4">
            @if ($specs || $agentHealth)
            <x-ui.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Datos técnicos sincronizados</h3></x-slot:header>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Hostname</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'hostname', data_get($agentDeviceData, 'Hostname', data_get($agentDeviceData, 'hostname'))) ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Sistema operativo</span>
                            <span class="text-slate-700 text-right">
                                {{ trim((data_get($specs, 'operating_system', data_get($agentDeviceData, 'OperatingSystem', data_get($agentDeviceData, 'operatingSystem'))) ?? '') . ' ' . (data_get($specs, 'os_version', data_get($agentDeviceData, 'OsVersion', data_get($agentDeviceData, 'osVersion'))) ?? '')) ?: '—' }}
                            </span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Arquitectura</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'os_architecture', data_get($agentDeviceData, 'OsArchitecture', data_get($agentDeviceData, 'osArchitecture'))) ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">CPU</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'cpu', data_get($agentDeviceData, 'ProcessorName', data_get($agentDeviceData, 'processorName'))) ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">RAM</span>
                            <span class="text-slate-700 text-right">
                                {{ data_get($specs, 'ram_gb', data_get($agentDeviceData, 'TotalMemoryGb', data_get($agentDeviceData, 'totalMemoryGb'))) !== null ? data_get($specs, 'ram_gb', data_get($agentDeviceData, 'TotalMemoryGb', data_get($agentDeviceData, 'totalMemoryGb'))) . ' GB' : '—' }}
                            </span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Almacenamiento total</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'total_storage_gb', data_get($agentDeviceData, 'TotalStorageGb', data_get($agentDeviceData, 'totalStorageGb'))) !== null ? data_get($specs, 'total_storage_gb', data_get($agentDeviceData, 'TotalStorageGb', data_get($agentDeviceData, 'totalStorageGb'))) . ' GB' : '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Almacenamiento libre</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'free_storage_gb', data_get($agentDeviceData, 'FreeStorageGb', data_get($agentDeviceData, 'freeStorageGb'))) !== null ? data_get($specs, 'free_storage_gb', data_get($agentDeviceData, 'FreeStorageGb', data_get($agentDeviceData, 'freeStorageGb'))) . ' GB' : '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Dominio</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'domain') ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Usuario</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'user_name') ?? '—' }}</span>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
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
                            <span class="text-slate-700 text-right">{{ $asset->serial_number ?? data_get($agentDeviceData, 'SerialNumber', data_get($agentDeviceData, 'serialNumber')) ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">BIOS</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'bios_version', data_get($agentDeviceData, 'BiosVersion', data_get($agentDeviceData, 'biosVersion'))) ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Placa madre</span>
                            <span class="text-slate-700 text-right">
                                {{ trim((data_get($specs, 'motherboard_manufacturer', data_get($agentDeviceData, 'MotherboardManufacturer', data_get($agentDeviceData, 'motherboardManufacturer'))) ?? '') . ' ' . (data_get($specs, 'motherboard_product', data_get($agentDeviceData, 'MotherboardProduct', data_get($agentDeviceData, 'motherboardProduct'))) ?? '')) ?: '—' }}
                            </span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Serie placa madre</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'motherboard_serial_number', data_get($agentDeviceData, 'MotherboardSerialNumber', data_get($agentDeviceData, 'motherboardSerialNumber'))) ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Último arranque</span>
                            <span class="text-slate-700 text-right">
                                {{ data_get($specs, 'last_boot_time_utc', data_get($agentDeviceData, 'LastBootTimeUtc', data_get($agentDeviceData, 'lastBootTimeUtc'))) ? \Illuminate\Support\Carbon::parse(data_get($specs, 'last_boot_time_utc', data_get($agentDeviceData, 'LastBootTimeUtc', data_get($agentDeviceData, 'lastBootTimeUtc'))))->timezone(config('app.timezone'))->format('d/m/Y H:i') : '—' }}
                            </span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Snapshot</span>
                            <span class="text-slate-700 text-right">
                                {{ data_get($extra, 'collected_at_utc') ? \Illuminate\Support\Carbon::parse(data_get($extra, 'collected_at_utc'))->timezone(config('app.timezone'))->format('d/m/Y H:i') : '—' }}
                            </span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Software detectado</span>
                            <span class="text-slate-700 text-right">{{ data_get($specs, 'software_count', count(data_get($agentSnapshot, 'InstalledSoftware', data_get($agentSnapshot, 'installedSoftware', [])))) ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase mb-2">IPs detectadas</p>
                        @if (!empty($ipAddresses))
                            <div class="flex flex-wrap gap-2">
                                @foreach ($ipAddresses as $ip)
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-mono text-slate-700">{{ $ip }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-500">Sin IPs registradas.</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase mb-2">Volúmenes de almacenamiento</p>
                        @if (!empty($storageVolumes))
                            <div class="space-y-2 text-sm">
                                @foreach ($storageVolumes as $volume)
                                    <div class="rounded-xl border border-slate-200 p-3">
                                        <p class="font-medium text-slate-800">
                                            {{ data_get($volume, 'Name', data_get($volume, 'name', '—')) }}
                                            @if (data_get($volume, 'Label', data_get($volume, 'label')))
                                                <span class="text-slate-500">· {{ data_get($volume, 'Label', data_get($volume, 'label')) }}</span>
                                            @endif
                                        </p>
                                        <p class="text-slate-600">
                                            {{ data_get($volume, 'FileSystem', data_get($volume, 'fileSystem', '—')) }}
                                            · Total {{ data_get($volume, 'TotalGb', data_get($volume, 'totalGb', '—')) }} GB
                                            · Libre {{ data_get($volume, 'FreeGb', data_get($volume, 'freeGb', '—')) }} GB
                                            · Usado {{ data_get($volume, 'UsedPercent', data_get($volume, 'usedPercent', '—')) }}%
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-slate-500">Sin volúmenes registrados.</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase mb-2">Salud del equipo</p>
                        @if ($agentHealth)
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center gap-2">
                                    <x-ui.badge :tone="$healthTone" :dot="true">{{ strtoupper(data_get($agentHealth, 'overallStatus', 'unknown')) }}</x-ui.badge>
                                </div>
                                <p class="text-slate-700">Uptime: {{ data_get($agentHealth, 'uptimeHours') ? number_format((float) data_get($agentHealth, 'uptimeHours'), 2) . ' h' : '—' }}</p>
                                <p class="text-slate-700">RAM usada: {{ data_get($agentHealth, 'memory.usedPercent') !== null ? number_format((float) data_get($agentHealth, 'memory.usedPercent'), 2) . '%' : '—' }}</p>
                                @php $warnings = collect(data_get($agentHealth, 'warnings', []))->filter()->values(); @endphp
                                @if ($warnings->isNotEmpty())
                                    @foreach ($warnings as $warning)
                                        <p class="text-slate-700">{{ $warning }}</p>
                                    @endforeach
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-slate-500">Sin estado de salud registrado.</p>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase mb-2">Últimos programas detectados</p>
                    @if ($topSoftware->isEmpty())
                        <p class="text-sm text-slate-500">Sin programas listados en el snapshot sincronizado.</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach ($topSoftware as $program)
                                <span class="inline-flex items-center rounded-full bg-sigat-50 px-2.5 py-1 text-xs text-sigat-700">{{ $program }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </x-ui.card>
            @endif

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

<x-layouts.app title="Detalle Agente" moduleLabel="Agentes" pageTitle="Detalle de Agente">

    @php
        $agentTone = match($agentDevice->status->value) {
            'activo' => 'success', 'inactivo' => 'neutral', 'desconectado' => 'danger', default => 'neutral',
        };

        $snap = $agentDevice->last_snapshot_json ?? [];
        $health = $agentDevice->last_health_json ?? [];

        $deviceData = data_get($snap, 'Device', data_get($snap, 'device', []));
        $software = data_get($snap, 'InstalledSoftware', data_get($snap, 'installedSoftware', [])) ?? [];
        $binding = data_get($snap, 'AdministrativeBinding', data_get($snap, 'administrativeBinding', []));

        $hostname = data_get($deviceData, 'Hostname', data_get($deviceData, 'hostname'));
        $operatingSystem = data_get($deviceData, 'OperatingSystem', data_get($deviceData, 'operatingSystem'));
        $osVersion = data_get($deviceData, 'OsVersion', data_get($deviceData, 'osVersion'));
        $osArchitecture = data_get($deviceData, 'OsArchitecture', data_get($deviceData, 'osArchitecture'));
        $processorName = data_get($deviceData, 'ProcessorName', data_get($deviceData, 'processorName'));
        $totalMemoryGb = data_get($deviceData, 'TotalMemoryGb', data_get($deviceData, 'totalMemoryGb'));
        $manufacturer = data_get($deviceData, 'Manufacturer', data_get($deviceData, 'manufacturer'));
        $model = data_get($deviceData, 'Model', data_get($deviceData, 'model'));
        $serialNumber = data_get($deviceData, 'SerialNumber', data_get($deviceData, 'serialNumber'));
        $biosVersion = data_get($deviceData, 'BiosVersion', data_get($deviceData, 'biosVersion'));
        $domain = data_get($deviceData, 'Domain', data_get($deviceData, 'domain'));
        $userName = data_get($deviceData, 'UserName', data_get($deviceData, 'userName'));
        $collectedAtUtc = data_get($snap, 'CollectedAtUtc', data_get($snap, 'collectedAtUtc'));
        $lastBootTimeUtc = data_get($deviceData, 'LastBootTimeUtc', data_get($deviceData, 'lastBootTimeUtc'));
        $ipAddresses = data_get($deviceData, 'IpAddresses', data_get($deviceData, 'ipAddresses', [])) ?? [];
        $storageVolumes = data_get($deviceData, 'StorageVolumes', data_get($deviceData, 'storageVolumes', [])) ?? [];
        $totalStorageGb = data_get($deviceData, 'TotalStorageGb', data_get($deviceData, 'totalStorageGb'));
        $freeStorageGb = data_get($deviceData, 'FreeStorageGb', data_get($deviceData, 'freeStorageGb'));
        $motherboardManufacturer = data_get($deviceData, 'MotherboardManufacturer', data_get($deviceData, 'motherboardManufacturer'));
        $motherboardProduct = data_get($deviceData, 'MotherboardProduct', data_get($deviceData, 'motherboardProduct'));
        $motherboardSerialNumber = data_get($deviceData, 'MotherboardSerialNumber', data_get($deviceData, 'motherboardSerialNumber'));
        $topSoftware = collect($software)
            ->map(fn ($item) => data_get($item, 'Name', data_get($item, 'name')))
            ->filter()
            ->take(8)
            ->values();

        $healthTone = match(data_get($health, 'overallStatus')) {
            'critical' => 'danger',
            'warning' => 'warning',
            'ok' => 'success',
            default => 'neutral',
        };
    @endphp

    <x-ui.page-header
        :title="$agentDevice->hostname ?? $agentDevice->uuid"
        :description="$agentDevice->asset?->name ?? 'Sin activo vinculado'"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('agents.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">

        <div class="xl:col-span-1 space-y-4">

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Estado del agente</h3>
                    <x-ui.badge :tone="$agentTone" :dot="true">{{ $agentDevice->status->label() }}</x-ui.badge>
                </x-slot:header>

                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-slate-500 mb-0.5">UUID</p>
                        <p class="font-mono text-xs text-slate-700 break-all">{{ $agentDevice->uuid }}</p>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Versión</span>
                        <span class="font-medium text-slate-800">{{ $agentDevice->agent_version ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">IP</span>
                        <span class="font-mono text-slate-700 text-right">{{ $agentDevice->last_ip ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Último heartbeat</span>
                        <span class="text-slate-700 text-right">{{ $agentDevice->last_heartbeat_at ? $agentDevice->last_heartbeat_at->diffForHumans() : 'Nunca' }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Fecha heartbeat</span>
                        <span class="text-slate-700 text-right">{{ $agentDevice->last_heartbeat_at?->format('d/m/Y H:i') ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Registrado</span>
                        <span class="text-slate-700 text-right">{{ $agentDevice->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </x-ui.card>

            @if ($health)
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Salud del equipo</h3>
                    <x-ui.badge :tone="$healthTone" :dot="true">{{ strtoupper(data_get($health, 'overallStatus', 'unknown')) }}</x-ui.badge>
                </x-slot:header>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Generado</span>
                        <span class="text-slate-700 text-right">
                            {{ data_get($health, 'generatedAtUtc') ? \Illuminate\Support\Carbon::parse(data_get($health, 'generatedAtUtc'))->timezone(config('app.timezone'))->format('d/m/Y H:i') : '—' }}
                        </span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Uptime</span>
                        <span class="text-slate-700 text-right">{{ data_get($health, 'uptimeHours') ? number_format((float) data_get($health, 'uptimeHours'), 2) . ' h' : '—' }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">RAM usada</span>
                        <span class="text-slate-700 text-right">{{ data_get($health, 'memory.usedPercent') !== null ? number_format((float) data_get($health, 'memory.usedPercent'), 2) . '%' : '—' }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-1">Advertencias</p>
                        @php $warnings = collect(data_get($health, 'warnings', []))->filter()->values(); @endphp
                        @if ($warnings->isEmpty())
                            <p class="text-sm text-slate-700">Sin alertas registradas.</p>
                        @else
                            <div class="space-y-1">
                                @foreach ($warnings as $warning)
                                    <p class="text-sm text-slate-700">{{ $warning }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </x-ui.card>
            @endif

            @if ($agentDevice->asset)
            <x-ui.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Activo vinculado</h3></x-slot:header>
                <div class="space-y-2 text-sm">
                    <p class="font-medium text-slate-800">{{ $agentDevice->asset->name }}</p>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Código</span>
                        <span class="font-mono text-slate-700">{{ $agentDevice->asset->internal_code }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tipo</span>
                        <span class="text-slate-700">{{ $agentDevice->asset->asset_type->label() }}</span>
                    </div>
                </div>
                @can('asset.view')
                <x-slot:footer>
                    <x-ui.button variant="ghost" size="xs" href="{{ route('assets.show', $agentDevice->asset) }}">Ver activo →</x-ui.button>
                </x-slot:footer>
                @endcan
            </x-ui.card>
            @endif
        </div>

        <div class="xl:col-span-2 space-y-4">
            @if ($agentDevice->last_snapshot_json)
            <x-ui.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Último snapshot</h3></x-slot:header>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Recolectado</span>
                            <span class="text-slate-700 text-right">
                                {{ $collectedAtUtc ? \Illuminate\Support\Carbon::parse($collectedAtUtc)->timezone(config('app.timezone'))->format('d/m/Y H:i') : '—' }}
                            </span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Hostname</span>
                            <span class="text-slate-700 text-right">{{ $hostname ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Sistema operativo</span>
                            <span class="text-slate-700 text-right">{{ trim(($operatingSystem ?? '') . ' ' . ($osVersion ?? '')) ?: '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Arquitectura</span>
                            <span class="text-slate-700 text-right">{{ $osArchitecture ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">CPU</span>
                            <span class="text-slate-700 text-right">{{ $processorName ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">RAM</span>
                            <span class="text-slate-700 text-right">{{ $totalMemoryGb !== null ? $totalMemoryGb . ' GB' : '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Almacenamiento total</span>
                            <span class="text-slate-700 text-right">{{ $totalStorageGb !== null ? $totalStorageGb . ' GB' : '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Almacenamiento libre</span>
                            <span class="text-slate-700 text-right">{{ $freeStorageGb !== null ? $freeStorageGb . ' GB' : '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Software detectado</span>
                            <span class="text-slate-700 text-right">{{ is_countable($software) ? count($software) : 0 }}</span>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Fabricante</span>
                            <span class="text-slate-700 text-right">{{ $manufacturer ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Modelo</span>
                            <span class="text-slate-700 text-right">{{ $model ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Serie</span>
                            <span class="text-slate-700 text-right">{{ $serialNumber ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">BIOS</span>
                            <span class="text-slate-700 text-right">{{ $biosVersion ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Placa madre</span>
                            <span class="text-slate-700 text-right">{{ trim(($motherboardManufacturer ?? '') . ' ' . ($motherboardProduct ?? '')) ?: '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Serie placa madre</span>
                            <span class="text-slate-700 text-right">{{ $motherboardSerialNumber ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Dominio</span>
                            <span class="text-slate-700 text-right">{{ $domain ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Usuario</span>
                            <span class="text-slate-700 text-right">{{ $userName ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Último arranque</span>
                            <span class="text-slate-700 text-right">
                                {{ $lastBootTimeUtc ? \Illuminate\Support\Carbon::parse($lastBootTimeUtc)->timezone(config('app.timezone'))->format('d/m/Y H:i') : '—' }}
                            </span>
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
                        <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase mb-2">Binding administrativo</p>
                        <div class="space-y-1 text-sm">
                            <p class="text-slate-700">AssetCode: {{ data_get($binding, 'AssetCode', data_get($binding, 'assetCode')) ?? '—' }}</p>
                            <p class="text-slate-700">Unidad orgánica ID: {{ data_get($binding, 'OrganizationalUnitId', data_get($binding, 'organizationalUnitId')) ?? '—' }}</p>
                            <p class="text-slate-700">Responsable ID: {{ data_get($binding, 'ResponsibleEmployeeId', data_get($binding, 'responsibleEmployeeId')) ?? '—' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase mb-2">Últimos programas detectados</p>
                    @if ($topSoftware->isEmpty())
                        <p class="text-sm text-slate-500">Sin programas listados en el snapshot.</p>
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

            <x-ui.data-table>
                <x-slot:toolbar>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900">Historial de sincronizaciones</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $agentDevice->syncs->count() }} sync(s)</p>
                    </div>
                </x-slot:toolbar>

                <x-slot:head>
                    <x-ui.th>Tipo</x-ui.th>
                    <x-ui.th>Estado</x-ui.th>
                    <x-ui.th>IP</x-ui.th>
                    <x-ui.th>Versión</x-ui.th>
                    <x-ui.th>Fecha</x-ui.th>
                </x-slot:head>

                @forelse ($agentDevice->syncs->take(50) as $sync)
                @php
                    $syncStatus = $sync->status?->value ?? (string) $sync->status;
                    $syncType = $sync->sync_type?->value ?? (string) $sync->sync_type;
                    $syncTone = match($syncStatus) {
                        'procesado' => 'success', 'recibido' => 'neutral', 'error' => 'danger', default => 'neutral',
                    };
                    $syncPayload = $sync->payload_json ?? [];
                    $syncIp = data_get($syncPayload, 'last_ip', data_get($syncPayload, 'lastIp'));
                    $syncVersion = data_get($syncPayload, 'agent_version', data_get($syncPayload, 'agentVersion'));
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <x-ui.td>
                        <x-ui.badge tone="{{ $syncType === 'heartbeat' ? 'neutral' : ($syncType === 'snapshot' ? 'info' : 'primary') }}" size="sm">
                            {{ $sync->sync_type->label() }}
                        </x-ui.badge>
                    </x-ui.td>
                    <x-ui.td><x-ui.badge :tone="$syncTone" size="sm">{{ $sync->status->label() }}</x-ui.badge></x-ui.td>
                    <x-ui.td><span class="font-mono text-sm text-slate-600">{{ $syncIp ?? '—' }}</span></x-ui.td>
                    <x-ui.td><span class="text-sm text-slate-600">{{ $syncVersion ?? '—' }}</span></x-ui.td>
                    <x-ui.td><span class="text-sm text-slate-500">{{ $sync->created_at->format('d/m/Y H:i') }}</span></x-ui.td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-slate-400 text-sm">Sin sincronizaciones registradas.</td>
                </tr>
                @endforelse
            </x-ui.data-table>
        </div>
    </div>

</x-layouts.app>

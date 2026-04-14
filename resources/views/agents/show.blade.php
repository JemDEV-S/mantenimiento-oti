<x-layouts.app title="Detalle Agente" moduleLabel="Agentes" pageTitle="Detalle de Agente">

    @php
        $agentTone = match($agentDevice->status->value) {
            'activo' => 'success', 'inactivo' => 'neutral', 'desconectado' => 'danger', default => 'neutral',
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

        {{-- Sidebar info --}}
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
                    <div class="flex justify-between">
                        <span class="text-slate-500">Versión</span>
                        <span class="font-medium text-slate-800">{{ $agentDevice->agent_version ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">IP</span>
                        <span class="font-mono text-slate-700">{{ $agentDevice->last_ip ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Último heartbeat</span>
                        <span class="text-slate-700">{{ $agentDevice->last_heartbeat_at ? $agentDevice->last_heartbeat_at->diffForHumans() : 'Nunca' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Registrado</span>
                        <span class="text-slate-700">{{ $agentDevice->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </x-ui.card>

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

            {{-- Last snapshot summary --}}
            @if ($agentDevice->last_snapshot_json)
            <x-ui.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Último snapshot</h3></x-slot:header>
                @php $snap = $agentDevice->last_snapshot_json; @endphp
                <div class="space-y-2 text-sm">
                    @if (isset($snap['os']))
                    <div class="flex justify-between">
                        <span class="text-slate-500">OS</span>
                        <span class="text-slate-700 text-right">{{ $snap['os'] }}</span>
                    </div>
                    @endif
                    @if (isset($snap['cpu']))
                    <div class="flex justify-between">
                        <span class="text-slate-500">CPU</span>
                        <span class="text-slate-700 text-right max-w-[180px] truncate">{{ $snap['cpu'] }}</span>
                    </div>
                    @endif
                    @if (isset($snap['ram_gb']))
                    <div class="flex justify-between">
                        <span class="text-slate-500">RAM</span>
                        <span class="text-slate-700">{{ $snap['ram_gb'] }} GB</span>
                    </div>
                    @endif
                    @if (isset($snap['disk_gb']))
                    <div class="flex justify-between">
                        <span class="text-slate-500">Disco</span>
                        <span class="text-slate-700">{{ $snap['disk_gb'] }} GB</span>
                    </div>
                    @endif
                </div>
            </x-ui.card>
            @endif
        </div>

        {{-- Sync history --}}
        <div class="xl:col-span-2">
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
                    $syncTone = match($sync->status) {
                        'exitoso' => 'success', 'pendiente' => 'neutral', 'error' => 'danger', default => 'neutral',
                    };
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <x-ui.td>
                        <x-ui.badge tone="{{ $sync->sync_type === 'heartbeat' ? 'neutral' : ($sync->sync_type === 'snapshot' ? 'info' : 'primary') }}" size="sm">
                            {{ $sync->sync_type }}
                        </x-ui.badge>
                    </x-ui.td>
                    <x-ui.td><x-ui.badge :tone="$syncTone" size="sm">{{ $sync->status }}</x-ui.badge></x-ui.td>
                    <x-ui.td><span class="font-mono text-sm text-slate-600">{{ $sync->ip_address ?? '—' }}</span></x-ui.td>
                    <x-ui.td><span class="text-sm text-slate-600">{{ $sync->agent_version ?? '—' }}</span></x-ui.td>
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

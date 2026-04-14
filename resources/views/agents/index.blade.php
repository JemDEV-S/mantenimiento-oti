<x-layouts.app title="Agentes de Monitoreo" moduleLabel="Agentes" pageTitle="Agentes de Monitoreo">

    <x-ui.page-header
        title="Agentes de monitoreo"
        description="Dispositivos de software instalados en los equipos para sincronización automática."
    >
    </x-ui.page-header>

    {{-- Filters --}}
    <form method="GET" action="{{ route('agents.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="w-40">
            <select name="status" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todos los estados</option>
                @foreach (\App\Enums\AgentDeviceStatus::cases() as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <x-ui.button type="submit" variant="secondary" size="sm">Filtrar</x-ui.button>
        @if (request('status'))
            <x-ui.button variant="ghost" size="sm" href="{{ route('agents.index') }}">Limpiar</x-ui.button>
        @endif
    </form>

    <x-ui.data-table>
        <x-slot:toolbar>
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Dispositivos registrados</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $devices->total() }} agente(s)</p>
            </div>
        </x-slot:toolbar>

        <x-slot:head>
            <x-ui.th>Dispositivo</x-ui.th>
            <x-ui.th>Activo vinculado</x-ui.th>
            <x-ui.th>Estado</x-ui.th>
            <x-ui.th>Versión</x-ui.th>
            <x-ui.th>IP</x-ui.th>
            <x-ui.th>Último heartbeat</x-ui.th>
            <x-ui.th></x-ui.th>
        </x-slot:head>

        @forelse ($devices as $device)
        @php
            $agentTone = match($device->status->value) {
                'activo' => 'success', 'inactivo' => 'neutral', 'desconectado' => 'danger', default => 'neutral',
            };
        @endphp
        <tr class="hover:bg-slate-50/50 transition-colors">
            <x-ui.td>
                <div>
                    <p class="font-medium text-slate-900">{{ $device->hostname ?? $device->uuid }}</p>
                    <p class="font-mono text-xs text-slate-400">{{ $device->uuid }}</p>
                </div>
            </x-ui.td>
            <x-ui.td>
                @if ($device->asset)
                    @can('asset.view')
                    <a href="{{ route('assets.show', $device->asset) }}" class="text-sm font-medium text-sigat-600 hover:text-sigat-700">{{ $device->asset->name }}</a>
                    @else
                    <span class="text-sm text-slate-700">{{ $device->asset->name }}</span>
                    @endcan
                    <p class="text-xs text-slate-400">{{ $device->asset->internal_code }}</p>
                @else
                    <span class="text-xs text-slate-400">—</span>
                @endif
            </x-ui.td>
            <x-ui.td><x-ui.badge :tone="$agentTone" :dot="true">{{ $device->status->label() }}</x-ui.badge></x-ui.td>
            <x-ui.td><span class="text-sm text-slate-600">{{ $device->agent_version ?? '—' }}</span></x-ui.td>
            <x-ui.td><span class="font-mono text-sm text-slate-600">{{ $device->last_ip ?? '—' }}</span></x-ui.td>
            <x-ui.td>
                <span class="text-sm text-slate-500">
                    {{ $device->last_heartbeat_at ? $device->last_heartbeat_at->diffForHumans() : 'Nunca' }}
                </span>
            </x-ui.td>
            <x-ui.td>
                <x-ui.button variant="ghost" size="xs" href="{{ route('agents.show', $device) }}">Ver detalle</x-ui.button>
            </x-ui.td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="py-16">
                <x-ui.empty-state title="Sin agentes registrados" description="Los agentes se registran automáticamente desde los equipos." />
            </td>
        </tr>
        @endforelse

        <x-slot:pagination>{{ $devices->withQueryString()->links() }}</x-slot:pagination>
    </x-ui.data-table>

</x-layouts.app>

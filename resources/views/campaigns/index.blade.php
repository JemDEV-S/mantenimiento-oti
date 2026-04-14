<x-layouts.app title="Campañas de Mantenimiento" moduleLabel="Mantenimiento" pageTitle="Campañas">

    <x-ui.page-header
        title="Campañas de mantenimiento"
        description="Planificación y ejecución de mantenimientos masivos programados."
    >
        <x-slot:actions>
            @can('campaign.create')
            <x-ui.button href="{{ route('campaigns.create') }}" size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nueva campaña
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <form method="GET" action="{{ route('campaigns.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="flex-1 min-w-[200px]">
            <x-ui.search-input name="search" :value="request('search')" placeholder="Buscar por nombre o código..." />
        </div>
        <div class="w-40">
            <select name="status" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todos los estados</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <x-ui.button type="submit" variant="secondary" size="sm">Filtrar</x-ui.button>
        @if (request()->hasAny(['search', 'status']))
            <x-ui.button variant="ghost" size="sm" href="{{ route('campaigns.index') }}">Limpiar</x-ui.button>
        @endif
    </form>

    <x-ui.data-table>
        <x-slot:toolbar>
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Campañas registradas</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $campaigns->total() }} campaña(s)</p>
            </div>
        </x-slot:toolbar>

        <x-slot:head>
            <x-ui.th>Campaña</x-ui.th>
            <x-ui.th>Estado</x-ui.th>
            <x-ui.th>Coordinador</x-ui.th>
            <x-ui.th>Activos</x-ui.th>
            <x-ui.th>Inicio</x-ui.th>
            <x-ui.th>Fin</x-ui.th>
            <x-ui.th></x-ui.th>
        </x-slot:head>

        @forelse ($campaigns as $campaign)
        @php
            $statusTone = match($campaign->status->value) {
                'planificada' => 'neutral', 'en_curso' => 'info',
                'pausada' => 'warning', 'completada' => 'success', 'cancelada' => 'danger',
                default => 'neutral',
            };
        @endphp
        <tr class="hover:bg-slate-50/50 transition-colors">
            <x-ui.td>
                <div>
                    <a href="{{ route('campaigns.show', $campaign) }}" class="font-medium text-slate-900 hover:text-sigat-600 transition-colors">{{ $campaign->name }}</a>
                    <p class="font-mono text-xs text-slate-400">{{ $campaign->code }}</p>
                </div>
            </x-ui.td>
            <x-ui.td><x-ui.badge :tone="$statusTone" :dot="true">{{ $campaign->status->label() }}</x-ui.badge></x-ui.td>
            <x-ui.td>
                @if ($campaign->coordinator)
                    <div class="flex items-center gap-2">
                        <x-ui.avatar :name="$campaign->coordinator->full_name" size="xs" />
                        <span class="text-sm text-slate-600">{{ $campaign->coordinator->full_name }}</span>
                    </div>
                @else
                    <span class="text-xs text-slate-400">—</span>
                @endif
            </x-ui.td>
            <x-ui.td>
                <span class="text-sm font-semibold text-slate-700">{{ $campaign->campaignAssets?->count() ?? 0 }}</span>
            </x-ui.td>
            <x-ui.td><span class="text-sm text-slate-600">{{ $campaign->start_date?->format('d/m/Y') ?? '—' }}</span></x-ui.td>
            <x-ui.td><span class="text-sm text-slate-600">{{ $campaign->end_date?->format('d/m/Y') ?? '—' }}</span></x-ui.td>
            <x-ui.td>
                <div class="flex items-center gap-1 justify-end">
                    <x-ui.button variant="ghost" size="xs" href="{{ route('campaigns.show', $campaign) }}">Ver</x-ui.button>
                    @can('campaign.edit')
                    <x-ui.button variant="ghost" size="xs" href="{{ route('campaigns.edit', $campaign) }}">Editar</x-ui.button>
                    @endcan
                    @can('campaign.delete')
                    <form method="POST" action="{{ route('campaigns.destroy', $campaign) }}"
                          onsubmit="return confirm('¿Eliminar esta campaña?')" class="inline">
                        @csrf @method('DELETE')
                        <x-ui.button type="submit" variant="ghost" size="xs" class="text-rose-600 hover:text-rose-700">Eliminar</x-ui.button>
                    </form>
                    @endcan
                </div>
            </x-ui.td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="py-16">
                <x-ui.empty-state title="Sin campañas" description="Crea la primera campaña de mantenimiento." />
            </td>
        </tr>
        @endforelse

        <x-slot:pagination>{{ $campaigns->withQueryString()->links() }}</x-slot:pagination>
    </x-ui.data-table>

</x-layouts.app>

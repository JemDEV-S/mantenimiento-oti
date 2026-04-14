<x-layouts.app title="Movimientos de Activos" moduleLabel="Activos" pageTitle="Movimientos de Activos">

    <x-ui.page-header
        title="Movimientos de activos"
        description="Trazabilidad completa de desplazamientos y cambios de responsabilidad."
    >
        <x-slot:actions>
            @can('asset-movement.create')
            <x-ui.button href="{{ route('asset-movements.create') }}" size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Registrar movimiento
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <form method="GET" action="{{ route('asset-movements.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="w-44">
            <select name="type" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todos los tipos</option>
                @foreach ($types as $type)
                    <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>
        <x-ui.button type="submit" variant="secondary" size="sm">Filtrar</x-ui.button>
        @if (request()->hasAny(['type', 'asset_id']))
            <x-ui.button variant="ghost" size="sm" href="{{ route('asset-movements.index') }}">Limpiar</x-ui.button>
        @endif
    </form>

    <x-ui.data-table>
        <x-slot:toolbar>
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Historial de movimientos</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $movements->total() }} movimiento(s)</p>
            </div>
        </x-slot:toolbar>

        <x-slot:head>
            <x-ui.th>Tipo</x-ui.th>
            <x-ui.th>Activo</x-ui.th>
            <x-ui.th>Destino / Descripción</x-ui.th>
            <x-ui.th>Responsable destino</x-ui.th>
            <x-ui.th>Fecha</x-ui.th>
            <x-ui.th>Registrado por</x-ui.th>
        </x-slot:head>

        @forelse ($movements as $movement)
        @php
            $movTone = match($movement->type->value) {
                'asignacion' => 'success', 'traslado' => 'info', 'devolucion' => 'warning',
                'baja' => 'danger', 'ingreso' => 'primary', 'prestamo' => 'neutral', default => 'neutral',
            };
        @endphp
        <tr class="hover:bg-slate-50/50 transition-colors">
            <x-ui.td>
                <x-ui.badge :tone="$movTone" :dot="true">{{ $movement->type->label() }}</x-ui.badge>
            </x-ui.td>
            <x-ui.td>
                <div>
                    @can('asset.view')
                    <a href="{{ route('assets.show', $movement->asset) }}" class="font-medium text-slate-900 hover:text-sigat-600">{{ $movement->asset->name }}</a>
                    @else
                    <span class="font-medium text-slate-900">{{ $movement->asset->name }}</span>
                    @endcan
                    <p class="text-xs text-slate-400">{{ $movement->asset->internal_code }}</p>
                </div>
            </x-ui.td>
            <x-ui.td>
                <div class="max-w-xs">
                    @if ($movement->destination_unit)
                        <p class="text-sm text-slate-700">{{ $movement->destination_unit }}</p>
                    @endif
                    @if ($movement->notes)
                        <p class="text-xs text-slate-500 truncate">{{ $movement->notes }}</p>
                    @endif
                </div>
            </x-ui.td>
            <x-ui.td>
                @if ($movement->destinationEmployee)
                    <div class="flex items-center gap-2">
                        <x-ui.avatar :name="$movement->destinationEmployee->full_name" size="xs" />
                        <span class="text-sm text-slate-600">{{ $movement->destinationEmployee->full_name }}</span>
                    </div>
                @else
                    <span class="text-xs text-slate-400">—</span>
                @endif
            </x-ui.td>
            <x-ui.td>
                <span class="text-sm text-slate-600">{{ $movement->movement_date?->format('d/m/Y') ?? $movement->created_at->format('d/m/Y') }}</span>
            </x-ui.td>
            <x-ui.td>
                <span class="text-sm text-slate-500">{{ $movement->createdBy?->name ?? '—' }}</span>
            </x-ui.td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="py-16">
                <x-ui.empty-state title="Sin movimientos" description="Aún no se han registrado movimientos de activos." />
            </td>
        </tr>
        @endforelse

        <x-slot:pagination>{{ $movements->withQueryString()->links() }}</x-slot:pagination>
    </x-ui.data-table>

</x-layouts.app>

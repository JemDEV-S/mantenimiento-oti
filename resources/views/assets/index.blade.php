<x-layouts.app title="Inventario de Activos" moduleLabel="Activos" pageTitle="Inventario de Activos">

    <x-ui.page-header
        title="Inventario de activos"
        description="Gestión centralizada de todos los equipos e infraestructura TI."
    >
        <x-slot:actions>
            @can('asset.create')
            <x-ui.button href="{{ route('assets.create') }}" size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Registrar activo
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <form method="GET" action="{{ route('assets.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="flex-1 min-w-[220px]">
            <x-ui.search-input name="search" :value="request('search')" placeholder="Buscar por nombre, código o serie..." />
        </div>
        <div class="w-44">
            <select name="type" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todos los tipos</option>
                @foreach ($types as $type)
                    <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-40">
            <select name="status" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todos los estados</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <select name="unit_id" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todas las unidades</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
        <x-ui.button type="submit" variant="secondary" size="sm">Filtrar</x-ui.button>
        @if (request()->hasAny(['search', 'type', 'status', 'unit_id']))
            <x-ui.button variant="ghost" size="sm" href="{{ route('assets.index') }}">Limpiar</x-ui.button>
        @endif
    </form>

    <x-ui.data-table>
        <x-slot:toolbar>
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Activos registrados</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $assets->total() }} activo(s) en inventario</p>
            </div>
        </x-slot:toolbar>

        <x-slot:head>
            <x-ui.th>Activo</x-ui.th>
            <x-ui.th>Tipo</x-ui.th>
            <x-ui.th>Estado</x-ui.th>
            <x-ui.th>Condición</x-ui.th>
            <x-ui.th>Unidad</x-ui.th>
            <x-ui.th>Responsable</x-ui.th>
            <x-ui.th></x-ui.th>
        </x-slot:head>

        @forelse ($assets as $asset)
        @php
            $statusTone = match($asset->status->value) {
                'activo'        => 'success',
                'en_uso'        => 'info',
                'en_almacen'    => 'neutral',
                'en_reparacion' => 'warning',
                'dado_de_baja'  => 'danger',
                'extraviado'    => 'danger',
                default         => 'neutral',
            };
            $conditionTone = match($asset->condition->value) {
                'bueno'    => 'success',
                'regular'  => 'warning',
                'malo'     => 'danger',
                'obsoleto' => 'neutral',
                default    => 'neutral',
            };
        @endphp
        <tr class="hover:bg-slate-50/50 transition-colors">
            <x-ui.td>
                <div>
                    <a href="{{ route('assets.show', $asset) }}" class="font-medium text-slate-900 hover:text-sigat-600 transition-colors">{{ $asset->name }}</a>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="font-mono text-xs text-slate-400">{{ $asset->internal_code }}</span>
                        @if ($asset->brand)
                            <span class="text-xs text-slate-400">&middot; {{ $asset->brand }}</span>
                        @endif
                    </div>
                </div>
            </x-ui.td>
            <x-ui.td><span class="text-sm text-slate-600">{{ $asset->asset_type->label() }}</span></x-ui.td>
            <x-ui.td><x-ui.badge :tone="$statusTone" :dot="true">{{ $asset->status->label() }}</x-ui.badge></x-ui.td>
            <x-ui.td><x-ui.badge :tone="$conditionTone">{{ $asset->condition->label() }}</x-ui.badge></x-ui.td>
            <x-ui.td><span class="text-sm text-slate-600">{{ $asset->organizationalUnit?->name ?? '—' }}</span></x-ui.td>
            <x-ui.td>
                @if ($asset->responsible)
                    <div class="flex items-center gap-2">
                        <x-ui.avatar :name="$asset->responsible->full_name" size="xs" />
                        <span class="text-sm text-slate-600">{{ $asset->responsible->full_name }}</span>
                    </div>
                @else
                    <span class="text-xs text-slate-400">—</span>
                @endif
            </x-ui.td>
            <x-ui.td>
                <div class="flex items-center gap-1 justify-end">
                    <x-ui.button variant="ghost" size="xs" href="{{ route('assets.show', $asset) }}">Ver</x-ui.button>
                    @can('asset.edit')
                    <x-ui.button variant="ghost" size="xs" href="{{ route('assets.edit', $asset) }}">Editar</x-ui.button>
                    @endcan
                    @can('asset.delete')
                    <form method="POST" action="{{ route('assets.destroy', $asset) }}"
                          onsubmit="return confirm('¿Dar de baja este activo?')" class="inline">
                        @csrf @method('DELETE')
                        <x-ui.button type="submit" variant="ghost" size="xs" class="text-rose-600 hover:text-rose-700">Baja</x-ui.button>
                    </form>
                    @endcan
                </div>
            </x-ui.td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="py-16">
                <x-ui.empty-state title="No hay activos registrados" description="Registra el primer activo tecnológico." />
            </td>
        </tr>
        @endforelse

        <x-slot:pagination>{{ $assets->withQueryString()->links() }}</x-slot:pagination>
    </x-ui.data-table>

</x-layouts.app>

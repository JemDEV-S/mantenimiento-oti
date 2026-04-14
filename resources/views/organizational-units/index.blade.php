<x-layouts.app title="Unidades Organizacionales" moduleLabel="Organización" pageTitle="Estructura Organizacional">

    <x-ui.page-header
        title="Unidades organizacionales"
        description="Estructura jerárquica institucional de la MDSJ."
    >
        <x-slot:actions>
            @can('org-unit.create')
            <x-ui.button href="{{ route('organizational-units.create') }}" size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nueva unidad
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <form method="GET" action="{{ route('organizational-units.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="flex-1 min-w-[200px]">
            <x-ui.search-input name="search" :value="request('search')" placeholder="Buscar por nombre..." />
        </div>
        <div class="w-44">
            <select name="type" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todos los tipos</option>
                @foreach (\App\Enums\OrgUnitType::cases() as $type)
                    <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-32">
            <select name="status" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Estado</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activas</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivas</option>
            </select>
        </div>
        <x-ui.button type="submit" variant="secondary" size="sm">Filtrar</x-ui.button>
        @if (request()->hasAny(['search', 'type', 'status']))
            <x-ui.button variant="ghost" size="sm" href="{{ route('organizational-units.index') }}">Limpiar</x-ui.button>
        @endif
    </form>

    <x-ui.data-table>
        <x-slot:toolbar>
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Unidades registradas</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $units->total() }} unidad(es)</p>
            </div>
        </x-slot:toolbar>

        <x-slot:head>
            <x-ui.th>Nombre</x-ui.th>
            <x-ui.th>Tipo</x-ui.th>
            <x-ui.th>Unidad padre</x-ui.th>
            <x-ui.th>Responsable</x-ui.th>
            <x-ui.th>Estado</x-ui.th>
            <x-ui.th></x-ui.th>
        </x-slot:head>

        @forelse ($units as $unit)
        <tr class="hover:bg-slate-50/50 transition-colors">
            <x-ui.td>
                <div>
                    <a href="{{ route('organizational-units.show', $unit) }}" class="font-medium text-slate-900 hover:text-sigat-600 transition-colors">{{ $unit->name }}</a>
                    @if ($unit->code)
                        <p class="text-xs text-slate-400">{{ $unit->code }}</p>
                    @endif
                </div>
            </x-ui.td>
            <x-ui.td>
                <x-ui.badge tone="neutral">{{ $unit->type->label() }}</x-ui.badge>
            </x-ui.td>
            <x-ui.td>
                <span class="text-sm text-slate-600">{{ $unit->parent?->name ?? '—' }}</span>
            </x-ui.td>
            <x-ui.td>
                @if ($unit->responsible)
                    <div class="flex items-center gap-2">
                        <x-ui.avatar :name="$unit->responsible->full_name" size="xs" />
                        <span class="text-sm text-slate-600">{{ $unit->responsible->full_name }}</span>
                    </div>
                @else
                    <span class="text-xs text-slate-400">Sin responsable</span>
                @endif
            </x-ui.td>
            <x-ui.td>
                @if ($unit->is_active)
                    <x-ui.badge tone="success" :dot="true">Activa</x-ui.badge>
                @else
                    <x-ui.badge tone="neutral" :dot="true">Inactiva</x-ui.badge>
                @endif
            </x-ui.td>
            <x-ui.td>
                <div class="flex items-center gap-1 justify-end">
                    <x-ui.button variant="ghost" size="xs" href="{{ route('organizational-units.show', $unit) }}">Ver</x-ui.button>
                    @can('org-unit.edit')
                    <x-ui.button variant="ghost" size="xs" href="{{ route('organizational-units.edit', $unit) }}">Editar</x-ui.button>
                    @endcan
                    @can('org-unit.delete')
                    <form method="POST" action="{{ route('organizational-units.destroy', $unit) }}"
                          onsubmit="return confirm('¿Eliminar esta unidad?')" class="inline">
                        @csrf @method('DELETE')
                        <x-ui.button type="submit" variant="ghost" size="xs" class="text-rose-600 hover:text-rose-700">Eliminar</x-ui.button>
                    </form>
                    @endcan
                </div>
            </x-ui.td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="py-16">
                <x-ui.empty-state title="No hay unidades registradas" description="Crea la primera unidad organizacional." />
            </td>
        </tr>
        @endforelse

        <x-slot:pagination>{{ $units->withQueryString()->links() }}</x-slot:pagination>
    </x-ui.data-table>

</x-layouts.app>

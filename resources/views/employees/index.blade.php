<x-layouts.app title="Empleados" moduleLabel="Empleados" pageTitle="Gestión de Empleados">

    <x-ui.page-header
        title="Empleados"
        description="Registro y administración del personal de la institución."
    >
        <x-slot:actions>
            @can('employee.create')
            <x-ui.button href="{{ route('employees.create') }}" size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nuevo empleado
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <form method="GET" action="{{ route('employees.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="flex-1 min-w-[200px]">
            <x-ui.search-input name="search" :value="request('search')" placeholder="Buscar por nombre, DNI o correo..." />
        </div>
        <div class="w-48">
            <select name="unit_id" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todas las unidades</option>
                @foreach ($units as $unit)
                    <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <select name="is_technician" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todos</option>
                <option value="1" {{ request('is_technician') === '1' ? 'selected' : '' }}>Solo técnicos</option>
                <option value="0" {{ request('is_technician') === '0' ? 'selected' : '' }}>No técnicos</option>
            </select>
        </div>
        <div class="w-32">
            <select name="status" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Estado</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>
        <x-ui.button type="submit" variant="secondary" size="sm">Filtrar</x-ui.button>
        @if (request()->hasAny(['search', 'unit_id', 'is_technician', 'status']))
            <x-ui.button variant="ghost" size="sm" href="{{ route('employees.index') }}">Limpiar</x-ui.button>
        @endif
    </form>

    <x-ui.data-table>
        <x-slot:toolbar>
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Personal registrado</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $employees->total() }} empleado(s)</p>
            </div>
        </x-slot:toolbar>

        <x-slot:head>
            <x-ui.th>Empleado</x-ui.th>
            <x-ui.th>DNI</x-ui.th>
            <x-ui.th>Unidad</x-ui.th>
            <x-ui.th>Cargo</x-ui.th>
            <x-ui.th>Tipo</x-ui.th>
            <x-ui.th>Estado</x-ui.th>
            <x-ui.th></x-ui.th>
        </x-slot:head>

        @forelse ($employees as $employee)
        <tr class="hover:bg-slate-50/50 transition-colors">
            <x-ui.td>
                <div class="flex items-center gap-3">
                    <x-ui.avatar :name="$employee->full_name" size="sm" />
                    <div>
                        <a href="{{ route('employees.show', $employee) }}" class="font-medium text-slate-900 hover:text-sigat-600 transition-colors">
                            {{ $employee->full_name }}
                        </a>
                        <p class="text-xs text-slate-400">{{ $employee->email }}</p>
                    </div>
                </div>
            </x-ui.td>
            <x-ui.td><span class="font-mono text-sm text-slate-600">{{ $employee->dni }}</span></x-ui.td>
            <x-ui.td>
                <span class="text-sm text-slate-600">{{ $employee->organizationalUnit?->name ?? '—' }}</span>
            </x-ui.td>
            <x-ui.td><span class="text-sm text-slate-600">{{ $employee->position ?? '—' }}</span></x-ui.td>
            <x-ui.td>
                @if ($employee->is_technician)
                    <x-ui.badge tone="info">Técnico</x-ui.badge>
                @else
                    <x-ui.badge tone="neutral">Empleado</x-ui.badge>
                @endif
            </x-ui.td>
            <x-ui.td>
                @if ($employee->is_active)
                    <x-ui.badge tone="success" :dot="true">Activo</x-ui.badge>
                @else
                    <x-ui.badge tone="neutral" :dot="true">Inactivo</x-ui.badge>
                @endif
            </x-ui.td>
            <x-ui.td>
                <div class="flex items-center gap-1 justify-end">
                    <x-ui.button variant="ghost" size="xs" href="{{ route('employees.show', $employee) }}">Ver</x-ui.button>
                    @can('employee.edit')
                    <x-ui.button variant="ghost" size="xs" href="{{ route('employees.edit', $employee) }}">Editar</x-ui.button>
                    <form method="POST" action="{{ route('employees.toggle-active', $employee) }}" class="inline">
                        @csrf @method('PATCH')
                        <x-ui.button type="submit" variant="ghost" size="xs">
                            {{ $employee->is_active ? 'Desactivar' : 'Activar' }}
                        </x-ui.button>
                    </form>
                    @endcan
                    @can('employee.delete')
                    <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                          onsubmit="return confirm('¿Eliminar este empleado?')" class="inline">
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
                <x-ui.empty-state
                    title="No hay empleados registrados"
                    description="Agrega el primer empleado para comenzar."
                />
            </td>
        </tr>
        @endforelse

        <x-slot:pagination>{{ $employees->withQueryString()->links() }}</x-slot:pagination>
    </x-ui.data-table>

</x-layouts.app>

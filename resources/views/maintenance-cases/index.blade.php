<x-layouts.app title="Casos de Mantenimiento" moduleLabel="Mantenimiento" pageTitle="Casos de Mantenimiento">

    <x-ui.page-header
        title="Casos de mantenimiento"
        description="Registro detallado de cada intervención técnica sobre los activos."
    >
        <x-slot:actions>
            @can('maintenance-case.create')
            <x-ui.button href="{{ route('maintenance-cases.create') }}" size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nuevo caso
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <form method="GET" action="{{ route('maintenance-cases.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="flex-1 min-w-[200px]">
            <x-ui.search-input name="search" :value="request('search')" placeholder="Buscar por código o activo..." />
        </div>
        <div class="w-36">
            <select name="status" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Estado</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <select name="type" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Tipo</option>
                @foreach ($types as $type)
                    <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-44">
            <select name="technician_id" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Técnico</option>
                @foreach ($technicians as $tech)
                    <option value="{{ $tech->id }}" {{ request('technician_id') == $tech->id ? 'selected' : '' }}>{{ $tech->full_name }}</option>
                @endforeach
            </select>
        </div>
        <x-ui.button type="submit" variant="secondary" size="sm">Filtrar</x-ui.button>
        @if (request()->hasAny(['search', 'status', 'type', 'technician_id']))
            <x-ui.button variant="ghost" size="sm" href="{{ route('maintenance-cases.index') }}">Limpiar</x-ui.button>
        @endif
    </form>

    <x-ui.data-table>
        <x-slot:toolbar>
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Casos registrados</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $cases->total() }} caso(s)</p>
            </div>
        </x-slot:toolbar>

        <x-slot:head>
            <x-ui.th>Código</x-ui.th>
            <x-ui.th>Activo</x-ui.th>
            <x-ui.th>Tipo</x-ui.th>
            <x-ui.th>Prioridad</x-ui.th>
            <x-ui.th>Estado</x-ui.th>
            <x-ui.th>Técnico</x-ui.th>
            <x-ui.th>Fecha</x-ui.th>
            <x-ui.th></x-ui.th>
        </x-slot:head>

        @forelse ($cases as $case)
        @php
            $typeTone = match($case->maintenance_type->value) {
                'preventivo' => 'primary', 'correctivo' => 'info',
                'predictivo' => 'neutral', 'emergencia' => 'danger', default => 'neutral',
            };
            $statusTone = match($case->status->value) {
                'pendiente' => 'neutral', 'en_progreso' => 'warning', 'en_espera' => 'warning',
                'completado' => 'success', 'cancelado' => 'danger', default => 'neutral',
            };
            $priorityTone = match($case->priority->value) {
                'baja' => 'neutral', 'media' => 'warning', 'alta' => 'warning', 'critica' => 'danger', default => 'neutral',
            };
        @endphp
        <tr class="hover:bg-slate-50/50 transition-colors">
            <x-ui.td>
                <a href="{{ route('maintenance-cases.show', $case) }}" class="font-mono text-xs font-semibold text-sigat-600 bg-sigat-50 px-2 py-0.5 rounded-md hover:bg-sigat-100 transition-colors">
                    {{ $case->code }}
                </a>
            </x-ui.td>
            <x-ui.td>
                <div>
                    <p class="font-medium text-slate-900">{{ $case->asset?->name ?? '—' }}</p>
                    <p class="text-xs text-slate-400">{{ $case->asset?->internal_code ?? '' }}</p>
                </div>
            </x-ui.td>
            <x-ui.td><x-ui.badge :tone="$typeTone" size="sm">{{ $case->maintenance_type->label() }}</x-ui.badge></x-ui.td>
            <x-ui.td><x-ui.badge :tone="$priorityTone" size="sm">{{ $case->priority->label() }}</x-ui.badge></x-ui.td>
            <x-ui.td><x-ui.badge :tone="$statusTone" :dot="true">{{ $case->status->label() }}</x-ui.badge></x-ui.td>
            <x-ui.td>
                @if ($case->assignedTechnician)
                    <div class="flex items-center gap-1.5">
                        <x-ui.avatar :name="$case->assignedTechnician->full_name" size="xs" />
                        <span class="text-sm text-slate-600">{{ $case->assignedTechnician->full_name }}</span>
                    </div>
                @else
                    <span class="text-xs text-slate-400">Sin asignar</span>
                @endif
            </x-ui.td>
            <x-ui.td><span class="text-sm text-slate-500">{{ $case->created_at->format('d/m/Y') }}</span></x-ui.td>
            <x-ui.td>
                <div class="flex items-center gap-1 justify-end">
                    <x-ui.button variant="ghost" size="xs" href="{{ route('maintenance-cases.show', $case) }}">Ver</x-ui.button>
                    @can('maintenance-case.edit')
                    @if (!in_array($case->status->value, ['completado', 'cancelado']))
                    <x-ui.button variant="ghost" size="xs" href="{{ route('maintenance-cases.edit', $case) }}">Editar</x-ui.button>
                    @endif
                    @endcan
                </div>
            </x-ui.td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="py-16">
                <x-ui.empty-state title="Sin casos" description="Crea el primer caso de mantenimiento." />
            </td>
        </tr>
        @endforelse

        <x-slot:pagination>{{ $cases->withQueryString()->links() }}</x-slot:pagination>
    </x-ui.data-table>

</x-layouts.app>

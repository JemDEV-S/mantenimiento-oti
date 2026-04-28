<x-layouts.app title="Plantillas de Mantenimiento" moduleLabel="Mantenimiento" pageTitle="Plantillas">

    <x-ui.page-header
        title="Plantillas de mantenimiento"
        description="Plantillas configurables para agilizar el registro de casos técnicos."
    >
        <x-slot:actions>
            @can('maintenance-template.create')
            <x-ui.button href="{{ route('maintenance-templates.create') }}" size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nueva plantilla
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <form method="GET" action="{{ route('maintenance-templates.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="w-44">
            <select name="type" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todos los tipos</option>
                @foreach ($types as $type)
                    <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>
        <x-ui.button type="submit" variant="secondary" size="sm">Filtrar</x-ui.button>
        @if (request('type'))
            <x-ui.button variant="ghost" size="sm" href="{{ route('maintenance-templates.index') }}">Limpiar</x-ui.button>
        @endif
    </form>

    <x-ui.data-table>
        <x-slot:toolbar>
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Plantillas registradas</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $templates->total() }} plantilla(s)</p>
            </div>
        </x-slot:toolbar>

        <x-slot:head>
            <x-ui.th>Nombre</x-ui.th>
            <x-ui.th>Tipo</x-ui.th>
            <x-ui.th>Ítems</x-ui.th>
            <x-ui.th>Intervalo sugerido</x-ui.th>
            <x-ui.th>Estado</x-ui.th>
            <x-ui.th></x-ui.th>
        </x-slot:head>

        <x-slot:body>
            @forelse ($templates as $template)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-4 py-3">
                    <p class="text-sm font-medium text-slate-800">{{ $template->name }}</p>
                    @if ($template->description)
                        <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $template->description }}</p>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if ($template->maintenance_type)
                        <x-ui.badge tone="primary">{{ $template->maintenance_type->label() }}</x-ui.badge>
                    @else
                        <span class="text-xs text-slate-400">Todos</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm text-slate-700">{{ $template->items->count() }} ítem(s)</span>
                </td>
                <td class="px-4 py-3">
                    @if ($template->next_maintenance_interval_days)
                        <span class="text-sm text-slate-700">{{ $template->next_maintenance_interval_days }} días</span>
                    @else
                        <span class="text-xs text-slate-400">—</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if ($template->is_active)
                        <x-ui.badge tone="success" :dot="true">Activa</x-ui.badge>
                    @else
                        <x-ui.badge tone="neutral" :dot="true">Inactiva</x-ui.badge>
                    @endif
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        @can('maintenance-template.edit')
                        <x-ui.button variant="ghost" size="xs" href="{{ route('maintenance-templates.edit', $template) }}">Editar</x-ui.button>
                        @endcan
                        @can('maintenance-template.delete')
                        <form method="POST" action="{{ route('maintenance-templates.destroy', $template) }}"
                              onsubmit="return confirm('¿Eliminar esta plantilla?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 font-medium px-2 py-1 rounded-lg hover:bg-rose-50 transition-colors">Eliminar</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">No hay plantillas registradas.</td>
            </tr>
            @endforelse
        </x-slot:body>
    </x-ui.data-table>

    <div class="mt-4">{{ $templates->links() }}</div>

</x-layouts.app>

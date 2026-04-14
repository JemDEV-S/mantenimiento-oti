<x-layouts.app title="Permisos" moduleLabel="Administración" pageTitle="Gestión de Permisos">

    <x-ui.page-header
        title="Permisos del sistema"
        description="Permisos granulares que controlan el acceso a cada funcionalidad."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.permissions.create') }}" size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nuevo permiso
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @php
        $grouped = $permissions->groupBy(fn($p) => explode('.', $p->name)[0]);
    @endphp

    <div class="space-y-4">
        @foreach ($grouped as $module => $modulePerms)
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-sm font-semibold text-slate-900 capitalize">{{ $module }}</h3>
                <x-ui.badge tone="neutral">{{ $modulePerms->count() }}</x-ui.badge>
            </x-slot:header>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach ($modulePerms as $permission)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 group">
                    <div>
                        <p class="font-mono text-sm font-medium text-slate-800">{{ $permission->name }}</p>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <x-ui.button variant="ghost" size="xs" href="{{ route('admin.permissions.edit', $permission) }}">Editar</x-ui.button>
                        <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}"
                              onsubmit="return confirm('¿Eliminar este permiso?')" class="inline">
                            @csrf @method('DELETE')
                            <x-ui.button type="submit" variant="ghost" size="xs" class="text-rose-600 hover:text-rose-700">Eliminar</x-ui.button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </x-ui.card>
        @endforeach

        @if ($permissions->isEmpty())
        <x-ui.empty-state title="Sin permisos" description="No hay permisos registrados en el sistema." />
        @endif
    </div>

</x-layouts.app>

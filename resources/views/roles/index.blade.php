<x-layouts.app title="Roles" moduleLabel="Administración" pageTitle="Gestión de Roles">

    <x-ui.page-header
        title="Roles del sistema"
        description="Gestiona los roles de acceso y sus permisos asociados."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.roles.create') }}" size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nuevo rol
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse ($roles as $role)
        <x-ui.card :hover="true">
            <x-slot:header>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-sigat-50 text-sigat-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-slate-900">{{ $role->name }}</h3>
                </div>
                <x-ui.badge tone="neutral">{{ $role->permissions->count() }} permisos</x-ui.badge>
            </x-slot:header>

            <div class="flex flex-wrap gap-1.5 min-h-[40px]">
                @foreach ($role->permissions->take(6) as $permission)
                    <span class="inline-flex px-2 py-0.5 rounded-md bg-slate-100 text-xs text-slate-600 font-mono">{{ $permission->name }}</span>
                @endforeach
                @if ($role->permissions->count() > 6)
                    <span class="inline-flex px-2 py-0.5 rounded-md bg-slate-100 text-xs text-slate-400">+{{ $role->permissions->count() - 6 }} más</span>
                @endif
                @if ($role->permissions->isEmpty())
                    <span class="text-xs text-slate-400">Sin permisos asignados</span>
                @endif
            </div>

            <x-slot:footer>
                <x-ui.button variant="ghost" size="xs" href="{{ route('admin.roles.edit', $role) }}">Editar</x-ui.button>
                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                      onsubmit="return confirm('¿Eliminar este rol?')" class="inline ml-auto">
                    @csrf @method('DELETE')
                    <x-ui.button type="submit" variant="ghost" size="xs" class="text-rose-600 hover:text-rose-700">Eliminar</x-ui.button>
                </form>
            </x-slot:footer>
        </x-ui.card>
        @empty
        <div class="col-span-full">
            <x-ui.empty-state title="Sin roles" description="Crea el primer rol del sistema." />
        </div>
        @endforelse
    </div>

</x-layouts.app>

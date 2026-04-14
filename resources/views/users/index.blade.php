<x-layouts.app title="Usuarios" moduleLabel="Usuarios" pageTitle="Gestión de Usuarios">

    <x-ui.page-header
        title="Usuarios del sistema"
        description="Gestiona las cuentas de acceso al sistema SIGAT."
    >
        <x-slot:actions>
            @can('user.create')
            <x-ui.button href="{{ route('users.create') }}" size="sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nuevo usuario
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="flex-1 min-w-[200px]">
            <x-ui.search-input name="search" :value="request('search')" placeholder="Buscar por nombre o correo..." />
        </div>
        <div class="w-40">
            <select name="role" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todos los roles</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <select name="status" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todos</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>
        <x-ui.button type="submit" variant="secondary" size="sm">Filtrar</x-ui.button>
        @if (request()->hasAny(['search', 'role', 'status']))
            <x-ui.button variant="ghost" size="sm" href="{{ route('users.index') }}">Limpiar</x-ui.button>
        @endif
    </form>

    <x-ui.data-table>
        <x-slot:toolbar>
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Listado de usuarios</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $users->total() }} usuario(s) registrado(s)</p>
            </div>
        </x-slot:toolbar>

        <x-slot:head>
            <x-ui.th>Usuario</x-ui.th>
            <x-ui.th>Correo</x-ui.th>
            <x-ui.th>Rol</x-ui.th>
            <x-ui.th>Estado</x-ui.th>
            <x-ui.th>Último acceso</x-ui.th>
            <x-ui.th></x-ui.th>
        </x-slot:head>

        @forelse ($users as $user)
        <tr class="hover:bg-slate-50/50 transition-colors">
            <x-ui.td>
                @php
                    $displayName = $user->employee?->full_name ?? $user->name;
                @endphp
                <div class="flex items-center gap-3">
                    <x-ui.avatar :name="$displayName" size="sm" />
                    <div>
                        <p class="font-medium text-slate-900">{{ $displayName }}</p>
                        <p class="text-xs text-slate-400">
                            {{ $user->employee ? $user->username : $user->username . ' · Sin empleado asociado' }}
                        </p>
                    </div>
                </div>
            </x-ui.td>
            <x-ui.td>
                <span class="text-sm text-slate-600">{{ $user->email }}</span>
            </x-ui.td>
            <x-ui.td>
                @php $roleName = $user->roles->first()?->name @endphp
                @if ($roleName)
                    <x-ui.badge tone="primary">{{ $roleName }}</x-ui.badge>
                @else
                    <span class="text-xs text-slate-400">Sin rol</span>
                @endif
            </x-ui.td>
            <x-ui.td>
                @if ($user->is_active)
                    <x-ui.badge tone="success" :dot="true">Activo</x-ui.badge>
                @else
                    <x-ui.badge tone="neutral" :dot="true">Inactivo</x-ui.badge>
                @endif
            </x-ui.td>
            <x-ui.td>
                <span class="text-sm text-slate-500">
                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}
                </span>
            </x-ui.td>
            <x-ui.td>
                <div class="flex items-center gap-1 justify-end">
                    @can('user.edit')
                    <x-ui.button variant="ghost" size="xs" href="{{ route('users.edit', $user) }}">Editar</x-ui.button>
                    <form method="POST" action="{{ route('users.toggle-active', $user) }}" class="inline">
                        @csrf @method('PATCH')
                        <x-ui.button type="submit" variant="ghost" size="xs">
                            {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                        </x-ui.button>
                    </form>
                    @endcan
                    @can('user.delete')
                    @if ($user->id !== auth()->id())
                    <form method="POST" action="{{ route('users.destroy', $user) }}"
                          onsubmit="return confirm('¿Eliminar este usuario?')" class="inline">
                        @csrf @method('DELETE')
                        <x-ui.button type="submit" variant="ghost" size="xs" class="text-rose-600 hover:text-rose-700">Eliminar</x-ui.button>
                    </form>
                    @endif
                    @endcan
                </div>
            </x-ui.td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="py-16">
                <x-ui.empty-state
                    title="No hay usuarios"
                    description="Crea el primer usuario para dar acceso al sistema."
                />
            </td>
        </tr>
        @endforelse

        <x-slot:pagination>{{ $users->withQueryString()->links() }}</x-slot:pagination>
    </x-ui.data-table>

</x-layouts.app>

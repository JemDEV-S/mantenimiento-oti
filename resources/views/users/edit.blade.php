<x-layouts.app title="Editar Usuario" moduleLabel="Usuarios" pageTitle="Editar Usuario">

    @php
        $displayName = $user->employee?->full_name ?? $user->username;
    @endphp

    <x-ui.page-header
        title="Editar usuario"
        :description="'Modificando cuenta de: ' . $displayName"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('users.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @php
        $employeeOptions = $employees
            ->mapWithKeys(fn ($e) => [
                $e->id => $e->full_name . ' — DNI ' . $e->dni,
            ])
            ->toArray();
    @endphp

    <form method="POST" action="{{ route('users.update', $user) }}" class="max-w-2xl">
        @csrf @method('PUT')

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-sm font-semibold text-slate-900">Información del usuario</h3>
                @if ($user->is_active)
                    <x-ui.badge tone="success" :dot="true">Activo</x-ui.badge>
                @else
                    <x-ui.badge tone="neutral" :dot="true">Inactivo</x-ui.badge>
                @endif
            </x-slot:header>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <x-ui.searchable-select
                        name="employee_id"
                        label="Empleado asociado"
                        :value="old('employee_id', $user->employee_id)"
                        :options="$employeeOptions"
                        placeholder="Sin empleado asociado"
                        searchPlaceholder="Buscar por nombre o DNI..."
                        hint="Solo se listan empleados activos sin cuenta de usuario."
                    />
                </div>

                <x-ui.input name="username" label="Nombre de usuario" :value="old('username', $user->username)" />
                <x-ui.input name="email" type="email" label="Correo electrónico" :value="old('email', $user->email)" />
                <x-ui.input name="password" type="password" label="Nueva contraseña" placeholder="Dejar en blanco para no cambiar" />
                <x-ui.input name="password_confirmation" type="password" label="Confirmar nueva contraseña" placeholder="Dejar en blanco para no cambiar" />

                <div class="sm:col-span-2">
                    <x-ui.select
                        name="role"
                        label="Rol"
                        :value="old('role', $currentRole)"
                        :options="$roles->pluck('name', 'name')->toArray()"
                    />
                </div>

                <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                    <x-ui.toggle
                        name="is_active"
                        label="Cuenta activa"
                        :checked="old('is_active', $user->is_active)"
                        hint="Si se desactiva, el usuario no podrá iniciar sesión."
                    />
                </div>
            </div>

            <x-slot:footer>
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('users.index') }}">Cancelar</x-ui.button>
            </x-slot:footer>
        </x-ui.card>
    </form>

</x-layouts.app>

<x-layouts.app title="Nuevo Usuario" moduleLabel="Usuarios" pageTitle="Crear Usuario">

    <x-ui.page-header
        title="Nuevo usuario"
        description="Crea una cuenta de acceso para el sistema, asociada a un empleado registrado."
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

    <form method="POST" action="{{ route('users.store') }}" class="max-w-2xl">
        @csrf

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-sm font-semibold text-slate-900">Información del usuario</h3>
            </x-slot:header>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <x-ui.searchable-select
                        name="employee_id"
                        label="Empleado asociado"
                        :value="old('employee_id')"
                        :options="$employeeOptions"
                        placeholder="Sin empleado asociado"
                        searchPlaceholder="Buscar por nombre o DNI..."
                        hint="Solo se listan empleados activos sin cuenta de usuario."
                    />
                </div>

                <x-ui.input name="username" label="Nombre de usuario" :value="old('username')" placeholder="Ej: jperez" />
                <x-ui.input name="email" type="email" label="Correo electrónico" :value="old('email')" placeholder="correo@mdsj.gob.pe" />
                <x-ui.input name="password" type="password" label="Contraseña" placeholder="Mínimo 8 caracteres" />
                <x-ui.input name="password_confirmation" type="password" label="Confirmar contraseña" placeholder="Repite la contraseña" />

                <div class="sm:col-span-2">
                    <x-ui.select
                        name="role"
                        label="Rol"
                        :value="old('role')"
                        :options="$roles->pluck('name', 'name')->toArray()"
                        placeholder="Seleccionar rol..."
                    />
                </div>

                <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                    <x-ui.toggle
                        name="is_active"
                        label="Cuenta activa"
                        :checked="old('is_active', true)"
                        hint="Si se desactiva, el usuario no podrá iniciar sesión."
                    />
                </div>
            </div>

            <x-slot:footer>
                <x-ui.button type="submit">Crear usuario</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('users.index') }}">Cancelar</x-ui.button>
            </x-slot:footer>
        </x-ui.card>
    </form>

</x-layouts.app>

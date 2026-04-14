<x-layouts.app title="Nuevo Empleado" moduleLabel="Empleados" pageTitle="Registrar Empleado">

    <x-ui.page-header
        title="Nuevo empleado"
        description="Registra un nuevo miembro del personal de la institución."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('employees.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('employees.store') }}" class="max-w-2xl">
        @csrf

        <div class="space-y-4">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Datos personales</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-ui.input name="first_name" label="Nombres" :value="old('first_name')" placeholder="Ej: Juan Carlos" />
                    <x-ui.input name="last_name" label="Apellidos" :value="old('last_name')" placeholder="Ej: Pérez García" />
                    <x-ui.input name="dni" label="DNI" :value="old('dni')" placeholder="12345678" maxlength="8" />
                    <x-ui.input name="phone" label="Teléfono" :value="old('phone')" placeholder="Ej: 987654321" />
                    <div class="sm:col-span-2">
                        <x-ui.input name="email" type="email" label="Correo electrónico" :value="old('email')" placeholder="empleado@mdsj.gob.pe" />
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Información laboral</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-ui.input name="position" label="Cargo" :value="old('position')" placeholder="Ej: Asistente administrativo" />
                    <x-ui.select
                        name="organizational_unit_id"
                        label="Unidad organizacional"
                        :value="old('organizational_unit_id')"
                        :options="$units->pluck('name', 'id')->toArray()"
                        placeholder="Seleccionar unidad..."
                    />
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100" x-data="{ isTech: {{ old('is_technician') ? 'true' : 'false' }} }">
                    <x-ui.toggle name="is_technician" label="Es técnico de mantenimiento"
                        :checked="old('is_technician', false)"
                        hint="Los técnicos pueden ser asignados a casos de mantenimiento."
                        x-model="isTech"
                    />

                    <div x-show="isTech" x-cloak class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-ui.input name="specialty" label="Especialidad técnica" :value="old('specialty')" placeholder="Ej: Hardware, Redes, Software..." />
                        <x-ui.input name="technical_level" label="Nivel técnico" :value="old('technical_level')" placeholder="Ej: Junior, Senior..." />
                    </div>
                </div>
            </x-ui.card>

            <div class="flex gap-3">
                <x-ui.button type="submit">Registrar empleado</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('employees.index') }}">Cancelar</x-ui.button>
            </div>
        </div>
    </form>

</x-layouts.app>

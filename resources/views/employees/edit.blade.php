<x-layouts.app title="Editar Empleado" moduleLabel="Empleados" pageTitle="Editar Empleado">

    <x-ui.page-header
        :title="'Editar: ' . $employee->full_name"
        description="Actualiza la información del empleado."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('employees.show', $employee) }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('employees.update', $employee) }}" class="max-w-2xl">
        @csrf @method('PUT')

        <div class="space-y-4">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Datos personales</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-ui.input name="first_name" label="Nombres" :value="old('first_name', $employee->first_name)" />
                    <x-ui.input name="last_name" label="Apellidos" :value="old('last_name', $employee->last_name)" />
                    <x-ui.input name="dni" label="DNI" :value="old('dni', $employee->dni)" maxlength="8" />
                    <x-ui.input name="phone" label="Teléfono" :value="old('phone', $employee->phone)" />
                    <div class="sm:col-span-2">
                        <x-ui.input name="email" type="email" label="Correo electrónico" :value="old('email', $employee->email)" />
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Información laboral</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-ui.input name="position" label="Cargo" :value="old('position', $employee->position)" />
                    <x-ui.select
                        name="organizational_unit_id"
                        label="Unidad organizacional"
                        :value="old('organizational_unit_id', $employee->organizational_unit_id)"
                        :options="$units->pluck('name', 'id')->toArray()"
                    />
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100" x-data="{ isTech: {{ old('is_technician', $employee->is_technician) ? 'true' : 'false' }} }">
                    <x-ui.toggle name="is_technician" label="Es técnico de mantenimiento"
                        :checked="old('is_technician', $employee->is_technician)"
                        x-model="isTech"
                    />
                    <div x-show="isTech" x-cloak class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-ui.input name="specialty" label="Especialidad técnica" :value="old('specialty', $employee->specialty)" />
                        <x-ui.input name="technical_level" label="Nivel técnico" :value="old('technical_level', $employee->technical_level)" />
                    </div>
                </div>
            </x-ui.card>

            <div class="flex gap-3">
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('employees.show', $employee) }}">Cancelar</x-ui.button>
            </div>
        </div>
    </form>

</x-layouts.app>

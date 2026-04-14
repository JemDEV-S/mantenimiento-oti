<x-layouts.app title="Editar Unidad" moduleLabel="Organización" pageTitle="Editar Unidad Organizacional">

    <x-ui.page-header
        :title="'Editar: ' . $organizationalUnit->name"
        description="Modifica la información de la unidad organizacional."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('organizational-units.show', $organizationalUnit) }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('organizational-units.update', $organizationalUnit) }}" class="max-w-2xl">
        @csrf @method('PUT')

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-sm font-semibold text-slate-900">Datos de la unidad</h3>
            </x-slot:header>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <x-ui.input name="name" label="Nombre" :value="old('name', $organizationalUnit->name)" />
                </div>
                <x-ui.input name="code" label="Código" :value="old('code', $organizationalUnit->code)" />
                <x-ui.select
                    name="type"
                    label="Tipo de unidad"
                    :value="old('type', $organizationalUnit->type->value)"
                    :options="collect(\App\Enums\OrgUnitType::cases())->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()"
                />
                <x-ui.select
                    name="parent_id"
                    label="Unidad padre"
                    :value="old('parent_id', $organizationalUnit->parent_id)"
                    :options="$parents->pluck('name', 'id')->toArray()"
                    placeholder="Sin unidad padre"
                />
                <x-ui.select
                    name="responsible_employee_id"
                    label="Responsable"
                    :value="old('responsible_employee_id', $organizationalUnit->responsible_employee_id)"
                    :options="$employees->pluck('full_name', 'id')->toArray()"
                    placeholder="Sin responsable asignado"
                />
            </div>

            <x-slot:footer>
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('organizational-units.show', $organizationalUnit) }}">Cancelar</x-ui.button>
            </x-slot:footer>
        </x-ui.card>
    </form>

</x-layouts.app>

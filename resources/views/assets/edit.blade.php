<x-layouts.app title="Editar Activo" moduleLabel="Activos" pageTitle="Editar Activo">

    <x-ui.page-header
        :title="'Editar: ' . $asset->name"
        :description="$asset->internal_code"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('assets.show', $asset) }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('assets.update', $asset) }}" class="max-w-3xl">
        @csrf @method('PUT')

        <div class="space-y-4">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Identificación</h3>
                </x-slot:header>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <x-ui.input name="name" label="Nombre del activo" :value="old('name', $asset->name)" />
                    </div>
                    <x-ui.input name="internal_code" label="Código interno" :value="old('internal_code', $asset->internal_code)" />
                    <x-ui.input name="patrimonial_code" label="Código patrimonial" :value="old('patrimonial_code', $asset->patrimonial_code)" />
                    <x-ui.select
                        name="asset_type"
                        label="Tipo de activo"
                        :value="old('asset_type', $asset->asset_type->value)"
                        :options="collect($types)->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()"
                    />
                    <x-ui.input name="serial_number" label="Número de serie" :value="old('serial_number', $asset->serial_number)" />
                    <x-ui.input name="brand" label="Marca" :value="old('brand', $asset->brand)" />
                    <x-ui.input name="model" label="Modelo" :value="old('model', $asset->model)" />
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Estado y condición</h3>
                </x-slot:header>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-ui.select
                        name="status"
                        label="Estado operativo"
                        :value="old('status', $asset->status->value)"
                        :options="collect($statuses)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()"
                    />
                    <x-ui.select
                        name="condition"
                        label="Condición física"
                        :value="old('condition', $asset->condition->value)"
                        :options="collect($conditions)->mapWithKeys(fn($c) => [$c->value => $c->label()])->toArray()"
                    />
                    <x-ui.input name="purchase_date" type="date" label="Fecha de compra" :value="old('purchase_date', $asset->purchase_date?->format('Y-m-d'))" />
                    <x-ui.input name="reference_value" type="number" label="Valor referencial (S/)" :value="old('reference_value', $asset->reference_value)" prefix="S/" />
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Asignación</h3>
                </x-slot:header>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-ui.select
                        name="organizational_unit_id"
                        label="Unidad organizacional"
                        :value="old('organizational_unit_id', $asset->organizational_unit_id)"
                        :options="$units->pluck('name', 'id')->toArray()"
                        placeholder="Sin asignar"
                    />
                    <x-ui.select
                        name="responsible_employee_id"
                        label="Empleado responsable"
                        :value="old('responsible_employee_id', $asset->responsible_employee_id)"
                        :options="$employees->pluck('full_name', 'id')->toArray()"
                        placeholder="Sin responsable"
                    />
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Observaciones</h3></x-slot:header>
                <x-ui.textarea name="notes" label="Notas adicionales" :value="old('notes', $asset->notes)" rows="3" />
            </x-ui.card>

            <div class="flex gap-3">
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('assets.show', $asset) }}">Cancelar</x-ui.button>
            </div>
        </div>
    </form>

</x-layouts.app>

<x-layouts.app title="Registrar Movimiento" moduleLabel="Activos" pageTitle="Registrar Movimiento">

    <x-ui.page-header
        title="Registrar movimiento"
        description="Documenta un desplazamiento físico o cambio de responsabilidad de un activo."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('asset-movements.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('asset-movements.store') }}" class="max-w-2xl">
        @csrf

        <div class="space-y-4">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Datos del movimiento</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <x-ui.select
                            name="asset_id"
                            label="Activo"
                            :value="old('asset_id', request('asset_id'))"
                            :options="$assets->pluck('name', 'id')->toArray()"
                            placeholder="Seleccionar activo..."
                        />
                    </div>
                    <x-ui.select
                        name="type"
                        label="Tipo de movimiento"
                        :value="old('type')"
                        :options="collect($types)->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()"
                        placeholder="Seleccionar tipo..."
                    />
                    <x-ui.input name="movement_date" type="date" label="Fecha del movimiento" :value="old('movement_date', now()->format('Y-m-d'))" />
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Destino</h3>
                    <p class="text-xs text-slate-500">Al menos uno de los dos campos de destino es requerido.</p>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-ui.select
                        name="destination_unit_id"
                        label="Unidad organizacional destino"
                        :value="old('destination_unit_id')"
                        :options="$units->pluck('name', 'id')->toArray()"
                        placeholder="Sin cambio de unidad"
                    />
                    <x-ui.select
                        name="destination_employee_id"
                        label="Empleado responsable destino"
                        :value="old('destination_employee_id')"
                        :options="$employees->pluck('full_name', 'id')->toArray()"
                        placeholder="Sin cambio de responsable"
                    />
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Documentación</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-ui.input name="document_number" label="N.° de documento de respaldo" :value="old('document_number')" placeholder="Ej: ACTA-2024-001" />
                    <div class="sm:col-span-2" style="display: none;"><!-- placeholder for sm:col-span-2 --></div>
                    <div class="sm:col-span-2">
                        <x-ui.textarea name="notes" label="Observaciones" :value="old('notes')" rows="3" placeholder="Motivo del movimiento, condiciones especiales..." />
                    </div>
                </div>
            </x-ui.card>

            <div class="flex gap-3">
                <x-ui.button type="submit">Registrar movimiento</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('asset-movements.index') }}">Cancelar</x-ui.button>
            </div>
        </div>
    </form>

</x-layouts.app>

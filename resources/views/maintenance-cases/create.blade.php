<x-layouts.app title="Nuevo Caso" moduleLabel="Mantenimiento" pageTitle="Nuevo Caso de Mantenimiento">

    <x-ui.page-header
        title="Nuevo caso de mantenimiento"
        description="Registra una nueva intervención técnica sobre un activo."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('maintenance-cases.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('maintenance-cases.store') }}" class="max-w-2xl">
        @csrf

        <div class="space-y-4">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Datos del caso</h3>
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
                        name="maintenance_type"
                        label="Tipo de mantenimiento"
                        :value="old('maintenance_type')"
                        :options="collect($types)->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()"
                        placeholder="Seleccionar tipo..."
                    />
                    <x-ui.select
                        name="priority"
                        label="Prioridad"
                        :value="old('priority', 'media')"
                        :options="collect($priorities)->mapWithKeys(fn($p) => [$p->value => $p->label()])->toArray()"
                    />
                    <x-ui.select
                        name="assigned_technician_id"
                        label="Técnico asignado"
                        :value="old('assigned_technician_id')"
                        :options="$technicians->pluck('full_name', 'id')->toArray()"
                        placeholder="Sin asignar"
                    />
                    <x-ui.select
                        name="reported_by_employee_id"
                        label="Reportado por"
                        :value="old('reported_by_employee_id')"
                        :options="$employees->pluck('full_name', 'id')->toArray()"
                        placeholder="Seleccionar empleado..."
                    />
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Descripción</h3>
                </x-slot:header>
                <div class="space-y-4">
                    <x-ui.textarea name="problem_description" label="Descripción del problema" :value="old('problem_description')" rows="3" placeholder="Describe el problema o motivo del mantenimiento..." />
                </div>
            </x-ui.card>

            <div class="flex gap-3">
                <x-ui.button type="submit">Crear caso</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('maintenance-cases.index') }}">Cancelar</x-ui.button>
            </div>
        </div>
    </form>

</x-layouts.app>

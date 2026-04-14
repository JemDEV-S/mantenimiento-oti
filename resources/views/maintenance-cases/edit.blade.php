<x-layouts.app title="Editar Caso" moduleLabel="Mantenimiento" pageTitle="Editar Caso">

    <x-ui.page-header
        :title="'Editar caso: ' . $maintenanceCase->code"
        :description="$maintenanceCase->asset?->name ?? ''"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('maintenance-cases.show', $maintenanceCase) }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('maintenance-cases.update', $maintenanceCase) }}" class="max-w-2xl">
        @csrf @method('PUT')

        <div class="space-y-4">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Datos del caso</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <x-ui.select
                        name="maintenance_type"
                        label="Tipo de mantenimiento"
                        :value="old('maintenance_type', $maintenanceCase->maintenance_type->value)"
                        :options="collect($types)->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()"
                    />
                    <x-ui.select
                        name="priority"
                        label="Prioridad"
                        :value="old('priority', $maintenanceCase->priority->value)"
                        :options="collect($priorities)->mapWithKeys(fn($p) => [$p->value => $p->label()])->toArray()"
                    />
                    <x-ui.select
                        name="status"
                        label="Estado"
                        :value="old('status', $maintenanceCase->status->value)"
                        :options="collect($statuses)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()"
                    />
                    <x-ui.select
                        name="assigned_technician_id"
                        label="Técnico asignado"
                        :value="old('assigned_technician_id', $maintenanceCase->assigned_technician_id)"
                        :options="$technicians->pluck('full_name', 'id')->toArray()"
                        placeholder="Sin asignar"
                    />
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Diagnóstico y acciones</h3>
                </x-slot:header>
                <div class="space-y-4">
                    <x-ui.textarea name="problem_description" label="Descripción del problema" :value="old('problem_description', $maintenanceCase->problem_description)" rows="3" />
                    <x-ui.textarea name="diagnosis" label="Diagnóstico técnico" :value="old('diagnosis', $maintenanceCase->diagnosis)" rows="3" placeholder="Diagnóstico realizado por el técnico..." />
                    <x-ui.textarea name="actions_taken" label="Acciones realizadas" :value="old('actions_taken', $maintenanceCase->actions_taken)" rows="3" placeholder="Describe las acciones realizadas..." />
                    <x-ui.input name="next_maintenance_date" type="date" label="Próxima fecha de mantenimiento sugerida" :value="old('next_maintenance_date', $maintenanceCase->next_maintenance_date?->format('Y-m-d'))" />
                </div>
            </x-ui.card>

            <div class="flex gap-3">
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('maintenance-cases.show', $maintenanceCase) }}">Cancelar</x-ui.button>
            </div>
        </div>
    </form>

</x-layouts.app>

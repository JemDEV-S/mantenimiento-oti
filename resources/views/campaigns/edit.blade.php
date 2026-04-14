<x-layouts.app title="Editar Campaña" moduleLabel="Mantenimiento" pageTitle="Editar Campaña">

    <x-ui.page-header
        :title="'Editar: ' . $campaign->name"
        :description="$campaign->code"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('campaigns.show', $campaign) }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('campaigns.update', $campaign) }}" class="max-w-2xl">
        @csrf @method('PUT')

        <div class="space-y-4">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Datos de la campaña</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <x-ui.input name="name" label="Nombre de la campaña" :value="old('name', $campaign->name)" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.textarea name="objective" label="Objetivo" :value="old('objective', $campaign->objective)" rows="2" />
                    </div>
                    <x-ui.select
                        name="coordinator_id"
                        label="Coordinador"
                        :value="old('coordinator_id', $campaign->coordinator_id)"
                        :options="$coordinators->pluck('full_name', 'id')->toArray()"
                    />
                    <x-ui.select
                        name="status"
                        label="Estado"
                        :value="old('status', $campaign->status->value)"
                        :options="collect($statuses)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()"
                    />
                    <x-ui.input name="start_date" type="date" label="Fecha de inicio" :value="old('start_date', $campaign->start_date?->format('Y-m-d'))" />
                    <x-ui.input name="end_date" type="date" label="Fecha de fin estimada" :value="old('end_date', $campaign->end_date?->format('Y-m-d'))" />
                </div>
            </x-ui.card>

            <div class="flex gap-3">
                <x-ui.button type="submit">Guardar cambios</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('campaigns.show', $campaign) }}">Cancelar</x-ui.button>
            </div>
        </div>
    </form>

</x-layouts.app>

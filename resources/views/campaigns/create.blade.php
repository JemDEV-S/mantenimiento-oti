<x-layouts.app title="Nueva Campaña" moduleLabel="Mantenimiento" pageTitle="Nueva Campaña">

    <x-ui.page-header
        title="Nueva campaña de mantenimiento"
        description="Planifica un mantenimiento masivo sobre un conjunto de activos."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('campaigns.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('campaigns.store') }}" class="max-w-2xl">
        @csrf

        <div class="space-y-4">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Datos de la campaña</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <x-ui.input name="name" label="Nombre de la campaña" :value="old('name')" placeholder="Ej: Mantenimiento Preventivo Q1 2024" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.textarea name="objective" label="Objetivo" :value="old('objective')" rows="2" placeholder="Describe el objetivo principal de la campaña..." />
                    </div>
                    <x-ui.select
                        name="coordinator_id"
                        label="Coordinador"
                        :value="old('coordinator_id')"
                        :options="$coordinators->pluck('full_name', 'id')->toArray()"
                        placeholder="Seleccionar coordinador..."
                    />
                    <x-ui.select
                        name="status"
                        label="Estado inicial"
                        :value="old('status', 'planificada')"
                        :options="collect($statuses)->mapWithKeys(fn($s) => [$s->value => $s->label()])->toArray()"
                    />
                    <x-ui.input name="start_date" type="date" label="Fecha de inicio" :value="old('start_date')" />
                    <x-ui.input name="end_date" type="date" label="Fecha de fin estimada" :value="old('end_date')" />
                </div>
            </x-ui.card>

            <div class="flex gap-3">
                <x-ui.button type="submit">Crear campaña</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('campaigns.index') }}">Cancelar</x-ui.button>
            </div>

            {{-- Info bulk ops --}}
            <div class="flex items-start gap-3 px-4 py-3 bg-sigat-50 border border-sigat-200 rounded-xl">
                <svg class="w-5 h-5 text-sigat-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-sigat-800">Asignación masiva disponible tras crear</p>
                    <p class="text-xs text-sigat-600 mt-0.5">
                        Una vez creada la campaña podrás <strong>agregar activos por unidad organizacional</strong>
                        asignándolos a un técnico, y <strong>crear casos de mantenimiento masivos</strong>
                        para todos los activos de la campaña en un solo paso.
                    </p>
                </div>
            </div>
        </div>
    </form>

</x-layouts.app>

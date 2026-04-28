<x-layouts.app title="Nueva Plantilla" moduleLabel="Mantenimiento" pageTitle="Nueva Plantilla">

    <x-ui.page-header
        title="Nueva plantilla de mantenimiento"
        description="Define los campos y materiales predeterminados que el técnico podrá cargar en un clic."
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('maintenance-templates.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('maintenance-templates.store') }}"
          x-data="templateForm()" class="max-w-3xl">
        @csrf

        <div class="space-y-4">

            {{-- Info general --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Información general</h3>
                </x-slot:header>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <x-ui.input name="name" label="Nombre de la plantilla" :value="old('name')" required placeholder="Ej: Mantenimiento preventivo PC" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tipo de mantenimiento</label>
                        <select name="maintenance_type" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                            <option value="">Aplicar a todos los tipos</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" {{ old('maintenance_type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-ui.input name="next_maintenance_interval_days" type="number" label="Intervalo siguiente mantenimiento (días)" :value="old('next_maintenance_interval_days')" placeholder="Ej: 180" min="1" max="3650" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-ui.textarea name="description" label="Descripción interna" :value="old('description')" rows="2" placeholder="Describe cuándo usar esta plantilla..." />
                    </div>
                    <div class="sm:col-span-2 flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-slate-300 text-sigat-600 focus:ring-sigat-500">
                        <label for="is_active" class="text-sm text-slate-700">Plantilla activa (disponible para técnicos)</label>
                    </div>
                </div>
            </x-ui.card>

            {{-- Textos prefabricados --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Textos prefabricados</h3>
                    <p class="text-xs text-slate-500">El técnico podrá cargar estos textos en el caso con un clic y editarlos si es necesario.</p>
                </x-slot:header>
                <div class="space-y-4">
                    <x-ui.textarea name="problem_description" label="Descripción del problema (predeterminado)" :value="old('problem_description')" rows="2" placeholder="Ej: Mantenimiento preventivo programado del equipo..." />
                    <x-ui.textarea name="diagnosis_template" label="Diagnóstico técnico (predeterminado)" :value="old('diagnosis_template')" rows="3" placeholder="Ej: Equipo presenta acumulación de polvo, temperatura elevada..." />
                    <x-ui.textarea name="actions_template" label="Acciones realizadas (predeterminado)" :value="old('actions_template')" rows="3" placeholder="Ej: 1. Limpieza interna con aire comprimido&#10;2. Verificación de memoria RAM&#10;3. Revisión de disco duro..." />
                </div>
            </x-ui.card>

            {{-- Pasos / Checklist --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Pasos del proceso (checklist)</h3>
                    <p class="text-xs text-slate-500">Lista de verificación que el técnico seguirá durante el mantenimiento.</p>
                </x-slot:header>

                <div class="space-y-2 mb-3">
                    <template x-for="(step, i) in steps" :key="i">
                        <div class="flex gap-2 items-start p-3 rounded-xl bg-slate-50">
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <input type="text" :name="`steps_json[${i}][title]`" x-model="step.title"
                                       placeholder="Título del paso *" required
                                       class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                                <input type="text" :name="`steps_json[${i}][detail]`" x-model="step.detail"
                                       placeholder="Detalle (opcional)"
                                       class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                            </div>
                            <button type="button" @click="removeStep(i)" class="mt-1.5 text-slate-400 hover:text-rose-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addStep()"
                        class="inline-flex items-center gap-1.5 text-sm text-sigat-600 hover:text-sigat-700 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Agregar paso
                </button>
            </x-ui.card>

            {{-- Ítems predeterminados --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Ítems predeterminados</h3>
                    <p class="text-xs text-slate-500">Repuestos, insumos y herramientas que se agregarán automáticamente al caso.</p>
                </x-slot:header>

                <div class="space-y-2 mb-3">
                    <template x-for="(item, i) in items" :key="i">
                        <div class="grid grid-cols-12 gap-2 items-start p-3 rounded-xl bg-slate-50">
                            <div class="col-span-12 sm:col-span-2">
                                <select :name="`items[${i}][item_type]`" x-model="item.item_type"
                                        class="block w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                                    @foreach ($itemTypes as $t)
                                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-12 sm:col-span-4">
                                <input type="text" :name="`items[${i}][name]`" x-model="item.name"
                                       placeholder="Nombre del ítem *" required
                                       class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <input type="number" :name="`items[${i}][quantity]`" x-model="item.quantity"
                                       placeholder="Cantidad" step="0.01" min="0.01" value="1"
                                       class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <input type="number" :name="`items[${i}][unit_cost]`" x-model="item.unit_cost"
                                       placeholder="Costo unit." step="0.01" min="0" value="0"
                                       class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                            </div>
                            <div class="col-span-12 sm:col-span-1 flex items-center justify-end">
                                <button type="button" @click="removeItem(i)" class="text-slate-400 hover:text-rose-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-1.5 text-sm text-sigat-600 hover:text-sigat-700 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Agregar ítem
                </button>
            </x-ui.card>

            <div class="flex gap-3">
                <x-ui.button type="submit">Crear plantilla</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('maintenance-templates.index') }}">Cancelar</x-ui.button>
            </div>
        </div>
    </form>

    <script>
    function templateForm() {
        return {
            steps: @json(old('steps_json', [])),
            items: @json(old('items', [])),
            addStep() { this.steps.push({ title: '', detail: '' }); },
            removeStep(i) { this.steps.splice(i, 1); },
            addItem() { this.items.push({ item_type: 'insumo', name: '', quantity: 1, unit_cost: 0 }); },
            removeItem(i) { this.items.splice(i, 1); },
        };
    }
    </script>

</x-layouts.app>

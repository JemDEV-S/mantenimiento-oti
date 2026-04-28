@php
    $isCreateMode = $mode === 'create';
    $case = $maintenanceCase ?? null;
    $isClosedCase = $isClosed ?? false;
    $currentStatus = old('status', $case?->status->value ?? 'pendiente');
    $currentType = old('maintenance_type', $case?->maintenance_type->value ?? '');
    $currentPriority = old('priority', $case?->priority->value ?? 'media');
    $currentProblem = old('problem_description', $case?->problem_description ?? '');
    $currentDiagnosis = old('diagnosis', $case?->diagnosis ?? '');
    $currentActions = old('actions_taken', $case?->actions_taken ?? '');
    $currentNotes = old('notes', $case?->notes ?? '');
    $currentNextDate = old('next_maintenance_date', $case?->next_maintenance_date?->format('Y-m-d') ?? '');
    $selectedAssetId = old('asset_id', $preselectedAsset?->id ?? $case?->asset_id ?? '');
@endphp

<div
    x-data="technicianMaintenanceForm({
        mode: @js($mode),
        templatesListUrl: @js(route('maintenance-templates.list')),
        templateDataUrlPattern: @js(route('maintenance-templates.data', ['maintenanceTemplate' => '__ID__'])),
        selectedAssetId: @js((string) $selectedAssetId),
        maintenanceType: @js($currentType),
        priority: @js($currentPriority),
        status: @js($currentStatus),
        finalStatus: @js(old('final_status', 'pendiente')),
        problemDescription: @js($currentProblem),
        diagnosis: @js($currentDiagnosis),
        actionsTaken: @js($currentActions),
        notes: @js($currentNotes),
        nextMaintenanceDate: @js($currentNextDate),
        templateOptions: @js($templates->map(fn ($template) => ['id' => $template->id, 'name' => $template->name])->values()),
        items: @js(collect(old('items', []))->values()),
    })"
    class="space-y-4"
>
    @if ($isClosedCase && ! $isCreateMode)
    <x-ui.card>
        <div class="flex items-start gap-3">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="text-sm font-medium text-slate-900">Este caso ya esta cerrado.</p>
                <p class="text-sm text-slate-500">Puedes revisar el resumen del caso o volver a la cola de trabajo.</p>
            </div>
        </div>
    </x-ui.card>
    @endif

    <form method="POST" action="{{ $submitRoute }}" class="space-y-4">
        @csrf
        @if (! $isCreateMode)
        @method('PATCH')
        @endif

        @if ($isClosedCase && ! $isCreateMode)
        <fieldset disabled class="space-y-4 opacity-70">
        @endif

        @if ($isCreateMode)
        <input type="hidden" name="final_status" :value="finalStatus">
        @else
        <input type="hidden" name="status" :value="status">
        <input type="hidden" name="close_case" :value="submitMode === 'close' ? 1 : 0">
        @endif

        <input type="hidden" name="problem_description" :value="problemDescription">
        <input type="hidden" name="diagnosis" :value="diagnosis">
        <input type="hidden" name="actions_taken" :value="actionsTaken">
        <input type="hidden" name="notes" :value="notes">
        <input type="hidden" name="next_maintenance_date" :value="nextMaintenanceDate">

        @if ($isCreateMode)
        <input type="hidden" name="asset_id" :value="selectedAssetId">
        <input type="hidden" name="maintenance_type" :value="maintenanceType">
        <input type="hidden" name="priority" :value="priority">
        @endif

        <template x-for="(item, index) in items" :key="index">
            <div>
                <input type="hidden" :name="'items[' + index + '][item_type]'" :value="item.item_type">
                <input type="hidden" :name="'items[' + index + '][name]'" :value="item.name">
                <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity">
                <input type="hidden" :name="'items[' + index + '][unit_cost]'" :value="item.unit_cost">
            </div>
        </template>

        <x-ui.card>
            <x-slot:header>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ $isCreateMode ? 'Nuevo mantenimiento' : 'Continuar mantenimiento' }}</h3>
                    <p class="mt-1 text-xs text-slate-500">{{ $isCreateMode ? 'Registra un caso nuevo con un formulario continuo.' : 'Actualiza el caso desde una sola pantalla operativa.' }}</p>
                </div>
            </x-slot:header>

            @if ($isCreateMode)
            <div class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Activo</label>
                    <select x-model="selectedAssetId"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                        <option value="">Seleccionar activo...</option>
                        @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}">{{ $asset->name }}{{ $asset->internal_code ? ' - ' . $asset->internal_code : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tipo de mantenimiento</label>
                        <select x-model="maintenanceType" @change="refreshTemplates()"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                            <option value="">Seleccionar tipo...</option>
                            @foreach ($types as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Prioridad</label>
                        <select x-model="priority"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                            @foreach ($priorities as $priorityOption)
                            <option value="{{ $priorityOption->value }}">{{ $priorityOption->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @else
            <div class="space-y-4">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $case->code }}</p>
                            <p class="text-sm text-slate-600">{{ $case->asset?->name ?? 'Sin activo' }}</p>
                            @if ($case->asset?->internal_code)
                            <p class="text-xs font-mono text-slate-400">{{ $case->asset->internal_code }}</p>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                            <select x-model="maintenanceType" @change="refreshTemplates()"
                                    class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                                @foreach ($types as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                            <select x-model="priority"
                                    class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                                @foreach ($priorities as $priorityOption)
                                <option value="{{ $priorityOption->value }}">{{ $priorityOption->label() }}</option>
                                @endforeach
                            </select>
                            <select x-model="status"
                                    class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                                <option value="en_progreso">En progreso</option>
                                <option value="en_espera">En espera por repuesto</option>
                            </select>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-slate-400">Guardar progreso lo pasa a en progreso automaticamente. Usa espera solo si falta repuesto o insumo.</p>
                </div>

                <input type="hidden" name="maintenance_type" :value="maintenanceType">
                <input type="hidden" name="priority" :value="priority">
            </div>
            @endif
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Plantilla</h3>
                    <p class="mt-1 text-xs text-slate-500">Carga textos sugeridos e items del proceso de mantenimiento.</p>
                </div>
            </x-slot:header>

            <div class="space-y-3">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <select x-model="selectedTemplateId"
                            class="flex-1 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                        <option value="">Seleccionar plantilla...</option>
                        <template x-for="template in availableTemplates" :key="template.id">
                            <option :value="template.id" x-text="template.name"></option>
                        </template>
                    </select>
                    <div class="flex items-center gap-2">
                        <x-ui.button type="button" variant="secondary" size="sm" @click="applyTemplate()" x-bind:disabled="!selectedTemplateId || loadingTemplate">
                            Aplicar plantilla
                        </x-ui.button>
                        <span x-show="loadingTemplate" class="text-xs text-slate-500">Cargando...</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400">La plantilla actualiza descripciones y agrega sus items al borrador del formulario.</p>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-sm font-semibold text-slate-900">Diagnostico y acciones</h3>
            </x-slot:header>

            <div class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Descripcion del problema</label>
                    <textarea x-model="problemDescription" rows="4"
                              placeholder="Describe el problema reportado o detectado..."
                              class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20"></textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Diagnostico tecnico</label>
                    <textarea x-model="diagnosis" rows="3"
                              placeholder="Causa raiz identificada, observaciones tecnicas..."
                              class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20"></textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones realizadas</label>
                    <textarea x-model="actionsTaken" rows="3"
                              placeholder="Describe las acciones realizadas..."
                              class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20"></textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Notas internas</label>
                    <textarea x-model="notes" rows="2"
                              placeholder="Notas adicionales para el equipo..."
                              class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20"></textarea>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">Items y repuestos</h3>
                    <p class="mt-1 text-xs text-slate-500">Agrega manualmente items o usa los de una plantilla.</p>
                </div>
            </x-slot:header>

            @if (! $isCreateMode && $case->items->isNotEmpty())
            <div class="mb-4 space-y-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Items registrados</p>
                @foreach ($case->items as $item)
                <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-800">{{ $item->name }}</p>
                        <p class="text-xs text-slate-400">{{ $item->quantity }} x S/ {{ number_format($item->unit_cost, 2) }}</p>
                    </div>
                    <span class="ml-3 text-sm font-semibold text-slate-700">S/ {{ number_format($item->total_cost, 2) }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div class="grid grid-cols-1 gap-2 sm:grid-cols-12">
                <div class="sm:col-span-3">
                    <select x-model="newItem.item_type"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                        <option value="">Tipo</option>
                        @foreach ($itemTypes as $itemType)
                        <option value="{{ $itemType->value }}">{{ $itemType->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-5">
                    <input type="text" x-model="newItem.name" placeholder="Nombre del item"
                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                </div>
                <div class="sm:col-span-2">
                    <input type="number" x-model="newItem.quantity" min="1" placeholder="Cant."
                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                </div>
                <div class="sm:col-span-2">
                    <input type="number" x-model="newItem.unit_cost" min="0" step="0.01" placeholder="Costo"
                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                </div>
            </div>

            <div class="mt-3 flex justify-end">
                <x-ui.button type="button" variant="secondary" size="sm" @click="addItem()">Agregar item</x-ui.button>
            </div>

            <div class="mt-4 space-y-2">
                <template x-if="items.length === 0">
                    <p class="rounded-xl border border-dashed border-slate-200 px-4 py-6 text-center text-sm text-slate-400">Sin items en borrador.</p>
                </template>
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-white p-3">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-800" x-text="item.name"></p>
                            <p class="text-xs text-slate-400" x-text="item.item_type + ' x ' + item.quantity + (item.unit_cost ? ' - S/ ' + Number(item.unit_cost).toFixed(2) : '')"></p>
                        </div>
                        <button type="button" @click="removeItem(index)" class="text-slate-300 transition-colors hover:text-rose-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-sm font-semibold text-slate-900">{{ $isCreateMode ? 'Estado final del caso' : 'Cierre del caso' }}</h3>
            </x-slot:header>

            @if ($isCreateMode)
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                <label class="cursor-pointer">
                    <input type="radio" x-model="finalStatus" value="pendiente" class="peer sr-only">
                    <div class="rounded-xl border-2 border-slate-200 px-3 py-3 text-center text-sm font-medium text-slate-600 transition-all peer-checked:border-slate-400 peer-checked:bg-slate-50">
                        Dejar pendiente
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" x-model="finalStatus" value="en_progreso" class="peer sr-only">
                    <div class="rounded-xl border-2 border-slate-200 px-3 py-3 text-center text-sm font-medium text-slate-600 transition-all peer-checked:border-amber-400 peer-checked:bg-amber-50 peer-checked:text-amber-800">
                        En progreso
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" x-model="finalStatus" value="completado" class="peer sr-only">
                    <div class="rounded-xl border-2 border-slate-200 px-3 py-3 text-center text-sm font-medium text-slate-600 transition-all peer-checked:border-emerald-400 peer-checked:bg-emerald-50 peer-checked:text-emerald-800">
                        Cerrar caso
                    </div>
                </label>
            </div>
            @else
            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nombre del conforme</label>
                        <input type="text" name="conformity_name" value="{{ old('conformity_name') }}"
                               class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha de conformidad</label>
                        <input type="date" name="conformity_date" value="{{ old('conformity_date', now()->format('Y-m-d')) }}"
                               class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Proximo mantenimiento</label>
                        <input type="date" x-model="nextMaintenanceDate"
                               class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Costo total (S/)</label>
                        <input type="number" name="total_cost" step="0.01" min="0" value="{{ old('total_cost', number_format($case->items->sum('total_cost'), 2, '.', '')) }}"
                               class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:outline-none focus:ring-2 focus:ring-sigat-500/20">
                    </div>
                </div>
                <p class="text-xs text-slate-400">Guardar progreso mantiene el caso abierto. Cerrar caso usa las acciones registradas en este mismo formulario.</p>
            </div>
            @endif
        </x-ui.card>

        @if (! $isClosedCase || $isCreateMode)
        <div class="sticky bottom-3 z-10 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur">
            <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                @if (! $isCreateMode)
                <x-ui.button type="submit" variant="secondary" size="sm" @click="submitMode = 'save'">
                    Guardar progreso
                </x-ui.button>
                <x-ui.button type="submit" size="sm" @click="submitMode = 'close'" x-bind:disabled="!actionsTaken.trim()">
                    Cerrar caso
                </x-ui.button>
                @else
                <x-ui.button type="submit" size="sm">
                    Registrar caso
                </x-ui.button>
                @endif
            </div>
        </div>
        @endif

        @if ($isClosedCase && ! $isCreateMode)
        </fieldset>
        @endif
    </form>

    <script>
    function technicianMaintenanceForm(config) {
        return {
            mode: config.mode,
            selectedAssetId: config.selectedAssetId ?? '',
            maintenanceType: config.maintenanceType ?? '',
            priority: config.priority ?? 'media',
            status: config.status ?? 'pendiente',
            finalStatus: config.finalStatus ?? 'pendiente',
            problemDescription: config.problemDescription ?? '',
            diagnosis: config.diagnosis ?? '',
            actionsTaken: config.actionsTaken ?? '',
            notes: config.notes ?? '',
            nextMaintenanceDate: config.nextMaintenanceDate ?? '',
            selectedTemplateId: '',
            availableTemplates: config.templateOptions ?? [],
            items: config.items ?? [],
            newItem: { item_type: '', name: '', quantity: 1, unit_cost: '' },
            loadingTemplate: false,
            submitMode: 'save',
            addItem() {
                if (!this.newItem.item_type || !this.newItem.name) return;

                this.items.push({
                    item_type: this.newItem.item_type,
                    name: this.newItem.name,
                    quantity: Number(this.newItem.quantity || 1),
                    unit_cost: this.newItem.unit_cost === '' ? '' : Number(this.newItem.unit_cost),
                });

                this.newItem = { item_type: '', name: '', quantity: 1, unit_cost: '' };
            },
            removeItem(index) {
                this.items.splice(index, 1);
            },
            async refreshTemplates() {
                const url = new URL(config.templatesListUrl, window.location.origin);
                if (this.maintenanceType) {
                    url.searchParams.set('type', this.maintenanceType);
                }

                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                this.availableTemplates = await response.json();
                this.selectedTemplateId = '';
            },
            async applyTemplate() {
                if (!this.selectedTemplateId) return;

                const shouldReplace = !(!this.problemDescription && !this.diagnosis && !this.actionsTaken && this.items.length === 0)
                    ? confirm('La plantilla reemplazara los textos actuales y agregara sus items. Deseas continuar?')
                    : true;

                if (!shouldReplace) return;

                this.loadingTemplate = true;

                try {
                    const url = config.templateDataUrlPattern.replace('__ID__', this.selectedTemplateId);
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const template = await response.json();

                    this.problemDescription = template.problem_description || '';
                    this.diagnosis = template.diagnosis_template || '';
                    this.actionsTaken = template.actions_template || '';

                    if (template.next_maintenance_interval_days) {
                        const date = new Date();
                        date.setDate(date.getDate() + Number(template.next_maintenance_interval_days));
                        this.nextMaintenanceDate = date.toISOString().split('T')[0];
                    }

                    const templateItems = Array.isArray(template.items)
                        ? template.items.map((item) => ({
                            item_type: item.item_type,
                            name: item.name,
                            quantity: Number(item.quantity || 1),
                            unit_cost: Number(item.unit_cost || 0),
                        }))
                        : [];

                    if (templateItems.length > 0) {
                        this.items = [...this.items, ...templateItems];
                    }
                } finally {
                    this.loadingTemplate = false;
                }
            },
        };
    }
    </script>
</div>

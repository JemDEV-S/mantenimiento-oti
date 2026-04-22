@php
    $page = [
        'title'             => 'Atender Equipo',
        'moduleLabel'       => 'Panel Técnico',
        'pageTitle'         => 'Atender Equipo',
        'headerTitle'       => 'Atender equipo',
        'headerDescription' => 'Registra una atención de mantenimiento en pocos pasos.',
    ];
@endphp

<x-layouts.app :page="$page">

    <x-ui.page-header
        :title="$page['headerTitle']"
        :description="$page['headerDescription']"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('tecnico.dashboard') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Mi panel
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div
        x-data="{
            step: 1,
            totalSteps: 4,

            // Step 1
            assetSearch: '',
            selectedAsset: {{ $preselectedAsset ? json_encode(['id' => $preselectedAsset->id, 'name' => $preselectedAsset->name, 'internal_code' => $preselectedAsset->internal_code]) : 'null' }},
            assets: {{ $assets->map(fn($a) => ['id' => $a->id, 'name' => $a->name, 'internal_code' => $a->internal_code ?? ''])->toJson() }},
            get filteredAssets() {
                if (this.assetSearch.length < 1) return this.assets.slice(0, 8);
                const q = this.assetSearch.toLowerCase();
                return this.assets.filter(a =>
                    a.name.toLowerCase().includes(q) || a.internal_code.toLowerCase().includes(q)
                ).slice(0, 10);
            },

            // Step 2
            maintenanceType: '',
            priority: '',

            // Step 3
            problemDescription: '',
            diagnosis: '',
            actionsTaken: '',
            notes: '',

            // Step 4 – items
            items: [],
            newItem: { item_type: '', name: '', quantity: 1, unit_cost: '' },
            addItem() {
                if (!this.newItem.item_type || !this.newItem.name) return;
                this.items.push({ ...this.newItem });
                this.newItem = { item_type: '', name: '', quantity: 1, unit_cost: '' };
            },
            removeItem(index) { this.items.splice(index, 1); },
            get itemsTotal() {
                return this.items.reduce((sum, i) => sum + (parseFloat(i.unit_cost || 0) * parseInt(i.quantity || 1)), 0);
            },

            finalStatus: 'pendiente',

            canGoNext() {
                if (this.step === 1) return this.selectedAsset !== null;
                if (this.step === 2) return this.maintenanceType !== '' && this.priority !== '';
                if (this.step === 3) return this.problemDescription.trim().length >= 5;
                return true;
            },
            next() { if (this.canGoNext() && this.step < this.totalSteps) this.step++; },
            prev() { if (this.step > 1) this.step--; },
        }"
        class="max-w-2xl mx-auto"
    >

        {{-- Progress bar --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-2">
                @foreach ([1 => 'Equipo', 2 => 'Tipo', 3 => 'Diagnóstico', 4 => 'Confirmar'] as $n => $label)
                <div class="flex items-center gap-2 flex-1">
                    <div class="flex items-center gap-1.5">
                        <div
                            :class="{
                                'bg-sigat-600 text-white': step >= {{ $n }},
                                'bg-slate-100 text-slate-400': step < {{ $n }}
                            }"
                            class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 transition-colors"
                        >
                            <template x-if="step > {{ $n }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <template x-if="step <= {{ $n }}">
                                <span>{{ $n }}</span>
                            </template>
                        </div>
                        <span
                            :class="step >= {{ $n }} ? 'text-slate-800 font-medium' : 'text-slate-400'"
                            class="text-xs hidden sm:inline transition-colors"
                        >{{ $label }}</span>
                    </div>
                    @if ($n < 4)
                    <div class="flex-1 h-px mx-1"
                         :class="step > {{ $n }} ? 'bg-sigat-300' : 'bg-slate-200'"></div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('tecnico.attend-asset.store') }}">
            @csrf

            {{-- Hidden fields populated by Alpine --}}
            <input type="hidden" name="asset_id"            :value="selectedAsset?.id">
            <input type="hidden" name="maintenance_type"    :value="maintenanceType">
            <input type="hidden" name="priority"            :value="priority">
            <input type="hidden" name="problem_description" :value="problemDescription">
            <input type="hidden" name="diagnosis"           :value="diagnosis">
            <input type="hidden" name="actions_taken"       :value="actionsTaken">
            <input type="hidden" name="notes"               :value="notes">
            <input type="hidden" name="final_status"        :value="finalStatus">

            {{-- Items hidden inputs (rendered by Alpine on submit) --}}
            <template x-for="(item, index) in items" :key="index">
                <div>
                    <input type="hidden" :name="'items[' + index + '][item_type]'" :value="item.item_type">
                    <input type="hidden" :name="'items[' + index + '][name]'"      :value="item.name">
                    <input type="hidden" :name="'items[' + index + '][quantity]'"  :value="item.quantity">
                    <input type="hidden" :name="'items[' + index + '][unit_cost]'" :value="item.unit_cost">
                </div>
            </template>

            {{-- STEP 1: Buscar y seleccionar equipo --}}
            <div x-show="step === 1" x-cloak>
                <x-ui.card>
                    <x-slot:header>
                        <h3 class="text-sm font-semibold text-slate-900">Paso 1 — ¿Qué equipo vas a atender?</h3>
                    </x-slot:header>

                    {{-- Selected asset display --}}
                    <template x-if="selectedAsset">
                        <div class="mb-4 flex items-center justify-between p-3 bg-sigat-50 border border-sigat-200 rounded-xl">
                            <div>
                                <p class="text-sm font-semibold text-sigat-800" x-text="selectedAsset.name"></p>
                                <p class="text-xs text-sigat-600 font-mono" x-text="selectedAsset.internal_code || 'Sin código'"></p>
                            </div>
                            <button type="button" @click="selectedAsset = null; assetSearch = ''"
                                    class="text-xs text-sigat-600 hover:text-sigat-800 font-medium underline">
                                Cambiar
                            </button>
                        </div>
                    </template>

                    <template x-if="!selectedAsset">
                        <div>
                            <div class="relative mb-3">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/></svg>
                                <input
                                    type="text"
                                    x-model="assetSearch"
                                    placeholder="Buscar por nombre o código..."
                                    class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500 focus:border-transparent"
                                    autocomplete="off"
                                >
                            </div>

                            <div class="space-y-1 max-h-64 overflow-y-auto">
                                <template x-for="asset in filteredAssets" :key="asset.id">
                                    <button
                                        type="button"
                                        @click="selectedAsset = asset; assetSearch = ''"
                                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-slate-50 text-left transition-colors group"
                                    >
                                        <div>
                                            <p class="text-sm font-medium text-slate-800 group-hover:text-slate-900" x-text="asset.name"></p>
                                            <p class="text-xs text-slate-400 font-mono" x-text="asset.internal_code || '—'"></p>
                                        </div>
                                        <svg class="w-4 h-4 text-slate-300 group-hover:text-sigat-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </template>
                                <template x-if="filteredAssets.length === 0">
                                    <p class="text-sm text-center text-slate-400 py-6">No se encontraron equipos.</p>
                                </template>
                            </div>
                        </div>
                    </template>
                </x-ui.card>
            </div>

            {{-- STEP 2: Tipo de atención y prioridad --}}
            <div x-show="step === 2" x-cloak>
                <x-ui.card>
                    <x-slot:header>
                        <h3 class="text-sm font-semibold text-slate-900">Paso 2 — Tipo de atención</h3>
                    </x-slot:header>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Tipo de mantenimiento</p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($types as $type)
                                @php
                                    $typeColors = [
                                        'preventivo' => 'peer-checked:bg-emerald-50 peer-checked:border-emerald-400 peer-checked:text-emerald-800',
                                        'correctivo' => 'peer-checked:bg-sky-50 peer-checked:border-sky-400 peer-checked:text-sky-800',
                                        'predictivo' => 'peer-checked:bg-violet-50 peer-checked:border-violet-400 peer-checked:text-violet-800',
                                        'emergencia' => 'peer-checked:bg-rose-50 peer-checked:border-rose-400 peer-checked:text-rose-800',
                                    ];
                                    $color = $typeColors[$type->value] ?? 'peer-checked:bg-sigat-50 peer-checked:border-sigat-400';
                                @endphp
                                <label class="cursor-pointer">
                                    <input type="radio" x-model="maintenanceType" value="{{ $type->value }}" class="peer sr-only">
                                    <div class="px-3 py-2.5 rounded-xl border-2 border-slate-200 text-sm font-medium text-slate-600 text-center transition-all {{ $color }} hover:border-slate-300">
                                        {{ $type->label() }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Prioridad</p>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach ($priorities as $priority)
                                @php
                                    $priColors = [
                                        'baja'    => 'peer-checked:bg-slate-100 peer-checked:border-slate-400 peer-checked:text-slate-800',
                                        'media'   => 'peer-checked:bg-yellow-50 peer-checked:border-yellow-400 peer-checked:text-yellow-800',
                                        'alta'    => 'peer-checked:bg-amber-50 peer-checked:border-amber-500 peer-checked:text-amber-800',
                                        'critica' => 'peer-checked:bg-rose-50 peer-checked:border-rose-500 peer-checked:text-rose-800',
                                    ];
                                    $priColor = $priColors[$priority->value] ?? '';
                                @endphp
                                <label class="cursor-pointer">
                                    <input type="radio" x-model="priority" value="{{ $priority->value }}" class="peer sr-only">
                                    <div class="px-2 py-2.5 rounded-xl border-2 border-slate-200 text-xs font-semibold text-slate-500 text-center transition-all {{ $priColor }} hover:border-slate-300">
                                        {{ $priority->label() }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </x-ui.card>
            </div>

            {{-- STEP 3: Diagnóstico --}}
            <div x-show="step === 3" x-cloak>
                <x-ui.card>
                    <x-slot:header>
                        <h3 class="text-sm font-semibold text-slate-900">Paso 3 — Diagnóstico</h3>
                    </x-slot:header>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Descripción del problema <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                x-model="problemDescription"
                                rows="3"
                                placeholder="Describe el problema reportado o detectado..."
                                class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500 focus:border-transparent resize-none"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Diagnóstico técnico <span class="text-slate-400 font-normal">(opcional)</span>
                            </label>
                            <textarea
                                x-model="diagnosis"
                                rows="2"
                                placeholder="Causa raíz identificada, observaciones técnicas..."
                                class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500 focus:border-transparent resize-none"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Acciones realizadas <span class="text-slate-400 font-normal">(opcional)</span>
                            </label>
                            <textarea
                                x-model="actionsTaken"
                                rows="2"
                                placeholder="Describe qué se hizo: limpiezas, reemplazos, configuraciones..."
                                class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500 focus:border-transparent resize-none"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Notas internas <span class="text-slate-400 font-normal">(opcional)</span>
                            </label>
                            <textarea
                                x-model="notes"
                                rows="2"
                                placeholder="Notas adicionales para el equipo..."
                                class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500 focus:border-transparent resize-none"
                            ></textarea>
                        </div>
                    </div>
                </x-ui.card>
            </div>

            {{-- STEP 4: Ítems + estado final --}}
            <div x-show="step === 4" x-cloak>
                <div class="space-y-4">

                    {{-- Items --}}
                    <x-ui.card>
                        <x-slot:header>
                            <h3 class="text-sm font-semibold text-slate-900">Paso 4 — Ítems y cierre</h3>
                        </x-slot:header>

                        {{-- Add item row --}}
                        <div class="grid grid-cols-12 gap-2 mb-3">
                            <div class="col-span-3">
                                <select x-model="newItem.item_type"
                                        class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sigat-500">
                                    <option value="">Tipo</option>
                                    @foreach ($itemTypes as $it)
                                    <option value="{{ $it->value }}">{{ $it->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-4">
                                <input type="text" x-model="newItem.name"
                                       placeholder="Nombre / descripción"
                                       class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sigat-500">
                            </div>
                            <div class="col-span-2">
                                <input type="number" x-model="newItem.quantity" min="1"
                                       placeholder="Cant."
                                       class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sigat-500">
                            </div>
                            <div class="col-span-2">
                                <input type="number" x-model="newItem.unit_cost" min="0" step="0.01"
                                       placeholder="Costo"
                                       class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sigat-500">
                            </div>
                            <div class="col-span-1 flex items-center">
                                <button type="button" @click="addItem()"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-sigat-600 text-white hover:bg-sigat-700 transition-colors shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Items list --}}
                        <div class="space-y-1.5 mb-3">
                            <template x-if="items.length === 0">
                                <p class="text-xs text-center text-slate-400 py-4">Sin ítems agregados. Puedes agregarlos después desde el caso.</p>
                            </template>
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-700" x-text="item.name"></p>
                                        <p class="text-xs text-slate-400" x-text="item.item_type + ' · x' + item.quantity + (item.unit_cost ? ' · S/ ' + parseFloat(item.unit_cost).toFixed(2) : '')"></p>
                                    </div>
                                    <button type="button" @click="removeItem(index)"
                                            class="text-slate-400 hover:text-rose-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <template x-if="items.length > 0">
                            <div class="flex justify-end pt-2 border-t border-slate-100">
                                <span class="text-sm font-semibold text-slate-700">
                                    Total estimado: S/ <span x-text="itemsTotal.toFixed(2)"></span>
                                </span>
                            </div>
                        </template>
                    </x-ui.card>

                    {{-- Estado final --}}
                    <x-ui.card>
                        <x-slot:header>
                            <h3 class="text-sm font-semibold text-slate-900">¿Cómo queda el caso?</h3>
                        </x-slot:header>

                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" x-model="finalStatus" value="pendiente" class="peer sr-only">
                                <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 border-slate-200 peer-checked:border-slate-400 peer-checked:bg-slate-50 hover:border-slate-300 transition-all text-center">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-semibold text-slate-600">Dejar pendiente</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" x-model="finalStatus" value="en_progreso" class="peer sr-only">
                                <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 border-slate-200 peer-checked:border-amber-400 peer-checked:bg-amber-50 hover:border-slate-300 transition-all text-center">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17l-4.655 4.655a2.121 2.121 0 11-3-3l4.655-4.655m3-3l1.06-1.06a2.121 2.121 0 013 0l1.415 1.414a2.121 2.121 0 010 3l-1.06 1.06m-3-3l3 3"/></svg>
                                    <span class="text-xs font-semibold text-amber-700">En progreso</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" x-model="finalStatus" value="completado" class="peer sr-only">
                                <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 border-slate-200 peer-checked:border-emerald-400 peer-checked:bg-emerald-50 hover:border-slate-300 transition-all text-center">
                                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-semibold text-emerald-700">Cerrar caso</span>
                                </div>
                            </label>
                        </div>

                        {{-- Resumen --}}
                        <div class="mt-4 p-3.5 bg-slate-50 rounded-xl space-y-1.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Equipo</span>
                                <span class="font-medium text-slate-800" x-text="selectedAsset?.name ?? '—'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Tipo</span>
                                <span class="font-medium text-slate-800" x-text="maintenanceType || '—'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Prioridad</span>
                                <span class="font-medium text-slate-800" x-text="priority || '—'"></span>
                            </div>
                            <template x-if="diagnosis">
                                <div class="flex justify-between gap-4">
                                    <span class="text-slate-500 shrink-0">Diagnóstico</span>
                                    <span class="font-medium text-slate-800 text-right truncate" x-text="diagnosis"></span>
                                </div>
                            </template>
                            <template x-if="actionsTaken">
                                <div class="flex justify-between gap-4">
                                    <span class="text-slate-500 shrink-0">Acciones</span>
                                    <span class="font-medium text-slate-800 text-right truncate" x-text="actionsTaken"></span>
                                </div>
                            </template>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Ítems</span>
                                <span class="font-medium text-slate-800" x-text="items.length + ' ítem(s)'"></span>
                            </div>
                        </div>
                    </x-ui.card>

                </div>
            </div>

            {{-- Navigation buttons --}}
            <div class="flex items-center justify-between mt-4">
                <button
                    type="button"
                    x-show="step > 1"
                    @click="prev()"
                    class="flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    Anterior
                </button>
                <div x-show="step === 1" class="text-xs text-slate-400">Selecciona un equipo para continuar</div>

                <button
                    type="button"
                    x-show="step < totalSteps"
                    @click="next()"
                    :disabled="!canGoNext()"
                    :class="canGoNext() ? 'bg-sigat-600 hover:bg-sigat-700 text-white' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                    class="flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-xl transition-colors ml-auto"
                >
                    Siguiente
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </button>

                <button
                    type="submit"
                    x-show="step === totalSteps"
                    class="flex items-center gap-1.5 px-5 py-2 text-sm font-semibold bg-sigat-600 hover:bg-sigat-700 text-white rounded-xl transition-colors ml-auto"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Registrar caso
                </button>
            </div>

        </form>
    </div>

</x-layouts.app>

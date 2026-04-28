<x-layouts.app title="Detalle Caso" moduleLabel="Mantenimiento" pageTitle="Detalle de Caso">

    @php
        $typeTone = match($maintenanceCase->maintenance_type->value) {
            'preventivo' => 'primary', 'correctivo' => 'info',
            'predictivo' => 'neutral', 'emergencia' => 'danger', default => 'neutral',
        };
        $statusTone = match($maintenanceCase->status->value) {
            'pendiente' => 'neutral', 'en_progreso' => 'warning', 'en_espera' => 'warning',
            'completado' => 'success', 'cancelado' => 'danger', default => 'neutral',
        };
        $priorityTone = match($maintenanceCase->priority->value) {
            'baja' => 'neutral', 'media' => 'warning', 'alta' => 'warning', 'critica' => 'danger', default => 'neutral',
        };
        $isClosed = in_array($maintenanceCase->status->value, ['completado', 'cancelado']);
    @endphp

    <x-ui.page-header
        :title="$maintenanceCase->code"
        :description="$maintenanceCase->asset?->name ?? 'Sin activo'"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('maintenance-cases.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
            @can('maintenance-case.edit')
            @if (!$isClosed)
            <x-ui.button variant="secondary" size="sm" href="{{ route('maintenance-cases.edit', $maintenanceCase) }}">Editar</x-ui.button>
            @endif
            @endcan
            @can('maintenance-case.close')
            @if ($maintenanceCase->status->value === 'en_progreso')
            <x-ui.button size="sm" x-data @click="$dispatch('open-modal', 'close-case')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Cerrar caso
            </x-ui.button>
            @endif
            @endcan
            <a href="{{ route('maintenance-cases.generate-informe', $maintenanceCase) }}"
               target="_blank"
               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                Informe técnico
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">

        {{-- Left sidebar --}}
        <div class="xl:col-span-1 space-y-4">

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Estado del caso</h3>
                </x-slot:header>
                <div class="flex flex-wrap gap-2 mb-4">
                    <x-ui.badge :tone="$statusTone" :dot="true">{{ $maintenanceCase->status->label() }}</x-ui.badge>
                    <x-ui.badge :tone="$typeTone">{{ $maintenanceCase->maintenance_type->label() }}</x-ui.badge>
                    <x-ui.badge :tone="$priorityTone">{{ $maintenanceCase->priority->label() }}</x-ui.badge>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Activo</span>
                        @if ($maintenanceCase->asset)
                        @can('asset.view')
                        <a href="{{ route('assets.show', $maintenanceCase->asset) }}" class="font-medium text-sigat-600 hover:text-sigat-700">
                            {{ $maintenanceCase->asset->internal_code }}
                        </a>
                        @else
                        <span class="font-medium text-slate-800">{{ $maintenanceCase->asset->internal_code }}</span>
                        @endcan
                        @endif
                    </div>
                    @if ($maintenanceCase->campaign)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Campaña</span>
                        <a href="{{ route('campaigns.show', $maintenanceCase->campaign) }}" class="font-medium text-sigat-600 hover:text-sigat-700">
                            {{ $maintenanceCase->campaign->code }}
                        </a>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500">Creado</span>
                        <span class="text-slate-700">{{ $maintenanceCase->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if ($maintenanceCase->finished_at)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Cerrado</span>
                        <span class="text-slate-700">{{ $maintenanceCase->finished_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    @if ($maintenanceCase->next_maintenance_date)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Próx. mantenimiento</span>
                        <span class="font-medium text-slate-800">{{ $maintenanceCase->next_maintenance_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </x-ui.card>

            {{-- Personas --}}
            <x-ui.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Personas</h3></x-slot:header>
                <div class="space-y-3">
                    @if ($maintenanceCase->reportedBy)
                    <div>
                        <p class="text-xs text-slate-500 mb-1.5">Reportado por</p>
                        <div class="flex items-center gap-2">
                            <x-ui.avatar :name="$maintenanceCase->reportedBy->full_name" size="xs" />
                            <span class="text-sm text-slate-700">{{ $maintenanceCase->reportedBy->full_name }}</span>
                        </div>
                    </div>
                    @endif
                    @if ($maintenanceCase->assignedTechnician)
                    <div>
                        <p class="text-xs text-slate-500 mb-1.5">Técnico asignado</p>
                        <div class="flex items-center gap-2">
                            <x-ui.avatar :name="$maintenanceCase->assignedTechnician->full_name" size="xs" />
                            <span class="text-sm text-slate-700">{{ $maintenanceCase->assignedTechnician->full_name }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </x-ui.card>

            {{-- Conformidad --}}
            @if ($maintenanceCase->conformity_name)
            <x-ui.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Conformidad</h3></x-slot:header>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Firmado por</span>
                        <span class="font-medium text-slate-800">{{ $maintenanceCase->conformity_name }}</span>
                    </div>
                    @if ($maintenanceCase->conformity_date)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Fecha</span>
                        <span class="text-slate-700">{{ $maintenanceCase->conformity_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </x-ui.card>
            @endif

            {{-- Costo total --}}
            @if ($maintenanceCase->items->isNotEmpty())
            <x-ui.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Costo total</h3></x-slot:header>
                <p class="text-2xl font-bold text-slate-900">S/ {{ number_format($maintenanceCase->total_cost ?? $maintenanceCase->items->sum('total_cost'), 2) }}</p>
                <p class="text-xs text-slate-500 mt-1">Suma de todos los ítems</p>
            </x-ui.card>
            @endif
        </div>

        {{-- Main content --}}
        <div class="xl:col-span-2 space-y-4">

            {{-- Descripción / Diagnóstico --}}
            <x-ui.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Descripción y diagnóstico</h3></x-slot:header>
                <div class="space-y-4">
                    @if ($maintenanceCase->problem_description)
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Problema</p>
                        <p class="text-sm text-slate-700">{{ $maintenanceCase->problem_description }}</p>
                    </div>
                    @endif
                    @if ($maintenanceCase->diagnosis)
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Diagnóstico</p>
                        <p class="text-sm text-slate-700">{{ $maintenanceCase->diagnosis }}</p>
                    </div>
                    @endif
                    @if ($maintenanceCase->actions_taken)
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Acciones realizadas</p>
                        <p class="text-sm text-slate-700">{{ $maintenanceCase->actions_taken }}</p>
                    </div>
                    @endif
                    @if (!$maintenanceCase->problem_description && !$maintenanceCase->diagnosis && !$maintenanceCase->actions_taken)
                        <p class="text-sm text-slate-400">Aún no se ha registrado diagnóstico ni acciones.</p>
                    @endif
                </div>
            </x-ui.card>

            {{-- Ítems de mantenimiento --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Ítems de mantenimiento</h3>
                    <x-ui.badge tone="neutral">{{ $maintenanceCase->items->count() }}</x-ui.badge>
                </x-slot:header>

                @if ($maintenanceCase->items->isNotEmpty())
                <div class="space-y-2 mb-4">
                    @foreach ($maintenanceCase->items as $item)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-slate-800">{{ $item->name }}</span>
                                <x-ui.badge tone="neutral" size="sm">{{ $item->item_type->label() }}</x-ui.badge>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $item->quantity }} × S/ {{ number_format($item->unit_cost, 2) }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-semibold text-slate-800 text-sm">S/ {{ number_format($item->total_cost, 2) }}</span>
                            @can('maintenance-case.edit')
                            @if (!$isClosed)
                            <form method="POST" action="{{ route('maintenance-cases.items.remove', [$maintenanceCase, $item]) }}"
                                  onsubmit="return confirm('¿Eliminar este ítem?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-rose-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                            @endif
                            @endcan
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-slate-400 mb-4">Sin ítems registrados.</p>
                @endif

                @can('maintenance-case.edit')
                @if (!$isClosed)

                {{-- Aplicar ítems desde plantilla --}}
                @if ($templates->isNotEmpty())
                <div class="border-t border-slate-100 pt-4 mb-4">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-2">Cargar ítems desde plantilla</p>
                    <form method="POST" action="{{ route('maintenance-cases.apply-template', $maintenanceCase) }}"
                          class="flex items-center gap-2"
                          onsubmit="return confirm('¿Agregar todos los ítems de la plantilla seleccionada?')">
                        @csrf
                        <select name="template_id" required
                                class="block flex-1 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                            <option value="">Seleccionar plantilla…</option>
                            @foreach ($templates as $tpl)
                                <option value="{{ $tpl->id }}">{{ $tpl->name }} ({{ $tpl->items->count() }} ítems)</option>
                            @endforeach
                        </select>
                        <x-ui.button type="submit" size="sm" variant="secondary">Aplicar</x-ui.button>
                    </form>
                </div>
                @endif

                <form method="POST" action="{{ route('maintenance-cases.items.add', $maintenanceCase) }}"
                      class="border-t border-slate-100 pt-4">
                    @csrf
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-3">Agregar ítem manual</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="col-span-2 sm:col-span-1">
                            <select name="item_type" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                                @foreach (\App\Enums\MaintenanceItemType::cases() as $t)
                                    <option value="{{ $t->value }}">{{ $t->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="text" name="name" placeholder="Nombre del ítem" required
                                   class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                        </div>
                        <div>
                            <input type="number" name="quantity" placeholder="Cant." value="1" min="1" required
                                   class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                        </div>
                        <div>
                            <input type="number" name="unit_cost" placeholder="Costo unit." step="0.01" min="0" required
                                   class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                        </div>
                    </div>
                    <div class="mt-3">
                        <x-ui.button type="submit" size="sm" variant="secondary">Agregar ítem</x-ui.button>
                    </div>
                </form>
                @endif
                @endcan
            </x-ui.card>

            {{-- Documentos --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Documentos</h3>
                    <x-ui.badge tone="neutral">{{ $maintenanceCase->documents->count() }}</x-ui.badge>
                </x-slot:header>
                @if ($maintenanceCase->documents->isEmpty())
                    <p class="text-sm text-slate-400">Sin documentos adjuntos.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($maintenanceCase->documents as $doc)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $doc->title }}</p>
                                <p class="text-xs text-slate-400">{{ $doc->document_type->label() }}</p>
                            </div>
                            @can('document.view')
                            <x-ui.button variant="ghost" size="xs" href="{{ route('documents.download', $doc) }}">Descargar</x-ui.button>
                            @endcan
                        </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>

    {{-- Modal: Cerrar caso --}}
    @can('maintenance-case.close')
    <x-ui.modal name="close-case" title="Cerrar caso de mantenimiento">
        <form method="POST" action="{{ route('maintenance-cases.close', $maintenanceCase) }}" id="close-case-form">
            @csrf @method('PATCH')
            <div class="space-y-4">
                <p class="text-sm text-slate-600">Completa los datos de conformidad para cerrar el caso.</p>
                <x-ui.input name="conformity_name" label="Nombre del responsable de conformidad" :value="old('conformity_name')" placeholder="Nombre completo" />
                <x-ui.input name="conformity_date" type="date" label="Fecha de conformidad" :value="old('conformity_date', now()->format('Y-m-d'))" />
                <x-ui.textarea name="actions_taken" label="Acciones realizadas (resumen final)" :value="old('actions_taken', $maintenanceCase->actions_taken)" rows="3" />
            </div>
        </form>
        <x-slot:footer>
            <x-ui.button type="submit" form="close-case-form">Confirmar cierre</x-ui.button>
            <x-ui.button variant="secondary" x-data @click="$dispatch('close-modal', 'close-case')">Cancelar</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
    @endcan

</x-layouts.app>

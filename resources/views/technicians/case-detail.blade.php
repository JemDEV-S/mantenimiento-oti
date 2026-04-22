@php
    $typeTone = match($maintenanceCase->maintenance_type->value) {
        'preventivo' => 'primary', 'correctivo' => 'info',
        'predictivo' => 'neutral', 'emergencia' => 'danger', default => 'neutral',
    };
    $priorityTone = match($maintenanceCase->priority->value) {
        'baja' => 'neutral', 'media' => 'warning', 'alta' => 'warning', 'critica' => 'danger', default => 'neutral',
    };
    $priorityBorder = match($maintenanceCase->priority->value) {
        'critica' => 'border-l-rose-500', 'alta' => 'border-l-amber-500',
        'media'   => 'border-l-yellow-400', default => 'border-l-slate-300',
    };

    $statusFlow = [
        ['key' => 'pendiente',   'label' => 'Pendiente',   'icon' => 'clock'],
        ['key' => 'en_progreso', 'label' => 'En Progreso', 'icon' => 'wrench'],
        ['key' => 'en_espera',   'label' => 'En Espera',   'icon' => 'pause'],
        ['key' => 'completado',  'label' => 'Completado',  'icon' => 'check'],
    ];
    $statusIndex = array_search(
        $maintenanceCase->status->value,
        array_column($statusFlow, 'key')
    );

    $page = [
        'title'       => $maintenanceCase->code,
        'moduleLabel' => 'Panel Técnico',
        'pageTitle'   => 'Atención ' . $maintenanceCase->code,
    ];
@endphp

<x-layouts.app :page="$page">

    {{-- Header --}}
    <x-ui.page-header
        :title="$maintenanceCase->code"
        :description="$maintenanceCase->asset?->name ?? 'Sin activo asignado'"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('tecnico.work-queue') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Cola
            </x-ui.button>
            @can('maintenance-case.view')
            <x-ui.button variant="ghost" size="sm" href="{{ route('maintenance-cases.show', $maintenanceCase) }}">
                Vista admin
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- ── Barra de flujo de estado ───────────────────────────────────────── --}}
    <x-ui.card class="mb-4">
        <div class="flex items-center gap-0">
            @foreach ($statusFlow as $i => $step)
            @php
                $isDone    = $statusIndex !== false && $i < $statusIndex;
                $isCurrent = $statusIndex !== false && $i === $statusIndex;
                $isNext    = ! $isClosed && $i === ($statusIndex !== false ? $statusIndex + 1 : 0) && $step['key'] !== 'completado';
            @endphp
            <div class="flex-1 flex items-center {{ $i > 0 ? '' : '' }}">
                @if ($i > 0)
                <div class="h-px flex-1 {{ $isDone || $isCurrent ? 'bg-sigat-300' : 'bg-slate-200' }} mx-2"></div>
                @endif
                <div class="flex flex-col items-center gap-1 shrink-0">
                    @if (! $isClosed && $step['key'] !== 'completado' && ! $isCurrent)
                    <form method="POST" action="{{ route('tecnico.cases.progress', $maintenanceCase) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $step['key'] }}">
                        <button type="submit"
                                class="w-9 h-9 rounded-full flex items-center justify-center transition-all
                                    {{ $isDone ? 'bg-sigat-600 text-white' : 'bg-slate-100 text-slate-400 hover:bg-slate-200' }}
                                    {{ $isNext ? 'ring-2 ring-sigat-300 ring-offset-1 cursor-pointer' : '' }}"
                                title="Cambiar a {{ $step['label'] }}">
                            @if ($isDone)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <span class="text-xs font-bold">{{ $i + 1 }}</span>
                            @endif
                        </button>
                    </form>
                    @else
                    <div class="w-9 h-9 rounded-full flex items-center justify-center
                                {{ $isCurrent && ! $isClosed ? 'bg-sigat-600 text-white ring-4 ring-sigat-100' : '' }}
                                {{ $isClosed && $step['key'] === 'completado' ? 'bg-emerald-500 text-white' : '' }}
                                {{ ! $isCurrent && ! ($isClosed && $step['key'] === 'completado') ? 'bg-slate-100 text-slate-400' : '' }}">
                        @if ($isCurrent && ! $isClosed)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17l-4.655 4.655a2.121 2.121 0 11-3-3l4.655-4.655m3-3l1.06-1.06a2.121 2.121 0 013 0l1.415 1.414a2.121 2.121 0 010 3l-1.06 1.06m-3-3l3 3"/></svg>
                        @elseif ($isClosed && $step['key'] === 'completado')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <span class="text-xs font-bold">{{ $i + 1 }}</span>
                        @endif
                    </div>
                    @endif
                    <span class="text-xs {{ $isCurrent ? 'font-semibold text-sigat-700' : 'text-slate-400' }} whitespace-nowrap">
                        {{ $step['label'] }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </x-ui.card>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">

        {{-- ── Columna izquierda: info del caso ──────────────────────────── --}}
        <div class="space-y-4 xl:col-span-1">

            {{-- Info activo --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Equipo</h3>
                    <div class="flex gap-1.5">
                        <x-ui.badge :tone="$typeTone">{{ $maintenanceCase->maintenance_type->label() }}</x-ui.badge>
                        <x-ui.badge :tone="$priorityTone">{{ $maintenanceCase->priority->label() }}</x-ui.badge>
                    </div>
                </x-slot:header>
                @if ($maintenanceCase->asset)
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="font-semibold text-slate-900 text-base">{{ $maintenanceCase->asset->name }}</p>
                        <p class="font-mono text-xs text-slate-400">{{ $maintenanceCase->asset->internal_code }}</p>
                    </div>
                    @if ($maintenanceCase->asset->organizationalUnit)
                    <div class="flex items-center gap-1.5 text-slate-500">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/></svg>
                        <span class="text-xs">{{ $maintenanceCase->asset->organizationalUnit->name }}</span>
                    </div>
                    @endif
                    @can('asset.view')
                    <x-ui.button variant="ghost" size="xs" href="{{ route('assets.show', $maintenanceCase->asset) }}">
                        Ver ficha del equipo →
                    </x-ui.button>
                    @endcan
                </div>
                @else
                <p class="text-sm text-slate-400">Sin activo vinculado.</p>
                @endif
            </x-ui.card>

            {{-- Fechas y metadatos --}}
            <x-ui.card>
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Detalles del caso</h3></x-slot:header>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Creado</span>
                        <span class="text-slate-700">{{ $maintenanceCase->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if ($maintenanceCase->reportedBy)
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Reportado por</span>
                        <span class="text-slate-700 font-medium">{{ $maintenanceCase->reportedBy->full_name }}</span>
                    </div>
                    @endif
                    @if ($maintenanceCase->campaign)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Campaña</span>
                        <a href="{{ route('campaigns.show', $maintenanceCase->campaign) }}" class="text-sigat-600 font-medium hover:underline">
                            {{ $maintenanceCase->campaign->code }}
                        </a>
                    </div>
                    @endif
                    @if ($maintenanceCase->finished_at)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Cerrado</span>
                        <span class="text-slate-700">{{ $maintenanceCase->finished_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    @if ($maintenanceCase->next_maintenance_date)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Próx. mantenimiento</span>
                        <span class="font-medium text-sigat-700">{{ $maintenanceCase->next_maintenance_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </x-ui.card>

            {{-- Conformidad (si está cerrado) --}}
            @if ($maintenanceCase->conformity_name)
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Conformidad</h3>
                    <x-ui.badge tone="success" :dot="true">Firmado</x-ui.badge>
                </x-slot:header>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nombre</span>
                        <span class="font-semibold text-slate-800">{{ $maintenanceCase->conformity_name }}</span>
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
                <x-slot:header><h3 class="text-sm font-semibold text-slate-900">Costo total estimado</h3></x-slot:header>
                <p class="text-3xl font-bold text-slate-900">
                    S/ {{ number_format($maintenanceCase->total_cost ?? $maintenanceCase->items->sum('total_cost'), 2) }}
                </p>
                <p class="text-xs text-slate-400 mt-1">{{ $maintenanceCase->items->count() }} ítem(s) registrado(s)</p>
            </x-ui.card>
            @endif

        </div>

        {{-- ── Columna derecha: acciones y evidencias ─────────────────────── --}}
        <div class="space-y-4 xl:col-span-2">

            {{-- ─ Descripción del problema ─────────────────────────────── --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Problema reportado</h3>
                </x-slot:header>
                <p class="text-sm text-slate-700 leading-relaxed">
                    {{ $maintenanceCase->problem_description ?? '—' }}
                </p>
            </x-ui.card>

            {{-- ─ Progreso de trabajo (editable si no está cerrado) ─────── --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Progreso de trabajo</h3>
                    @if (! $isClosed)
                    <x-ui.badge tone="warning" :dot="true">Editable</x-ui.badge>
                    @endif
                </x-slot:header>

                @if ($isClosed)
                {{-- Solo lectura --}}
                <div class="space-y-4">
                    @if ($maintenanceCase->diagnosis)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Diagnóstico</p>
                        <p class="text-sm text-slate-700">{{ $maintenanceCase->diagnosis }}</p>
                    </div>
                    @endif
                    @if ($maintenanceCase->actions_taken)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Acciones realizadas</p>
                        <p class="text-sm text-slate-700">{{ $maintenanceCase->actions_taken }}</p>
                    </div>
                    @endif
                    @if ($maintenanceCase->notes)
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Notas</p>
                        <p class="text-sm text-slate-700">{{ $maintenanceCase->notes }}</p>
                    </div>
                    @endif
                    @if (! $maintenanceCase->diagnosis && ! $maintenanceCase->actions_taken)
                    <p class="text-sm text-slate-400">Sin diagnóstico ni acciones registradas.</p>
                    @endif
                </div>
                @else
                {{-- Formulario editable --}}
                @can('maintenance-case.edit')
                <form method="POST" action="{{ route('tecnico.cases.progress', $maintenanceCase) }}" class="space-y-4">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Diagnóstico técnico</label>
                        <textarea name="diagnosis" rows="2"
                                  placeholder="Causa raíz identificada, observaciones técnicas..."
                                  class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500 focus:border-transparent resize-none">{{ old('diagnosis', $maintenanceCase->diagnosis) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Acciones realizadas</label>
                        <textarea name="actions_taken" rows="2"
                                  placeholder="Describe qué se hizo hasta ahora..."
                                  class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500 focus:border-transparent resize-none">{{ old('actions_taken', $maintenanceCase->actions_taken) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Notas internas</label>
                        <textarea name="notes" rows="1"
                                  placeholder="Notas adicionales..."
                                  class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500 focus:border-transparent resize-none">{{ old('notes', $maintenanceCase->notes) }}</textarea>
                    </div>
                    <x-ui.button type="submit" variant="secondary" size="sm">Guardar progreso</x-ui.button>
                </form>
                @endcan
                @endif
            </x-ui.card>

            {{-- ─ Ítems / repuestos ─────────────────────────────────────── --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Ítems y repuestos</h3>
                    <x-ui.badge tone="neutral">{{ $maintenanceCase->items->count() }}</x-ui.badge>
                </x-slot:header>

                @if ($maintenanceCase->items->isNotEmpty())
                <div class="space-y-2 mb-4">
                    @foreach ($maintenanceCase->items as $item)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-slate-800">{{ $item->name }}</span>
                                <x-ui.badge tone="neutral" size="sm">{{ $item->item_type->label() }}</x-ui.badge>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $item->quantity }} × S/ {{ number_format($item->unit_cost, 2) }}
                                @if ($item->description) · {{ $item->description }} @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-3 ml-3 shrink-0">
                            <span class="text-sm font-semibold text-slate-700">S/ {{ number_format($item->total_cost, 2) }}</span>
                            @can('maintenance-case.edit')
                            @if (! $isClosed)
                            <form method="POST" action="{{ route('maintenance-cases.items.remove', [$maintenanceCase, $item]) }}"
                                  onsubmit="return confirm('¿Eliminar este ítem?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-300 hover:text-rose-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                            @endif
                            @endcan
                        </div>
                    </div>
                    @endforeach

                    <div class="flex justify-end pt-1">
                        <span class="text-sm font-bold text-slate-800">
                            Subtotal: S/ {{ number_format($maintenanceCase->items->sum('total_cost'), 2) }}
                        </span>
                    </div>
                </div>
                @else
                <p class="text-sm text-slate-400 mb-4">Sin ítems registrados aún.</p>
                @endif

                @can('maintenance-case.edit')
                @if (! $isClosed)
                <form method="POST" action="{{ route('maintenance-cases.items.add', $maintenanceCase) }}"
                      class="border-t border-slate-100 pt-4">
                    @csrf
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Agregar ítem</p>
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-3">
                            <select name="item_type" required
                                    class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sigat-500">
                                <option value="">Tipo</option>
                                @foreach ($itemTypes as $it)
                                <option value="{{ $it->value }}">{{ $it->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-5">
                            <input type="text" name="name" placeholder="Nombre del ítem" required
                                   class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sigat-500">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="quantity" placeholder="Cant." value="1" min="1" required
                                   class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sigat-500">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="unit_cost" placeholder="S/ 0.00" step="0.01" min="0" required
                                   class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sigat-500">
                        </div>
                    </div>
                    <div class="mt-2">
                        <x-ui.button type="submit" size="sm" variant="secondary">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            Agregar ítem
                        </x-ui.button>
                    </div>
                </form>
                @endif
                @endcan
            </x-ui.card>

            {{-- ─ Evidencias / documentos ──────────────────────────────── --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Evidencias y documentos</h3>
                    <x-ui.badge tone="neutral">{{ $maintenanceCase->documents->count() }}</x-ui.badge>
                </x-slot:header>

                @if ($maintenanceCase->documents->isNotEmpty())
                <div class="space-y-2 mb-4">
                    @foreach ($maintenanceCase->documents as $doc)
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                        <div class="w-8 h-8 rounded-lg bg-sigat-50 text-sigat-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $doc->title }}</p>
                            <p class="text-xs text-slate-400">{{ $doc->document_type->label() }}</p>
                        </div>
                        <x-ui.button variant="ghost" size="xs" href="{{ route('documents.download', $doc) }}">
                            Descargar
                        </x-ui.button>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-slate-400 mb-4">Sin documentos adjuntos.</p>
                @endif

                @can('document.create')
                @if (! $isClosed)
                <form method="POST" action="{{ route('documents.store') }}"
                      enctype="multipart/form-data"
                      class="border-t border-slate-100 pt-4 space-y-3">
                    @csrf
                    <input type="hidden" name="reference_type" value="{{ \App\Models\MaintenanceCase::class }}">
                    <input type="hidden" name="reference_id"   value="{{ $maintenanceCase->id }}">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Subir evidencia</p>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <select name="document_type" required
                                    class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sigat-500">
                                <option value="">Tipo de documento</option>
                                @foreach ($documentTypes as $dt)
                                <option value="{{ $dt->value }}">{{ $dt->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <input type="text" name="title" placeholder="Título del documento" required
                                   class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-sigat-500">
                        </div>
                    </div>
                    <div>
                        <input type="file" name="file" required accept=".pdf,.doc,.docx,.xlsx,.jpg,.png"
                               class="block w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-sigat-50 file:text-sigat-700 hover:file:bg-sigat-100 cursor-pointer">
                        <p class="text-xs text-slate-400 mt-1">PDF, DOC, XLS, JPG, PNG — máx. 10 MB</p>
                    </div>
                    <x-ui.button type="submit" size="sm" variant="secondary">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                        Subir archivo
                    </x-ui.button>
                </form>
                @endif
                @endcan
            </x-ui.card>

            {{-- ─ Cierre del caso (solo si no está cerrado) ────────────── --}}
            @if (! $isClosed)
            @can('maintenance-case.close')
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Cerrar caso</h3>
                    <x-ui.badge tone="neutral">Paso final</x-ui.badge>
                </x-slot:header>

                <p class="text-sm text-slate-500 mb-4">
                    Completa los datos para dejar constancia del trabajo realizado y obtener la conformidad del usuario.
                </p>

                <form method="POST" action="{{ route('maintenance-cases.close', $maintenanceCase) }}"
                      class="space-y-4">
                    @csrf @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Acciones realizadas <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="actions_taken" rows="3" required
                                  placeholder="Describe todo lo que se hizo para resolver el problema..."
                                  class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500 focus:border-transparent resize-none">{{ old('actions_taken', $maintenanceCase->actions_taken) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Nombre del conforme
                            </label>
                            <input type="text" name="conformity_name"
                                   placeholder="Nombre completo del responsable"
                                   value="{{ old('conformity_name') }}"
                                   class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Fecha de conformidad
                            </label>
                            <input type="date" name="conformity_date"
                                   value="{{ old('conformity_date', now()->format('Y-m-d')) }}"
                                   class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Próximo mantenimiento
                            </label>
                            <input type="date" name="next_maintenance_date"
                                   value="{{ old('next_maintenance_date') }}"
                                   class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Costo total (S/)
                            </label>
                            <input type="number" name="total_cost" step="0.01" min="0"
                                   value="{{ old('total_cost', number_format($maintenanceCase->items->sum('total_cost'), 2, '.', '')) }}"
                                   placeholder="0.00"
                                   class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sigat-500">
                            <p class="text-xs text-slate-400 mt-1">Pre-calculado de los ítems. Puedes ajustarlo.</p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                        <p class="text-xs text-slate-400">El caso quedará como <strong>Completado</strong> y no podrá editarse.</p>
                        <x-ui.button type="submit" size="sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Confirmar cierre
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
            @endcan
            @endif

        </div>
    </div>

</x-layouts.app>

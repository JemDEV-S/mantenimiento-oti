@php
    $page = [
        'title'             => 'Cola de Trabajo',
        'moduleLabel'       => 'Panel Técnico',
        'pageTitle'         => 'Cola de Trabajo',
        'headerTitle'       => 'Cola de trabajo',
        'headerDescription' => 'Tus casos organizados por estado.',
    ];

    $columns = [
        'pendiente'   => ['label' => 'Pendientes',       'tone' => 'neutral', 'color' => 'bg-slate-100 text-slate-600'],
        'en_progreso' => ['label' => 'En Proceso',       'tone' => 'warning', 'color' => 'bg-amber-100 text-amber-700'],
        'en_espera'   => ['label' => 'Esperando Repuesto','tone' => 'warning', 'color' => 'bg-sky-100 text-sky-700'],
        'completado'  => ['label' => 'Completados Hoy',  'tone' => 'success', 'color' => 'bg-emerald-100 text-emerald-700'],
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
            <x-ui.button size="sm" href="{{ route('tecnico.attend-asset') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Atender equipo
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if (! $employee)
    <x-ui.card class="mb-4">
        <div class="flex items-start gap-3 p-1">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
            <p class="text-sm text-slate-700">Tu cuenta no tiene perfil de empleado. No hay casos para mostrar.</p>
        </div>
    </x-ui.card>
    @endif

    {{-- Kanban grid --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">

        @foreach ($columns as $statusKey => $col)
        @php $items = $queues[$statusKey]; @endphp
        <div class="flex flex-col gap-3">

            {{-- Column header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $col['color'] }}">
                        {{ $col['label'] }}
                    </span>
                </div>
                <span class="text-xs text-slate-400 font-medium">{{ $items->count() }}</span>
            </div>

            {{-- Cards --}}
            <div class="flex flex-col gap-2 min-h-[120px]">
                @forelse ($items as $case)
                @php
                    $priorityTone = match($case->priority->value) {
                        'baja'    => 'neutral',
                        'media'   => 'warning',
                        'alta'    => 'warning',
                        'critica' => 'danger',
                        default   => 'neutral',
                    };
                    $priorityBorder = match($case->priority->value) {
                        'critica' => 'border-l-rose-500',
                        'alta'    => 'border-l-amber-500',
                        'media'   => 'border-l-yellow-400',
                        default   => 'border-l-slate-200',
                    };
                @endphp
                <a href="{{ $case->status->value === 'completado' ? route('tecnico.cases.show', $case) : route('tecnico.cases.workflow', $case) }}"
                   class="block bg-white rounded-xl border border-slate-200 border-l-4 {{ $priorityBorder }} p-3.5 hover:shadow-md hover:border-slate-300 transition-all duration-150 group">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <span class="font-mono text-xs font-semibold text-sigat-600 bg-sigat-50 px-1.5 py-0.5 rounded">
                            {{ $case->code }}
                        </span>
                        <x-ui.badge :tone="$priorityTone" size="xs">{{ $case->priority->label() }}</x-ui.badge>
                    </div>
                    <p class="text-sm font-medium text-slate-800 group-hover:text-slate-900 leading-snug mb-1">
                        {{ $case->asset?->name ?? '—' }}
                    </p>
                    <p class="text-xs text-slate-400 truncate">
                        {{ Str::limit($case->problem_description, 60) }}
                    </p>
                    <div class="mt-2.5 flex items-center justify-between">
                        <span class="text-xs text-slate-400">{{ $case->maintenance_type->label() }}</span>
                        @if ($case->asset?->internal_code)
                        <span class="text-xs font-mono text-slate-400">{{ $case->asset->internal_code }}</span>
                        @endif
                    </div>
                    <div class="mt-3 text-xs font-semibold text-sigat-600">
                        {{ $case->status->value === 'completado' ? 'Ver detalle del caso' : 'Continuar mantenimiento' }}
                    </div>
                </a>
                @empty
                <div class="flex items-center justify-center h-24 rounded-xl border-2 border-dashed border-slate-200">
                    <p class="text-xs text-slate-400">Sin casos</p>
                </div>
                @endforelse
            </div>

        </div>
        @endforeach

    </div>

</x-layouts.app>

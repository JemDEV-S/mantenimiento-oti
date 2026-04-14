<x-layouts.app title="Unidad Organizacional" moduleLabel="Organización" pageTitle="Detalle de Unidad">

    <x-ui.page-header
        :title="$organizationalUnit->name"
        :description="$organizationalUnit->full_path ?? $organizationalUnit->type->label()"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('organizational-units.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
            @can('org-unit.edit')
            <x-ui.button size="sm" href="{{ route('organizational-units.edit', $organizationalUnit) }}">Editar</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">

        {{-- Info card --}}
        <div class="xl:col-span-1 space-y-4">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Información</h3>
                    <x-ui.badge tone="{{ $organizationalUnit->is_active ? 'success' : 'neutral' }}" :dot="true">
                        {{ $organizationalUnit->is_active ? 'Activa' : 'Inactiva' }}
                    </x-ui.badge>
                </x-slot:header>

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Tipo</span>
                        <x-ui.badge tone="neutral">{{ $organizationalUnit->type->label() }}</x-ui.badge>
                    </div>
                    @if ($organizationalUnit->code)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Código</span>
                        <span class="font-mono font-medium text-slate-800">{{ $organizationalUnit->code }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Unidad padre</span>
                        <span class="text-slate-800">{{ $organizationalUnit->parent?->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Subunidades</span>
                        <span class="font-semibold text-slate-800">{{ $organizationalUnit->children->count() }}</span>
                    </div>
                    @if ($organizationalUnit->responsible)
                    <div class="pt-2 border-t border-slate-100">
                        <p class="text-xs text-slate-500 mb-2">Responsable</p>
                        <div class="flex items-center gap-2">
                            <x-ui.avatar :name="$organizationalUnit->responsible->full_name" size="sm" />
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $organizationalUnit->responsible->full_name }}</p>
                                <p class="text-xs text-slate-400">{{ $organizationalUnit->responsible->position ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </x-ui.card>

            {{-- Subunidades --}}
            @if ($organizationalUnit->children->isNotEmpty())
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Subunidades</h3>
                    <x-ui.badge tone="neutral">{{ $organizationalUnit->children->count() }}</x-ui.badge>
                </x-slot:header>
                <div class="space-y-1">
                    @foreach ($organizationalUnit->children as $child)
                    <a href="{{ route('organizational-units.show', $child) }}"
                       class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                        <span class="text-sm font-medium text-slate-700">{{ $child->name }}</span>
                        <x-ui.badge tone="neutral" size="sm">{{ $child->type->label() }}</x-ui.badge>
                    </a>
                    @endforeach
                </div>
            </x-ui.card>
            @endif
        </div>

        {{-- Empleados y activos --}}
        <div class="xl:col-span-2 space-y-4">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Empleados</h3>
                    <x-ui.badge tone="neutral">{{ $organizationalUnit->empoyees->count() }}</x-ui.badge>
                </x-slot:header>
                @if ($organizationalUnit->empoyees->isEmpty())
                    <p class="text-sm text-slate-400 py-4 text-center">Sin empleados en esta unidad.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($organizationalUnit->empoyees as $emp)
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50">
                            <x-ui.avatar :name="$emp->full_name" size="xs" />
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $emp->full_name }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ $emp->position ?? '' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Activos</h3>
                    <x-ui.badge tone="neutral">{{ $organizationalUnit->assets->count() }}</x-ui.badge>
                </x-slot:header>
                @if ($organizationalUnit->assets->isEmpty())
                    <p class="text-sm text-slate-400 py-4 text-center">Sin activos registrados en esta unidad.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($organizationalUnit->assets->take(8) as $asset)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $asset->name }}</p>
                                <p class="text-xs text-slate-400">{{ $asset->internal_code }} &middot; {{ $asset->asset_type->label() }}</p>
                            </div>
                            @can('asset.view')
                            <x-ui.button variant="ghost" size="xs" href="{{ route('assets.show', $asset) }}">Ver</x-ui.button>
                            @endcan
                        </div>
                        @endforeach
                        @if ($organizationalUnit->assets->count() > 8)
                            <p class="text-xs text-slate-400 text-center pt-1">+{{ $organizationalUnit->assets->count() - 8 }} más activos</p>
                        @endif
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>

</x-layouts.app>

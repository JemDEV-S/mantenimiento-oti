<x-layouts.app title="Detalle Empleado" moduleLabel="Empleados" pageTitle="Detalle de Empleado">

    <x-ui.page-header
        :title="$employee->full_name"
        :description="$employee->position ?? 'Sin cargo asignado'"
    >
        <x-slot:actions>
            <x-ui.button variant="secondary" size="sm" href="{{ route('employees.index') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Volver
            </x-ui.button>
            @can('employee.edit')
            <x-ui.button size="sm" href="{{ route('employees.edit', $employee) }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                Editar
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">

        {{-- Profile card --}}
        <div class="xl:col-span-1">
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Perfil</h3>
                    @if ($employee->is_active)
                        <x-ui.badge tone="success" :dot="true">Activo</x-ui.badge>
                    @else
                        <x-ui.badge tone="neutral" :dot="true">Inactivo</x-ui.badge>
                    @endif
                </x-slot:header>

                <div class="flex flex-col items-center py-4">
                    <x-ui.avatar :name="$employee->full_name" size="xl" />
                    <h2 class="mt-3 text-base font-semibold text-slate-900">{{ $employee->full_name }}</h2>
                    <p class="text-sm text-slate-500">{{ $employee->position ?? '—' }}</p>
                    @if ($employee->is_technician)
                        <x-ui.badge tone="info" class="mt-2">Técnico</x-ui.badge>
                    @endif
                </div>

                <div class="space-y-3 border-t border-slate-100 pt-4 mt-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">DNI</span>
                        <span class="font-mono font-medium text-slate-800">{{ $employee->dni }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Correo</span>
                        <span class="text-slate-800">{{ $employee->email ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Teléfono</span>
                        <span class="text-slate-800">{{ $employee->phone ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Unidad</span>
                        <span class="text-slate-800 text-right">{{ $employee->organizationalUnit?->name ?? '—' }}</span>
                    </div>
                    @if ($employee->is_technician)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Especialidad</span>
                        <span class="text-slate-800">{{ $employee->specialty ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Nivel técnico</span>
                        <span class="text-slate-800">{{ $employee->technical_level ?? '—' }}</span>
                    </div>
                    @endif
                </div>

                @can('employee.edit')
                <x-slot:footer>
                    <form method="POST" action="{{ route('employees.toggle-active', $employee) }}" class="w-full">
                        @csrf @method('PATCH')
                        <x-ui.button type="submit" variant="secondary" class="w-full justify-center">
                            {{ $employee->is_active ? 'Desactivar empleado' : 'Activar empleado' }}
                        </x-ui.button>
                    </form>
                </x-slot:footer>
                @endcan
            </x-ui.card>
        </div>

        {{-- Tabs: activos, casos --}}
        <div class="xl:col-span-2 space-y-4">

            {{-- Activos asignados --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Activos asignados</h3>
                    <x-ui.badge tone="neutral">{{ $employee->assignedAssets->count() }}</x-ui.badge>
                </x-slot:header>

                @if ($employee->assignedAssets->isEmpty())
                    <p class="text-sm text-slate-400 py-4 text-center">Sin activos asignados.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($employee->assignedAssets as $asset)
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
                    </div>
                @endif
            </x-ui.card>

            {{-- Casos de mantenimiento --}}
            @if ($employee->is_technician)
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Casos asignados</h3>
                    <x-ui.badge tone="neutral">{{ $employee->maintenanceCases->count() }}</x-ui.badge>
                </x-slot:header>

                @if ($employee->maintenanceCases->isEmpty())
                    <p class="text-sm text-slate-400 py-4 text-center">Sin casos asignados.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($employee->maintenanceCases->take(5) as $case)
                        @php
                            $statusTone = match($case->status->value) {
                                'pendiente' => 'neutral', 'en_progreso' => 'warning',
                                'completado' => 'success', 'cancelado' => 'danger', default => 'neutral',
                            };
                        @endphp
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $case->code }}</p>
                                <p class="text-xs text-slate-400">{{ $case->asset?->name ?? '—' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-ui.badge :tone="$statusTone" size="sm">{{ $case->status->label() }}</x-ui.badge>
                                @can('maintenance-case.view')
                                <x-ui.button variant="ghost" size="xs" href="{{ route('maintenance-cases.show', $case) }}">Ver</x-ui.button>
                                @endcan
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>
            @endif

            {{-- Usuario del sistema --}}
            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-semibold text-slate-900">Cuenta del sistema</h3>
                </x-slot:header>
                @if ($employee->user)
                    <div class="flex items-center gap-3">
                        <x-ui.avatar :name="$employee->user->name" size="sm" />
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $employee->user->name }}</p>
                            <p class="text-xs text-slate-400">@{{ $employee->user->username }}</p>
                        </div>
                        @can('user.edit')
                        <x-ui.button variant="ghost" size="xs" href="{{ route('users.edit', $employee->user) }}" class="ml-auto">Editar cuenta</x-ui.button>
                        @endcan
                    </div>
                @else
                    <p class="text-sm text-slate-400">Este empleado no tiene cuenta de acceso vinculada.</p>
                @endif
            </x-ui.card>
        </div>
    </div>

</x-layouts.app>

<x-layouts.app title="Configuración" moduleLabel="Configuración" pageTitle="Configuración del Sistema">

    <x-ui.page-header
        title="Configuración del sistema"
        description="Parámetros operativos ajustables sin necesidad de despliegue de código."
    >
    </x-ui.page-header>

    {{-- Group tabs --}}
    @if ($groups->isNotEmpty())
    <div class="flex flex-wrap gap-2 mb-4">
        <a href="{{ route('settings.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition-colors {{ !request('group') ? 'bg-sigat-600 text-white' : 'bg-white text-slate-600 border border-slate-300 hover:bg-slate-50' }}">
            Todos
        </a>
        @foreach ($groups as $group)
        <a href="{{ route('settings.index', ['group' => $group]) }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition-colors capitalize {{ request('group') === $group ? 'bg-sigat-600 text-white' : 'bg-white text-slate-600 border border-slate-300 hover:bg-slate-50' }}">
            {{ $group }}
        </a>
        @endforeach
    </div>
    @endif

    <div class="space-y-3">
        @forelse ($settings as $setting)
        <x-ui.card>
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <p class="font-medium text-slate-900">{{ $setting->label ?? $setting->key }}</p>
                        <x-ui.badge tone="neutral" size="sm">{{ $setting->group_name }}</x-ui.badge>
                        <x-ui.badge tone="neutral" size="sm">{{ $setting->type }}</x-ui.badge>
                    </div>
                    @if ($setting->description)
                        <p class="text-sm text-slate-500">{{ $setting->description }}</p>
                    @endif
                    <p class="font-mono text-xs text-slate-400 mt-1">{{ $setting->key }}</p>
                </div>

                @can('setting.edit')
                <div class="shrink-0" x-data="{ editing: false }">
                    <div x-show="!editing" class="flex items-center gap-2">
                        <span class="text-sm font-medium text-slate-700 font-mono">
                            @if ($setting->type === 'boolean')
                                {{ $setting->value ? 'true' : 'false' }}
                            @elseif ($setting->type === 'json')
                                [JSON]
                            @else
                                {{ $setting->value }}
                            @endif
                        </span>
                        <x-ui.button variant="ghost" size="xs" x-on:click="editing = true">Editar</x-ui.button>
                    </div>

                    <div x-show="editing" x-cloak>
                        <form method="POST" action="{{ route('settings.update', $setting) }}" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            @if ($setting->type === 'boolean')
                                <select name="value" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                                    <option value="1" {{ $setting->value ? 'selected' : '' }}>true</option>
                                    <option value="0" {{ !$setting->value ? 'selected' : '' }}>false</option>
                                </select>
                            @elseif ($setting->type === 'text' || $setting->type === 'json')
                                <textarea name="value" rows="3" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none w-64">{{ $setting->raw_value }}</textarea>
                            @else
                                <input type="{{ $setting->type === 'integer' ? 'number' : 'text' }}" name="value"
                                       value="{{ $setting->raw_value }}"
                                       class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none w-48">
                            @endif
                            <x-ui.button type="submit" size="xs">Guardar</x-ui.button>
                            <x-ui.button type="button" variant="ghost" size="xs" x-on:click="editing = false">Cancelar</x-ui.button>
                        </form>
                    </div>
                </div>
                @else
                <span class="text-sm font-medium text-slate-700 font-mono">{{ $setting->value }}</span>
                @endcan
            </div>
        </x-ui.card>
        @empty
        <x-ui.empty-state title="Sin configuraciones" description="No hay parámetros disponibles en este grupo." />
        @endforelse
    </div>

</x-layouts.app>

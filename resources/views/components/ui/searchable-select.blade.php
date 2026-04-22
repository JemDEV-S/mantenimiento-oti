@props([
    'label'       => null,
    'name',
    'options'     => [],  // array asociativo ['id' => 'nombre'] o colección
    'value'       => '',
    'placeholder' => 'Seleccionar...',
    'searchPlaceholder' => 'Buscar...',
    'hint'        => null,
    'nullable'    => true,  // muestra opción "Sin asignar" al inicio
])

@php
    // Normaliza a array de objetos {id, name} para Alpine
    $normalized = collect($options)
        ->map(fn($name, $id) => ['id' => (string) $id, 'name' => (string) $name])
        ->values()
        ->toArray();

    $currentValue = (string) old($name, $value ?? '');
    $nullLabel    = $placeholder;
@endphp

<div
    class="space-y-1.5"
    x-data="{
        open: false,
        search: '',
        selected: { id: '{{ $currentValue }}', name: '' },
        options: {{ Js::from($normalized) }},
        get filtered() {
            if (!this.search) return this.options;
            const q = this.search.toLowerCase();
            return this.options.filter(o => o.name.toLowerCase().includes(q));
        },
        init() {
            const match = this.options.find(o => o.id === this.selected.id);
            if (match) this.selected.name = match.name;
        },
        select(option) {
            this.selected = option;
            this.search   = '';
            this.open     = false;
        },
        clear() {
            this.selected = { id: '', name: '' };
            this.search   = '';
        }
    }"
    x-init="init()"
    @click.outside="open = false"
>
    @if ($label)
        <label for="{{ $name }}_trigger" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    @if ($hint)
        <p class="text-xs text-slate-400">{{ $hint }}</p>
    @endif

    <input type="hidden" name="{{ $name }}" :value="selected.id">

    <div class="relative">
        {{-- Trigger --}}
        <div
            id="{{ $name }}_trigger"
            @click="open = !open"
            class="flex items-center justify-between w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm cursor-pointer transition-colors focus-within:border-sigat-500 focus-within:ring-2 focus-within:ring-sigat-500/20"
            :class="open ? 'border-sigat-500 ring-2 ring-sigat-500/20' : ''"
        >
            <span x-show="!open" class="truncate" :class="selected.name ? 'text-slate-900' : 'text-slate-400'">
                <span x-text="selected.name || '{{ $nullLabel }}'"></span>
            </span>
            <input
                x-show="open"
                x-ref="searchInput"
                x-model="search"
                @click.stop
                placeholder="{{ $searchPlaceholder }}"
                class="flex-1 outline-none bg-transparent text-sm text-slate-900 placeholder-slate-400"
                x-transition
            >
            <div class="flex items-center gap-1 ml-2 shrink-0">
                <button
                    x-show="selected.id"
                    @click.stop="clear()"
                    type="button"
                    class="text-slate-400 hover:text-slate-600 p-0.5 rounded"
                    title="Limpiar"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-150" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        {{-- Dropdown --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            @click.stop
            class="absolute z-50 mt-1 w-full rounded-xl border border-slate-200 bg-white shadow-lg py-1 max-h-56 overflow-y-auto"
            x-effect="if(open) $nextTick(() => $refs.searchInput.focus())"
        >
            <div x-show="filtered.length === 0" class="px-4 py-2.5 text-sm text-slate-400 text-center">
                Sin resultados
            </div>

            @if ($nullable)
                <button
                    type="button"
                    @click="select({ id: '', name: '' })"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 transition-colors"
                    :class="selected.id === '' ? 'bg-sigat-50 text-sigat-700 font-medium' : 'text-slate-400'"
                >
                    {{ $placeholder }}
                </button>
            @endif

            <template x-for="option in filtered" :key="option.id">
                <button
                    type="button"
                    @click="select(option)"
                    class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 transition-colors"
                    :class="selected.id === option.id ? 'bg-sigat-50 text-sigat-700 font-medium' : 'text-slate-700'"
                    x-text="option.name"
                ></button>
            </template>
        </div>
    </div>

    @error($name)
        <p class="text-xs text-rose-600 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $message }}
        </p>
    @enderror
</div>

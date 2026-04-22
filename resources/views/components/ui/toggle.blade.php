@props([
    'label',
    'name',
    'checked' => false,
    'hint' => null,
])

<label class="group flex items-center justify-between gap-3 cursor-pointer select-none">
    <span>
        <span class="block text-sm font-medium text-slate-700">{{ $label }}</span>
        @if ($hint)
            <span class="block text-xs text-slate-400 mt-0.5">{{ $hint }}</span>
        @endif
    </span>

    <span x-data="{ on: {{ $checked ? 'true' : 'false' }} }" class="relative shrink-0">
        <input type="hidden" name="{{ $name }}" :value="on ? 1 : 0">
        <button
            type="button"
            @click="on = !on; $dispatch('toggle:{{ $name }}', { value: on })"
            :class="on ? 'bg-sigat-600' : 'bg-slate-200'"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-sigat-500/20 focus:ring-offset-2"
            {{ $attributes }}
        >
            <span
                :class="on ? 'translate-x-6' : 'translate-x-1'"
                class="inline-block h-4 w-4 rounded-full bg-white shadow-sm transition-transform duration-200"
            ></span>
        </button>
    </span>
</label>

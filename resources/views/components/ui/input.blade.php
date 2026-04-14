@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => '',
    'hint' => null,
    'prefix' => null,
])

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    @if ($hint)
        <p class="text-xs text-slate-400">{{ $hint }}</p>
    @endif

    <div class="relative">
        @if ($prefix)
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 pointer-events-none">{{ $prefix }}</span>
        @endif
        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
            {{ $attributes->merge(['class' => 'block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 transition-colors focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none disabled:bg-slate-50 disabled:text-slate-400' . ($prefix ? ' pl-10' : '')]) }}
        >
    </div>

    @error($name)
        <p class="text-xs text-rose-600 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            {{ $message }}
        </p>
    @enderror
</div>

@props([
    'label',
    'name',
    'checked' => false,
    'hint' => null,
    'value' => 1,
])

<div class="space-y-1">
    <label class="group flex items-start gap-3 cursor-pointer select-none">
        <input type="hidden" name="{{ $name }}" value="0">
        <input
            type="checkbox"
            name="{{ $name }}"
            value="{{ $value }}"
            @checked(old($name, $checked) == $value || (old($name) === null && $checked))
            {{ $attributes->merge(['class' => 'mt-0.5 h-4.5 w-4.5 rounded-md border-slate-300 text-sigat-600 focus:ring-sigat-500/20 transition-colors']) }}
        >
        <span>
            <span class="block text-sm font-medium text-slate-700 group-hover:text-slate-900 transition-colors">{{ $label }}</span>
            @if ($hint)
                <span class="block text-xs text-slate-400 mt-0.5">{{ $hint }}</span>
            @endif
        </span>
    </label>

    @error($name)
        <p class="text-xs text-rose-600 pl-7">{{ $message }}</p>
    @enderror
</div>

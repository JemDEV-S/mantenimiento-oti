@props([
    'title',
    'description' => null,
    'eyebrow' => null,
])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        @if ($eyebrow)
            <p class="text-xs font-bold text-sigat-600 uppercase tracking-wider mb-1">{{ $eyebrow }}</p>
        @endif
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-sm text-slate-500 max-w-2xl">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex items-center gap-2 shrink-0">
            {{ $actions }}
        </div>
    @endisset
</div>

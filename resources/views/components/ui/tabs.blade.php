@props([
    'active' => null,
    'tabs' => [],
])

<div x-data="{ activeTab: '{{ $active ?? (count($tabs) ? array_key_first($tabs) : '') }}' }" {{ $attributes }}>
    {{-- Tab Headers --}}
    <div class="flex items-center gap-1 p-1 bg-slate-100/80 rounded-xl border border-slate-200/60 w-fit">
        @foreach ($tabs as $key => $label)
            <button
                @click="activeTab = '{{ $key }}'"
                :class="activeTab === '{{ $key }}'
                    ? 'bg-white text-slate-900 shadow-sm'
                    : 'text-slate-500 hover:text-slate-700'"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Tab Panels --}}
    <div class="mt-4">
        {{ $slot }}
    </div>
</div>

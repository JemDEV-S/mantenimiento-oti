@props([
    'striped' => false,
])

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    @isset($toolbar)
        <div class="flex items-center justify-between gap-4 px-5 py-3.5 border-b border-slate-100">
            {{ $toolbar }}
        </div>
    @endisset

    <div class="overflow-x-auto">
        <table class="w-full min-w-[640px]" {{ $attributes }}>
            @isset($head)
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50">
                        {{ $head }}
                    </tr>
                </thead>
            @endisset
            <tbody class="{{ $striped ? '[&>tr:nth-child(even)]:bg-slate-50/50' : '' }} divide-y divide-slate-100">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @isset($pagination)
        <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between">
            {{ $pagination }}
        </div>
    @endisset
</div>

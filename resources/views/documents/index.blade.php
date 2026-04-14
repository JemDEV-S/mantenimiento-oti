<x-layouts.app title="Documentos" moduleLabel="Documentos" pageTitle="Gestión de Documentos">

    <x-ui.page-header
        title="Documentos"
        description="Repositorio de archivos digitales asociados a los activos y casos del sistema."
    >
        <x-slot:actions>
            @can('document.create')
            <x-ui.button size="sm" x-data @click="$dispatch('open-modal', 'upload-document')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                Subir documento
            </x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <form method="GET" action="{{ route('documents.index') }}" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="w-44">
            <select name="type" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-sigat-500 focus:ring-2 focus:ring-sigat-500/20 focus:outline-none">
                <option value="">Todos los tipos</option>
                @foreach ($types as $type)
                    <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>
        <x-ui.button type="submit" variant="secondary" size="sm">Filtrar</x-ui.button>
        @if (request()->hasAny(['type']))
            <x-ui.button variant="ghost" size="sm" href="{{ route('documents.index') }}">Limpiar</x-ui.button>
        @endif
    </form>

    <x-ui.data-table>
        <x-slot:toolbar>
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Documentos registrados</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $documents->total() }} documento(s)</p>
            </div>
        </x-slot:toolbar>

        <x-slot:head>
            <x-ui.th>Documento</x-ui.th>
            <x-ui.th>Tipo</x-ui.th>
            <x-ui.th>Asociado a</x-ui.th>
            <x-ui.th>Subido por</x-ui.th>
            <x-ui.th>Fecha</x-ui.th>
            <x-ui.th></x-ui.th>
        </x-slot:head>

        @forelse ($documents as $doc)
        <tr class="hover:bg-slate-50/50 transition-colors">
            <x-ui.td>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <div>
                        <p class="font-medium text-slate-900">{{ $doc->title }}</p>
                        @if ($doc->file_name)
                            <p class="text-xs text-slate-400">{{ $doc->file_name }}</p>
                        @endif
                    </div>
                </div>
            </x-ui.td>
            <x-ui.td>
                <x-ui.badge tone="neutral">{{ $doc->document_type->label() }}</x-ui.badge>
            </x-ui.td>
            <x-ui.td>
                @if ($doc->reference)
                    <span class="text-sm text-slate-600">{{ class_basename($doc->reference_type) }}</span>
                @else
                    <span class="text-xs text-slate-400">—</span>
                @endif
            </x-ui.td>
            <x-ui.td>
                <span class="text-sm text-slate-500">{{ $doc->generatedBy?->name ?? '—' }}</span>
            </x-ui.td>
            <x-ui.td>
                <span class="text-sm text-slate-500">{{ $doc->created_at->format('d/m/Y') }}</span>
            </x-ui.td>
            <x-ui.td>
                <div class="flex items-center gap-1 justify-end">
                    @can('document.view')
                    <x-ui.button variant="ghost" size="xs" href="{{ route('documents.download', $doc) }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        Descargar
                    </x-ui.button>
                    @endcan
                    @can('document.delete')
                    <form method="POST" action="{{ route('documents.destroy', $doc) }}"
                          onsubmit="return confirm('¿Eliminar este documento?')" class="inline">
                        @csrf @method('DELETE')
                        <x-ui.button type="submit" variant="ghost" size="xs" class="text-rose-600 hover:text-rose-700">Eliminar</x-ui.button>
                    </form>
                    @endcan
                </div>
            </x-ui.td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="py-16">
                <x-ui.empty-state title="Sin documentos" description="Sube el primer documento al repositorio." />
            </td>
        </tr>
        @endforelse

        <x-slot:pagination>{{ $documents->withQueryString()->links() }}</x-slot:pagination>
    </x-ui.data-table>

    {{-- Modal: Subir documento --}}
    @can('document.create')
    <x-ui.modal name="upload-document" title="Subir documento">
        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" id="upload-document-form">
            @csrf
            <div class="space-y-4">
                <x-ui.input name="title" label="Título del documento" :value="old('title')" placeholder="Ej: Acta de entrega — PC-00123" />
                <x-ui.select
                    name="document_type"
                    label="Tipo de documento"
                    :value="old('document_type')"
                    :options="collect($types)->mapWithKeys(fn($t) => [$t->value => $t->label()])->toArray()"
                    placeholder="Seleccionar tipo..."
                />
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700">Archivo</label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx,.xlsx,.jpg,.png" required
                           class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-sigat-50 file:text-sigat-700 hover:file:bg-sigat-100 transition-colors">
                    <p class="text-xs text-slate-400">PDF, DOC, DOCX, XLSX, JPG, PNG — máx. 10 MB</p>
                </div>
            </div>
        </form>
        <x-slot:footer>
            <x-ui.button type="submit" form="upload-document-form">Subir documento</x-ui.button>
            <x-ui.button variant="secondary" x-data @click="$dispatch('close-modal', 'upload-document')">Cancelar</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
    @endcan

</x-layouts.app>

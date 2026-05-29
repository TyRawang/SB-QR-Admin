@props(['preset', 'action', 'method' => 'POST'])

<form method="POST" action="{{ $action }}" class="space-y-8">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    {{-- Identity --}}
    <div class="rounded-lg bg-white shadow p-6 space-y-4">
        <h2 class="text-lg font-semibold text-gray-900">Identity</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $preset->name) }}" required maxlength="100"
                       class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
            <div class="flex items-end">
                <label class="inline-flex items-center gap-2">
                    <input type="hidden" name="is_default" value="0">
                    <input type="checkbox" name="is_default" value="1" {{ old('is_default', $preset->is_default) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                    <span class="text-sm text-gray-700">Use as default preset</span>
                </label>
            </div>
        </div>
    </div>

    {{-- Layout --}}
    <div class="rounded-lg bg-white shadow p-6 space-y-4">
        <h2 class="text-lg font-semibold text-gray-900">Layout</h2>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div>
                <label for="page_size" class="block text-sm font-medium text-gray-700">Page Size</label>
                <select name="page_size" id="page_size"
                        class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                    @foreach(\App\Models\PrintPreset::PAGE_SIZES as $size)
                        <option value="{{ $size }}" {{ old('page_size', $preset->page_size) === $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('page_size')" class="mt-1" />
            </div>
            <div>
                <label for="cols" class="block text-sm font-medium text-gray-700">Columns</label>
                <input type="number" name="cols" id="cols" min="1" max="20" value="{{ old('cols', $preset->cols ?? 4) }}" required
                       class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                <x-input-error :messages="$errors->get('cols')" class="mt-1" />
            </div>
            <div>
                <label for="rows" class="block text-sm font-medium text-gray-700">Rows</label>
                <input type="number" name="rows" id="rows" min="1" max="20" value="{{ old('rows', $preset->rows ?? 5) }}" required
                       class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                <x-input-error :messages="$errors->get('rows')" class="mt-1" />
            </div>
            <div>
                <label for="text_size" class="block text-sm font-medium text-gray-700">Text Size (pt)</label>
                <input type="number" name="text_size" id="text_size" min="4" max="48" value="{{ old('text_size', $preset->text_size ?? 8) }}" required
                       class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                <x-input-error :messages="$errors->get('text_size')" class="mt-1" />
            </div>
        </div>

        <div>
            <label class="inline-flex items-center gap-2">
                <input type="hidden" name="show_text" value="0">
                <input type="checkbox" name="show_text" value="1" {{ old('show_text', $preset->show_text ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                <span class="text-sm text-gray-700">Show text under each QR code</span>
            </label>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-900 mb-2">Margins (mm)</h3>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                @foreach(['top', 'right', 'bottom', 'left'] as $side)
                    @php $field = "margin_{$side}"; @endphp
                    <div>
                        <label for="{{ $field }}" class="block text-xs font-medium text-gray-600 capitalize">{{ $side }}</label>
                        <input type="number" step="0.1" min="0" max="100" name="{{ $field }}" id="{{ $field }}"
                               value="{{ old($field, $preset->$field ?? 10) }}" required
                               class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                        <x-input-error :messages="$errors->get($field)" class="mt-1" />
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <h3 class="text-sm font-medium text-gray-900 mb-2">Gaps (mm)</h3>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div>
                    <label for="gap_x" class="block text-xs font-medium text-gray-600">Horizontal</label>
                    <input type="number" step="0.1" min="0" max="50" name="gap_x" id="gap_x"
                           value="{{ old('gap_x', $preset->gap_x ?? 5) }}" required
                           class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                    <x-input-error :messages="$errors->get('gap_x')" class="mt-1" />
                </div>
                <div>
                    <label for="gap_y" class="block text-xs font-medium text-gray-600">Vertical</label>
                    <input type="number" step="0.1" min="0" max="50" name="gap_y" id="gap_y"
                           value="{{ old('gap_y', $preset->gap_y ?? 5) }}" required
                           class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                    <x-input-error :messages="$errors->get('gap_y')" class="mt-1" />
                </div>
            </div>
        </div>
    </div>

    {{-- Images --}}
    <div class="rounded-lg bg-white shadow p-6 space-y-4" x-data="{ logo: '{{ old('logo_url', $preset->logo_url) }}', bg: '{{ old('background_url', $preset->background_url) }}' }">
        <h2 class="text-lg font-semibold text-gray-900">Images</h2>
        <p class="text-sm text-gray-500">Public URLs. Leave blank to use no image. The edge function falls back to the <code class="text-xs bg-gray-100 px-1 rounded">COMPANY_LOGO_URL</code> / <code class="text-xs bg-gray-100 px-1 rounded">QR_BACKGROUND_IMAGE_URL</code> secrets when both are blank.</p>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div>
                <label for="logo_url" class="block text-sm font-medium text-gray-700">Logo URL</label>
                <input type="url" name="logo_url" id="logo_url" x-model="logo" maxlength="2048"
                       placeholder="https://..."
                       class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                <x-input-error :messages="$errors->get('logo_url')" class="mt-1" />
                <div class="mt-2 rounded border border-dashed border-gray-300 p-2 h-32 flex items-center justify-center bg-gray-50">
                    <template x-if="logo">
                        <img :src="logo" alt="Logo preview" class="max-h-28 max-w-full object-contain" @error="$el.style.display='none'">
                    </template>
                    <template x-if="!logo">
                        <span class="text-xs text-gray-400">No logo</span>
                    </template>
                </div>
            </div>

            <div>
                <label for="background_url" class="block text-sm font-medium text-gray-700">QR Background URL</label>
                <input type="url" name="background_url" id="background_url" x-model="bg" maxlength="2048"
                       placeholder="https://..."
                       class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                <x-input-error :messages="$errors->get('background_url')" class="mt-1" />
                <div class="mt-2 rounded border border-dashed border-gray-300 p-2 h-32 flex items-center justify-center bg-gray-50">
                    <template x-if="bg">
                        <img :src="bg" alt="Background preview" class="max-h-28 max-w-full object-contain" @error="$el.style.display='none'">
                    </template>
                    <template x-if="!bg">
                        <span class="text-xs text-gray-400">No background</span>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.print-presets.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
            {{ $preset->exists ? 'Update Preset' : 'Create Preset' }}
        </button>
    </div>
</form>

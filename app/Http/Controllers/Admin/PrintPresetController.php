<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintPreset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PrintPresetController extends Controller
{
    public function index()
    {
        $presets = PrintPreset::orderByDesc('is_default')->orderBy('name')->get();
        return view('admin.print-presets.index', compact('presets'));
    }

    public function create()
    {
        $preset = new PrintPreset();
        return view('admin.print-presets.create', compact('preset'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $preset = PrintPreset::create($data);

        if ($request->boolean('is_default') || PrintPreset::count() === 1) {
            $preset->makeDefault();
        }

        return redirect()->route('admin.print-presets.index')
            ->with('success', "Preset \"{$preset->name}\" created.");
    }

    public function edit(PrintPreset $printPreset)
    {
        return view('admin.print-presets.edit', ['preset' => $printPreset]);
    }

    public function update(Request $request, PrintPreset $printPreset)
    {
        $data = $this->validated($request, $printPreset->id);
        $printPreset->update($data);

        if ($request->boolean('is_default')) {
            $printPreset->makeDefault();
        }

        return redirect()->route('admin.print-presets.index')
            ->with('success', "Preset \"{$printPreset->name}\" updated.");
    }

    public function destroy(PrintPreset $printPreset)
    {
        if ($printPreset->is_default && PrintPreset::count() > 1) {
            return back()->with('error', 'Set another preset as default before deleting this one.');
        }

        $name = $printPreset->name;
        $printPreset->delete();

        return redirect()->route('admin.print-presets.index')
            ->with('success', "Preset \"{$name}\" deleted.");
    }

    public function setDefault(PrintPreset $printPreset)
    {
        $printPreset->makeDefault();
        return back()->with('success', "\"{$printPreset->name}\" is now the default preset.");
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('print_presets', 'name')->ignore($ignoreId)],
            'is_default' => ['nullable', 'boolean'],
            'cols' => ['required', 'integer', 'min:1', 'max:20'],
            'rows' => ['required', 'integer', 'min:1', 'max:20'],
            'page_size' => ['required', Rule::in(PrintPreset::PAGE_SIZES)],
            'margin_top' => ['required', 'numeric', 'min:0', 'max:100'],
            'margin_right' => ['required', 'numeric', 'min:0', 'max:100'],
            'margin_bottom' => ['required', 'numeric', 'min:0', 'max:100'],
            'margin_left' => ['required', 'numeric', 'min:0', 'max:100'],
            'gap_x' => ['required', 'numeric', 'min:0', 'max:50'],
            'gap_y' => ['required', 'numeric', 'min:0', 'max:50'],
            'show_text' => ['nullable', 'boolean'],
            'text_size' => ['required', 'integer', 'min:4', 'max:48'],
            'logo_url' => ['nullable', 'url', 'max:2048'],
            'background_url' => ['nullable', 'url', 'max:2048'],
            'background_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);
    }
}

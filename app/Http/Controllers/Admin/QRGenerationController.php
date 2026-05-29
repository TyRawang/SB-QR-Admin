<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrintPreset;
use App\Services\SupabaseService;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QRGenerationController extends Controller
{
    public function __construct(
        protected SupabaseService $supabase,
        protected SupabaseStorageService $storage,
    ) {}

    public function index()
    {
        $unclaimedBoxes = $this->supabase->getBoxes([
            'is_claimed' => 'eq.false',
            'select' => 'id, qr_uuid, created_at',
            'order' => 'created_at.desc',
        ]);

        $exports = [];
        try {
            $exports = $this->storage->listFiles('exports');
        } catch (\Exception $e) {
            Log::warning('QR Generation: failed to list exports', ['error' => $e->getMessage()]);
        }

        $presets = PrintPreset::orderByDesc('is_default')->orderBy('name')->get();
        $defaultPresetId = PrintPreset::defaultPreset()?->id;

        return view('admin.qr.index', compact('unclaimedBoxes', 'exports', 'presets', 'defaultPresetId'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:20',
            'pdf_count' => 'required|integer|min:1|max:10',
            'preset_id' => 'nullable|integer|exists:print_presets,id',
        ]);

        $preset = $request->preset_id
            ? PrintPreset::find($request->preset_id)
            : PrintPreset::defaultPreset();

        $payload = array_merge(
            ['count' => (int) $request->count],
            $preset?->toEdgeFunctionPayload() ?? [],
        );

        $results = [];

        for ($i = 0; $i < $request->pdf_count; $i++) {
            $results[] = $this->supabase->callEdgeFunction('sticker_sheet_pdf', $payload);
        }

        $presetLabel = $preset ? " using \"{$preset->name}\"" : '';

        return back()->with('success', "Generated {$request->pdf_count} PDF(s) with {$request->count} QR codes each{$presetLabel}.")
                     ->with('pdf_results', $results);
    }
}

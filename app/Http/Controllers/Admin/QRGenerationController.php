<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('admin.qr.index', compact('unclaimedBoxes', 'exports'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:20',
            'pdf_count' => 'required|integer|min:1|max:10',
        ]);

        $results = [];

        for ($i = 0; $i < $request->pdf_count; $i++) {
            $result = $this->supabase->callEdgeFunction('sticker_sheet_pdf', [
                'count' => (int) $request->count,
            ]);
            $results[] = $result;
        }

        return back()->with('success', "Generated {$request->pdf_count} PDF(s) with {$request->count} QR codes each.")
                     ->with('pdf_results', $results);
    }
}

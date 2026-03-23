<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BoxController extends Controller
{
    public function __construct(
        protected SupabaseService $supabase,
        protected SupabaseStorageService $storage,
    ) {}

    public function index(Request $request)
    {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 25;
        $search = SupabaseService::sanitizeSearch($request->query('search', ''));
        $claimedFilter = in_array($request->query('claimed', ''), ['true', 'false']) ? $request->query('claimed') : '';
        $userId = $request->query('user_id', '');
        $locationId = $request->query('location_id', '');

        $params = [
            'order' => 'created_at.desc',
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage,
        ];

        $countFilters = [];

        if ($search) {
            $params['or'] = "(name.ilike.*{$search}*,qr_uuid.ilike.*{$search}*)";
            $countFilters['or'] = $params['or'];
        }

        if ($claimedFilter !== '') {
            $params['is_claimed'] = "eq.{$claimedFilter}";
            $countFilters['is_claimed'] = $params['is_claimed'];
        }

        if ($userId) {
            $params['user_id'] = "eq.{$userId}";
            $countFilters['user_id'] = $params['user_id'];
        }

        if ($locationId) {
            $params['location_id'] = "eq.{$locationId}";
            $countFilters['location_id'] = $params['location_id'];
        }

        $boxes = $this->supabase->getBoxes($params);
        $total = $this->supabase->count('box', $countFilters);
        $totalPages = max(1, ceil($total / $perPage));

        if ($request->header('HX-Request')) {
            return view('admin.boxes._list', compact('boxes', 'total', 'page', 'totalPages', 'perPage'));
        }

        return view('admin.boxes.index', compact('boxes', 'total', 'page', 'totalPages', 'perPage', 'search', 'claimedFilter'));
    }

    public function show(string $id)
    {
        $box = $this->supabase->getBox($id);

        if (!$box) {
            abort(404, 'Box not found');
        }

        $items = $this->supabase->getBoxItems([
            'box_id' => "eq.{$id}",
            'order' => 'created_at.desc',
        ]);

        $images = $this->supabase->getBoxImages([
            'box_id' => "eq.{$id}",
            'order' => 'uploaded_at.desc',
        ]);

        // Generate signed URLs for images
        foreach ($images as &$image) {
            try {
                $image['signed_url'] = $this->storage->getSignedUrl('box-images', $image['storage_path']);
            } catch (\Exception $e) {
                Log::warning('Box show: failed to sign image URL', ['image_id' => $image['id'] ?? null, 'error' => $e->getMessage()]);
                $image['signed_url'] = null;
            }
        }
        unset($image);

        return view('admin.boxes.show', compact('box', 'items', 'images'));
    }

    public function transfer(Request $request, string $id)
    {
        $request->validate(['email' => 'required|email']);

        $this->supabase->rpc('transfer_box', [
            'p_box_id' => $id,
            'p_target_email' => $request->email,
        ]);

        return back()->with('success', "Box transferred to {$request->email}.");
    }

    public function unclaim(string $id)
    {
        $this->supabase->update('box', ['id' => "eq.{$id}"], [
            'is_claimed' => false,
            'user_id' => null,
            'claimed_at' => null,
        ]);

        return back()->with('success', 'Box has been unclaimed.');
    }

    public function destroy(string $id)
    {
        // Delete items and images first
        $this->supabase->delete('box_items', ['box_id' => "eq.{$id}"]);
        $this->supabase->delete('box_images', ['box_id' => "eq.{$id}"]);
        $this->supabase->delete('box', ['id' => "eq.{$id}"]);

        return redirect()->route('admin.boxes.index')->with('success', 'Box deleted.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:unclaim,delete',
            'box_ids' => 'required|array',
            'box_ids.*' => 'required|uuid',
        ]);

        $action = $request->action;
        $count = 0;

        foreach ($request->box_ids as $boxId) {
            if ($action === 'unclaim') {
                $this->supabase->update('box', ['id' => "eq.{$boxId}"], [
                    'is_claimed' => false,
                    'user_id' => null,
                    'claimed_at' => null,
                ]);
            } elseif ($action === 'delete') {
                $this->supabase->delete('box_items', ['box_id' => "eq.{$boxId}"]);
                $this->supabase->delete('box_images', ['box_id' => "eq.{$boxId}"]);
                $this->supabase->delete('box', ['id' => "eq.{$boxId}"]);
            }
            $count++;
        }

        return back()->with('success', "{$count} box(es) {$action}ed.");
    }
}

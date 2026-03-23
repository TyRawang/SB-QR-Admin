<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StorageController extends Controller
{
    public function __construct(
        protected SupabaseService $supabase,
        protected SupabaseStorageService $storage,
    ) {}

    public function index(Request $request)
    {
        $bucket = $request->query('bucket', 'box-images');
        $path = $request->query('path', '');
        $userId = $request->query('user_id', '');
        $boxId = $request->query('box_id', '');

        $buckets = [];
        try {
            $buckets = $this->storage->listBuckets();
        } catch (\Exception $e) {
            Log::warning('Storage: failed to list buckets', ['error' => $e->getMessage()]);
        }

        $images = [];
        $params = [
            'order' => 'uploaded_at.desc',
            'limit' => 50,
        ];

        if ($userId) {
            $params['uploaded_by'] = "eq.{$userId}";
        }

        if ($boxId) {
            $params['box_id'] = "eq.{$boxId}";
        }

        $images = $this->supabase->getBoxImages($params);

        // Generate signed URLs
        foreach ($images as &$image) {
            try {
                $image['signed_url'] = $this->storage->getSignedUrl('box-images', $image['storage_path']);
            } catch (\Exception $e) {
                Log::warning('Storage: failed to sign image URL', ['image_id' => $image['id'] ?? null, 'error' => $e->getMessage()]);
                $image['signed_url'] = null;
            }
        }
        unset($image);

        return view('admin.storage.index', compact('buckets', 'images', 'bucket', 'userId', 'boxId'));
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'required|uuid',
        ]);

        foreach ($request->image_ids as $imageId) {
            $images = $this->supabase->get('box_images', [
                'id' => "eq.{$imageId}",
                'select' => 'storage_path',
            ]);

            if (!empty($images)) {
                try {
                    $this->storage->deleteFile('box-images', [$images[0]['storage_path']]);
                } catch (\Exception $e) {
                    Log::warning('Storage: failed to delete file from bucket', ['image_id' => $imageId, 'error' => $e->getMessage()]);
                }
                $this->supabase->delete('box_images', ['id' => "eq.{$imageId}"]);
            }
        }

        return back()->with('success', count($request->image_ids) . ' image(s) deleted.');
    }
}

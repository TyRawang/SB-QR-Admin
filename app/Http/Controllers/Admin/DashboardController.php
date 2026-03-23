<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use App\Services\SupabaseStorageService;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct(
        protected SupabaseService $supabase,
        protected SupabaseStorageService $storage,
    ) {}

    public function index()
    {
        $userCount = $this->supabase->count('profiles');
        $boxCount = $this->supabase->count('box');
        $claimedBoxCount = $this->supabase->count('box', ['is_claimed' => 'eq.true']);
        $unclaimedBoxCount = $boxCount - $claimedBoxCount;
        $itemCount = $this->supabase->count('box_items');
        $imageCount = $this->supabase->count('box_images');
        $locationCount = $this->supabase->count('locations');
        $feedbackCount = $this->supabase->count('feedback');

        $recentBoxes = $this->supabase->getBoxes([
            'is_claimed' => 'eq.true',
            'order' => 'claimed_at.desc',
            'limit' => 5,
        ]);

        $recentUsers = $this->supabase->getProfiles([
            'order' => 'created_at.desc',
            'limit' => 5,
        ]);

        $recentFeedback = $this->supabase->getFeedback([
            'limit' => 5,
        ]);

        $buckets = [];
        try {
            $buckets = $this->storage->listBuckets();
        } catch (\Exception $e) {
            Log::warning('Dashboard: failed to list storage buckets', ['error' => $e->getMessage()]);
        }

        return view('admin.dashboard', compact(
            'userCount', 'boxCount', 'claimedBoxCount', 'unclaimedBoxCount',
            'itemCount', 'imageCount', 'locationCount', 'feedbackCount',
            'recentBoxes', 'recentUsers', 'recentFeedback', 'buckets'
        ));
    }
}

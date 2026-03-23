<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use App\Services\SupabaseStorageService;
use Illuminate\Support\Facades\Log;

class SystemController extends Controller
{
    public function __construct(
        protected SupabaseService $supabase,
        protected SupabaseStorageService $storage,
    ) {}

    public function index()
    {
        $supabaseConnected = $this->supabase->healthCheck();

        $storageConnected = false;
        $buckets = [];
        try {
            $buckets = $this->storage->listBuckets();
            $storageConnected = true;
        } catch (\Exception $e) {
            Log::warning('System: storage health check failed', ['error' => $e->getMessage()]);
        }

        $envInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'supabase_url' => config('services.supabase.url'),
            'service_key_set' => !empty(config('services.supabase.service_role_key')),
            'app_env' => config('app.env'),
            'app_debug' => config('app.debug'),
        ];

        return view('admin.system.index', compact('supabaseConnected', 'storageConnected', 'buckets', 'envInfo'));
    }
}

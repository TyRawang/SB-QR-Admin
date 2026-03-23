<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(
        protected SupabaseService $supabase,
    ) {}

    public function index(Request $request)
    {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 25;
        $search = SupabaseService::sanitizeSearch($request->query('search', ''));

        $params = [
            'select' => '*, profiles(id, email, display_name), box(count)',
            'order' => 'name.asc',
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage,
        ];

        $countFilters = [];

        if ($search) {
            $params['name'] = "ilike.*{$search}*";
            $countFilters['name'] = $params['name'];
        }

        $locations = $this->supabase->getLocations($params);

        // Extract box count from the embedded resource
        foreach ($locations as &$location) {
            $location['box_count'] = $location['box'][0]['count'] ?? 0;
            unset($location['box']);
        }

        $total = $this->supabase->count('locations', $countFilters);
        $totalPages = max(1, ceil($total / $perPage));

        if ($request->header('HX-Request')) {
            return view('admin.locations._list', compact('locations', 'total', 'page', 'totalPages', 'perPage'));
        }

        return view('admin.locations.index', compact('locations', 'total', 'page', 'totalPages', 'perPage', 'search'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $this->supabase->update('locations', ['id' => "eq.{$id}"], [
            'name' => $request->name,
        ]);

        if ($request->header('HX-Request')) {
            return response($request->name);
        }

        return back()->with('success', 'Location updated.');
    }

    public function destroy(string $id)
    {
        $boxCount = $this->supabase->count('box', ['location_id' => "eq.{$id}"]);

        if ($boxCount > 0) {
            return back()->with('error', 'Cannot delete location with boxes. Reassign boxes first.');
        }

        $this->supabase->delete('locations', ['id' => "eq.{$id}"]);

        return back()->with('success', 'Location deleted.');
    }
}

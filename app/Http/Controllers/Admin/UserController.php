<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use App\Services\SupabaseAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct(
        protected SupabaseService $supabase,
        protected SupabaseAuthService $auth,
    ) {}

    public function index(Request $request)
    {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 25;
        $search = SupabaseService::sanitizeSearch($request->query('search', ''));
        $roleFilter = in_array($request->query('role', ''), ['admin', 'customer']) ? $request->query('role') : '';
        $activeFilter = in_array($request->query('active', ''), ['true', 'false']) ? $request->query('active') : '';

        $params = [
            'order' => 'created_at.desc',
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage,
        ];

        $countFilters = [];

        if ($search) {
            $params['or'] = "(display_name.ilike.*{$search}*,email.ilike.*{$search}*)";
            $countFilters['or'] = $params['or'];
        }

        if ($roleFilter) {
            $params['role'] = "eq.{$roleFilter}";
            $countFilters['role'] = $params['role'];
        }

        if ($activeFilter !== '') {
            $params['is_active'] = "eq.{$activeFilter}";
            $countFilters['is_active'] = $params['is_active'];
        }

        $users = $this->supabase->getProfiles($params);
        $total = $this->supabase->count('profiles', $countFilters);
        $totalPages = max(1, ceil($total / $perPage));

        if ($request->header('HX-Request')) {
            return view('admin.users._list', compact('users', 'total', 'page', 'totalPages', 'perPage'));
        }

        return view('admin.users.index', compact('users', 'total', 'page', 'totalPages', 'perPage', 'search', 'roleFilter', 'activeFilter'));
    }

    public function show(string $id)
    {
        $profile = $this->supabase->getProfile($id);

        if (!$profile) {
            abort(404, 'User not found');
        }

        $authUser = null;
        try {
            $authUser = $this->auth->getUser($id);
        } catch (\Exception $e) {
            Log::warning('User show: failed to fetch auth user', ['user_id' => $id, 'error' => $e->getMessage()]);
        }

        $boxes = $this->supabase->getBoxes([
            'user_id' => "eq.{$id}",
            'order' => 'created_at.desc',
        ]);

        $locations = $this->supabase->getLocations([
            'user_id' => "eq.{$id}",
        ]);

        $feedback = $this->supabase->getFeedback([
            'user_id' => "eq.{$id}",
            'order' => 'created_at.desc',
        ]);

        $images = $this->supabase->getBoxImages([
            'uploaded_by' => "eq.{$id}",
            'order' => 'uploaded_at.desc',
            'limit' => 20,
        ]);

        return view('admin.users.show', compact('profile', 'authUser', 'boxes', 'locations', 'feedback', 'images'));
    }

    public function toggleRole(Request $request, string $id)
    {
        $profile = $this->supabase->getProfile($id);

        if (!$profile) {
            abort(404, 'User not found');
        }

        $newRole = $profile['role'] === 'admin' ? 'customer' : 'admin';

        $this->supabase->update('profiles', ['id' => "eq.{$id}"], ['role' => $newRole]);

        if ($request->header('HX-Request')) {
            $profile['role'] = $newRole;
            return view('admin.users._role_badge', ['user' => $profile]);
        }

        return back()->with('success', "User role changed to {$newRole}.");
    }

    public function toggleActive(Request $request, string $id)
    {
        $profile = $this->supabase->getProfile($id);

        if (!$profile) {
            abort(404, 'User not found');
        }

        $newActive = !$profile['is_active'];

        $this->supabase->update('profiles', ['id' => "eq.{$id}"], ['is_active' => $newActive]);

        if ($request->header('HX-Request')) {
            $profile['is_active'] = $newActive;
            return view('admin.users._active_badge', ['user' => $profile]);
        }

        return back()->with('success', 'User status updated.');
    }

    public function resetPassword(string $id)
    {
        $profile = $this->supabase->getProfile($id);

        if (!$profile) {
            abort(404, 'User not found');
        }

        if (!($profile['email'] ?? null)) {
            return back()->with('error', 'User has no email address.');
        }

        $this->auth->sendPasswordReset($profile['email']);

        return back()->with('success', "Password reset email sent to {$profile['email']}.");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct(
        protected SupabaseService $supabase,
    ) {}

    public function index(Request $request)
    {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 25;
        $ratingFilter = in_array($request->query('rating', ''), ['1', '2', '3', '4', '5', 'unrated']) ? $request->query('rating') : '';

        $params = [
            'order' => 'created_at.desc',
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage,
        ];

        $countFilters = [];

        if ($ratingFilter !== '') {
            if ($ratingFilter === 'unrated') {
                $params['rating'] = 'is.null';
                $countFilters['rating'] = $params['rating'];
            } else {
                $params['rating'] = "eq.{$ratingFilter}";
                $countFilters['rating'] = $params['rating'];
            }
        }

        $feedback = $this->supabase->getFeedback($params);
        $total = $this->supabase->count('feedback', $countFilters);
        $totalPages = max(1, ceil($total / $perPage));

        if ($request->header('HX-Request')) {
            return view('admin.feedback._list', compact('feedback', 'total', 'page', 'totalPages', 'perPage'));
        }

        return view('admin.feedback.index', compact('feedback', 'total', 'page', 'totalPages', 'perPage', 'ratingFilter'));
    }

    public function destroy(string $id)
    {
        $this->supabase->delete('feedback', ['id' => "eq.{$id}"]);

        return back()->with('success', 'Feedback deleted.');
    }
}

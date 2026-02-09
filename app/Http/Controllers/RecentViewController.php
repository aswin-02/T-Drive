<?php

namespace App\Http\Controllers;

use App\Models\RecentView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecentViewController extends Controller
{
    /**
     * Display recent file views for the authenticated user.
     */
    public function index()
    {
        $recentFiles = RecentView::with('file')
            ->where('user_id', Auth::id())
            ->whereHas('file') // Ensure file still exists
            ->orderBy('viewed_at', 'desc')
            ->get();

        return view('recent.view', compact('recentFiles'));
    }
}

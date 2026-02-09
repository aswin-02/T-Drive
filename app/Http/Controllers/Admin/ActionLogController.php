<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionLog;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ActionLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('View Action Logs');
        if ($request->ajax()) {
            return $this->indexAPI(app(DataTables::class));
        }
        return view('admin.action-log.index');
    }

    // DataTables AJAX endpoint
    public function indexAPI(DataTables $datatables)
    {
        $query = ActionLog::with('user')->latest();
        return $datatables->eloquent($query)
            ->addIndexColumn()
            ->addColumn('user_name', function ($log) {
                if ($log->user) {
                    $badgeColor = $log->user->user_type == 'admin' ? 'danger' : 'warning';
                    return $log->user->name . ' <span class="badge bg-' . $badgeColor . '">' . $log->user->user_type . '</span>';
                }
                return '-';
            })
            ->editColumn('created_at', function ($log) {
                return $log->created_at ? $log->created_at->format('d-M-y h:i:s A') : '-';
            })
            ->addColumn('view', function ($log) {
                return '<a href="' . route('admin.action-logs.show', $log->id) . '" class="btn btn-sm btn-info">View</a>';
            })
            ->rawColumns(['view', 'user_name'])
            ->make(true);
    }

    public function show($id)
    {
        $this->authorize('View Action Logs');
        $log = ActionLog::findOrFail($id);
        return view('admin.action-log.show', compact('log'));
    }
}

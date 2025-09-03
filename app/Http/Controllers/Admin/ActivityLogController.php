<?php
// app/Http/Controllers/Admin/ActivityLogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller
{
    public function index()
    {
        return view('admin.activity-logs.index');
    }

    public function getData(Request $request)
    {
        $query = ActivityLog::with('user');

        // Apply filters
        if ($request->has('action') && !empty($request->action)) {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('user_name', function ($log) {
                return $log->user->name ?? 'N/A';
            })
            ->addColumn('action_badge', function ($log) {
                $colors = [
                    'created' => 'success',
                    'updated' => 'warning',
                    'deleted' => 'danger',
                    'status_changed' => 'info'
                ];
                $color = $colors[$log->action] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . ucfirst(str_replace('_', ' ', $log->action)) . '</span>';
            })
            ->addColumn('model_name', function ($log) {
                return class_basename($log->model_type);
            })
            ->addColumn('created_at_formatted', function ($log) {
                return $log->created_at->format('M d, Y H:i:s');
            })
            ->rawColumns(['action_badge'])
            ->make(true);
    }
}

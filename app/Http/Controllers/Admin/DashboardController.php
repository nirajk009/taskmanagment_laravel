<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $stats = [
                'total_users' => User::where('role', 1)->count(),
                'total_tasks' => Task::count(),
                'pending_tasks' => Task::where('status', 'pending')->count(),
                'completed_tasks' => Task::where('status', 'completed')->count(),
                'high_priority_tasks' => Task::where('priority', 'high')->where('status', 'pending')->count(),
                'overdue_tasks' => Task::where('due_date', '<', now())->where('status', 'pending')->count(),
            ];
            
            $recent_activities = ActivityLog::with('user')
                ->latest()
                ->limit(10)
                ->get();
        } else {
            $stats = [
                'my_tasks' => Task::where('user_id', $user->id)->count(),
                'pending_tasks' => Task::where('user_id', $user->id)->where('status', 'pending')->count(),
                'completed_tasks' => Task::where('user_id', $user->id)->where('status', 'completed')->count(),
                'high_priority_tasks' => Task::where('user_id', $user->id)->where('priority', 'high')->where('status', 'pending')->count(),
                'overdue_tasks' => Task::where('user_id', $user->id)->where('due_date', '<', now())->where('status', 'pending')->count(),
            ];
            
            $recent_activities = ActivityLog::where('user_id', $user->id)
                ->latest()
                ->limit(10)
                ->get();
        }

        return view('admin.dashboard', compact('stats', 'recent_activities'));
    }
}

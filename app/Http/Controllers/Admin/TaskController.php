<?php
// app/Http/Controllers/Admin/TaskController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{

// app/Http/Controllers/Admin/TaskController.php

public function index(Request $request)
{
    $user = auth()->user();
    $query = Task::with(['user', 'creator']);

    // If not admin, only show user's own tasks
    if (!$user->isAdmin()) {
        $query->where('user_id', $user->id);
    }

    // Apply filters if provided
    if ($request->filled('priority')) {
        $query->where('priority', $request->priority);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('from_date')) {
        $query->whereDate('due_date', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('due_date', '<=', $request->to_date);
    }

    if ($request->filled('assigned_to') && $user->isAdmin()) {
        $query->where('user_id', $request->assigned_to);
    }

    // Order by due date and paginate
    $tasks = $query->orderBy('due_date', 'asc')->paginate(10);
    
    // Keep filters in pagination links
    $tasks->appends($request->all());

    $users = User::where('role', 1)->where('status', 'active')->get();

    return view('admin.tasks.index', compact('tasks', 'users'));
}

    public function create()
    {
        $users = User::where('role', 1)->where('status', 'active')->get();
        return view('admin.tasks.create', compact('users'));
    }

    public function store(TaskRequest $request)
    {
        try {
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'priority' => $request->priority,
                'user_id' => $request->user_id,
                'created_by' => auth()->id(),
                'status' => 'pending'
            ]);

            ActivityLogService::log('created', $task, "Created task: {$task->title}");

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating task: ' . $e->getMessage()
            ]);
        }
    }

  // app/Http/Controllers/Admin/TaskController.php

public function edit($id)
{
    $task = Task::findOrFail($id);
    $users = User::where('role', 1)->where('status', 'active')->get();
    
    // Check permissions
    if (!auth()->user()->isAdmin() && $task->user_id != auth()->id()) {
        return redirect()->route('admin.tasks.index')->with('error', 'Access denied');
    }

    return view('admin.tasks.edit', compact('task', 'users'));
}


   public function update(Request $request, $id)
{
    try {
        $task = Task::findOrFail($id);
        
        // Check permissions
        if (!auth()->user()->isAdmin() && $task->user_id != auth()->id()) {
            return redirect()->route('admin.tasks.index')->with('error', 'Access denied');
        }

        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after_or_equal:today',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,completed',
            'user_id' => 'required|exists:users,id',
        ]);

        $oldData = $task->toArray();
        
        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'priority' => $request->priority,
            'status' => $request->status,
            'user_id' => $request->user_id,
        ]);

        $changes = array_diff_assoc($task->fresh()->toArray(), $oldData);
        ActivityLogService::log('updated', $task, "Updated task: {$task->title}", $changes);

        return redirect()->route('admin.tasks.index')->with('success', 'Task updated successfully!');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Error updating task: ' . $e->getMessage());
    }
}


public function updateStatus(Request $request, $id)
{
    try {
        $task = Task::findOrFail($id);
        
        // Check permissions
        if (!auth()->user()->isAdmin() && $task->user_id != auth()->id()) {
            return redirect()->route('admin.tasks.index')->with('error', 'Access denied');
        }

        $oldStatus = $task->status;
        $task->update(['status' => $request->status]);

        ActivityLogService::log('status_changed', $task, "Changed task status from {$oldStatus} to {$request->status}: {$task->title}");

        return redirect()->route('admin.tasks.index')->with('success', 'Task status updated successfully!');
    } catch (\Exception $e) {
        return redirect()->route('admin.tasks.index')->with('error', 'Error updating status: ' . $e->getMessage());
    }
}

public function destroy($id)
{
    try {
        $task = Task::findOrFail($id);
        
        // Only admin can delete tasks
        if (!auth()->user()->isAdmin()) {
            return redirect()->route('admin.tasks.index')->with('error', 'Access denied');
        }

        $title = $task->title;
        ActivityLogService::log('deleted', $task, "Deleted task: {$title}");
        $task->delete();

        return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully!');
    } catch (\Exception $e) {
        return redirect()->route('admin.tasks.index')->with('error', 'Error deleting task: ' . $e->getMessage());
    }
}

    public function getData(Request $request)
    {
        $user = auth()->user();
        $query = Task::with(['user', 'creator']);

        // If not admin, only show user's own tasks
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        // Apply filters
        if ($request->has('priority') && !empty($request->priority)) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('due_date', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('due_date', '<=', $request->to_date);
        }

        if ($request->has('assigned_to') && !empty($request->assigned_to) && $user->isAdmin()) {
            $query->where('user_id', $request->assigned_to);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('user_name', function ($task) {
                return $task->user->name ?? 'N/A';
            })
            ->addColumn('creator_name', function ($task) {
                return $task->creator->name ?? 'N/A';
            })
            ->addColumn('priority_badge', function ($task) {
                $colors = [
                    'low' => 'success',
                    'medium' => 'warning',
                    'high' => 'danger'
                ];
                return '<span class="badge badge-' . $colors[$task->priority] . '">' . ucfirst($task->priority) . '</span>';
            })
            ->addColumn('status_badge', function ($task) {
                $color = $task->status == 'completed' ? 'success' : 'warning';
                return '<span class="badge badge-' . $color . '">' . ucfirst($task->status) . '</span>';
            })
            ->addColumn('due_date_formatted', function ($task) {
                $isOverdue = $task->due_date < now() && $task->status == 'pending';
                $class = $isOverdue ? 'text-danger fw-bold' : '';
                return '<span class="' . $class . '">' . $task->due_date->format('M d, Y') . '</span>';
            })
            ->addColumn('actions', function ($task) {
                $user = auth()->user();
                $buttons = '';
                
                // Edit button
                if ($user->isAdmin() || $task->user_id == $user->id) {
                    $buttons .= '<button class="btn btn-sm btn-primary edit-task me-1" data-id="' . $task->id . '">
                                    <i class="fa fa-edit"></i>
                                </button>';
                }
                
                // Status toggle button
                if ($user->isAdmin() || $task->user_id == $user->id) {
                    $statusText = $task->status == 'pending' ? 'Complete' : 'Pending';
                    $statusValue = $task->status == 'pending' ? 'completed' : 'pending';
                    $statusClass = $task->status == 'pending' ? 'success' : 'warning';
                    
                    $buttons .= '<button class="btn btn-sm btn-' . $statusClass . ' status-toggle me-1" 
                                    data-id="' . $task->id . '" 
                                    data-status="' . $statusValue . '">
                                    <i class="fa fa-check"></i> ' . $statusText . '
                                </button>';
                }
                
                // Delete button (admin only)
                if ($user->isAdmin()) {
                    $buttons .= '<button class="btn btn-sm btn-danger delete-task" data-id="' . $task->id . '">
                                    <i class="fa fa-trash"></i>
                                </button>';
                }
                
                return $buttons;
            })
            ->rawColumns(['priority_badge', 'status_badge', 'due_date_formatted', 'actions'])
            ->make(true);
    }
    // Add this method to TaskController
public function getTaskData($id)
{
    try {
        $task = Task::findOrFail($id);
        
        // Check permissions
        if (!auth()->user()->isAdmin() && $task->user_id != auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ]);
        }

        return response()->json($task);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Task not found'
        ]);
    }
}

}

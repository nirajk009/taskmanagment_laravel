@extends('layouts.admin')

@section('title', 'Task List')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Task Management</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title">Task List</h4>
                        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add Task
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <form method="GET" action="{{ route('admin.tasks.index') }}">
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label>From Date</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label>To Date</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label>Priority</label>
                                    <select name="priority" class="form-select">
                                        <option value="">All</option>
                                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>
                                @if(auth()->user()->isAdmin())
                                    <div class="col-md-2">
                                        <label>Assigned To</label>
                                        <select name="assigned_to" class="form-select">
                                            <option value="">All</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </form>

                        <!-- Task Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Assigned To</th>
                                        @if(auth()->user()->isAdmin())
                                            <th>Created By</th>
                                        @endif
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tasks as $task)
                                        <tr>
                                            <td>{{ ($tasks->currentPage() - 1) * $tasks->perPage() + $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $task->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                            </td>
                                            <td>{{ $task->user->name ?? 'N/A' }}</td>
                                            @if(auth()->user()->isAdmin())
                                                <td>{{ $task->creator->name ?? 'N/A' }}</td>
                                            @endif
                                            <td>
                                                @php
                                                    $priorityColors = [
                                                        'low' => 'success',
                                                        'medium' => 'warning',
                                                        'high' => 'danger'
                                                    ];
                                                    $color = $priorityColors[$task->priority] ?? 'secondary';
                                                @endphp
                                                <span class="badge badge-{{ $color }}">{{ ucfirst($task->priority) }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColor = $task->status == 'completed' ? 'success' : 'warning';
                                                @endphp
                                                <span class="badge badge-{{ $statusColor }}">{{ ucfirst($task->status) }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $isOverdue = $task->due_date < now() && $task->status == 'pending';
                                                @endphp
                                                <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                                                    {{ $task->due_date->format('M d, Y') }}
                                                </span>
                                                @if($isOverdue)
                                                    <br><small class="text-danger">Overdue</small>
                                                @endif
                                            </td>
                                            <td>
                                                <!-- Edit Button -->
                                                @if(auth()->user()->isAdmin() || $task->user_id == auth()->id())
                                                    <a href="{{ route('admin.tasks.edit', $task->id) }}" class="btn btn-sm btn-primary me-1">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif

                                                <!-- Status Toggle -->
                                                @if(auth()->user()->isAdmin() || $task->user_id == auth()->id())
                                                    <form action="{{ route('admin.tasks.updateStatus', $task->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="{{ $task->status == 'pending' ? 'completed' : 'pending' }}">
                                                        <button type="submit" class="btn btn-sm btn-{{ $task->status == 'pending' ? 'success' : 'warning' }} me-1" 
                                                                onclick="return confirm('Change task status?')">
                                                            <i class="fa fa-check"></i>
                                                            {{ $task->status == 'pending' ? 'Complete' : 'Pending' }}
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Delete Button (Admin Only) -->
                                                @if(auth()->user()->isAdmin())
                                                    <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this task?')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ auth()->user()->isAdmin() ? '8' : '7' }}" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-tasks fa-3x mb-3"></i>
                                                    <h5>No tasks found</h5>
                                                    <p>Create your first task to get started!</p>
                                                    <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary">
                                                        <i class="fa fa-plus"></i> Create Task
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($tasks->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <small class="text-muted">
                                        Showing {{ $tasks->firstItem() }} to {{ $tasks->lastItem() }} of {{ $tasks->total() }} tasks
                                    </small>
                                </div>
                                <div>
                                    {{ $tasks->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function showNotification(title, message, type) {
        $.notify({
            icon: 'icon-bell',
            title: `<strong>${title}</strong>`,
            message: message,
        }, {
            type: type,
            placement: {
                from: "top",
                align: "right"
            },
            time: 3000,
        });
    }

    // Show success/error messages from session
    @if(session('success'))
        showNotification("Success", "{{ session('success') }}", "success");
    @endif

    @if(session('error'))
        showNotification("Error", "{{ session('error') }}", "danger");
    @endif
});
</script>
@endsection

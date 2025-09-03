@extends('layouts.admin')

@section('title', 'Edit Task')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Edit Task</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Edit Task</div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.tasks.update', $task->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="title">Task Title</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="{{ old('title', $task->title) }}" placeholder="Enter task title">
                                        @error('title')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="5" 
                                                  placeholder="Enter task description">{{ old('description', $task->description) }}</textarea>
                                        @error('description')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="due_date">Due Date</label>
                                                <input type="date" class="form-control" id="due_date" name="due_date" 
                                                       value="{{ old('due_date', $task->due_date->format('Y-m-d')) }}">
                                                @error('due_date')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="priority">Priority</label>
                                                <select class="form-select" id="priority" name="priority">
                                                    <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                                    <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                                                </select>
                                                @error('priority')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-select" id="status" name="status">
                                                    <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="user_id">Assign To</label>
                                                <select class="form-select" id="user_id" name="user_id">
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}" {{ old('user_id', $task->user_id) == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('user_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group d-flex justify-content-end">
                                        <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update Task</button>
                                    </div>
                                </div>
                            </div>
                        </form>
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

{{-- resources/views/admin/tasks/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Create Task')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Create New Task</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Add Task</div>
                    </div>
                    <div class="card-body">
                        <form id="createTaskForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="title">Task Title</label>
                                        <input type="text" class="form-control" id="title" placeholder="Enter task title" required>
                                        <div class="invalid-feedback" id="title-error"></div>
                                    </div>
                                    
                                <div class="form-group">
    <label for="description">Description</label>
    <textarea class="form-control" id="description" rows="5" placeholder="Enter task description"></textarea>
    <div class="invalid-feedback" id="description-error"></div>
</div>

                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="due_date">Due Date</label>
                                                <input type="date" class="form-control" id="due_date" required>
                                                <div class="invalid-feedback" id="due_date-error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="priority">Priority</label>
                                                <select class="form-select" id="priority" required>
                                                    <option value="">Select Priority</option>
                                                    <option value="low">Low</option>
                                                    <option value="medium" selected>Medium</option>
                                                    <option value="high">High</option>
                                                </select>
                                                <div class="invalid-feedback" id="priority-error"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="user_id">Assign To</label>
                                        <select class="form-select" id="user_id" required>
                                            <option value="">Select User</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="user_id-error"></div>
                                    </div>
                                    
                                    <div class="form-group d-flex justify-content-end">
                                        <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                        <button type="submit" class="btn btn-primary" id="createTaskBtn">Create Task</button>
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
<!-- Include CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let editor;
    
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

    function clearErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    function showErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const input = document.getElementById(field);
            const errorDiv = document.getElementById(field + '-error');
            if (input && errorDiv) {
                input.classList.add('is-invalid');
                errorDiv.textContent = messages[0];
            }
        }
    }

    // Remove required attribute from hidden fields to prevent Chrome validation errors
    function removeRequiredFromHiddenFields() {
        const elements = document.querySelectorAll('input[required], textarea[required], select[required]');
        elements.forEach(element => {
            if (element.offsetParent === null || element.style.display === 'none') {
                element.removeAttribute('required');
            }
        });
    }

    // Initialize CKEditor
    ClassicEditor
        .create(document.querySelector('#description'))
        .then(editorInstance => {
            editor = editorInstance;
        })
        .catch(error => {
            console.error(error);
        });

    // Set minimum date to today
    document.getElementById('due_date').min = new Date().toISOString().split('T')[0];

    // Create Task Form Submit
    document.getElementById('createTaskForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();

        // Remove required from any hidden fields
        removeRequiredFromHiddenFields();

        const title = document.getElementById('title').value.trim();
        const description = editor ? editor.getData() : document.getElementById('description').value.trim();
        const due_date = document.getElementById('due_date').value;
        const priority = document.getElementById('priority').value;
        const user_id = document.getElementById('user_id').value;

        // Client-side validation (since we removed required attributes)
        if (!title) {
            showNotification("Error", "Please enter task title", "danger");
            document.getElementById('title').classList.add('is-invalid');
            document.getElementById('title-error').textContent = 'Task title is required';
            return;
        }

        if (!description) {
            showNotification("Error", "Please enter task description", "danger");
            return;
        }

        if (!due_date) {
            showNotification("Error", "Please select due date", "danger");
            document.getElementById('due_date').classList.add('is-invalid');
            document.getElementById('due_date-error').textContent = 'Due date is required';
            return;
        }

        if (!priority) {
            showNotification("Error", "Please select priority", "danger");
            document.getElementById('priority').classList.add('is-invalid');
            document.getElementById('priority-error').textContent = 'Priority is required';
            return;
        }

        if (!user_id) {
            showNotification("Error", "Please assign task to a user", "danger");
            document.getElementById('user_id').classList.add('is-invalid');
            document.getElementById('user_id-error').textContent = 'Please select a user';
            return;
        }

        const createBtn = document.getElementById('createTaskBtn');
        const originalText = createBtn.innerHTML;
        createBtn.disabled = true;
        createBtn.innerHTML = 'Creating... <i class="fa fa-spinner fa-spin"></i>';

        fetch("{{ route('admin.tasks.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                title: title,
                description: description,
                due_date: due_date,
                priority: priority,
                user_id: user_id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification("Success", data.message, "success");
                // Clear form
                document.getElementById('title').value = '';
                if (editor) {
                    editor.setData('');
                } else {
                    document.getElementById('description').value = '';
                }
                document.getElementById('due_date').value = '';
                document.getElementById('priority').value = 'medium';
                document.getElementById('user_id').value = '';
                
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = "{{ route('admin.tasks.index') }}";
                }, 2000);
            } else {
                showNotification("Error", data.message, "danger");
                if (data.errors) {
                    showErrors(data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification("Error", "An error occurred while creating task", "danger");
        })
        .finally(() => {
            createBtn.disabled = false;
            createBtn.innerHTML = originalText;
        });
    });
});
</script>
@endsection


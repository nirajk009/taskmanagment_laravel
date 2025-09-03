@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">User Management</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title">User List</h4>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add User
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <form method="GET" action="{{ route('admin.users.index') }}">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label>From Date</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label>To Date</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <label>Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </form>

                        <!-- Users Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $user->name }}</strong>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if($user->status == 'active')
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <!-- Edit Button -->
                                                <button class="btn btn-sm btn-primary edit-user me-1" data-id="{{ $user->id }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>

                                                <!-- Delete Button (prevent deleting self) -->
                                                @if($user->id != auth()->id())
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this user?')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-users fa-3x mb-3"></i>
                                                    <h5>No users found</h5>
                                                    <p>Create your first user to get started!</p>
                                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                                        <i class="fa fa-plus"></i> Create User
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($users->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <small class="text-muted">
                                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                                    </small>
                                </div>
                                <div>
                                    {{ $users->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="edit_user_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_name">Full Name</label>
                                <input type="text" class="form-control" id="edit_name">
                                <div class="invalid-feedback" id="edit_name-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_email">Email Address</label>
                                <input type="email" class="form-control" id="edit_email">
                                <div class="invalid-feedback" id="edit_email-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_password">Password (Leave blank to keep current)</label>
                                <input type="password" class="form-control" id="edit_password">
                                <div class="invalid-feedback" id="edit_password-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_status">Status</label>
                                <select class="form-select" id="edit_status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback" id="edit_status-error"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateUserBtn">Update User</button>
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

    // Edit User functionality - Fetch real user data
    $(document).on('click', '.edit-user', function() {
        const userId = $(this).data('id');
        
        // Show loading
        const button = $(this);
        const originalText = button.html();
        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        // Fetch user data
        fetch(`{{ url('admin/users') }}/${userId}/edit`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Fill modal fields with real data
            $('#edit_user_id').val(data.id);
            $('#edit_name').val(data.name);
            $('#edit_email').val(data.email);
            $('#edit_status').val(data.status);
            $('#edit_password').val('');
            
            // Clear errors and show modal
            clearErrors();
            $('#editUserModal').modal('show');
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification("Error", "Failed to load user data", "danger");
        })
        .finally(() => {
            button.prop('disabled', false).html(originalText);
        });
    });

    // Update User
    $('#updateUserBtn').on('click', function() {
        clearErrors();
        const userId = $('#edit_user_id').val();
        
        // Create FormData for proper form submission
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('name', $('#edit_name').val());
        formData.append('email', $('#edit_email').val());
        formData.append('password', $('#edit_password').val());
        formData.append('status', $('#edit_status').val());

        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('Updating... <i class="fa fa-spinner fa-spin"></i>');

        fetch(`{{ url('admin/users') }}/${userId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification("Success", data.message, "success");
                $('#editUserModal').modal('hide');
                // Reload the page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification("Error", data.message, "danger");
                if (data.errors) {
                    showErrors(data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification("Error", "An error occurred while updating user", "danger");
        })
        .finally(() => {
            btn.prop('disabled', false).html(originalText);
        });
    });

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

{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Create User')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Create New User</h3>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Add User</div>
                    </div>
                    <div class="card-body">
                        <form id="createUserForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Full Name</label>
                                                <input type="text" class="form-control" id="name" placeholder="Enter full name" required>
                                                <div class="invalid-feedback" id="name-error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email Address</label>
                                                <input type="email" class="form-control" id="email" placeholder="Enter email address" required>
                                                <div class="invalid-feedback" id="email-error"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input type="password" class="form-control" id="password" placeholder="Enter password" required>
                                                <div class="invalid-feedback" id="password-error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password_confirmation">Confirm Password</label>
                                                <input type="password" class="form-control" id="password_confirmation" placeholder="Confirm password" required>
                                                <div class="invalid-feedback" id="password_confirmation-error"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="role">Role</label>
                                                <select class="form-select" id="role" required>
                                                    <option value="">Select Role</option>
                                                    <option value="1" selected>User</option>
                                                    <option value="0">Admin</option>
                                                </select>
                                                <div class="invalid-feedback" id="role-error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select class="form-select" id="status" required>
                                                    <option value="">Select Status</option>
                                                    <option value="active" selected>Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                                <div class="invalid-feedback" id="status-error"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group d-flex justify-content-end">
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                        <button type="submit" class="btn btn-primary" id="createUserBtn">Create User</button>
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

    // Create User Form Submit
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();

        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (password !== confirmPassword) {
            showNotification("Error", "Passwords do not match", "danger");
            document.getElementById('password_confirmation').classList.add('is-invalid');
            document.getElementById('password_confirmation-error').textContent = 'Passwords do not match';
            return;
        }

        const formData = {
            name: document.getElementById('name').value.trim(),
            email: document.getElementById('email').value.trim(),
            password: password,
            password_confirmation: confirmPassword,
            role: document.getElementById('role').value,
            status: document.getElementById('status').value
        };

        const createBtn = document.getElementById('createUserBtn');
        const originalText = createBtn.innerHTML;
        createBtn.disabled = true;
        createBtn.innerHTML = 'Creating... <i class="fa fa-spinner fa-spin"></i>';

        fetch("{{ route('admin.users.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification("Success", data.message, "success");
                // Clear form
                this.reset();
                
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = "{{ route('admin.users.index') }}";
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
            showNotification("Error", "An error occurred while creating user", "danger");
        })
        .finally(() => {
            createBtn.disabled = false;
            createBtn.innerHTML = originalText;
        });
    });
});
</script>
@endsection

{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Create Account</h4>
                </div>
                <div class="card-body">
                    <form id="registerForm">
                        @csrf
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            <div class="invalid-feedback" id="password-error"></div>
                            <small class="form-text text-muted">Password must be at least 8 characters long.</small>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <div class="invalid-feedback" id="password_confirmation-error"></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="registerBtn">Register</button>
                    </form>
                    <div class="text-center mt-3">
                        <p>Already have an account? <a href="{{ route('login') }}">Login here</a></p>
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
            time: 4000,
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

    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();

        // Check if passwords match
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        if (password !== confirmPassword) {
            showNotification("Error", "Passwords do not match", "danger");
            document.getElementById('password_confirmation').classList.add('is-invalid');
            document.getElementById('password_confirmation-error').textContent = 'Passwords do not match';
            return;
        }

        const formData = new FormData(this);
        const registerBtn = document.getElementById('registerBtn');
        const originalText = registerBtn.innerHTML;

        registerBtn.disabled = true;
        registerBtn.innerHTML = 'Registering... <i class="fa fa-spinner fa-spin"></i>';

        fetch("{{ route('register') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification("Success", data.message, "success");
                // Wait a moment then redirect
                setTimeout(() => {
                    window.location.href = data.redirect;
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
            showNotification("Error", "Network error. Please check your connection and try again.", "danger");
        })
        .finally(() => {
            registerBtn.disabled = false;
            registerBtn.innerHTML = originalText;
        });
    });
});
</script>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container py-3 px-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>List of Superadmin and Admin</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerAdminModal">Register New Admin</button>
        </div>
        <!-- Display register success message -->
        @if (session('register-success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('register-success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- Display delete success message -->
        @if (session('delete-success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('delete-success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Display delete error message -->
        @if (session('delete-error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('delete-error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admins as $admin)
                    <tr>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->role }}</td> 
                        <td>
                            @if ($admin->role !== 'superadmin')
                                <button type="button" class="btn btn-danger btn-sm" 
                                    onclick="showAdminDeleteModal('{{ $admin->id }}', '{{ $admin->name }}')">Delete</button>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

<!-- Register Admin Modal -->
<div class="modal fade" id="registerAdminModal" tabindex="-1" aria-labelledby="registerAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerAdminModalLabel">Register New Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <!-- Display Validation Errors Inside Modal -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="registerAdminForm" method="POST" action="{{ route('admins.register') }}" onsubmit="return validateAdminRegisterForm()">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        <div id="emailError" class="invalid-feedback" style="display: none;">
                            Please enter a valid email address.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        <div id="passwordError" class="invalid-feedback" style="display: none;">
                            Passwords do not match.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="superadmin">Superadmin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="adminNameToDelete"></strong>?</p>
                <form id="deleteAdminForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="password" class="form-label">Enter your password to confirm</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div id="deletePasswordError" class="invalid-feedback" style="display: none;">
                            Incorrect password. Please try again.
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Automatically open the modal if there are validation errors
    @if ($errors->any())
        var registerModal = new bootstrap.Modal(document.getElementById('registerAdminModal'));
        registerModal.show();
    @endif
});
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 10000); // this is in milliseconds, 1000ms = 1 second
</script>

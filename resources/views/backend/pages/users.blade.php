@extends('backend.layout.main')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
                <div class="breadcrumb-title pe-3 text-dark font-weight-bold">Access Control</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">User Management</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- End Breadcrumb -->

            <!-- Success Alert -->
            @if(session('success'))
                <div class="alert alert-success border-0 bg-light-success text-success alert-dismissible fade show font-14 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-check-circle fs-4 me-2"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 bg-light-danger text-danger alert-dismissible fade show font-14 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-x-circle fs-4 me-2"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card radius-10 border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 pt-4 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 font-weight-bold text-dark">System Users & Permissions</h5>
                            <p class="mb-0 text-muted font-12 mt-1">Configure role assignments and toggle dynamic access permissions for admins.</p>
                        </div>
                        <button type="button" class="btn btn-primary radius-30 px-4 font-weight-bold" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="bx bx-user-plus me-1"></i>Add New User
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover">
                            <thead class="table-light text-secondary font-12 text-uppercase">
                                <tr>
                                    <th>User Info</th>
                                    <th>Email</th>
                                    <th>Assigned Role</th>
                                    <th>Permissions Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="font-14 text-dark">
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="widgets-icons-2 rounded-circle bg-light-primary text-primary me-3">
                                                    <i class='bx bx-user'></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold">{{ $user->name }}</h6>
                                                    <small class="text-muted">ID: #{{ $user->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->hasRole('super-admin'))
                                                <span class="badge bg-light-danger text-danger px-3 py-1 radius-30 font-12">Super Admin</span>
                                            @else
                                                <span class="badge bg-light-primary text-primary px-3 py-1 radius-30 font-12">Admin</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->hasRole('super-admin'))
                                                <span class="text-success font-weight-bold"><i class="bx bx-check-double me-1"></i>All Access (Bypassed)</span>
                                            @else
                                                <span class="text-secondary">
                                                    <i class="bx bx-key me-1"></i>{{ $user->permissions->count() }} of {{ $permissions->count() }} active
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-outline-primary btn-sm radius-30 px-3 font-weight-bold" data-bs-toggle="modal" data-bs-target="#editPermissionsModal-{{ $user->id }}">
                                                <i class="bx bx-edit-alt me-1"></i>Configure
                                            </button>
                                            @if($user->id !== 1 && $user->id !== Auth::user()->id)
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to delete this user account?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm radius-30 px-3 font-weight-bold">
                                                        <i class="bx bx-trash me-1"></i>Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Modal for editing permissions -->
                                    <div class="modal fade" id="editPermissionsModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content text-start">
                                                <form action="{{ route('users.permissions', $user->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title font-weight-bold text-dark">Configure Role & Permissions</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-4">
                                                            <h6 class="font-weight-bold text-secondary mb-1">User Information</h6>
                                                            <p class="mb-0 font-14">{{ $user->name }} ({{ $user->email }})</p>
                                                        </div>

                                                        <div class="mb-4">
                                                            <label class="form-label font-weight-bold text-secondary font-12 text-uppercase">Role Assignment</label>
                                                            @if($user->id === 1)
                                                                <select name="role_disabled" class="form-select user-role-select" data-user-id="{{ $user->id }}" disabled>
                                                                    <option value="super-admin" selected>Super Admin</option>
                                                                </select>
                                                                <input type="hidden" name="role" value="super-admin">
                                                            @else
                                                                <select name="role" class="form-select user-role-select" data-user-id="{{ $user->id }}" required>
                                                                    @foreach($roles as $role)
                                                                        @if($role->slug !== 'super-admin')
                                                                            <option value="{{ $role->slug }}" {{ $user->hasRole($role->slug) ? 'selected' : '' }}>
                                                                                {{ $role->name }}
                                                                            </option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        </div>

                                                        <div class="permissions-section-{{ $user->id }}">
                                                            <label class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-2">Access Permissions</label>
                                                            <div class="alert alert-info py-2 font-12 super-admin-alert-{{ $user->id }} {{ $user->hasRole('super-admin') ? '' : 'd-none' }}">
                                                                <i class="bx bx-info-circle me-1"></i>Super Admins automatically bypass permission validation checks.
                                                            </div>
                                                            <div class="row row-cols-1 row-cols-md-2 g-3 permissions-grid-{{ $user->id }} {{ $user->hasRole('super-admin') ? 'opacity-50' : '' }}">
                                                                @foreach($permissions as $permission)
                                                                    <div class="col">
                                                                        <div class="border rounded p-3" style="background-color: #fafafa;">
                                                                            <div class="form-check form-switch mb-0">
                                                                                <input class="form-check-input permission-checkbox-{{ $user->id }}" 
                                                                                       type="checkbox" 
                                                                                       name="permissions[]" 
                                                                                       value="{{ $permission->slug }}" 
                                                                                       id="perm-{{ $user->id }}-{{ $permission->id }}"
                                                                                       {{ $user->hasPermission($permission->slug) ? 'checked' : '' }}
                                                                                       {{ $user->hasRole('super-admin') ? 'disabled' : '' }}>
                                                                                <label class="form-check-label text-dark font-weight-bold font-13" for="perm-{{ $user->id }}-{{ $permission->id }}">
                                                                                    {{ $permission->name }}
                                                                                </label>
                                                                                <small class="d-block text-muted font-11 mt-1">{{ $permission->description }}</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light-subtle">
                                                        <button type="button" class="btn btn-secondary btn-sm radius-30 px-3" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary btn-sm radius-30 px-3">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal for creating new users -->
            <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content text-start">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title font-weight-bold text-dark">Add New User Account</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="create_name" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-1">Full Name</label>
                                        <input type="text" name="name" id="create_name" class="form-control" placeholder="Rajan Sharma" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="create_email" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-1">Email Address</label>
                                        <input type="email" name="email" id="create_email" class="form-control" placeholder="email@example.com" required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="create_password" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-1">Password</label>
                                        <input type="password" name="password" id="create_password" class="form-control" placeholder="•••••••• (Min 8 chars)" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="create_role_disabled" class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-1">Account Role</label>
                                        <select name="role_disabled" id="create_role_disabled" class="form-select" disabled>
                                            <option value="admin" selected>Admin</option>
                                        </select>
                                        <input type="hidden" name="role" value="admin">
                                    </div>
                                </div>

                                <div class="create-permissions-section">
                                    <label class="form-label font-weight-bold text-secondary font-12 text-uppercase mb-2">Access Permissions</label>
                                    <div class="alert alert-info py-2 font-12 create-super-admin-alert d-none">
                                        <i class="bx bx-info-circle me-1"></i>Super Admins automatically bypass permission validation checks.
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-2 g-3 create-permissions-grid">
                                        @foreach($permissions as $permission)
                                            <div class="col">
                                                <div class="border rounded p-3" style="background-color: #fafafa;">
                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input create-permission-checkbox" 
                                                               type="checkbox" 
                                                               name="permissions[]" 
                                                               value="{{ $permission->slug }}" 
                                                               id="create-perm-{{ $permission->id }}"
                                                               checked>
                                                        <label class="form-check-label text-dark font-weight-bold font-13" for="create-perm-{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                        <small class="d-block text-muted font-11 mt-1">{{ $permission->description }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light-subtle">
                                <button type="button" class="btn btn-secondary btn-sm radius-30 px-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary btn-sm radius-30 px-3">Create Account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const roleSelects = document.querySelectorAll('.user-role-select');
            
            roleSelects.forEach(select => {
                select.addEventListener('change', function () {
                    const userId = this.getAttribute('data-user-id');
                    const isSuperAdmin = this.value === 'super-admin';
                    
                    const checkboxes = document.querySelectorAll(`.permission-checkbox-${userId}`);
                    const alertBox = document.querySelector(`.super-admin-alert-${userId}`);
                    const grid = document.querySelector(`.permissions-grid-${userId}`);
                    
                    if (isSuperAdmin) {
                        checkboxes.forEach(cb => {
                            cb.disabled = true;
                            cb.checked = true;
                        });
                        alertBox.classList.remove('d-none');
                        grid.classList.add('opacity-50');
                    } else {
                        checkboxes.forEach(cb => {
                            cb.disabled = false;
                        });
                        alertBox.classList.add('d-none');
                        grid.classList.remove('opacity-50');
                    }
                });
            });

            // Logic for Create User Modal
            const createRoleSelect = document.getElementById('create_role');
            if (createRoleSelect) {
                createRoleSelect.addEventListener('change', function () {
                    const isSuperAdmin = this.value === 'super-admin';
                    const checkboxes = document.querySelectorAll('.create-permission-checkbox');
                    const alertBox = document.querySelector('.create-super-admin-alert');
                    const grid = document.querySelector('.create-permissions-grid');
                    
                    if (isSuperAdmin) {
                        checkboxes.forEach(cb => {
                            cb.disabled = true;
                            cb.checked = true;
                        });
                        alertBox.classList.remove('d-none');
                        grid.classList.add('opacity-50');
                    } else {
                        checkboxes.forEach(cb => {
                            cb.disabled = false;
                            cb.checked = true; // Default checks for standard permissions
                        });
                        alertBox.classList.add('d-none');
                        grid.classList.remove('opacity-50');
                    }
                });
            }
        });
    </script>
    @endpush
@endsection

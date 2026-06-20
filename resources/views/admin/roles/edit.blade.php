@extends('layouts.app')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')
@section('page-description', 'Modify role permissions and settings')

@section('content')

@include('partials.alerts')

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Role</h1>
            <p class="text-muted">Update role permissions and information</p>
        </div>
        <div>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Roles
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-shield me-2"></i>Role Information
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                        @csrf
                        @method('PUT')

                        <!-- Role Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $role->name) }}" 
                                           pattern="[a-z_]+" title="Only lowercase letters and underscores allowed"
                                           required {{ $role->is_system_role ? 'readonly' : '' }}>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Use lowercase letters and underscores only (e.g., shop_manager)
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                           id="display_name" name="display_name" 
                                           value="{{ old('display_name', $role->display_name) }}" required>
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Human-readable name for the role
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              maxlength="500">{{ old('description', $role->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Brief description of what this role can do
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Role Permissions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-key me-2"></i>Permissions
                                </h6>
                            </div>

                            @if($role->is_system_role)
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-lock me-2"></i>
                                        <strong>System Role:</strong> This is a system role and its permissions cannot be modified.
                                    </div>
                                </div>
                            @endif

                            <div class="col-12">
                                @php
                                    $currentPermissions = is_array($role->permissions) ? $role->permissions : [];
                                    $groupedPermissions = [];
                                    
                                    foreach($availablePermissions as $permission => $description) {
                                        $parts = explode('.', $permission);
                                        $module = ucfirst($parts[0]);
                                        $groupedPermissions[$module][] = [
                                            'permission' => $permission,
                                            'description' => $description
                                        ];
                                    }
                                @endphp

                                <div class="permissions-container">
                                    @foreach($groupedPermissions as $module => $permissions)
                                        <div class="permission-group mb-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <h6 class="mb-0 me-3">{{ $module }} Permissions</h6>
                                                <div class="form-check">
                                                    <input class="form-check-input select-all-checkbox" 
                                                           type="checkbox" 
                                                           id="select-all-{{ $module }}"
                                                           onchange="toggleModulePermissions('{{ $module }}', this.checked)"
                                                           {{ $role->is_system_role ? 'disabled' : '' }}>
                                                    <label class="form-check-label small text-muted" for="select-all-{{ $module }}">
                                                        Select All
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                @foreach($permissions as $perm)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-checkbox permission-{{ $module }}" 
                                                                   type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="{{ $perm['permission'] }}" 
                                                                   id="perm-{{ $perm['permission'] }}"
                                                                   {{ in_array($perm['permission'], $currentPermissions) ? 'checked' : '' }}
                                                                   {{ $role->is_system_role ? 'disabled' : '' }}
                                                                   onchange="updateSelectAllCheckbox('{{ $module }}')">
                                                            <label class="form-check-label" for="perm-{{ $perm['permission'] }}">
                                                                <strong>{{ ucfirst(str_replace(['.', '_'], [' › ', ' '], $perm['permission'])) }}</strong>
                                                                <br>
                                                                <small class="text-muted">{{ $perm['description'] }}</small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @error('permissions')
                                    <div class="alert alert-danger mt-3">{{ $message }}</div>
                                @enderror

                                @if(!$role->is_system_role)
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Note:</strong> Select at least one permission for this role. 
                                        Users with this role will only be able to perform actions for which permissions are granted.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                            
                            @if(!$role->is_system_role)
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Role
                                    </button>
                                </div>
                            @else
                                <div>
                                    <button type="button" class="btn btn-secondary" disabled>
                                        <i class="fas fa-lock me-2"></i>System Role (Read Only)
                                    </button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Role Information Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info me-2"></i>Role Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <label>Created:</label>
                        <span>{{ $role->created_at->format('M d, Y g:i A') }}</span>
                    </div>
                    
                    <div class="info-item">
                        <label>Last Updated:</label>
                        <span>{{ $role->updated_at->format('M d, Y g:i A') }}</span>
                    </div>
                    
                    @if($role->createdBy)
                        <div class="info-item">
                            <label>Created By:</label>
                            <span>{{ $role->createdBy->name }}</span>
                        </div>
                    @endif
                    
                    <div class="info-item">
                        <label>Role Type:</label>
                        <span>
                            @if($role->is_system_role)
                                <span class="badge bg-warning">System Role</span>
                            @else
                                <span class="badge bg-primary">Custom Role</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <label>Users with Role:</label>
                        <span>{{ $role->users()->count() }} users</span>
                    </div>
                    
                    <div class="info-item">
                        <label>Permissions Count:</label>
                        <span>{{ count($role->permissions ?? []) }} permissions</span>
                    </div>
                </div>
            </div>

            @if(!$role->is_system_role)
                <div class="card mt-3">
                    <div class="card-header bg-danger text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            Delete this role permanently. This action cannot be undone and will remove the role from all users.
                        </p>
                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="fas fa-trash me-2"></i>Delete Role
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.permission-group {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    background-color: #f8f9fa;
}

.permission-checkbox {
    margin-right: 0.5rem;
}

.form-check-label {
    cursor: pointer;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item label {
    font-weight: 600;
    color: #495057;
    margin: 0;
    min-width: 100px;
}

.info-item span {
    text-align: right;
    color: #212529;
    flex: 1;
}

.select-all-checkbox {
    transform: scale(0.9);
}

@media (max-width: 768px) {
    .permission-group .row .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>

<script>
function toggleModulePermissions(module, checked) {
    const checkboxes = document.querySelectorAll(`.permission-${module}`);
    checkboxes.forEach(checkbox => {
        if (!checkbox.disabled) {
            checkbox.checked = checked;
        }
    });
}

function updateSelectAllCheckbox(module) {
    const checkboxes = document.querySelectorAll(`.permission-${module}`);
    const selectAllCheckbox = document.getElementById(`select-all-${module}`);
    
    let allChecked = true;
    let anyChecked = false;
    
    checkboxes.forEach(checkbox => {
        if (!checkbox.disabled) {
            if (!checkbox.checked) {
                allChecked = false;
            } else {
                anyChecked = true;
            }
        }
    });
    
    selectAllCheckbox.checked = allChecked;
    selectAllCheckbox.indeterminate = anyChecked && !allChecked;
}

// Initialize select-all checkboxes on page load
document.addEventListener('DOMContentLoaded', function() {
    @foreach($groupedPermissions as $module => $permissions)
        updateSelectAllCheckbox('{{ $module }}');
    @endforeach

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]:checked');
        
        @if(!$role->is_system_role)
            if (permissionCheckboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one permission for this role.');
                return false;
            }
        @endif
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            submitBtn.disabled = true;
        }
    });
});
</script>
@endsection
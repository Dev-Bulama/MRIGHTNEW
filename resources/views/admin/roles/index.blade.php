@extends('layouts.app')

@section('title', 'Role Management')
@section('page-title', 'Role Management')
@section('page-description', 'Manage roles and permissions for union executives')

@push('styles')
<style>
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #667eea;
    }
    
    .role-card {
        transition: transform 0.2s;
        border-radius: 10px;
    }
    
    .role-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .permission-badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
        margin: 0.2rem;
        background: #e3f2fd;
        color: #1976d2;
        border-radius: 10px;
    }
    
    .system-role-badge {
        background: #fff3e0;
        color: #f57c00;
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
        border-radius: 10px;
    }
    
    .custom-role-badge {
        background: #e8f5e8;
        color: #388e3c;
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
        border-radius: 10px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-primary mb-1">{{ $stats['total_roles'] }}</h3>
                        <div class="text-muted">Total Roles</div>
                    </div>
                    <div class="text-primary opacity-50">
                        <i class="fas fa-user-tag fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-warning mb-1">{{ $stats['system_roles'] }}</h3>
                        <div class="text-muted">System Roles</div>
                    </div>
                    <div class="text-warning opacity-50">
                        <i class="fas fa-cog fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-success mb-1">{{ $stats['custom_roles'] }}</h3>
                        <div class="text-muted">Custom Roles</div>
                    </div>
                    <div class="text-success opacity-50">
                        <i class="fas fa-plus-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-info mb-1">{{ $stats['users_with_roles'] }}</h3>
                        <div class="text-muted">Users with Roles</div>
                    </div>
                    <div class="text-info opacity-50">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions & Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tag me-2"></i>System Roles
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-outline-warning me-2" onclick="createDefaultRoles()">
                        <i class="fas fa-cog me-2"></i>Create Default Roles
                    </button>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Custom Role
                    </a>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="row mt-3">
                <div class="col-md-4">
                    <form method="GET">
                        <select name="type" class="form-select" onchange="this.form.submit()">
                            <option value="">All Role Types</option>
                            <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>System Roles</option>
                            <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>Custom Roles</option>
                        </select>
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    </form>
                </div>
                <div class="col-md-8">
                    <form method="GET" class="d-flex">
                        <input type="search" name="search" class="form-control me-2" 
                               placeholder="Search roles by name or description..." 
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Grid -->
    @if($roles->count() > 0)
        <div class="row">
            @foreach($roles as $role)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card role-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $role->display_name }}</h6>
                            <span class="{{ $role->is_system_role ? 'system-role-badge' : 'custom-role-badge' }}">
                                {{ $role->is_system_role ? 'System' : 'Custom' }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted d-block">Role Name:</small>
                                <code>{{ $role->name }}</code>
                            </div>
                            
                            @if($role->description)
                                <div class="mb-3">
                                    <small class="text-muted d-block">Description:</small>
                                    <p class="text-dark mb-0 small">{{ $role->description }}</p>
                                </div>
                            @endif
                            
                            <!-- Permissions -->
                            <div class="mb-3">
                                <small class="text-muted d-block mb-2">Permissions ({{ count($role->permissions ?? []) }}):</small>
                                <div class="d-flex flex-wrap">
                                    @foreach($role->formatted_permissions as $permission)
                                        <span class="permission-badge">{{ $permission['name'] }}</span>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Users Count -->
                            <div class="row text-center">
                                <div class="col">
                                    <small class="text-muted d-block">Users</small>
                                    <strong>{{ $role->users()->count() }}</strong>
                                </div>
                                <div class="col">
                                    <small class="text-muted d-block">Created</small>
                                    <strong>{{ $role->created_at->format('M Y') }}</strong>
                                </div>
                            </div>
                            
                            @if($role->createdBy)
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>Created by {{ $role->createdBy->name }}
                                    </small>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                @if(!$role->is_system_role)
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                            onclick="deleteRole({{ $role->id }}, '{{ $role->display_name }}', {{ $role->users()->count() }})">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                        <i class="fas fa-lock me-1"></i>Protected
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $roles->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-user-tag text-muted mb-3" style="font-size: 4rem;"></i>
            <h4 class="text-muted">No Roles Found</h4>
            <p class="text-muted">Create roles to manage permissions for union executives.</p>
            <div>
                <button type="button" class="btn btn-outline-warning me-2" onclick="createDefaultRoles()">
                    <i class="fas fa-cog me-2"></i>Create Default Roles
                </button>
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create Custom Role
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function createDefaultRoles() {
    if (confirm('This will create the default system roles. Continue?')) {
        fetch('/admin/roles/create-default', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to create default roles: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred while creating default roles.');
        });
    }
}

function deleteRole(roleId, roleName, userCount) {
    if (userCount > 0) {
        alert(`Cannot delete role "${roleName}" because it is assigned to ${userCount} users. Remove all users first.`);
        return;
    }
    
    if (confirm(`Are you sure you want to delete role "${roleName}"? This action cannot be undone.`)) {
        fetch(`/admin/roles/${roleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to delete role: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred while deleting the role.');
        });
    }
}
</script>
@endpush
@endsection
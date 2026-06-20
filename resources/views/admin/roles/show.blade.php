@extends('layouts.app')

@section('title', 'Role Details')
@section('page-title', $role->display_name)
@section('page-description', 'View role details and manage user assignments')

@push('styles')
<style>
    .role-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .info-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }
    
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #667eea;
        height: 100%;
    }
    
    .permission-badge {
        background: #e3f2fd;
        color: #1976d2;
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
        margin: 0.2rem;
    }
    
    .user-card {
        transition: transform 0.2s;
        border-radius: 8px;
    }
    
    .user-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .system-role-indicator {
        background: #fff3e0;
        color: #f57c00;
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
        border-radius: 10px;
    }
    
    .custom-role-indicator {
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
    <!-- Role Header -->
    <div class="role-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-2">
                    <h2 class="mb-0 me-3">{{ $role->display_name }}</h2>
                    <span class="{{ $role->is_system_role ? 'system-role-indicator' : 'custom-role-indicator' }}">
                        {{ $role->is_system_role ? 'System Role' : 'Custom Role' }}
                    </span>
                </div>
                <p class="mb-1"><strong>Role Name:</strong> <code>{{ $role->name }}</code></p>
                @if($role->description)
                    <p class="mb-1">{{ $role->description }}</p>
                @endif
                <p class="mb-0 opacity-75">
                    Created {{ $role->created_at->format('F j, Y') }}
                    @if($role->createdBy)
                        by {{ $role->createdBy->name }}
                    @endif
                </p>
            </div>
            <div class="col-md-6 text-center">
                <div class="row">
                    <div class="col-6">
                        <div class="h3 mb-1">{{ count($role->permissions ?? []) }}</div>
                        <small>Permissions</small>
                    </div>
                    <div class="col-6">
                        <div class="h3 mb-1">{{ $stats['total_users'] }}</div>
                        <small>Users Assigned</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-primary mb-1">{{ $stats['total_users'] }}</h3>
                        <div class="text-muted">Total Users</div>
                    </div>
                    <div class="text-primary opacity-50">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-success mb-1">{{ $stats['union_users'] }}</h3>
                        <div class="text-muted">Union Users</div>
                    </div>
                    <div class="text-success opacity-50">
                        <i class="fas fa-user-tie fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-warning mb-1">{{ $stats['active_users'] }}</h3>
                        <div class="text-muted">Active Users</div>
                    </div>
                    <div class="text-warning opacity-50">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-info mb-1">{{ count($role->permissions ?? []) }}</h3>
                        <div class="text-muted">Permissions</div>
                    </div>
                    <div class="text-info opacity-50">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Role Information -->
        <div class="col-xl-6">
            <!-- Permissions -->
            <div class="info-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5><i class="fas fa-shield-alt me-2"></i>Permissions ({{ count($role->permissions ?? []) }})</h5>
                    @if(!$role->is_system_role)
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit me-1"></i>Edit Permissions
                        </a>
                    @endif
                </div>
                
                @if(count($role->permissions ?? []) > 0)
                    <div class="d-flex flex-wrap">
                        @foreach($role->formatted_permissions as $permission)
                            <span class="permission-badge">{{ $permission['name'] }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No permissions assigned to this role.</p>
                @endif
            </div>

            <!-- Role Actions -->
            <div class="info-card">
                <h5><i class="fas fa-cog me-2"></i>Role Actions</h5>
                
                <div class="d-grid gap-2">
                    @if(!$role->is_system_role)
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Edit Role Details
                        </a>
                    @endif
                    
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                        <i class="fas fa-user-plus me-2"></i>Assign Users to Role
                    </button>
                    
                    <a href="{{ route('admin.roles.export') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-download me-2"></i>Export Role Data
                    </a>
                    
                    @if(!$role->is_system_role && $stats['total_users'] === 0)
                        <button type="button" class="btn btn-outline-danger" onclick="deleteRole({{ $role->id }}, '{{ $role->display_name }}')">
                            <i class="fas fa-trash me-2"></i>Delete Role
                        </button>
                    @endif
                    
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Roles
                    </a>
                </div>
            </div>
        </div>

        <!-- Users with this Role -->
        <div class="col-xl-6">
            <div class="info-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5><i class="fas fa-users me-2"></i>Users with this Role</h5>
                    <span class="badge bg-primary">{{ $usersWithRole->total() }} Total</span>
                </div>
                
                @if($usersWithRole->count() > 0)
                    <div class="row">
                        @foreach($usersWithRole as $user)
                            <div class="col-md-12 mb-3">
                                <div class="card user-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-primary text-white rounded-circle me-3" 
                                                     style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                    <div class="mt-1">
                                                        <span class="badge bg-{{ $user->user_type === 'union' ? 'info' : 'secondary' }}">
                                                            {{ ucfirst(str_replace('_', ' ', $user->user_type)) }}
                                                        </span>
                                                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($user->status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">Assigned</small>
                                                <small class="text-muted">{{ $user->pivot->assigned_at->diffForHumans() }}</small>
                                                @if(!$role->is_system_role)
                                                    <div class="mt-1">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="removeUserFromRole({{ $user->id }}, {{ $role->id }}, '{{ $user->name }}')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($user->shop)
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-store me-1"></i>{{ $user->shop->shop_name }}
                                                    @if($user->shop->state)
                                                        - {{ $user->shop->state }}
                                                    @endif
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $usersWithRole->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">No Users Assigned</h6>
                        <p class="text-muted">This role hasn't been assigned to any users yet.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                            <i class="fas fa-user-plus me-2"></i>Assign First User
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Assign User Modal -->
<div class="modal fade" id="assignUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Users to Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Select users to assign the "{{ $role->display_name }}" role.</p>
                
                <div id="usersLoading" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading available users...</p>
                </div>
                
                <div id="usersList" style="display: none;">
                    <!-- Users will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="assignSelectedUsers()">
                    <i class="fas fa-check me-2"></i>Assign Selected
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const assignModal = document.getElementById('assignUserModal');
    
    assignModal.addEventListener('show.bs.modal', function() {
        loadAvailableUsers();
    });
});

function loadAvailableUsers() {
    fetch(`/admin/roles/{{ $role->id }}/available-users`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayUsers(data.users);
            } else {
                showUsersError('Failed to load users.');
            }
        })
        .catch(error => {
            showUsersError('An error occurred while loading users.');
        });
}

function displayUsers(users) {
    const loading = document.getElementById('usersLoading');
    const list = document.getElementById('usersList');
    
    loading.style.display = 'none';
    list.style.display = 'block';
    
    if (users.length === 0) {
        list.innerHTML = '<p class="text-muted text-center">No available users to assign this role.</p>';
        return;
    }
    
    let html = '<div class="row">';
    users.forEach(user => {
        html += `
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           name="user_ids[]" value="${user.id}" 
                           id="user_${user.id}">
                    <label class="form-check-label w-100" for="user_${user.id}">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <strong>${user.name}</strong>
                                <br>
                                <small class="text-muted">${user.email}</small>
                                <br>
                                <span class="badge bg-secondary">${user.user_type.replace('_', ' ')}</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    list.innerHTML = html;
}

function showUsersError(message) {
    const loading = document.getElementById('usersLoading');
    loading.innerHTML = `<p class="text-danger">${message}</p>`;
}

function assignSelectedUsers() {
    const selectedUsers = Array.from(document.querySelectorAll('input[name="user_ids[]"]:checked'))
                              .map(checkbox => checkbox.value);
    
    if (selectedUsers.length === 0) {
        alert('Please select at least one user to assign.');
        return;
    }
    
    const promises = selectedUsers.map(userId => {
        return fetch(`/admin/roles/{{ $role->id }}/assign-user`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ user_id: userId })
        }).then(response => response.json());
    });
    
    Promise.all(promises)
        .then(results => {
            const successful = results.filter(r => r.success).length;
            const failed = results.filter(r => !r.success).length;
            
            let message = `Successfully assigned role to ${successful} user(s).`;
            if (failed > 0) {
                message += ` ${failed} assignment(s) failed.`;
            }
            
            alert(message);
            location.reload();
        })
        .catch(error => {
            alert('An error occurred while assigning users.');
        });
}

function removeUserFromRole(userId, roleId, userName) {
    if (confirm(`Are you sure you want to remove the "${$role->display_name}" role from ${userName}?`)) {
        fetch(`/admin/roles/${roleId}/remove-user`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to remove role: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred while removing the role.');
        });
    }
}

function deleteRole(roleId, roleName) {
    if (confirm(`Are you sure you want to delete the role "${roleName}"? This action cannot be undone.`)) {
        fetch(`/admin/roles/${roleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/admin/roles';
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
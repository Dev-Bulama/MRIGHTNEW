@extends('layouts.app')

@section('title', 'Create Role')
@section('page-title', 'Create New Role')
@section('page-description', 'Create a custom role with specific permissions for union executives')

@push('styles')
<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #667eea;
    }
    
    .permission-card {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .permission-card:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .permission-card.selected {
        border-color: #667eea;
        background: #f8f9ff;
    }
    
    .permission-category {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .permission-badge {
        background: #e3f2fd;
        color: #1976d2;
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
        margin: 0.2rem;
    }
    
    .category-header {
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <form method="POST" action="{{ route('admin.roles.store') }}" id="roleForm">
                @csrf
                
                <!-- Basic Information -->
                <div class="form-section">
                    <h5><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="e.g., regional_manager"
                                   pattern="^[a-z_]+$"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Use lowercase letters and underscores only (e.g., regional_manager)
                            </small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Display Name *</label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                   name="display_name" 
                                   value="{{ old('display_name') }}" 
                                   placeholder="e.g., Regional Manager"
                                   required>
                            @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Human-readable name that will be displayed in the interface
                            </small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" 
                                  rows="3" 
                                  maxlength="500" 
                                  placeholder="Describe what this role does and who should have it...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Optional description to help identify the purpose of this role
                        </small>
                    </div>
                </div>

                <!-- Permission Selection -->
                <div class="form-section">
                    <h5><i class="fas fa-shield-alt me-2"></i>Permission Assignment</h5>
                    <p class="text-muted mb-3">Select the permissions this role should have. Users with this role will be able to perform these actions.</p>
                    
                    @php
                        $permissionCategories = [
                            'User Management' => [
                                'manage_shop_owners' => 'Manage Shop Owners',
                                'view_shop_owners' => 'View Shop Owners',
                                'manage_users' => 'Manage Users',
                                'view_users' => 'View Users',
                                'edit_users' => 'Edit Users',
                            ],
                            'Pre-approval Management' => [
                                'manage_pre_approvals' => 'Manage Pre-approvals',
                                'view_pre_approvals' => 'View Pre-approvals',
                                'upload_pre_approvals' => 'Upload Pre-approval Lists',
                            ],
                            'Location Management' => [
                                'manage_by_state' => 'Manage by State',
                                'manage_by_lga' => 'Manage by LGA',
                            ],
                            'Union Management' => [
                                'create_unions' => 'Create Unions',
                                'assign_unions' => 'Assign Unions to Locations',
                                'view_union_reports' => 'View Union Reports',
                            ],
                            'Payout Management' => [
                                'approve_payouts' => 'Approve Payout Requests',
                                'view_payouts' => 'View Payout Requests',
                                'process_payments' => 'Process Payments',
                            ],
                            'Statistics & Reports' => [
                                'view_shop_statistics' => 'View Shop Statistics',
                                'view_reports' => 'View Reports',
                                'export_data' => 'Export Data',
                                'view_analytics' => 'View Analytics',
                            ],
                            'System Management' => [
                                'manage_roles' => 'Manage Roles & Permissions',
                                'system_settings' => 'System Settings',
                                'manage_logos' => 'Manage System Logos',
                            ],
                            'Receipt Management' => [
                                'view_receipts' => 'View Receipts',
                                'manage_receipts' => 'Manage Receipts',
                            ],
                        ];
                    @endphp
                    
                    <!-- Permission Categories -->
                    @foreach($permissionCategories as $category => $permissions)
                        <div class="permission-category">
                            <div class="category-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-folder me-2"></i>{{ $category }}
                                    </h6>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="selectCategoryPermissions('{{ $category }}', true)">
                                            Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                onclick="selectCategoryPermissions('{{ $category }}', false)">
                                            Clear All
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                @foreach($permissions as $key => $name)
                                    <div class="col-md-6 mb-2">
                                        <div class="permission-card" data-category="{{ $category }}" onclick="togglePermission('{{ $key }}')">
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox" 
                                                       type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $key }}" 
                                                       id="permission_{{ $key }}"
                                                       {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label w-100" for="permission_{{ $key }}">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>{{ $name }}</strong>
                                                            <div class="small text-muted">{{ $key }}</div>
                                                        </div>
                                                        <i class="fas fa-check text-success permission-check" style="display: none;"></i>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    
                    @error('permissions')
                        <div class="alert alert-danger mt-3">{{ $message }}</div>
                    @enderror
                    
                    <!-- Selected Permissions Summary -->
                    <div class="mt-4">
                        <h6>Selected Permissions Summary:</h6>
                        <div id="selectedPermissionsSummary" class="d-flex flex-wrap">
                            <span class="text-muted">No permissions selected</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Roles
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Reset Form
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Role
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form
    updateSelectedPermissions();
    updatePermissionCards();
    
    // Auto-generate role name from display name
    const displayNameInput = document.querySelector('input[name="display_name"]');
    const nameInput = document.querySelector('input[name="name"]');
    
    displayNameInput.addEventListener('input', function() {
        if (!nameInput.value || nameInput.value === generateRoleName(displayNameInput.oldValue || '')) {
            nameInput.value = generateRoleName(this.value);
        }
        displayNameInput.oldValue = this.value;
    });
});

function generateRoleName(displayName) {
    return displayName.toLowerCase()
                     .replace(/[^a-z0-9\s]/g, '')
                     .replace(/\s+/g, '_')
                     .substring(0, 50);
}

function togglePermission(permissionKey) {
    const checkbox = document.getElementById(`permission_${permissionKey}`);
    const card = checkbox.closest('.permission-card');
    
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        card.classList.add('selected');
        card.querySelector('.permission-check').style.display = 'block';
    } else {
        card.classList.remove('selected');
        card.querySelector('.permission-check').style.display = 'none';
    }
    
    updateSelectedPermissions();
}

function selectCategoryPermissions(category, select) {
    const categoryCards = document.querySelectorAll(`.permission-card[data-category="${category}"]`);
    
    categoryCards.forEach(card => {
        const checkbox = card.querySelector('.permission-checkbox');
        const checkIcon = card.querySelector('.permission-check');
        
        checkbox.checked = select;
        
        if (select) {
            card.classList.add('selected');
            checkIcon.style.display = 'block';
        } else {
            card.classList.remove('selected');
            checkIcon.style.display = 'none';
        }
    });
    
    updateSelectedPermissions();
}

function updateSelectedPermissions() {
    const selectedCheckboxes = document.querySelectorAll('.permission-checkbox:checked');
    const summaryContainer = document.getElementById('selectedPermissionsSummary');
    
    if (selectedCheckboxes.length === 0) {
        summaryContainer.innerHTML = '<span class="text-muted">No permissions selected</span>';
        return;
    }
    
    let html = '';
    selectedCheckboxes.forEach(checkbox => {
        const label = document.querySelector(`label[for="${checkbox.id}"] strong`).textContent;
        html += `<span class="permission-badge">${label}</span>`;
    });
    
    summaryContainer.innerHTML = html;
}

function updatePermissionCards() {
    // Update cards based on checked state
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        const card = checkbox.closest('.permission-card');
        const checkIcon = card.querySelector('.permission-check');
        
        if (checkbox.checked) {
            card.classList.add('selected');
            checkIcon.style.display = 'block';
        } else {
            card.classList.remove('selected');
            checkIcon.style.display = 'none';
        }
    });
}

function resetForm() {
    if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
        document.getElementById('roleForm').reset();
        
        // Reset visual states
        document.querySelectorAll('.permission-card').forEach(card => {
            card.classList.remove('selected');
            card.querySelector('.permission-check').style.display = 'none';
        });
        
        updateSelectedPermissions();
    }
}

// Form validation
document.getElementById('roleForm').addEventListener('submit', function(e) {
    const selectedPermissions = document.querySelectorAll('.permission-checkbox:checked').length;
    const roleName = document.querySelector('input[name="name"]').value;
    const displayName = document.querySelector('input[name="display_name"]').value;
    
    if (selectedPermissions === 0) {
        e.preventDefault();
        alert('Please select at least one permission for this role.');
        return;
    }
    
    if (!roleName || !displayName) {
        e.preventDefault();
        alert('Please fill in both role name and display name.');
        return;
    }
    
    if (!/^[a-z_]+$/.test(roleName)) {
        e.preventDefault();
        alert('Role name must contain only lowercase letters and underscores.');
        return;
    }
});

// Add change event listeners to checkboxes
document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedPermissions);
});
</script>
@endpush
@endsection
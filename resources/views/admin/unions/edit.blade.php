@extends('layouts.app')

@section('title', 'Edit Union Executive')
@section('page-title', 'Edit Union Executive')
@section('page-description', 'Update union executive details and assignments')

@push('styles')
<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #667eea;
    }
    
    .state-checkbox {
        transition: all 0.2s;
    }
    
    .state-checkbox:hover {
        transform: scale(1.02);
    }
    
    .lga-section {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin: 0.5rem 0;
        border: 1px solid #e9ecef;
    }
    
    .role-card {
        border: 2px solid #e9ecef;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .role-card:hover {
        border-color: #667eea;
        transform: translateY(-2px);
    }
    
    .role-card.selected {
        border-color: #667eea;
        background: #f8f9ff;
    }
    
    .current-info {
        background: #e3f2fd;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <!-- Current Union Info -->
            <div class="current-info">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <div class="avatar bg-primary text-white rounded-circle mx-auto mb-2" 
                             style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                            {{ strtoupper(substr($union->first_name, 0, 1) . substr($union->last_name, 0, 1)) }}
                        </div>
                        <span class="badge bg-{{ $union->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($union->status) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <h4 class="mb-1">{{ $union->name }}</h4>
                        <p class="text-muted mb-1">{{ $union->email }}</p>
                        <p class="text-muted mb-1">Managing {{ $union->manageableShopOwners()->count() }} shop owners</p>
                        <p class="text-muted mb-0">Created {{ $union->created_at->format('F j, Y') }}</p>
                    </div>
                    <div class="col-md-3 text-end">
                        <a href="{{ route('admin.unions.show', $union) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>View Details
                        </a>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.unions.update', $union) }}" id="unionEditForm">
                @csrf
                @method('PUT')
                
                <!-- Personal Information -->
                <div class="form-section">
                    <h5><i class="fas fa-user me-2"></i>Personal Information</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   name="first_name" value="{{ old('first_name', $union->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   name="last_name" value="{{ old('last_name', $union->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email', $union->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                                   name="phone_number" value="{{ old('phone_number', $union->phone_number) }}" required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Secondary Phone</label>
                            <input type="tel" class="form-control @error('secondary_phone') is-invalid @enderror" 
                                   name="secondary_phone" value="{{ old('secondary_phone', $union->secondary_phone) }}">
                            @error('secondary_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="active" {{ old('status', $union->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $union->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $union->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Password Update (Optional) -->
                <div class="form-section">
                    <h5><i class="fas fa-lock me-2"></i>Password Update (Optional)</h5>
                    <p class="text-muted mb-3">Leave password fields empty to keep current password unchanged.</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   name="password_confirmation">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Location Assignment -->
                <div class="form-section">
                    <h5><i class="fas fa-map-marker-alt me-2"></i>Location Assignment</h5>
                    <p class="text-muted mb-3">Update the states and LGAs this union executive will manage.</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Assigned States *</label>
                        <div class="row" id="statesContainer">
                            @foreach($states as $state)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check state-checkbox">
                                        <input class="form-check-input state-input" type="checkbox" 
                                               name="assigned_states[]" value="{{ $state }}" 
                                               id="state_{{ $loop->index }}"
                                               {{ in_array($state, old('assigned_states', $union->assigned_states)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="state_{{ $loop->index }}">
                                            {{ $state }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('assigned_states')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Assigned LGAs (Optional)</label>
                        <small class="text-muted d-block mb-2">Select specific LGAs within the chosen states. Leave empty to manage all LGAs in selected states.</small>
                        <div id="lgasContainer">
                            <!-- LGAs will be loaded here -->
                        </div>
                        @error('assigned_lgas')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Role Assignment -->
                <div class="form-section">
                    <h5><i class="fas fa-user-tag me-2"></i>Role Assignment</h5>
                    <p class="text-muted mb-3">Update roles to define what permissions this union executive will have.</p>
                    
                    <div class="row">
                        @foreach($roles as $role)
                            <div class="col-md-6 mb-3">
                                <div class="card role-card h-100 {{ in_array($role->id, old('roles', $assignedRoleIds)) ? 'selected' : '' }}" 
                                     onclick="toggleRole({{ $role->id }})">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input role-input" type="checkbox" 
                                                   name="roles[]" value="{{ $role->id }}" 
                                                   id="role_{{ $role->id }}"
                                                   {{ in_array($role->id, old('roles', $assignedRoleIds)) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="role_{{ $role->id }}">
                                                <h6 class="mb-1">{{ $role->display_name }}</h6>
                                                <p class="text-muted mb-2 small">{{ $role->description }}</p>
                                                <div class="d-flex flex-wrap">
                                                    @foreach($role->formatted_permissions as $permission)
                                                        <span class="badge bg-light text-dark me-1 mb-1" style="font-size: 0.7rem;">
                                                            {{ $permission['name'] }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('roles')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.unions.show', $union) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Details
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Reset Changes
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Union Executive
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
    const stateInputs = document.querySelectorAll('.state-input');
    const lgasContainer = document.getElementById('lgasContainer');
    
    // Handle state selection changes
    stateInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateLGAs();
        });
    });
    
    // Initial LGA load
    updateLGAs();
    
    function updateLGAs() {
        const selectedStates = Array.from(document.querySelectorAll('.state-input:checked')).map(input => input.value);
        
        if (selectedStates.length === 0) {
            lgasContainer.innerHTML = '<p class="text-muted text-center py-3">Select states first to see available LGAs</p>';
            return;
        }
        
        fetch('/admin/unions/get-lgas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ states: selectedStates })
        })
        .then(response => response.json())
        .then(lgas => {
            displayLGAs(lgas);
        })
        .catch(error => {
            console.error('Error loading LGAs:', error);
            lgasContainer.innerHTML = '<p class="text-danger text-center py-3">Error loading LGAs. Please try again.</p>';
        });
    }
    
    function displayLGAs(lgas) {
        if (lgas.length === 0) {
            lgasContainer.innerHTML = '<p class="text-muted text-center py-3">No LGAs available for selected states</p>';
            return;
        }
        
        // Group LGAs by state
        const lgasByState = {};
        lgas.forEach(lga => {
            if (!lgasByState[lga.state]) {
                lgasByState[lga.state] = [];
            }
            lgasByState[lga.state].push(lga);
        });
        
        let html = '';
        Object.keys(lgasByState).forEach(state => {
            html += `
                <div class="lga-section">
                    <h6>${state} LGAs:</h6>
                    <div class="row">
            `;
            
            const currentLgas = @json(old('assigned_lgas', $union->assigned_lgas));
            lgasByState[state].forEach(lga => {
                const isChecked = currentLgas.includes(lga.value) ? 'checked' : '';
                html += `
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="assigned_lgas[]" value="${lga.value}" 
                                   id="lga_${lga.value.replace(/\s+/g, '_')}" ${isChecked}>
                            <label class="form-check-label" for="lga_${lga.value.replace(/\s+/g, '_')}">
                                ${lga.text}
                            </label>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        lgasContainer.innerHTML = html;
    }
    
    // Initialize role cards
    document.querySelectorAll('.role-input:checked').forEach(input => {
        input.closest('.role-card').classList.add('selected');
    });
});

function toggleRole(roleId) {
    const checkbox = document.getElementById(`role_${roleId}`);
    const card = checkbox.closest('.role-card');
    
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        card.classList.add('selected');
    } else {
        card.classList.remove('selected');
    }
}

function resetForm() {
    if (confirm('Are you sure you want to reset all changes? This will revert to the original values.')) {
        location.reload();
    }
}

// Form validation
document.getElementById('unionEditForm').addEventListener('submit', function(e) {
    const selectedStates = document.querySelectorAll('.state-input:checked').length;
    const selectedRoles = document.querySelectorAll('.role-input:checked').length;
    
    if (selectedStates === 0) {
        e.preventDefault();
        alert('Please select at least one state for this union executive.');
        return;
    }
    
    if (selectedRoles === 0) {
        e.preventDefault();
        alert('Please assign at least one role to this union executive.');
        return;
    }
});
</script>
@endpush
@endsection
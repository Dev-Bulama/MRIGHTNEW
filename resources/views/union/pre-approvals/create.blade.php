@extends('layouts.union')

@section('title', 'Add Pre-Approval')
@section('page-title', 'Add Pre-Approval')
@section('page-description', 'Pre-approve a shop owner for registration')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Create Pre-Approval</h1>
            <p class="text-muted">Add a new pre-approved registration</p>
        </div>
        <div>
            <a href="{{ route('union.pre-approvals.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Display Validation Errors -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-check me-2"></i>Pre-Approval Information
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('union.pre-approvals.store') }}" id="preApprovalForm">
                        @csrf

                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-user me-2"></i>Personal Information
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="{{ old('first_name') }}" 
                                           required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ old('last_name') }}" 
                                           required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" 
                                           name="phone_number" 
                                           value="{{ old('phone_number') }}" 
                                           placeholder="e.g., 08012345678"
                                           required>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>Location Information
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                    <select class="form-select @error('state') is-invalid @enderror" 
                                            id="state" 
                                            name="state" 
                                            required>
                                        <option value="">Select State</option>
                                        @if(isset($states) && !empty($states))
                                            @foreach($states as $state)
                                                <option value="{{ $state }}" {{ old('state') == $state ? 'selected' : '' }}>
                                                    {{ $state }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>No states assigned to you</option>
                                        @endif
                                    </select>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
    <div class="mb-3">
        <label for="local_government" class="form-label">Local Government <span class="text-danger">*</span></label>
        <select class="form-select @error('local_government') is-invalid @enderror" 
                id="local_government" 
                name="local_government" 
                required
                disabled>
            <option value="">Select LGA</option>
            @if(old('local_government'))
                <option value="{{ old('local_government') }}" selected>{{ old('local_government') }}</option>
            @endif
        </select>
        @error('local_government')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Select a state first to load assigned LGAs</small>
    </div>
</div>
                        </div>

                        <!-- Shop Information (Optional) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-store me-2"></i>Shop Information <small class="text-muted">(Optional)</small>
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="shop_name" class="form-label">Shop Name</label>
                                    <input type="text" 
                                           class="form-control @error('shop_name') is-invalid @enderror" 
                                           id="shop_name" 
                                           name="shop_name" 
                                           value="{{ old('shop_name') }}" 
                                           placeholder="e.g., John's Electronics">
                                    @error('shop_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expires_at" class="form-label">Expires On</label>
                                    <input type="date" 
                                           class="form-control @error('expires_at') is-invalid @enderror" 
                                           id="expires_at" 
                                           name="expires_at" 
                                           value="{{ old('expires_at', now()->addMonths(6)->format('Y-m-d')) }}"
                                           min="{{ now()->addDay()->format('Y-m-d') }}">
                                    @error('expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave blank for default 6 months expiry</small>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="business_address" class="form-label">Business Address</label>
                                    <textarea class="form-control @error('business_address') is-invalid @enderror" 
                                              id="business_address" 
                                              name="business_address" 
                                              rows="3" 
                                              placeholder="e.g., 123 Main Street, Ikeja, Lagos">{{ old('business_address') }}</textarea>
                                    @error('business_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('union.pre-approvals.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-2"></i>Create Pre-Approval
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-question-circle me-2"></i>How Pre-Approval Works
                    </h6>
                </div>
                <div class="card-body">
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <strong>Create Pre-Approval</strong>
                            <p>Fill in the applicant's details and location information.</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <strong>Code Generation</strong>
                            <p>System generates a unique approval code automatically.</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <strong>User Registration</strong>
                            <p>When the person registers, they'll be automatically approved if email/phone matches.</p>
                        </div>
                    </div>
                    
                    <div class="step-item">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <strong>Status Tracking</strong>
                            <p>Track usage status and expiry in the pre-approvals list.</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <h6 class="mb-2">Quick Tips:</h6>
                        <ul class="mb-0 small">
                            <li>States must be from your assigned locations</li>
                            <li>Email and phone must be unique</li>
                            <li>Pre-approvals expire after 6 months (default)</li>
                            <li>Users can register with either email or phone number</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-label {
    font-weight: 600;
    color: #495057;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 10px 10px 0 0 !important;
}

.text-primary {
    color: #0d6efd !important;
}

.border-bottom {
    border-bottom: 2px solid #e9ecef !important;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #ced4da;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn {
    border-radius: 6px;
    padding: 0.5rem 1rem;
}

.step-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.step-number {
    background: #0d6efd;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.step-content {
    flex: 1;
}

.step-content p {
    margin: 0.25rem 0 0 0;
    font-size: 0.875rem;
    color: #6c757d;
}

.invalid-feedback {
    font-size: 0.875em;
}
</style>
@endpush



<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Pre-Approval Create Form - JavaScript Loading...');
    
    const form = document.getElementById('preApprovalForm');
    const submitBtn = document.getElementById('submitBtn');
    const stateSelect = document.getElementById('state');
    const lgaSelect = document.getElementById('local_government');
    const phoneInput = document.getElementById('phone_number');
    
    // TEST: Verify elements exist
    console.log('Form:', form);
    console.log('State Select:', stateSelect);
    console.log('LGA Select:', lgaSelect);
    
    // ============================================================
    // DYNAMIC LGA LOADING - Same pattern as dashboard
    // ============================================================
    if (stateSelect && lgaSelect) {
        console.log('✅ State and LGA elements found - adding listener');
        
        stateSelect.addEventListener('change', function() {
            const selectedState = this.value;
            console.log('🔔 State changed to:', selectedState);
            alert('State changed to: ' + selectedState); // TEMPORARY TEST
            
            // Clear and disable LGA dropdown
            lgaSelect.innerHTML = '<option value="">Select LGA</option>';
            
            if (selectedState) {
                console.log('📡 Loading ASSIGNED LGAs for state:', selectedState);
                lgaSelect.innerHTML = '<option value="">Loading assigned LGAs...</option>';
                lgaSelect.disabled = true;
                
                // Use the SAME endpoint as dashboard - loads ONLY assigned LGAs
                fetch('{{ route("union.dashboard.get-lgas") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ state: selectedState })
                })
                .then(response => {
                    console.log('📡 LGA API response status:', response.status);
                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📦 LGA data received:', data);
                    
                    if (data.success) {
                        lgaSelect.innerHTML = '<option value="">Select LGA</option>';
                        
                        if (data.lgas && data.lgas.length > 0) {
                            console.log('✅ Loading ' + data.lgas.length + ' ASSIGNED LGAs:', data.lgas);
                            
                            // Add ONLY assigned LGAs
                            data.lgas.forEach(function(lga) {
                                var option = document.createElement('option');
                                option.value = lga;
                                option.textContent = lga;
                                
                                // Restore old input if exists
                                if ('{{ old("local_government") }}' === lga) {
                                    option.selected = true;
                                }
                                
                                lgaSelect.appendChild(option);
                            });
                            
                            lgaSelect.disabled = false;
                            console.log('✅ Assigned LGAs loaded successfully');
                            
                        } else {
                            console.log('⚠️ No LGAs assigned for this state');
                            lgaSelect.innerHTML = '<option value="">No LGAs assigned</option>';
                            lgaSelect.disabled = true;
                            alert('No LGAs assigned to you for ' + selectedState + '. Please contact your administrator.');
                        }
                        
                    } else {
                        console.error('❌ LGA API failed:', data.message);
                        lgaSelect.innerHTML = '<option value="">Error loading LGAs</option>';
                        lgaSelect.disabled = true;
                        alert('Failed to load LGAs: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(function(error) {
                    console.error('❌ Error loading LGAs:', error);
                    lgaSelect.innerHTML = '<option value="">Error loading LGAs</option>';
                    lgaSelect.disabled = true;
                    alert('Error loading LGAs. Please try again.');
                });
                
            } else {
                console.log('No state selected - disabling LGA');
                lgaSelect.disabled = true;
            }
        });
        
        // Trigger LGA loading if state already selected (for validation errors)
        var initialState = stateSelect.value;
        if (initialState) {
            console.log('🔄 Initial state detected:', initialState, '- Loading LGAs...');
            stateSelect.dispatchEvent(new Event('change'));
        }
    } else {
        console.error('❌ ERROR: State or LGA select elements not found!');
        console.log('State element:', stateSelect);
        console.log('LGA element:', lgaSelect);
    }
    
    // ============================================================
    // PHONE NUMBER FORMATTING
    // ============================================================
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            var value = this.value.replace(/[^0-9+]/g, '');
            
            // Nigerian phone number formatting
            if (value.startsWith('0') && value.length === 11) {
                // Valid Nigerian number format
            } else if (value.startsWith('+234') && value.length === 14) {
                // International format
            } else if (value.length > 0 && !value.startsWith('0') && !value.startsWith('+')) {
                // Add leading 0 if not present
                value = '0' + value;
            }
            
            this.value = value;
        });
    }
    
    // ============================================================
    // FORM VALIDATION & SUBMISSION
    // ============================================================
    if (form) {
        form.addEventListener('submit', function(e) {
            var requiredFields = form.querySelectorAll('[required]');
            var isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            // Additional LGA validation
            if (lgaSelect && !lgaSelect.value) {
                lgaSelect.classList.add('is-invalid');
                isValid = false;
                alert('Please select a Local Government Area.');
            }
            
            if (isValid) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
            } else {
                e.preventDefault();
            }
        });
    }
    
    // ============================================================
    // REMOVE VALIDATION ERRORS ON INPUT
    // ============================================================
    var inputs = form.querySelectorAll('.form-control, .form-select');
    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
        
        input.addEventListener('change', function() {
            this.classList.remove('is-invalid');
        });
    });
    
    console.log('✅ Pre-Approval form initialized successfully');
});
</script>
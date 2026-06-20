@extends('layouts.app')

@section('title', 'Edit Shop')
@section('page-title', 'Edit Shop Details')
@section('page-description', 'Update your shop information and settings')

@push('styles')
<style>
    .shop-edit-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .form-section {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }
    
    .form-section h5 {
        color: #667eea;
        border-bottom: 2px solid #f8f9fa;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .logo-preview {
        width: 100px;
        height: 100px;
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .logo-preview:hover {
        border-color: #667eea;
        background: #f0f0ff;
    }
    
    .logo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .current-status {
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
    }
    
    .status-pending {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
    }
    
    .status-approved {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .status-suspended {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Shop Edit Header -->
    <div class="shop-edit-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">
                    <i class="fas fa-edit me-3"></i>Edit Shop Details
                </h1>
                <p class="mb-0 opacity-75">
                    Update your shop information. Changes may require admin approval.
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('shop.show', $shop) }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Shop
                </a>
            </div>
        </div>
    </div>

    <!-- Current Status Alert -->
    <div class="current-status {{ $shop->approved ? 'status-approved' : ($shop->status === 'suspended' ? 'status-suspended' : 'status-pending') }}">
        <div class="d-flex align-items-center">
            <i class="fas fa-{{ $shop->approved ? 'check-circle' : ($shop->status === 'suspended' ? 'ban' : 'clock') }} fa-2x me-3"></i>
            <div>
                <h5 class="mb-1">
                    Current Status: {{ $shop->approved ? 'Approved' : ($shop->status === 'suspended' ? 'Suspended' : 'Pending Approval') }}
                </h5>
                <p class="mb-0">
                    @if($shop->approved)
                        Your shop is approved and active. You can generate receipts.
                    @elseif($shop->status === 'suspended')
                        Your shop has been suspended. Contact admin for more information.
                    @else
                        Your shop is pending approval. You'll be notified once approved.
                    @endif
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('shop.update', $shop) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="form-section">
                    <h5><i class="fas fa-store me-2"></i>Basic Information</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Shop Name *</label>
                            <input type="text" class="form-control @error('shop_name') is-invalid @enderror" 
                                   name="shop_name" value="{{ old('shop_name', $shop->shop_name) }}" required>
                            @error('shop_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Owner Full Name *</label>
                            <input type="text" class="form-control @error('owner_full_name') is-invalid @enderror" 
                                   name="owner_full_name" value="{{ old('owner_full_name', $shop->owner_full_name) }}" required>
                            @error('owner_full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Business Address *</label>
                        <textarea class="form-control @error('business_address') is-invalid @enderror" 
                                  name="business_address" rows="3" required>{{ old('business_address', $shop->business_address) }}</textarea>
                        @error('business_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Business Email *</label>
                        <input type="email" class="form-control @error('business_email') is-invalid @enderror" 
                               name="business_email" value="{{ old('business_email', $shop->business_email) }}" required>
                        @error('business_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="form-section">
                    <h5><i class="fas fa-phone me-2"></i>Contact Information</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Primary Phone *</label>
                            <input type="tel" class="form-control @error('business_phone_1') is-invalid @enderror" 
                                   name="business_phone_1" value="{{ old('business_phone_1', $shop->business_phone_1) }}" required>
                            @error('business_phone_1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secondary Phone</label>
                            <input type="tel" class="form-control @error('business_phone_2') is-invalid @enderror" 
                                   name="business_phone_2" value="{{ old('business_phone_2', $shop->business_phone_2) }}">
                            @error('business_phone_2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                 <!-- Location Information -->
<div class="form-section">
    <h5><i class="fas fa-map-marker-alt me-2"></i>Location Information</h5>
    
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Country *</label>
            <select class="form-select @error('country') is-invalid @enderror" name="country" id="country" required>
                <option value="">Select Country</option>
                <option value="Nigeria" {{ old('country', $shop->country) === 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
            </select>
            @error('country')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">State *</label>
            <select class="form-select @error('state') is-invalid @enderror" name="state" id="state" required>
                <option value="">Select State</option>
                @foreach($states as $state)
                    <option value="{{ $state }}" {{ old('state', $shop->state) === $state ? 'selected' : '' }}>
                        {{ $state }}
                    </option>
                @endforeach
            </select>
            @error('state')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Local Government *</label>
            <select class="form-select @error('local_government') is-invalid @enderror" name="local_government" id="lga" required>
                <option value="">Select LGA</option>
                @if(old('local_government', $shop->local_government))
                    <option value="{{ old('local_government', $shop->local_government) }}" selected>
                        {{ old('local_government', $shop->local_government) }}
                    </option>
                @endif
            </select>
            @error('local_government')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
                <!-- <div class="form-section">
                    <h5><i class="fas fa-map-marker-alt me-2"></i>Locationccc Information</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Country *</label>
                            <select class="form-select @error('country') is-invalid @enderror" name="country" required>
                                <option value="">Select Country</option>
                                <option value="Nigeria" {{ old('country', $shop->country) === 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                            </select>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State *</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                   name="state" value="{{ old('state', $shop->state) }}" required>
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Local Government *</label>
                            <input type="text" class="form-control @error('local_government') is-invalid @enderror" 
                                   name="local_government" value="{{ old('local_government', $shop->local_government) }}" required>
                            @error('local_government')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div> -->

                <!-- Additional Information -->
                <div class="form-section">
                    <h5><i class="fas fa-info-circle me-2"></i>Additional Information</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Terms and Conditions</label>
                        <textarea class="form-control @error('terms_and_conditions') is-invalid @enderror" 
                                  name="terms_and_conditions" rows="4" 
                                  placeholder="Enter any specific terms and conditions for your shop...">{{ old('terms_and_conditions', $shop->terms_and_conditions) }}</textarea>
                        @error('terms_and_conditions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Optional: Add any specific terms for your customers</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Shop Logo -->
                <div class="form-section">
                    <h5><i class="fas fa-image me-2"></i>Shop Logo</h5>
                    
                    <div class="text-center mb-3">
                        <div class="logo-preview" onclick="document.getElementById('logo').click()">
                            @if($shop->logo)
                                <img src="{{ asset('storage/' . $shop->logo) }}" alt="Current Logo" id="logoPreview">
                            @else
                                <div class="text-muted">
                                    <i class="fas fa-camera fa-2x mb-2"></i>
                                    <br>Click to upload logo
                                </div>
                            @endif
                        </div>
                        
                        <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                               name="logo" id="logo" accept="image/*" style="display: none;" 
                               onchange="previewLogo(this)">
                        @error('logo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">PNG, JPG up to 2MB</small>
                    </div>
                </div>

                <!-- Shop Statistics -->
                <div class="form-section">
                    <h5><i class="fas fa-chart-bar me-2"></i>Shop Statistics</h5>
                    
                    <div class="row text-center g-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">{{ $shop->total_receipts_generated }}</h4>
                                <small class="text-muted">Total Receipts</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-success mb-1">₦{{ number_format($shop->total_commission_earned, 2) }}</h4>
                                <small class="text-muted">Commission Earned</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <h6 class="text-muted mb-1">Member Since</h6>
                                <strong>{{ $shop->created_at->format('F j, Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-section">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Update Shop Details
                        </button>
                        
                        <a href="{{ route('shop.show', $shop) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel Changes
                        </a>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Changes may require admin approval before taking effect.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Dynamic LGA loading
document.addEventListener('DOMContentLoaded', function() {
    const stateSelect = document.getElementById('state');
    const lgaSelect = document.getElementById('lga');
    
    // Load LGAs when state changes
    stateSelect.addEventListener('change', function() {
        const state = this.value;
        lgaSelect.innerHTML = '<option value="">Select LGA</option>';
        
        if (state) {
            fetch(`/lgas/${state}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(lga => {
                        const option = document.createElement('option');
                        option.value = lga;
                        option.textContent = lga;
                        lgaSelect.appendChild(option);
                    });
                    
                    // Set selected LGA if exists
                    const selectedLGA = "{{ old('local_government', $shop->local_government) }}";
                    if (selectedLGA) {
                        lgaSelect.value = selectedLGA;
                    }
                })
                .catch(error => console.error('Error loading LGAs:', error));
        }
    });
    
    // Trigger change if state is already selected
    if (stateSelect.value) {
        stateSelect.dispatchEvent(new Event('change'));
    }
});
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            if (preview) {
                preview.src = e.target.result;
            } else {
                document.querySelector('.logo-preview').innerHTML = 
                    `<img src="${e.target.result}" alt="Logo Preview" id="logoPreview">`;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = ['shop_name', 'owner_full_name', 'business_address', 'business_email', 'business_phone_1', 'country', 'state', 'local_government'];
    let hasErrors = false;
    
    requiredFields.forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            hasErrors = true;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    if (hasErrors) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});
</script>
@endpush
@endsection
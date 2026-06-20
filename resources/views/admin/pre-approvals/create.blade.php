@extends('layouts.app')

@section('title', 'Add Pre-Approved User')
@section('page-title', 'Add Pre-Approved User')
@section('page-description', 'Manually add a user for pre-approval')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>Add Pre-Approved User
                        </h5>
                        <a href="{{ route('admin.pre-approvals.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.pre-approvals.store') }}">
                        @csrf
                        
                        <!-- Personal Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name *</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Primary Phone *</label>
                                <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                                       name="phone_number" value="{{ old('phone_number') }}" required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Secondary Phone</label>
                                <input type="tel" class="form-control @error('secondary_phone') is-invalid @enderror" 
                                       name="secondary_phone" value="{{ old('secondary_phone') }}">
                                @error('secondary_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">User Type *</label>
                                <select class="form-select @error('user_type') is-invalid @enderror" name="user_type" required>
                                    <option value="">Select Type</option>
                                    <option value="shop_owner" {{ old('user_type') === 'shop_owner' ? 'selected' : '' }}>Shop Owner</option>
                                    <option value="customer" {{ old('user_type') === 'customer' ? 'selected' : '' }}>Customer</option>
                                </select>
                                @error('user_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Business Information (for shop owners) -->
                        <div class="business-info" style="display: none;">
                            <h6 class="text-primary mt-4 mb-3">Business Information</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Shop Name</label>
                                <input type="text" class="form-control @error('shop_name') is-invalid @enderror" 
                                       name="shop_name" value="{{ old('shop_name') }}">
                                @error('shop_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Business Address</label>
                                <textarea class="form-control @error('business_address') is-invalid @enderror" 
                                          name="business_address" rows="3">{{ old('business_address') }}</textarea>
                                @error('business_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Business Phone</label>
                                    <input type="tel" class="form-control @error('business_phone') is-invalid @enderror" 
                                           name="business_phone" value="{{ old('business_phone') }}">
                                    @error('business_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">State</label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                           name="state" value="{{ old('state') }}">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Local Government</label>
                                    <input type="text" class="form-control @error('local_government') is-invalid @enderror" 
                                           name="local_government" value="{{ old('local_government') }}">
                                    @error('local_government')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.pre-approvals.index') }}" class="btn btn-secondary me-md-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Add Pre-Approved User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userTypeSelect = document.querySelector('select[name="user_type"]');
    const businessInfo = document.querySelector('.business-info');
    
    function toggleBusinessInfo() {
        if (userTypeSelect.value === 'shop_owner') {
            businessInfo.style.display = 'block';
        } else {
            businessInfo.style.display = 'none';
        }
    }
    
    userTypeSelect.addEventListener('change', toggleBusinessInfo);
    toggleBusinessInfo(); // Initial check
});
</script>
@endpush
@endsection
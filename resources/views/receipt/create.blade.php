@extends('layouts.app')

@section('title', 'Generate Digital Receipt')
@section('page-title', 'Generate Digital Receipt')
@section('page-description', 'Create a new digital receipt for phone sale')

@push('styles')
<style>
    /* Enhanced Form Validation Styles */

/* Valid/Invalid field states */
.form-control.is-valid,
.form-select.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.94-.94 1.66 1.66L6.78 5.57l.94.94L4.94 9.94z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6.7.7M5.8 7.2l.7-.7m-.7-.7.7.7'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

/* Focus states with validation */
.form-control.is-valid:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* FIXED: Custom validation messages - HIDDEN BY DEFAULT */
.invalid-feedback {
    display: none; /* Changed from display: block !important */
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
    font-weight: 500;
}

/* Only show invalid-feedback when field actually has is-invalid class */
.form-control.is-invalid + .invalid-feedback,
.form-select.is-invalid + .invalid-feedback,
.is-invalid .invalid-feedback {
    display: block;
}

.valid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #28a745;
    font-weight: 500;
}

.form-control.is-valid + .valid-feedback,
.form-select.is-valid + .valid-feedback {
    display: block;
}

/* Password strength indicator */
.password-strength .progress {
    height: 4px;
    background-color: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
}

.password-strength .progress-bar {
    transition: width 0.3s ease, background-color 0.3s ease;
}

/* Password requirements list */
.password-requirements {
    font-size: 0.8rem;
    margin-top: 0.5rem;
}

.password-requirements .requirement {
    margin-bottom: 0.25rem;
    transition: color 0.3s ease;
}

.password-requirements .requirement i {
    width: 12px;
    margin-right: 0.5rem;
    transition: all 0.3s ease;
}

/* Resale code strength indicator */
.strength-indicator {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.strength-indicator.text-danger { color: #dc3545 !important; }
.strength-indicator.text-warning { color: #ffc107 !important; }
.strength-indicator.text-info { color: #17a2b8 !important; }
.strength-indicator.text-success { color: #28a745 !important; }

/* Character counter styles */
.text-muted small {
    font-size: 0.8rem;
}

#address_count,
#customer_address_count {
    font-weight: 600;
    transition: color 0.3s ease;
}

/* Serial number matching indicators */
.serial-confirmation {
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.5rem;
    border-radius: 4px;
    margin-top: 0.5rem;
}

.serial-confirmation.text-success {
    background-color: rgba(40, 167, 69, 0.1);
    border: 1px solid rgba(40, 167, 69, 0.2);
}

.serial-confirmation.text-danger {
    background-color: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.2);
}

/* Form section styles */
.receipt-form-section {
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.receipt-form-section:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

.section-title i {
    background: linear-gradient(135deg, #0d8abc, #4dabf7);
    color: white;
    padding: 8px;
    border-radius: 8px;
    margin-right: 12px;
    font-size: 0.9rem;
}

/* Progress steps styles */
.progress-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
}

.progress-steps::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
}

.step-item {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #6c757d;
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
}

.step-item.active {
    background: #0d8abc;
    border-color: #0d8abc;
    color: white;
}

.step-item.completed {
    background: #28a745;
    border-color: #28a745;
    color: white;
}

/* Receipt preview styles */
.receipt-preview {
    background: white;
    border: 2px solid #28a745;
    border-radius: 12px;
    padding: 2rem;
    position: sticky;
    top: 20px;
}

.receipt-preview .badge {
    font-size: 0.75rem;
}

/* Toast notification styles */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    max-width: 400px;
    animation: slideInRight 0.4s ease, slideOutRight 0.4s ease 3.6s;
    animation-fill-mode: forwards;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

/* Input group validation states */
.input-group .form-control.is-valid {
    border-right: 1px solid #28a745;
}

.input-group .form-control.is-invalid {
    border-right: 1px solid #dc3545;
}

.input-group .btn {
    border-left: none;
}

.input-group .form-control.is-valid + .btn {
    border-color: #28a745;
}

.input-group .form-control.is-invalid + .btn {
    border-color: #dc3545;
}

/* Loading state for form submission */
.form-submitting {
    position: relative;
    pointer-events: none;
}

.form-submitting::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Accessibility improvements */
.form-control:focus,
.form-select:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

.form-control.is-invalid:focus {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

@media (max-width: 768px) {
    .receipt-form-section {
        padding: 1.5rem;
    }
    
    .receipt-preview {
        position: static;
        margin-top: 2rem;
    }
    
    .progress-steps {
        display: none;
    }
}
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="step-item active" data-step="1">1</div>
            <div class="step-item" data-step="2">2</div>
            <div class="step-item" data-step="3">3</div>
            <div class="step-item" data-step="4">4</div>
        </div>

        <!-- <form method="POST" action="{{ route('receipt.store') }}" id="receiptForm"> -->
            <form method="POST" action="{{ route('receipt.store') }}" id="receiptForm" novalidate>
            @csrf
            
            <!-- Hidden input to set receipt type as 'sale' by default -->
            <input type="hidden" name="receipt_type" value="sale">
            
            <!-- Customer Information Section -->
            <div class="receipt-form-section" data-section="1">
                <h5 class="section-title">
                    <i class="fas fa-user"></i>
                    Customer Information
                </h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer_name" class="form-label fw-medium">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('customer_name') is-invalid @enderror" 
                               id="customer_name" 
                               name="customer_name" 
                               value="{{ old('customer_name') }}" 
                               required
                               minlength="3"
                               maxlength="50"
                               
                               title="Full name must contain only letters and spaces, minimum 2 words"
                               data-validation="name">
                        <div class="invalid-feedback" id="customer_name_error">
                            Please enter a valid full name (letters and spaces only, 3-50 characters)
                        </div>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="customer_phone" class="form-label fw-medium">
                            Phone No <span class="text-danger">*</span>
                        </label>
                        <!-- <input type="tel" 
                               class="form-control @error('customer_phone') is-invalid @enderror" 
                               id="customer_phone" 
                               name="customer_phone" 
                               value="{{ old('customer_phone') }}" 
                               required
                               pattern="^0[789][01]\d{8}$"
                               maxlength="11"
                               minlength="11"
                               title="Enter a valid Nigerian phone number (11 digits starting with 070, 080, 081, 090, 091)"
                               data-validation="phone"> -->
                               <input 
    type="tel"
    class="form-control @error('customer_phone') is-invalid @enderror"
    id="customer_phone"
    name="customer_phone"
    value="{{ old('customer_phone') }}"
    required
    
    maxlength="14" 
    minlength="11" 
    title="Enter a valid Nigerian phone number (e.g., 08012345678 or +2348012345678)"
    data-validation="phone"
>
                        <div class="invalid-feedback" id="customer_phone_error">
                            Enter exactly 11 digits starting with 070, 080, 081, 090, or 091
                        </div>
                        <small class="text-muted">Format: 08012345678 (11 digits)</small>
                        @error('customer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer_email" class="form-label fw-medium">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
       class="form-control @error('customer_email') is-invalid @enderror" 
       id="customer_email" 
       name="customer_email" 
       value="{{ old('customer_email') }}"
       required
       >
                        <div class="invalid-feedback" id="customer_email_error">
                            Please enter a valid email address
                        </div>
                        @error('customer_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="customer_sex" class="form-label fw-medium">
                            Gender <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('customer_sex') is-invalid @enderror" 
                                id="customer_sex" 
                                name="customer_sex" 
                                required
                                data-validation="required">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('customer_sex') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('customer_sex') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('customer_sex') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <div class="invalid-feedback" id="customer_sex_error">
                            Please select a gender
                        </div>
                        @error('customer_sex')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="customer_address" class="form-label fw-medium">
                            Address <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('customer_address') is-invalid @enderror" 
                                  id="customer_address" 
                                  name="customer_address" 
                                  rows="2" 
                                  required
                                  minlength="15"
                                  maxlength="100"
                                  data-validation="address">{{ old('customer_address') }}</textarea>
                        <div class="invalid-feedback" id="customer_address_error">
                            Address must be between 15-100 characters
                        </div>
                        <small class="text-muted">
                            <span id="address_count">0</span>/100 characters (minimum 15 required)
                        </small>
                        @error('customer_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-end">
                    <button type="button" class="btn btn-primary" onclick="nextStep(2)" id="nextStep1Btn">
                        Next <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- Phone Information Section -->
            <div class="receipt-form-section" data-section="2" style="display: none;">
                <h5 class="section-title">
                    <i class="fas fa-mobile-alt"></i>
                    Phone Information
                </h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone_name" class="form-label fw-medium">
                            Phone Name/Model <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('phone_name') is-invalid @enderror" 
                               id="phone_name" 
                               name="phone_name" 
                               value="{{ old('phone_name') }}" 
                               required
                               minlength="2"
                               maxlength="100"
                              
                               title="Phone model name (letters, numbers, spaces, hyphens and plus signs only)"
                               data-validation="phone_model">
                        <div class="invalid-feedback" id="phone_name_error">
                            Enter a valid phone model (2-100 characters, no special symbols)
                        </div>
                        <small class="text-muted">e.g., iPhone 14 Pro Max, Samsung Galaxy S24</small>
                        @error('phone_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="phone_color" class="form-label fw-medium">
                            Phone Color <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('phone_color') is-invalid @enderror" 
                               id="phone_color" 
                               name="phone_color" 
                               value="{{ old('phone_color') }}" 
                               required
                               minlength="2"
                               maxlength="50"
                               
                               title="Phone color (letters, spaces and hyphens only)"
                               data-validation="color">
                        <div class="invalid-feedback" id="phone_color_error">
                            Enter a valid color (2-50 characters, letters only)
                        </div>
                        <small class="text-muted">e.g., Space Black, Midnight Blue, Rose Gold</small>
                        @error('phone_color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="phone_serial_number" class="form-label fw-medium">
                            Phone Serial Number <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
       class="form-control @error('phone_serial_number') is-invalid @enderror" 
       id="phone_serial_number" 
       name="phone_serial_number" 
       value="{{ old('phone_serial_number') }}" 
       required
       minlength="8"
       maxlength="50"
       
       title="Serial number (8-50 characters, letters, numbers and common symbols)"
       data-validation="serial">
                        <div class="invalid-feedback" id="phone_serial_number_error">
                            Enter a valid serial number (8-50 characters, letters and numbers only)
                        </div>
                        <small class="text-muted">Enter serial number (usually 8-25 characters)</small>
                        @error('phone_serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="phone_serial_confirmation" class="form-label fw-medium">
                            Confirm Serial Number <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('phone_serial_confirmation') is-invalid @enderror" 
                               id="phone_serial_confirmation" 
                               name="phone_serial_confirmation" 
                               value="{{ old('phone_serial_confirmation') }}" 
                               required
                               data-validation="serial_confirm">
                        <div class="invalid-feedback" id="phone_serial_confirmation_error">
                            Serial numbers must match exactly
                        </div>
                        
                        <div class="serial-confirmation mt-2" id="serialMatch" style="display: none;">
                            <div class="d-flex align-items-center text-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <span>Serial numbers match!</span>
                            </div>
                        </div>
                        
                        <div class="serial-confirmation mt-2" id="serialMismatch" style="display: none;">
                            <div class="d-flex align-items-center text-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                <span>Serial numbers do not match!</span>
                            </div>
                        </div>
                        @error('phone_serial_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn btn-primary" onclick="nextStep(3)" id="nextStep2Btn">
                        Next: Sale Details <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- Sale Information Section -->
            <div class="receipt-form-section" data-section="3" style="display: none;">
                <h5 class="section-title">
                    <i class="fas fa-tags"></i>
                    Sale Information
                </h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label fw-medium">
                            Sale Amount (₦) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">₦</span>
                            <input type="number" 
                                   class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" 
                                   name="amount" 
                                   value="{{ old('amount') }}" 
                                   required
                                   step="0.01"
                                   min="2000"
                                   max="50000000"
                                   data-validation="amount">
                            <div class="invalid-feedback" id="amount_error">
                                Enter a valid amount between ₦2,000 and ₦50,000,000
                            </div>
                        </div>
                        <small class="text-muted">Minimum: ₦2,000, Maximum: ₦50,000,000</small>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="payment_status" class="form-label fw-medium">
                            Payment Status <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('payment_status') is-invalid @enderror" 
                                id="payment_status" 
                                name="payment_status" 
                                required
                                data-validation="required">
                            <option value="">Select Payment Status</option>
                            <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid in Full</option>
                            <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Payment Pending</option>
                            <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>Partially Paid</option>
                        </select>
                        <div class="invalid-feedback" id="payment_status_error">
                            Please select a payment status
                        </div>
                        @error('payment_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="resale_code" class="form-label fw-medium">
                            Resale Code <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" 
       class="form-control @error('resale_code') is-invalid @enderror" 
       id="resale_code" 
       name="resale_code" 
       value="{{ old('resale_code') }}" 
       required
       minlength="4"
       maxlength="20"
     
       title="4-20 characters: letters, numbers and common symbols only"
       data-validation="resale_code">
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleResaleCodeVisibility()">
                                <i class="fas fa-eye" id="resale-code-eye"></i>
                            </button>
                            <div class="invalid-feedback" id="resale_code_error">
                                Resale code must be 4-20 characters (letters, numbers, symbols)
                            </div>
                        </div>
                        <small class="text-muted">
                            <span id="resale_code_strength" class="strength-indicator">Weak</span> - 
                            Keep this code secure for future resales
                        </small>
                        @error('resale_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="resale_code_confirmation" class="form-label fw-medium">
                            Confirm Resale Code <span class="text-danger">*</span>
                        </label>
                        <input type="password" 
                               class="form-control @error('resale_code_confirmation') is-invalid @enderror" 
                               id="resale_code_confirmation" 
                               name="resale_code_confirmation" 
                               value="{{ old('resale_code_confirmation') }}" 
                               required
                               data-validation="resale_code_confirm">
                        <div class="invalid-feedback" id="resale_code_confirmation_error">
                            Resale codes must match exactly
                        </div>
                        @error('resale_code_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="resaleCodeMatch" class="text-success small d-none mt-1">
                            <i class="fas fa-check me-1"></i>Resale codes match
                        </div>
                        <div id="resaleCodeMismatch" class="text-danger small d-none mt-1">
                            <i class="fas fa-times me-1"></i>Resale codes do not match
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn btn-primary" onclick="nextStep(4)" id="nextStep3Btn">
                        Next: Final Details <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            <!-- Additional Options Section -->
            
        
<!-- Step 4: Direct Payment Processing -->


<!-- Step 4: Direct Payment Processing -->
<div class="receipt-form-section" data-section="4" style="display: none;">
    <h5 class="section-title">
        <i class="fas fa-credit-card"></i>
        Complete Receipt & Payment
    </h5>
    
    <!-- Simplified Payment Info -->
    <div class="alert alert-primary mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="alert-heading mb-2">
                    <i class="fas fa-info-circle me-2"></i>Payment Information
                </h6>
                <div class="row">
                    <div class="col-6">
                        <strong>Item Value:</strong> ₦<span id="item-amount-display">0.00</span>
                        <small class="text-muted d-block">make you collected directly from the customer</small>
                    </div>
                    <div class="col-6">
                        <strong>Service Fee:</strong> ₦<span id="service-fee-amount">{{ config('services.app.receipt_generation_fee', 500) }}</span>
                        <small class="text-primary d-block">Amount to be charged</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <i class="fab fa-cc-visa fs-1 text-primary"></i>
                <i class="fab fa-cc-mastercard fs-1 text-warning mx-2"></i>
                <div class="badge bg-success mt-2">Secure Payment</div>
            </div>
        </div>
    </div>

    <!-- Hidden input for payment method (always Paystack) -->
    <input type="hidden" name="payment_method" value="paystack">

    <!-- Email & SMS Options -->
    <!--<div class="row mb-4">-->
    <!--    <div class="col-12">-->
    <!--        <h6 class="fw-medium mb-3">-->
    <!--            <i class="fas fa-envelope me-2"></i>Notification Preferences-->
    <!--        </h6>-->
    <!--        <div class="row">-->
    <!--            <div class="col-md-6">-->
    <!--                <div class="form-check">-->
    <!--                    <input class="form-check-input" -->
    <!--                           type="checkbox" -->
    <!--                           id="send_email" -->
    <!--                           name="send_email" -->
    <!--                           value="1" -->
    <!--                           checked>-->
    <!--                    <label class="form-check-label" for="send_email">-->
    <!--                        <i class="fas fa-envelope me-1"></i>Send receipt via email-->
    <!--                    </label>-->
    <!--                    <small class="text-muted d-block">Customer will receive PDF copy</small>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="col-md-6">-->
    <!--                <div class="form-check">-->
    <!--                    <input class="form-check-input" -->
    <!--                           type="checkbox" -->
    <!--                           id="send_sms" -->
    <!--                           name="send_sms" -->
    <!--                           value="1">-->
    <!--                    <label class="form-check-label" for="send_sms">-->
    <!--                        <i class="fas fa-sms me-1"></i>Send SMS notification-->
    <!--                    </label>-->
    <!--                    <small class="text-muted d-block">Brief receipt confirmation</small>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->

    <!-- Final Review -->
    <!--<div class="card border-success mb-4">-->
    <!--    <div class="card-header bg-light">-->
    <!--        <h6 class="card-title mb-0">-->
    <!--            <i class="fas fa-check-circle text-success me-2"></i>Ready to Generate Receipt-->
    <!--        </h6>-->
    <!--    </div>-->
    <!--    <div class="card-body">-->
    <!--        <div class="row">-->
    <!--            <div class="col-md-8">-->
                    <!--<p class="mb-2"><strong>Customer:</strong> <span id="final-customer-name">-</span></p>-->
                    <!--<p class="mb-2"><strong>Phone:</strong> <span id="final-phone-model">-</span> (<span id="final-phone-color">-</span>)</p>-->
                    <!--<p class="mb-0"><strong>Amount:</strong> ₦<span id="final-amount">0.00</span></p>-->
    <!--            </div>-->
    <!--            <div class="col-md-4 text-end">-->
    <!--                <div class="badge bg-primary fs-6 px-3 py-2">-->
    <!--                    Service Fee: ₦<span id="final-service-fee">{{ config('services.app.receipt_generation_fee', 500) }}</span>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->

    <!-- Action Buttons -->
    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-outline-secondary" onclick="prevStep(3)">
            <i class="fas fa-arrow-left me-2"></i>Back
        </button>
        <button type="submit" class="btn btn-success btn-lg px-4" id="generateReceiptBtn">
            <i class="fas fa-receipt me-2"></i>Generate Receipt & Pay
            <span class="spinner-border spinner-border-sm ms-2 d-none" id="generateSpinner"></span>
        </button>
    </div>
</div>

    <!-- Payment Summary -->
<!--    <div class="alert alert-info mb-4">-->
<!--        <h6 class="alert-heading">-->
<!--            <i class="fas fa-info-circle me-2"></i>Payment Summary-->
<!--        </h6>-->
<!--        <div class="row">-->
<!--            <div class="col-md-6">-->
<!--                <strong>Item Amount:</strong> ₦<span id="item-amount-display">0.00</span>-->
<!--                <small class="text-muted d-block">For your records only</small>-->
<!--            </div>-->
<!--            <div class="col-md-6">-->
<!--                <strong>Service Fee:</strong> ₦<span id="service-fee-amount">{{ config('services.app.receipt_generation_fee', 500) }}</span>-->
<!--                <small class="text-success d-block">Amount to be charged</small>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

    <!-- Email & SMS Options -->
<!--    <div class="row mb-4">-->
<!--        <div class="col-12">-->
<!--            <h6 class="fw-medium mb-3">-->
<!--                <i class="fas fa-envelope me-2"></i>Notification Options-->
<!--            </h6>-->
<!--            <div class="row">-->
<!--                <div class="col-md-6">-->
<!--                    <div class="form-check">-->
<!--                        <input class="form-check-input" -->
<!--                               type="checkbox" -->
<!--                               id="send_email" -->
<!--                               name="send_email" -->
<!--                               value="1" -->
<!--                               checked>-->
<!--                        <label class="form-check-label" for="send_email">-->
<!--                            <i class="fas fa-envelope me-1"></i>Send receipt via email-->
<!--                        </label>-->
<!--                        <small class="text-muted d-block">Customer will receive PDF copy</small>-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="col-md-6">-->
<!--                    <div class="form-check">-->
<!--                        <input class="form-check-input" -->
<!--                               type="checkbox" -->
<!--                               id="send_sms" -->
<!--                               name="send_sms" -->
<!--                               value="1">-->
<!--                        <label class="form-check-label" for="send_sms">-->
<!--                            <i class="fas fa-sms me-1"></i>Send SMS notification-->
<!--                        </label>-->
<!--                        <small class="text-muted d-block">Receipt link via SMS</small>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

    <!-- Additional Options -->
    <div class="row">
        <div class="col-12 mb-3">
            <label for="notes" class="form-label fw-medium">
                Receipt Notes <small class="text-muted">(Optional)</small>
            </label>
            <textarea class="form-control @error('notes') is-invalid @enderror" 
                      id="notes" 
                      name="notes" 
                      rows="3" 
                      placeholder="Any additional information about this sale">{{ old('notes') }}</textarea>
            @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

<!--    <div class="d-flex justify-content-between">-->
<!--        <button type="button" class="btn btn-outline-secondary" onclick="prevStep(3)">-->
<!--            <i class="fas fa-arrow-left me-1"></i> Back-->
<!--        </button>-->
<!--        <button type="submit" class="btn btn-success btn-lg" id="generateReceiptBtn">-->
<!--            <i class="fas fa-receipt me-2"></i>-->
<!--            <span id="button-text">Pay & Generate Receipt</span>-->
<!--            <span class="spinner-border spinner-border-sm ms-2 d-none" id="generateSpinner"></span>-->
<!--        </button>-->
<!--    </div>-->
<!--</div>  -->
        
        </form>
<!--    </div>-->

<!--    <div class="col-lg-4">-->
<!--        <div class="receipt-preview">-->
<!--            <div class="text-center mb-4">-->
<!--                <h5 class="fw-bold text-success">-->
<!--                    <i class="fas fa-receipt me-2"></i>Receipt Preview-->
<!--                </h5>-->
<!--                <div class="badge bg-success">Digital Receipt</div>-->
<!--            </div>-->
            
<!--            <div class="preview-content">-->
<!--                <h6 class="fw-bold mb-3">Customer:</h6>-->
<!--                <div class="row mb-2">-->
<!--                    <div class="col-5 text-muted">Name:</div>-->
<!--                    <div class="col-7" id="previewCustomerName">-</div>-->
<!--                </div>-->
<!--                <div class="row mb-2">-->
<!--                    <div class="col-5 text-muted">Phone:</div>-->
<!--                    <div class="col-7" id="previewCustomerPhone">-</div>-->
<!--                </div>-->
<!--                <div class="row mb-3">-->
<!--                    <div class="col-5 text-muted">Email:</div>-->
<!--                    <div class="col-7 small" id="previewCustomerEmail">-</div>-->
<!--                </div>-->
                
<!--                <hr class="my-3">-->
                
<!--                <h6 class="fw-bold mb-3">Phone:</h6>-->
<!--                <div class="row mb-2">-->
<!--                    <div class="col-5 text-muted">Model:</div>-->
<!--                    <div class="col-7" id="previewPhoneName">-</div>-->
<!--                </div>-->
<!--                <div class="row mb-2">-->
<!--                    <div class="col-5 text-muted">Color:</div>-->
<!--                    <div class="col-7" id="previewPhoneColor">-</div>-->
<!--                </div>-->
<!--                <div class="row mb-3">-->
<!--                    <div class="col-5 text-muted">Serial:</div>-->
<!--                    <div class="col-7 small" id="previewPhoneSerial">-</div>-->
<!--                </div>-->
                
<!--                <hr class="my-3">-->
                
<!--                <div class="row mb-2">-->
<!--                    <div class="col-5 fw-bold">Amount:</div>-->
<!--                    <div class="col-7 fw-bold text-success" id="previewAmount">₦0.00</div>-->
<!--                </div>-->
<!--                <div class="row mb-2">-->
<!--                    <div class="col-5 text-muted">Status:</div>-->
<!--                    <div class="col-7" id="previewPaymentStatus">-</div>-->
<!--                </div>-->
                
<!--                <div class="text-center mt-4">-->
<!--                    <small class="text-muted">-->
<!--                        <i class="fas fa-info-circle me-1"></i>-->
<!--                        Complete the form to generate the actual receipt.-->
<!--                    </small>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form step management
    let currentStep = 1;
    const totalSteps = 4;

    // Initialize form
    initializeForm();
    
    // Event listeners
    setupEventListeners();
    
    // Real-time preview updates
    setupPreviewUpdates();

    function initializeForm() {
        // Show first step
        showStep(1);
        
        // Set up validation - BUT DON'T TRIGGER IT IMMEDIATELY
        setupValidation();
        
        // Set up strict input restrictions
        setupInputRestrictions();
        
        // Initialize address counter
        updateAddressCounter();
    }

    function setupEventListeners() {
        // Serial number confirmation
        const serialField = document.getElementById('phone_serial_confirmation');
        const serialOriginal = document.getElementById('phone_serial_number');
        if (serialField && serialOriginal) {
            serialField.addEventListener('input', checkSerialMatch);
            serialOriginal.addEventListener('input', checkSerialMatch);
        }
        
        // Amount input
        const amountField = document.getElementById('amount');
        if (amountField) {
            amountField.addEventListener('input', updateAmountPreview);
        }
        
        // Form submission
        document.getElementById('receiptForm').addEventListener('submit', handleFormSubmit);
        
        // Phone number formatting
        const phoneField = document.getElementById('customer_phone');
        if (phoneField) {
            phoneField.addEventListener('input', formatPhoneNumber);
        }
        
        // Resale code confirmation
        const resaleField = document.getElementById('resale_code_confirmation');
        const resaleOriginal = document.getElementById('resale_code');
        if (resaleField && resaleOriginal) {
            resaleField.addEventListener('input', checkResaleCodeMatch);
            resaleOriginal.addEventListener('input', checkResaleCodeMatch);
        }
    }

    function setupInputRestrictions() {
        // Customer Name - Only letters and spaces
        const customerNameField = document.getElementById('customer_name');
        if (customerNameField) {
            customerNameField.addEventListener('keypress', allowOnlyLetters);
        }
        
        // Phone Number - Only digits
        const phoneField = document.getElementById('customer_phone');
        if (phoneField) {
            phoneField.addEventListener('keypress', allowOnlyNumbers);
        }
        
        // Amount - Only numbers and decimal
        const amountField = document.getElementById('amount');
        if (amountField) {
            amountField.addEventListener('keypress', allowNumbersAndDecimal);
        }
    }

    function setupPreviewUpdates() {
        // Customer name
        const customerNameField = document.getElementById('customer_name');
        if (customerNameField) {
            customerNameField.addEventListener('input', function() {
                document.getElementById('previewCustomerName').textContent = this.value || '-';
            });
        }
        
        // Customer phone
        const customerPhoneField = document.getElementById('customer_phone');
        if (customerPhoneField) {
            customerPhoneField.addEventListener('input', function() {
                document.getElementById('previewCustomerPhone').textContent = this.value || '-';
            });
        }
        
        // Customer email
        const customerEmailField = document.getElementById('customer_email');
        if (customerEmailField) {
            customerEmailField.addEventListener('input', function() {
                document.getElementById('previewCustomerEmail').textContent = this.value || '-';
            });
        }
        
        // Phone name
        const phoneNameField = document.getElementById('phone_name');
        if (phoneNameField) {
            phoneNameField.addEventListener('input', function() {
                document.getElementById('previewPhoneName').textContent = this.value || '-';
            });
        }
        
        // Phone color
        const phoneColorField = document.getElementById('phone_color');
        if (phoneColorField) {
            phoneColorField.addEventListener('input', function() {
                document.getElementById('previewPhoneColor').textContent = this.value || '-';
            });
        }
        
        // Phone serial
        const phoneSerialField = document.getElementById('phone_serial_number');
        if (phoneSerialField) {
            phoneSerialField.addEventListener('input', function() {
                document.getElementById('previewPhoneSerial').textContent = this.value || '-';
            });
        }
        
        // Amount
        const amountField = document.getElementById('amount');
        if (amountField) {
            amountField.addEventListener('input', function() {
                const amount = parseFloat(this.value) || 0;
                document.getElementById('previewAmount').textContent = '₦' + amount.toLocaleString('en-NG', {minimumFractionDigits: 2});
            });
        }
        
        // Payment status
        const paymentStatusField = document.getElementById('payment_status');
        if (paymentStatusField) {
            paymentStatusField.addEventListener('change', function() {
                const status = this.value;
                const badge = status === 'paid' ? 'bg-success' : status === 'pending' ? 'bg-warning' : 'bg-info';
                document.getElementById('previewPaymentStatus').innerHTML = status ? 
                    `<span class="badge ${badge}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>` : '-';
            });
        }
    }

    // FIXED VALIDATION FUNCTIONS - No errors by default, only show when needed
    function clearFieldError(field) {
        field.classList.remove('is-invalid');
        field.classList.remove('is-valid');
        
        // Clear any existing error messages
        const feedback = field.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.style.display = 'none';
        }
        
        // Also check for error divs with IDs
        const errorDiv = document.getElementById(field.name + '_error');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }

    function showFieldError(field, message) {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        
        // Find or create feedback element
        let feedback = field.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            // Try to find by ID first
            feedback = document.getElementById(field.name + '_error');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                field.parentNode.appendChild(feedback);
            }
        }
        
        feedback.textContent = message;
        feedback.style.display = 'block';
    }

    function showFieldValid(field) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        clearFieldError(field);
    }

    // FIXED: Setup validation - Only trigger on user interaction, not page load
    function setupValidation() {
        const form = document.getElementById('receiptForm');
        const inputs = form.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            let hasInteracted = false;
            
            // Mark field as interacted when user starts typing
            input.addEventListener('input', function() {
                hasInteracted = true;
                // Clear error immediately when user starts fixing
                if (this.classList.contains('is-invalid')) {
                    clearFieldError(this);
                }
                // Only validate after user has interacted
                if (hasInteracted && this.value.trim()) {
                    validateField(this);
                }
            });
            
            // Validate on blur only after user has interacted
            input.addEventListener('blur', function() {
                if (hasInteracted) {
                    validateField(this);
                }
            });
            
            // For required fields, mark as interacted when they try to leave empty
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    hasInteracted = true;
                }
            });
        });
    }

    function validateField(field) {
        const value = field.value.trim();
        
        // Apply validation based on field name/type
        if (field.name === 'customer_name') {
            return validateCustomerName(field);
        }
        
        if (field.name === 'customer_phone') {
            return validatePhoneNumber(field);
        }
        
        if (field.name === 'customer_email' && value) {
            return validateEmail(field);
        }
        
        if (field.name === 'customer_address') {
            return validateAddress(field);
        }
        
        if (field.name === 'amount') {
            return validateAmount(field);
        }
        
        // Basic required field validation
        if (field.hasAttribute('required') && !value) {
            showFieldError(field, 'This field is required');
            return false;
        }
        
        // If we get here, field is valid
        showFieldValid(field);
        return true;
    }

    function validateCustomerName(field) {
        const value = field.value.trim();
        const nameRegex = /^[a-zA-Z\s]+$/;
        const words = value.split(' ').filter(word => word.length > 0);
        
        if (!value) {
            showFieldError(field, 'Full name is required');
            return false;
        }
        
        if (words.length < 2) {
            showFieldError(field, 'Please enter first and last name');
            return false;
        }
        
        if (!nameRegex.test(value)) {
            showFieldError(field, 'Name can only contain letters and spaces');
            return false;
        }
        
        if (value.length < 3) {
            showFieldError(field, 'Name must be at least 3 characters');
            return false;
        }
        
        if (value.length > 50) {
            showFieldError(field, 'Name cannot exceed 50 characters');
            return false;
        }
        
        showFieldValid(field);
        return true;
    }

    function validatePhoneNumber(field) {
        const value = field.value.trim();
        const phoneRegex = /^0[789][01]\d{8}$/;
        
        if (!value) {
            showFieldError(field, 'Phone number is required');
            return false;
        }
        
        if (!phoneRegex.test(value)) {
            showFieldError(field, 'Enter a valid Nigerian phone number (11 digits starting with 070, 080, 081, 090, 091)');
            return false;
        }
        
        showFieldValid(field);
        return true;
    }

    function validateEmail(field) {
        const value = field.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (value && !emailRegex.test(value)) {
            showFieldError(field, 'Please enter a valid email address');
            return false;
        }
        
        showFieldValid(field);
        return true;
    }

    function validateAddress(field) {
        const value = field.value.trim();
        
        if (!value) {
            showFieldError(field, 'Address is required');
            return false;
        }
        
        if (value.length < 15) {
            showFieldError(field, 'Address must be at least 15 characters');
            return false;
        }
        
        if (value.length > 100) {
            showFieldError(field, 'Address cannot exceed 100 characters');
            return false;
        }
        
        showFieldValid(field);
        return true;
    }

    function validateAmount(field) {
        const value = parseFloat(field.value);
        
        if (isNaN(value) || value <= 0) {
            showFieldError(field, 'Amount must be a positive number');
            return false;
        }
        
        // Check minimum of ₦2,000
        if (value < 2000) {
            showFieldError(field, 'Minimum amount is ₦2,000');
            return false;
        }
        
        if (value > 50000000) {
            showFieldError(field, 'Amount cannot exceed ₦50,000,000');
            return false;
        }
        
        showFieldValid(field);
        return true;
    }

    // INPUT RESTRICTION FUNCTIONS
    function allowOnlyLetters(event) {
        const char = String.fromCharCode(event.which);
        if (!/[a-zA-Z\s]/.test(char)) {
            event.preventDefault();
        }
    }
    
    function allowOnlyNumbers(event) {
        const char = String.fromCharCode(event.which);
        if (!/[0-9]/.test(char)) {
            event.preventDefault();
        }
    }
    
    function allowNumbersAndDecimal(event) {
        const char = String.fromCharCode(event.which);
        const currentValue = event.target.value;
        
        // Allow numbers
        if (/[0-9]/.test(char)) {
            return true;
        }
        
        // Allow decimal point only if not already present
        if (char === '.' && !currentValue.includes('.')) {
            return true;
        }
        
        event.preventDefault();
    }

    function formatPhoneNumber(event) {
        let value = event.target.value.replace(/\D/g, '');
        
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        
        event.target.value = value;
    }

    function checkSerialMatch() {
        const serial1 = document.getElementById('phone_serial_number').value;
        const serial2 = document.getElementById('phone_serial_confirmation').value;
        const matchDiv = document.getElementById('serialMatch');
        const mismatchDiv = document.getElementById('serialMismatch');
        
        if (serial1 && serial2) {
            if (serial1 === serial2) {
                matchDiv.style.display = 'block';
                mismatchDiv.style.display = 'none';
                showFieldValid(document.getElementById('phone_serial_confirmation'));
            } else {
                matchDiv.style.display = 'none';
                mismatchDiv.style.display = 'block';
                showFieldError(document.getElementById('phone_serial_confirmation'), 'Serial numbers must match');
            }
        } else {
            matchDiv.style.display = 'none';
            mismatchDiv.style.display = 'none';
        }
    }

    function checkResaleCodeMatch() {
        const code1 = document.getElementById('resale_code').value;
        const code2 = document.getElementById('resale_code_confirmation').value;
        const matchDiv = document.getElementById('resaleCodeMatch');
        const mismatchDiv = document.getElementById('resaleCodeMismatch');
        
        if (code1 && code2) {
            if (code1 === code2) {
                matchDiv.classList.remove('d-none');
                mismatchDiv.classList.add('d-none');
                showFieldValid(document.getElementById('resale_code_confirmation'));
            } else {
                matchDiv.classList.add('d-none');
                mismatchDiv.classList.remove('d-none');
                showFieldError(document.getElementById('resale_code_confirmation'), 'Resale codes must match');
            }
        } else {
            matchDiv.classList.add('d-none');
            mismatchDiv.classList.add('d-none');
        }
    }

    function updateAmountPreview() {
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        document.getElementById('previewAmount').textContent = '₦' + amount.toLocaleString('en-NG', {minimumFractionDigits: 2});
    }

    function updateAddressCounter() {
        const addressField = document.getElementById('customer_address');
        const counterSpan = document.getElementById('address_count');
        
        if (addressField && counterSpan) {
            addressField.addEventListener('input', function() {
                counterSpan.textContent = this.value.length;
            });
            
            // Initialize counter
            counterSpan.textContent = addressField.value.length;
        }
    }

    function showStep(step) {
        // Hide all sections
        document.querySelectorAll('[data-section]').forEach(section => {
            section.style.display = 'none';
        });
        
        // Show target section
        const targetSection = document.querySelector(`[data-section="${step}"]`);
        if (targetSection) {
            targetSection.style.display = 'block';
        }
        
        // Update step indicators
        document.querySelectorAll('.step-item').forEach((item, index) => {
            const stepNumber = index + 1;
            item.classList.remove('active', 'completed');
            
            if (stepNumber < step) {
                item.classList.add('completed');
            } else if (stepNumber === step) {
                item.classList.add('active');
            }
        });
        
        currentStep = step;
    }

    function validateStep(step) {
        const section = document.querySelector(`[data-section="${step}"]`);
        const requiredFields = section.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            // Force validation on step validation
            if (!validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    function handleFormSubmit(e) {
    // Check for validation errors first
    const invalidFields = document.querySelectorAll('.is-invalid');
    if (invalidFields.length > 0) {
        e.preventDefault();
        handleValidationErrors();
        showToast('Please fix all validation errors before submitting', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('generateReceiptBtn');
    const spinner = document.getElementById('generateSpinner');
    
    // Basic validation - validate all steps
    let isValid = true;
    for (let i = 1; i <= totalSteps; i++) {
        if (!validateStep(i)) {
            isValid = false;
            // Show the step with errors
            showStep(i);
            break;
        }
    }
    
    if (!isValid) {
        e.preventDefault();
        showToast('Please fix all validation errors before submitting', 'error');
        return;
    }
    
    // Show loading state
    if (submitBtn) submitBtn.disabled = true;
    if (spinner) spinner.classList.remove('d-none');
}

    // function handleFormSubmit(e) {
    //     // Show loading state
    //     const submitBtn = document.getElementById('generateReceiptBtn');
    //     const spinner = document.getElementById('generateSpinner');
        
    //     // Basic validation - validate all steps
    //     let isValid = true;
    //     for (let i = 1; i <= totalSteps; i++) {
    //         if (!validateStep(i)) {
    //             isValid = false;
    //             // Show the step with errors
    //             showStep(i);
    //             break;
    //         }
    //     }
        
    //     if (!isValid) {
    //         e.preventDefault();
    //         showToast('Please fix all validation errors before submitting', 'error');
    //         return;
    //     }
        
    //     // Show loading state
    //     if (submitBtn) submitBtn.disabled = true;
    //     if (spinner) spinner.classList.remove('d-none');
    // }

    // Global functions for buttons
    window.nextStep = function(step) {
        const currentStepNumber = step - 1;
        
        if (validateStep(currentStepNumber)) {
            showStep(step);
        } else {
            const errorFields = document.querySelectorAll('.is-invalid');
            if (errorFields.length > 0) {
                errorFields[0].focus();
                showToast('Please fix the highlighted errors before proceeding', 'error');
            }
        }
    };

    window.prevStep = function(step) {
        showStep(step);
    };

    window.toggleResaleCodeVisibility = function() {
        const field = document.getElementById('resale_code');
        const icon = document.getElementById('resale-code-eye');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    };

    // Fix for hidden field validation errors
function handleValidationErrors() {
    // Check for invalid fields that are not visible
    const invalidFields = document.querySelectorAll('.is-invalid');
    
    invalidFields.forEach(field => {
        const section = field.closest('[data-section]');
        if (section && section.style.display === 'none') {
            // Find which step this field belongs to
            const sectionNumber = parseInt(section.getAttribute('data-section'));
            if (sectionNumber) {
                // Show the step with the error
                showStep(sectionNumber);
                // Focus on the first invalid field
                setTimeout(() => {
                    field.focus();
                    field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 100);
                return false; // Stop after first error
            }
        }
    });
}

    // Toast notification function
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : 'success'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }
});


document.addEventListener('DOMContentLoaded', function() {
    const paystackRadio = document.getElementById('paystack_payment');
    const offlineRadio = document.getElementById('offline_payment');
    const buttonText = document.getElementById('button-text');
    const itemAmountDisplay = document.getElementById('item-amount-display');
    const amountInput = document.getElementById('amount');

    // Update item amount display
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            itemAmountDisplay.textContent = amount.toFixed(2);
        });
    }

    // Update button text based on payment method
    function updateButtonText() {
        if (paystackRadio.checked) {
            buttonText.textContent = 'Pay & Generate Receipt';
        } else {
            buttonText.textContent = 'Generate Receipt';
        }
    }

    paystackRadio.addEventListener('change', updateButtonText);
    offlineRadio.addEventListener('change', updateButtonText);
    
    updateButtonText(); // Initial call
});

</script>
@endpush
@push('scripts')
<script>
// document.addEventListener('DOMContentLoaded', function() {
//     // Form step management
//     let currentStep = 1;
//     const totalSteps = 4;

//     // Initialize form
//     initializeForm();
    
//     // Event listeners
//     setupEventListeners();
    
//     // Real-time preview updates
//     setupPreviewUpdates();

//     function initializeForm() {
//         // Show first step
//         showStep(1);
        
//         // Set up validation - BUT DON'T TRIGGER IT IMMEDIATELY
//         setupValidation();
        
//         // Set up strict input restrictions
//         setupInputRestrictions();
        
//         // Initialize address counter
//         updateAddressCounter();
//     }

//     function setupEventListeners() {
//         // Serial number confirmation
//         const serialField = document.getElementById('phone_serial_confirmation');
//         const serialOriginal = document.getElementById('phone_serial_number');
//         if (serialField && serialOriginal) {
//             serialField.addEventListener('input', checkSerialMatch);
//             serialOriginal.addEventListener('input', checkSerialMatch);
//         }
        
//         // Amount input
//         const amountField = document.getElementById('amount');
//         if (amountField) {
//             amountField.addEventListener('input', updateAmountPreview);
//         }
        
//         // Form submission
//         document.getElementById('receiptForm').addEventListener('submit', handleFormSubmit);
        
//         // Phone number formatting
//         const phoneField = document.getElementById('customer_phone');
//         if (phoneField) {
//             phoneField.addEventListener('input', formatPhoneNumber);
//         }
        
//         // Resale code confirmation
//         const resaleField = document.getElementById('resale_code_confirmation');
//         const resaleOriginal = document.getElementById('resale_code');
//         if (resaleField && resaleOriginal) {
//             resaleField.addEventListener('input', checkResaleCodeMatch);
//             resaleOriginal.addEventListener('input', checkResaleCodeMatch);
//         }
//     }

//     function setupInputRestrictions() {
//         // Customer Name - Only letters and spaces
//         const customerNameField = document.getElementById('customer_name');
//         if (customerNameField) {
//             customerNameField.addEventListener('keypress', allowOnlyLetters);
//         }
        
//         // Phone Number - Only digits
//         const phoneField = document.getElementById('customer_phone');
//         if (phoneField) {
//             phoneField.addEventListener('keypress', allowOnlyNumbers);
//         }
        
//         // Amount - Only numbers and decimal
//         const amountField = document.getElementById('amount');
//         if (amountField) {
//             amountField.addEventListener('keypress', allowNumbersAndDecimal);
//         }
//     }

//     function setupPreviewUpdates() {
//         // Customer name
//         const customerNameField = document.getElementById('customer_name');
//         if (customerNameField) {
//             customerNameField.addEventListener('input', function() {
//                 document.getElementById('previewCustomerName').textContent = this.value || '-';
//             });
//         }
        
//         // Customer phone
//         const customerPhoneField = document.getElementById('customer_phone');
//         if (customerPhoneField) {
//             customerPhoneField.addEventListener('input', function() {
//                 document.getElementById('previewCustomerPhone').textContent = this.value || '-';
//             });
//         }
        
//         // Customer email
//         const customerEmailField = document.getElementById('customer_email');
//         if (customerEmailField) {
//             customerEmailField.addEventListener('input', function() {
//                 document.getElementById('previewCustomerEmail').textContent = this.value || '-';
//             });
//         }
        
//         // Phone name
//         const phoneNameField = document.getElementById('phone_name');
//         if (phoneNameField) {
//             phoneNameField.addEventListener('input', function() {
//                 document.getElementById('previewPhoneName').textContent = this.value || '-';
//             });
//         }
        
//         // Phone color
//         const phoneColorField = document.getElementById('phone_color');
//         if (phoneColorField) {
//             phoneColorField.addEventListener('input', function() {
//                 document.getElementById('previewPhoneColor').textContent = this.value || '-';
//             });
//         }
        
//         // Phone serial
//         const phoneSerialField = document.getElementById('phone_serial_number');
//         if (phoneSerialField) {
//             phoneSerialField.addEventListener('input', function() {
//                 document.getElementById('previewPhoneSerial').textContent = this.value || '-';
//             });
//         }
        
//         // Amount
//         const amountField = document.getElementById('amount');
//         if (amountField) {
//             amountField.addEventListener('input', function() {
//                 const amount = parseFloat(this.value) || 0;
//                 document.getElementById('previewAmount').textContent = '₦' + amount.toLocaleString('en-NG', {minimumFractionDigits: 2});
//             });
//         }
        
//         // Payment status
//         const paymentStatusField = document.getElementById('payment_status');
//         if (paymentStatusField) {
//             paymentStatusField.addEventListener('change', function() {
//                 const status = this.value;
//                 const badge = status === 'paid' ? 'bg-success' : status === 'pending' ? 'bg-warning' : 'bg-info';
//                 document.getElementById('previewPaymentStatus').innerHTML = status ? 
//                     `<span class="badge ${badge}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>` : '-';
//             });
//         }
//     }

//     // FIXED VALIDATION FUNCTIONS - No errors by default, only show when needed
//     function clearFieldError(field) {
//         field.classList.remove('is-invalid');
//         field.classList.remove('is-valid');
        
//         // Clear any existing error messages
//         const feedback = field.parentNode.querySelector('.invalid-feedback');
//         if (feedback) {
//             feedback.style.display = 'none';
//         }
        
//         // Also check for error divs with IDs
//         const errorDiv = document.getElementById(field.name + '_error');
//         if (errorDiv) {
//             errorDiv.style.display = 'none';
//         }
//     }

//     function showFieldError(field, message) {
//         field.classList.remove('is-valid');
//         field.classList.add('is-invalid');
        
//         // Find or create feedback element
//         let feedback = field.parentNode.querySelector('.invalid-feedback');
//         if (!feedback) {
//             // Try to find by ID first
//             feedback = document.getElementById(field.name + '_error');
//             if (!feedback) {
//                 feedback = document.createElement('div');
//                 feedback.className = 'invalid-feedback';
//                 field.parentNode.appendChild(feedback);
//             }
//         }
        
//         feedback.textContent = message;
//         feedback.style.display = 'block';
//     }

//     function showFieldValid(field) {
//         field.classList.remove('is-invalid');
//         field.classList.add('is-valid');
//         clearFieldError(field);
//     }

//     // FIXED: Setup validation - Only trigger on user interaction, not page load
//     function setupValidation() {
//         const form = document.getElementById('receiptForm');
//         const inputs = form.querySelectorAll('input, select, textarea');
        
//         inputs.forEach(input => {
//             let hasInteracted = false;
            
//             // Mark field as interacted when user starts typing
//             input.addEventListener('input', function() {
//                 hasInteracted = true;
//                 // Clear error immediately when user starts fixing
//                 if (this.classList.contains('is-invalid')) {
//                     clearFieldError(this);
//                 }
//                 // Only validate after user has interacted
//                 if (hasInteracted && this.value.trim()) {
//                     validateField(this);
//                 }
//             });
            
//             // Validate on blur only after user has interacted
//             input.addEventListener('blur', function() {
//                 if (hasInteracted) {
//                     validateField(this);
//                 }
//             });
            
//             // For required fields, mark as interacted when they try to leave empty
//             input.addEventListener('blur', function() {
//                 if (this.hasAttribute('required') && !this.value.trim()) {
//                     hasInteracted = true;
//                 }
//             });
//         });
//     }

//     function validateField(field) {
//         const value = field.value.trim();
        
//         // Apply validation based on field name/type
//         if (field.name === 'customer_name') {
//             return validateCustomerName(field);
//         }
        
//         if (field.name === 'customer_phone') {
//             return validatePhoneNumber(field);
//         }
        
//         if (field.name === 'customer_email' && value) {
//             return validateEmail(field);
//         }
        
//         if (field.name === 'customer_address') {
//             return validateAddress(field);
//         }
        
//         if (field.name === 'amount') {
//             return validateAmount(field);
//         }
        
//         // Basic required field validation
//         if (field.hasAttribute('required') && !value) {
//             showFieldError(field, 'This field is required');
//             return false;
//         }
        
//         // If we get here, field is valid
//         showFieldValid(field);
//         return true;
//     }

//     function validateCustomerName(field) {
//         const value = field.value.trim();
//         const nameRegex = /^[a-zA-Z\s]+$/;
//         const words = value.split(' ').filter(word => word.length > 0);
        
//         if (!value) {
//             showFieldError(field, 'Full name is required');
//             return false;
//         }
        
//         if (words.length < 2) {
//             showFieldError(field, 'Please enter first and last name');
//             return false;
//         }
        
//         if (!nameRegex.test(value)) {
//             showFieldError(field, 'Name can only contain letters and spaces');
//             return false;
//         }
        
//         if (value.length < 3) {
//             showFieldError(field, 'Name must be at least 3 characters');
//             return false;
//         }
        
//         if (value.length > 50) {
//             showFieldError(field, 'Name cannot exceed 50 characters');
//             return false;
//         }
        
//         showFieldValid(field);
//         return true;
//     }

//     function validatePhoneNumber(field) {
//         const value = field.value.trim();
//         const phoneRegex = /^0[789][01]\d{8}$/;
        
//         if (!value) {
//             showFieldError(field, 'Phone number is required');
//             return false;
//         }
        
//         if (!phoneRegex.test(value)) {
//             showFieldError(field, 'Enter a valid Nigerian phone number (11 digits starting with 070, 080, 081, 090, 091)');
//             return false;
//         }
        
//         showFieldValid(field);
//         return true;
//     }

//     function validateEmail(field) {
//         const value = field.value.trim();
//         const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
//         if (value && !emailRegex.test(value)) {
//             showFieldError(field, 'Please enter a valid email address');
//             return false;
//         }
        
//         showFieldValid(field);
//         return true;
//     }

//     function validateAddress(field) {
//         const value = field.value.trim();
        
//         if (!value) {
//             showFieldError(field, 'Address is required');
//             return false;
//         }
        
//         if (value.length < 15) {
//             showFieldError(field, 'Address must be at least 15 characters');
//             return false;
//         }
        
//         if (value.length > 100) {
//             showFieldError(field, 'Address cannot exceed 100 characters');
//             return false;
//         }
        
//         showFieldValid(field);
//         return true;
//     }

//     function validateAmount(field) {
//         const value = parseFloat(field.value);
        
//         if (isNaN(value) || value <= 0) {
//             showFieldError(field, 'Amount must be a positive number');
//             return false;
//         }
        
//         // Check minimum of ₦2,000
//         if (value < 2000) {
//             showFieldError(field, 'Minimum amount is ₦2,000');
//             return false;
//         }
        
//         if (value > 50000000) {
//             showFieldError(field, 'Amount cannot exceed ₦50,000,000');
//             return false;
//         }
        
//         showFieldValid(field);
//         return true;
//     }

//     // INPUT RESTRICTION FUNCTIONS
//     function allowOnlyLetters(event) {
//         const char = String.fromCharCode(event.which);
//         if (!/[a-zA-Z\s]/.test(char)) {
//             event.preventDefault();
//         }
//     }
    
//     function allowOnlyNumbers(event) {
//         const char = String.fromCharCode(event.which);
//         if (!/[0-9]/.test(char)) {
//             event.preventDefault();
//         }
//     }
    
//     function allowNumbersAndDecimal(event) {
//         const char = String.fromCharCode(event.which);
//         const currentValue = event.target.value;
        
//         // Allow numbers
//         if (/[0-9]/.test(char)) {
//             return true;
//         }
        
//         // Allow decimal point only if not already present
//         if (char === '.' && !currentValue.includes('.')) {
//             return true;
//         }
        
//         event.preventDefault();
//     }

//     function formatPhoneNumber(event) {
//         let value = event.target.value.replace(/\D/g, '');
        
//         if (value.length > 11) {
//             value = value.substring(0, 11);
//         }
        
//         event.target.value = value;
//     }

//     function checkSerialMatch() {
//         const serial1 = document.getElementById('phone_serial_number').value;
//         const serial2 = document.getElementById('phone_serial_confirmation').value;
//         const matchDiv = document.getElementById('serialMatch');
//         const mismatchDiv = document.getElementById('serialMismatch');
        
//         if (serial1 && serial2) {
//             if (serial1 === serial2) {
//                 matchDiv.style.display = 'block';
//                 mismatchDiv.style.display = 'none';
//                 showFieldValid(document.getElementById('phone_serial_confirmation'));
//             } else {
//                 matchDiv.style.display = 'none';
//                 mismatchDiv.style.display = 'block';
//                 showFieldError(document.getElementById('phone_serial_confirmation'), 'Serial numbers must match');
//             }
//         } else {
//             matchDiv.style.display = 'none';
//             mismatchDiv.style.display = 'none';
//         }
//     }

//     function checkResaleCodeMatch() {
//         const code1 = document.getElementById('resale_code').value;
//         const code2 = document.getElementById('resale_code_confirmation').value;
//         const matchDiv = document.getElementById('resaleCodeMatch');
//         const mismatchDiv = document.getElementById('resaleCodeMismatch');
        
//         if (code1 && code2) {
//             if (code1 === code2) {
//                 matchDiv.classList.remove('d-none');
//                 mismatchDiv.classList.add('d-none');
//                 showFieldValid(document.getElementById('resale_code_confirmation'));
//             } else {
//                 matchDiv.classList.add('d-none');
//                 mismatchDiv.classList.remove('d-none');
//                 showFieldError(document.getElementById('resale_code_confirmation'), 'Resale codes must match');
//             }
//         } else {
//             matchDiv.classList.add('d-none');
//             mismatchDiv.classList.add('d-none');
//         }
//     }

//     function updateAmountPreview() {
//         const amount = parseFloat(document.getElementById('amount').value) || 0;
//         document.getElementById('previewAmount').textContent = '₦' + amount.toLocaleString('en-NG', {minimumFractionDigits: 2});
//     }

//     function updateAddressCounter() {
//         const addressField = document.getElementById('customer_address');
//         const counterSpan = document.getElementById('address_count');
        
//         if (addressField && counterSpan) {
//             addressField.addEventListener('input', function() {
//                 counterSpan.textContent = this.value.length;
//             });
            
//             // Initialize counter
//             counterSpan.textContent = addressField.value.length;
//         }
//     }

//     function showStep(step) {
//     // Hide all sections - use the correct class
//     document.querySelectorAll('.receipt-form-section').forEach(section => {
//         section.style.display = 'none';
//     });
    
//     // Show target section
//     const targetSection = document.querySelector(`[data-section="${step}"]`);
//     if (targetSection) {
//         targetSection.style.display = 'block';
//     }
    
//     // Update step indicators
//     document.querySelectorAll('.step-item').forEach((item, index) => {
//         const stepNumber = index + 1;
//         item.classList.remove('active', 'completed');
        
//         if (stepNumber < step) {
//             item.classList.add('completed');
//         } else if (stepNumber === step) {
//             item.classList.add('active');
//         }
//     });
    
//     currentStep = step;
// }
        
//         // Update step indicators
//         document.querySelectorAll('.step-item').forEach((item, index) => {
//             const stepNumber = index + 1;
//             item.classList.remove('active', 'completed');
            
//             if (stepNumber < step) {
//                 item.classList.add('completed');
//             } else if (stepNumber === step) {
//                 item.classList.add('active');
//             }
//         });
        
//         currentStep = step;
//     }

//     function validateStep(step) {
//         const section = document.querySelector(`[data-section="${step}"]`);
//         const requiredFields = section.querySelectorAll('[required]');
//         let isValid = true;
        
//         requiredFields.forEach(field => {
//             // Force validation on step validation
//             if (!validateField(field)) {
//                 isValid = false;
//             }
//         });
        
//         return isValid;
//     }
//     function handleFormSubmit(e) {
//     e.preventDefault(); // Always prevent default submission
    
//     // Check for validation errors first
//     const invalidFields = document.querySelectorAll('.is-invalid');
//     if (invalidFields.length > 0) {
//         handleValidationErrors();
//         showToast('Please fix all validation errors before submitting', 'error');
//         return;
//     }
    
//     // Show loading state
//     const submitBtn = document.getElementById('generateReceiptBtn');
//     const spinner = document.getElementById('generateSpinner');
    
//     // Basic validation - validate all steps
//     let isValid = true;
//     for (let i = 1; i <= totalSteps; i++) {
//         if (!validateStep(i)) {
//             isValid = false;
//             // Show the step with errors
//             showStep(i);
//             break;
//         }
//     }
    
//     if (!isValid) {
//         showToast('Please fix all validation errors before submitting', 'error');
//         return;
//     }
    
//     // Show loading state
//     if (submitBtn) {
//         submitBtn.disabled = true;
//         submitBtn.innerHTML = '<i class="fas fa-receipt me-2"></i>Processing... <span class="spinner-border spinner-border-sm ms-2"></span>';
//     }
//     if (spinner) spinner.classList.remove('d-none');
    
//     // Get customer email for payment
//     const customerEmail = document.getElementById('customer_email').value;
//     const customerPhone = document.getElementById('customer_phone').value;
    
//     if (!customerEmail) {
//         showToast('Customer email is required for payment processing', 'error');
//         resetSubmitButton();
//         return;
//     }
    
//     // Submit form to create receipt
//     const form = document.getElementById('receiptForm');
//     const formData = new FormData(form);
    
//     // Force paystack payment method
//     formData.set('payment_method', 'paystack');
    
//     fetch(form.action, {
//         method: 'POST',
//         body: formData,
//         headers: {
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//         }
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             // Receipt created successfully, now initialize payment
//             initializePayment(data.receipt.id, customerEmail, customerPhone);
//         } else {
//             showToast(data.message || 'Failed to create receipt', 'error');
//             resetSubmitButton();
//         }
//     })
//     .catch(error => {
//         console.error('Error:', error);
//         showToast('Failed to create receipt. Please try again.', 'error');
//         resetSubmitButton();
//     });
// }
    
// //     function handleFormSubmit(e) {
// //     // Check for validation errors first
// //     const invalidFields = document.querySelectorAll('.is-invalid');
// //     if (invalidFields.length > 0) {
// //         e.preventDefault();
// //         handleValidationErrors();
// //         showToast('Please fix all validation errors before submitting', 'error');
// //         return;
// //     }
    
// //     // Show loading state
// //     const submitBtn = document.getElementById('generateReceiptBtn');
// //     const spinner = document.getElementById('generateSpinner');
    
// //     // Basic validation - validate all steps
// //     let isValid = true;
// //     for (let i = 1; i <= totalSteps; i++) {
// //         if (!validateStep(i)) {
// //             isValid = false;
// //             // Show the step with errors
// //             showStep(i);
// //             break;
// //         }
// //     }
    
// //     if (!isValid) {
// //         e.preventDefault();
// //         showToast('Please fix all validation errors before submitting', 'error');
// //         return;
// //     }
    
// //     // Show loading state
// //     if (submitBtn) submitBtn.disabled = true;
// //     if (spinner) spinner.classList.remove('d-none');
// // }

  

//     // Global functions for buttons
// //     window.nextStep = function(step) {
// //     const currentStepNumber = step - 1;
    
// //     if (validateStep(currentStepNumber)) {
// //         showStep(step);
        
// //         // Update final preview when moving to step 4
// //         if (step === 4) {
// //             updateFinalPreview();
// //         }
// //     } else {
// //         const errorFields = document.querySelectorAll('.is-invalid');
// //         if (errorFields.length > 0) {
// //             errorFields[0].focus();
// //             showToast('Please fix the highlighted errors before proceeding', 'error');
// //         }
// //     }
// // };
//      window.nextStep = function(step) {
//         const currentStepNumber = step - 1;
        
//         if (validateStep(currentStepNumber)) {
//             showStep(step);
//         } else {
//             const errorFields = document.querySelectorAll('.is-invalid');
//             if (errorFields.length > 0) {
//                 errorFields[0].focus();
//                 showToast('Please fix the highlighted errors before proceeding', 'error');
//             }
//         }
//     };

//     window.prevStep = function(step) {
//         showStep(step);
//     };

//     window.toggleResaleCodeVisibility = function() {
//         const field = document.getElementById('resale_code');
//         const icon = document.getElementById('resale-code-eye');
        
//         if (field.type === 'password') {
//             field.type = 'text';
//             icon.classList.remove('fa-eye');
//             icon.classList.add('fa-eye-slash');
//         } else {
//             field.type = 'password';
//             icon.classList.remove('fa-eye-slash');
//             icon.classList.add('fa-eye');
//         }
//     };

//     // Fix for hidden field validation errors
// function handleValidationErrors() {
//     // Check for invalid fields that are not visible
//     const invalidFields = document.querySelectorAll('.is-invalid');
    
//     invalidFields.forEach(field => {
//         const section = field.closest('[data-section]');
//         if (section && section.style.display === 'none') {
//             // Find which step this field belongs to
//             const sectionNumber = parseInt(section.getAttribute('data-section'));
//             if (sectionNumber) {
//                 // Show the step with the error
//                 showStep(sectionNumber);
//                 // Focus on the first invalid field
//                 setTimeout(() => {
//                     field.focus();
//                     field.scrollIntoView({ behavior: 'smooth', block: 'center' });
//                 }, 100);
//                 return false; // Stop after first error
//             }
//         }
//     });
// }

//     // Toast notification function
//     function showToast(message, type) {
//         const toast = document.createElement('div');
//         toast.className = `alert alert-${type === 'error' ? 'danger' : 'success'} position-fixed`;
//         toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
//         toast.innerHTML = `
//             <div class="d-flex align-items-center">
//                 <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
//                 ${message}
//                 <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
//             </div>
//         `;
        
//         document.body.appendChild(toast);
        
//         setTimeout(() => {
//             if (toast.parentNode) {
//                 toast.remove();
//             }
//         }, 5000);
//     }
// });

// // Simplified payment handling - always Paystack
// document.addEventListener('DOMContentLoaded', function() {
//     const itemAmountDisplay = document.getElementById('item-amount-display');
//     const amountInput = document.getElementById('amount');

//     // Update item amount display
//     if (amountInput && itemAmountDisplay) {
//         amountInput.addEventListener('input', function() {
//             const amount = parseFloat(this.value) || 0;
//             itemAmountDisplay.textContent = amount.toFixed(2);
//         });
//     }
// });
// // document.addEventListener('DOMContentLoaded', function() {
// //     const paystackRadio = document.getElementById('paystack_payment');
// //     const offlineRadio = document.getElementById('offline_payment');
// //     const buttonText = document.getElementById('button-text');
// //     const itemAmountDisplay = document.getElementById('item-amount-display');
// //     const amountInput = document.getElementById('amount');

// //     // Update item amount display
// //     if (amountInput) {
// //         amountInput.addEventListener('input', function() {
// //             const amount = parseFloat(this.value) || 0;
// //             itemAmountDisplay.textContent = amount.toFixed(2);
// //         });
// //     }

// //     // Update button text based on payment method
// //     function updateButtonText() {
// //         if (paystackRadio.checked) {
// //             buttonText.textContent = 'Pay & Generate Receipt';
// //         } else {
// //             buttonText.textContent = 'Generate Receipt';
// //         }
// //     }

// //     paystackRadio.addEventListener('change', updateButtonText);
// //     offlineRadio.addEventListener('change', updateButtonText);
    
// //     updateButtonText(); // Initial call
// // });

// // Payment initialization function
// function initializePayment(receiptId, email, phone) {
//     fetch(`/payment/${receiptId}/initialize`, {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
//         },
//         body: JSON.stringify({
//             email: email,
//             phone: phone
//         })
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success && data.data.authorization_url) {
//             // Show success message briefly then redirect to payment
//             showToast('Receipt generated successfully! Redirecting to payment...', 'success');
            
//             setTimeout(() => {
//                 window.location.href = data.data.authorization_url;
//             }, 1500);
//         } else {
//             showToast('Payment initialization failed. Please try again.', 'error');
//             resetSubmitButton();
//         }
//     })
//     .catch(error => {
//         console.error('Payment Error:', error);
//         showToast('Payment initialization failed. Please try again.', 'error');
//         resetSubmitButton();
//     });
// }

// // Reset submit button function
// function resetSubmitButton() {
//     const submitBtn = document.getElementById('generateReceiptBtn');
//     const spinner = document.getElementById('generateSpinner');
    
//     if (submitBtn) {
//         submitBtn.disabled = false;
//         submitBtn.innerHTML = '<i class="fas fa-receipt me-2"></i>Generate Receipt & Pay';
//     }
//     if (spinner) spinner.classList.add('d-none');
// }

// // Update final preview function
// function updateFinalPreview() {
//     const finalCustomerName = document.getElementById('final-customer-name');
//     const finalPhoneModel = document.getElementById('final-phone-model');
//     const finalPhoneColor = document.getElementById('final-phone-color');
//     const finalAmount = document.getElementById('final-amount');
    
//     if (finalCustomerName) finalCustomerName.textContent = document.getElementById('customer_name').value || '-';
//     if (finalPhoneModel) finalPhoneModel.textContent = document.getElementById('phone_name').value || '-';
//     if (finalPhoneColor) finalPhoneColor.textContent = document.getElementById('phone_color').value || '-';
//     if (finalAmount) finalAmount.textContent = formatCurrency(document.getElementById('amount').value || 0);
// }

// // Format currency function
// function formatCurrency(amount) {
//     const num = parseFloat(amount) || 0;
//     return '₦' + num.toLocaleString('en-NG', {minimumFractionDigits: 2});
// }

</script>
@endpush
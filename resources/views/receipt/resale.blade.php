@extends('layouts.app')

@section('title', 'Phone Resale')
@section('page-title', 'Phone Resale')
@section('page-description', 'Transfer ownership of your phone')

@push('styles')
<style>
.resale-container {
    max-width: 800px;
    margin: 0 auto;
}

.resale-card {
    background: white;
    border-radius: 12px;
    padding: 2.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    border: 1px solid #e9ecef;
    min-height: 500px;
    position: relative;
}

/* Multi-step Progress Bar */
.step-progress {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2.5rem;
    position: relative;
}

.step-progress::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
}

.step-progress .progress-bar {
    position: absolute;
    top: 20px;
    left: 0;
    height: 2px;
    background: #0d8abc;
    transition: width 0.3s ease;
    z-index: 2;
}

.step-item {
    position: relative;
    z-index: 3;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #6c757d;
    transition: all 0.3s ease;
}

.step-item.active {
    border-color: #0d8abc;
    color: #0d8abc;
}

.step-item.completed {
    border-color: #28a745;
    background: #28a745;
    color: white;
}

.step-content {
    display: none;
}

.step-content.active {
    display: block !important;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.form-control:focus, .form-select:focus {
    border-color: #0d8abc;
    box-shadow: 0 0 0 0.2rem rgba(13, 138, 188, 0.25);
    outline: none;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.form-control.is-valid {
    border-color: #28a745;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.btn-step {
    background: linear-gradient(135deg, #0d8abc, #4dabf7);
    border: none;
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-step:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(13, 138, 188, 0.3);
    color: white;
}

.btn-step:disabled {
    background: #6c757d;
    transform: none;
    box-shadow: none;
}

.btn-step.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.btn-step.btn-success:hover {
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.step-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

/* Phone Details Popup */
.phone-popup {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1050;
}

.phone-popup.show {
    display: flex !important;
}

.popup-content {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    max-width: 500px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    position: relative;
    animation: popupSlide 0.3s ease;
}

@keyframes popupSlide {
    from { opacity: 0; transform: scale(0.9) translateY(-20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.popup-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6c757d;
    cursor: pointer;
}

.phone-detail-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.phone-detail-row:last-child {
    border-bottom: none;
}

.phone-detail-label {
    font-weight: 600;
    color: #0d8abc;
}

.forgot-code-link {
    display: inline-block;
    margin-top: 0.5rem;
    color: #0d8abc;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.forgot-code-link:hover {
    color: #0a6b94;
    text-decoration: underline;
}

.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.modal-header {
    background: linear-gradient(135deg, #0d8abc, #4dabf7);
    color: white;
    border-radius: 12px 12px 0 0;
}

.security-notice {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.email-sent-success {
    background: #d1e7dd;
    border: 1px solid #badbcc;
    border-radius: 8px;
    padding: 1.5rem;
    text-align: center;
    display: none;
}

.masked-code {
    font-family: 'Courier New', monospace;
    font-size: 1.2rem;
    font-weight: bold;
    color: #0d8abc;
    background: #e8f4f8;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    display: inline-block;
    margin: 0.5rem 0;
}

/* Toast notifications */
.custom-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    padding: 1rem;
    z-index: 1050;
    transform: translateX(400px);
    transition: all 0.3s ease;
}

.custom-toast.show {
    transform: translateX(0);
}

.custom-toast.success {
    border-left: 4px solid #28a745;
}

.custom-toast.error {
    border-left: 4px solid #dc3545;
}

@media (max-width: 768px) {
    .resale-card {
        padding: 1.5rem;
        margin: 0 1rem;
    }
    
    .step-navigation {
        flex-direction: column;
        gap: 1rem;
    }
    
    .step-navigation .btn {
        width: 100%;
    }
    
    .custom-toast {
        left: 10px;
        right: 10px;
        min-width: auto;
        transform: translateY(-100px);
    }
    
    .custom-toast.show {
        transform: translateY(0);
    }
    
    .popup-content {
        width: 95%;
        padding: 1.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="resale-container">
    <div class="resale-card">
        <!-- Multi-step Progress -->
        <div class="step-progress">
            <div class="progress-bar" id="progressBar"></div>
            <div class="step-item active" data-step="1">1</div>
            <div class="step-item" data-step="2">2</div>
            <div class="step-item" data-step="3">3</div>
        </div>
<form method="POST" action="{{ route('receipt.resale.process-payment') }}" id="resaleForm">
        <!-- <form method="POST" action="{{ route('receipt.resale.process') }}" id="resaleForm"> -->
            @csrf
            
            <!-- Step 1: Phone Details -->
            <div class="step-content active" data-step="1">
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-primary">Phone Details</h4>
                    <p class="text-muted">Enter your phone information</p>
                </div>

                <div class="row">
    <div class="col-md-6 mb-3">
        <label for="original_serial_number" class="form-label fw-medium">
            Phone Serial Number <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control @error('original_serial_number') is-invalid @enderror" 
               id="original_serial_number" 
               name="original_serial_number" 
               value="{{ old('original_serial_number') }}" 
               placeholder="Enter phone serial number"
               required>
        @error('original_serial_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="resale_code" class="form-label fw-medium">
            Resale Code <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control @error('resale_code') is-invalid @enderror" 
               id="resale_code" 
               name="resale_code" 
               value="{{ old('resale_code') }}" 
               placeholder="Enter resale code"
               required>
        @error('resale_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Forgot resale code link - Now positioned below both fields -->
<div class="row">
    <div class="col-12 text-center mb-3">
        <a href="#" class="forgot-code-link" onclick="openForgotCodeModal()">
            <i class="fas fa-key me-1"></i>Forgot your resale code?
        </a>
        <br>
        <small class="text-muted">Click here if you can't remember your resale code</small>
    </div>
</div>

                <div class="step-navigation">
                    <div></div>
                    <button type="button" class="btn btn-step" onclick="verifyAndProceed()">
                        Proceed <i class="fas fa-arrow-right ms-2"></i>
                        <span class="spinner-border spinner-border-sm ms-2 d-none" id="verifySpinner"></span>
                    </button>
                </div>
            </div>

            <!-- Step 2: Customer Details -->
            <div class="step-content" data-step="2">
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-primary">New Customer</h4>
                    <p class="text-muted">Enter customer information</p>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="new_customer_name" class="form-label fw-medium">
                            Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('new_customer_name') is-invalid @enderror" 
                               id="new_customer_name" 
                               name="new_customer_name" 
                               value="{{ old('new_customer_name') }}" 
                               placeholder="Enter customer name"
                               required>
                        @error('new_customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="new_customer_phone" class="form-label fw-medium">
                            Phone <span class="text-danger">*</span>
                        </label>
                        <input type="tel" 
                               class="form-control @error('new_customer_phone') is-invalid @enderror" 
                               id="new_customer_phone" 
                               name="new_customer_phone" 
                               value="{{ old('new_customer_phone') }}" 
                               placeholder="Enter phone number"
                               required>
                        @error('new_customer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="new_customer_email" class="form-label fw-medium">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               class="form-control @error('new_customer_email') is-invalid @enderror" 
                               id="new_customer_email" 
                               name="new_customer_email" 
                               value="{{ old('new_customer_email') }}" 
                               placeholder="Enter email address"
                               required>
                        @error('new_customer_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="new_amount" class="form-label fw-medium">
                            Sale Amount (₦) <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control @error('new_amount') is-invalid @enderror" 
                               id="new_amount" 
                               name="new_amount" 
                               value="{{ old('new_amount') }}" 
                               placeholder="Enter sale amount"
                               min="1"
                               step="0.01"
                               required>
                        @error('new_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="payment_status" class="form-label fw-medium">
                            Payment Status <span class="text-danger">*</span>
                        </label>
                        <select class="form-control @error('payment_status') is-invalid @enderror" 
                                id="payment_status" 
                                name="payment_status" 
                                required>
                            <option value="">Select payment status</option>
                            <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid in Full</option>
                            <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Payment Pending</option>
                            <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>Partially Paid</option>
                        </select>
                        @error('payment_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
<div class="col-12">
    <hr class="my-4">
    <h6 class="fw-bold text-primary mb-3">
        <i class="fas fa-key me-2"></i>Create New Resale Code
    </h6>
    <p class="text-muted small mb-3">
        This code will allow the new customer to resell this phone in the future. Make it secure and memorable.
    </p>
</div>

<div class="col-md-6 mb-3">
    <label for="new_resale_code" class="form-label fw-medium">
        New Resale Code <span class="text-danger">*</span>
    </label>
    <div class="input-group">
        <input type="password" 
               class="form-control @error('new_resale_code') is-invalid @enderror" 
               id="new_resale_code" 
               name="new_resale_code" 
               value="{{ old('new_resale_code') }}" 
               placeholder="Create secure resale code"
               required
               minlength="4"
               maxlength="20"
              pattern="^[A-Za-z0-9@#$%^&*()_+\-=\[\]{}|;':&quot;,.\/<>?]{4,20}$" 
  title="4-20 characters: letters, numbers, and common symbols only" 
  data-validation="new_resale_code">
        <button type="button" class="btn btn-outline-secondary" onclick="toggleNewResaleCodeVisibility()">
            <i class="fas fa-eye" id="new-resale-code-eye"></i>
        </button>
        <div class="invalid-feedback" id="new_resale_code_error">
            Resale code must be 4-20 characters (letters, numbers, symbols)
        </div>
    </div>
    <small class="text-muted">
        <span id="new_resale_code_strength" class="strength-indicator">Weak</span> - 
        Keep this code secure for future resales
    </small>
    @error('new_resale_code')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-6 mb-3">
    <label for="new_resale_code_confirmation" class="form-label fw-medium">
        Confirm New Resale Code <span class="text-danger">*</span>
    </label>
    <input type="password" 
           class="form-control @error('new_resale_code_confirmation') is-invalid @enderror" 
           id="new_resale_code_confirmation" 
           name="new_resale_code_confirmation" 
           value="{{ old('new_resale_code_confirmation') }}" 
           required
           data-validation="new_resale_code_confirm">
    <div class="invalid-feedback" id="new_resale_code_confirmation_error">
        Resale codes must match exactly
    </div>
    @error('new_resale_code_confirmation')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <div id="newResaleCodeMatch" class="text-success small d-none mt-1">
        <i class="fas fa-check me-1"></i>New resale codes match
    </div>
    <div id="newResaleCodeMismatch" class="text-danger small d-none mt-1">
        <i class="fas fa-times me-1"></i>New resale codes do not match
    </div>
</div>

                    <div class="col-md-6 mb-3">
                        <label for="new_customer_address" class="form-label fw-medium">
                            Address <small class="text-muted">(Optional)</small>
                        </label>
                        <textarea class="form-control @error('new_customer_address') is-invalid @enderror" 
                                  id="new_customer_address" 
                                  name="new_customer_address" 
                                  rows="1" 
                                  placeholder="Enter customer address">{{ old('new_customer_address') }}</textarea>
                        @error('new_customer_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

<!-- <div class="col-md-6 mb-3">
    <label for="new_resale_code_confirmation" class="form-label fw-medium">
        Confirm New Resale Code <span class="text-danger">*</span>
    </label>
    <input type="password" 
           class="form-control @error('new_resale_code_confirmation') is-invalid @enderror" 
           id="new_resale_code_confirmation" 
           name="new_resale_code_confirmation" 
           value="{{ old('new_resale_code_confirmation') }}" 
           required
           data-validation="new_resale_code_confirm">
    <div class="invalid-feedback" id="new_resale_code_confirmation_error">
        Resale codes must match exactly
    </div>
    @error('new_resale_code_confirmation')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <div id="newResaleCodeMatch" class="text-success small d-none mt-1">
        <i class="fas fa-check me-1"></i>New resale codes match
    </div>
    <div id="newResaleCodeMismatch" class="text-danger small d-none mt-1">
        <i class="fas fa-times me-1"></i>New resale codes do not match
    </div>
</div>
                    <div class="col-md-6 mb-3">
                        <label for="new_customer_address" class="form-label fw-medium">
                            Address <small class="text-muted">(Optional)</small>
                        </label>
                        <textarea class="form-control @error('new_customer_address') is-invalid @enderror" 
                                  id="new_customer_address" 
                                  name="new_customer_address" 
                                  rows="1" 
                                  placeholder="Enter customer address">{{ old('new_customer_address') }}</textarea>
                        @error('new_customer_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> 

                </div>
 -->
                <div class="step-navigation">
                    <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </button>
                    <button type="button" class="btn btn-step" onclick="nextStep()">
                        Continue <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Final Details -->
            <div class="step-content" data-step="3">
                <div class="text-center mb-4">
                    <h4 class="fw-bold text-primary">Final Details</h4>
                    <p class="text-muted">Complete the resale</p>
                </div>

               

              <!-- Payment Method Selection for Resale -->
<!--<div class="row mb-4">-->
<!--    <div class="col-12">-->
<!--        <h6 class="fw-bold mb-3">-->
<!--            <i class="fas fa-credit-card me-2"></i>Payment Method-->
<!--        </h6>-->
<!--        <div class="row">-->
<!--            <div class="col-md-6 mb-3">-->
<!--                <div class="payment-option">-->
<!--                    <input type="radio" -->
<!--                           class="btn-check" -->
<!--                           name="payment_method" -->
<!--                           id="paystack_resale" -->
<!--                           value="paystack" -->
<!--                           checked>-->
<!--                    <label class="btn btn-outline-primary w-100 h-100 p-3" for="paystack_resale">-->
<!--                        <i class="fas fa-credit-card fs-3 mb-2 d-block"></i>-->
<!--                        <h6 class="fw-medium mb-1">Online Payment</h6>-->
<!--                        <small class="text-muted">Pay ₦<span id="resale-fee-display">{{ config('services.app.resale_fee', 300) }}</span> via Paystack</small>-->
<!--                        <div class="badge bg-success mt-2">Secure & Instant</div>-->
<!--                    </label>-->
<!--                </div>-->
<!--            </div>-->
            <!--<div class="col-md-6 mb-3">-->
            <!--    <div class="payment-option">-->
            <!--        <input type="radio" -->
            <!--               class="btn-check" -->
            <!--               name="payment_method" -->
            <!--               id="offline_resale" -->
            <!--               value="offline">-->
            <!--        <label class="btn btn-outline-secondary w-100 h-100 p-3" for="offline_resale">-->
            <!--            <i class="fas fa-money-bill-wave fs-3 mb-2 d-block"></i>-->
            <!--            <h6 class="fw-medium mb-1">Offline Payment</h6>-->
            <!--            <small class="text-muted">Cash or Bank Transfer</small>-->
            <!--            <div class="badge bg-warning mt-2">Manual Process</div>-->
            <!--        </label>-->
            <!--    </div>-->
            <!--</div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!-- Resale Payment Summary -->
<!--<div class="alert alert-info mb-4">-->
<!--    <h6 class="alert-heading">-->
<!--        <i class="fas fa-info-circle me-2"></i>Resale Payment Summary-->
<!--    </h6>-->
<!--    <div class="row">-->
<!--        <div class="col-md-6">-->
<!--            <strong>New Sale Amount:</strong> ₦<span id="new-sale-amount">0.00</span>-->
<!--            <small class="text-muted d-block">For your records only</small>-->
<!--        </div>-->
<!--        <div class="col-md-6">-->
<!--            <strong>Resale Processing Fee:</strong> ₦<span id="resale-processing-fee">{{ config('services.app.resale_fee', 300) }}</span>-->
<!--            <small class="text-danger d-block">Amount to be charged</small>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!-- Payment Method (Single Option: Paystack) -->
<div class="row mb-4">
    <div class="col-12">
        <h6 class="fw-bold mb-3">
            <i class="fas fa-credit-card me-2"></i> Payment Method
        </h6>

        <!-- Hidden input since Paystack is the only option -->
        <input type="hidden" name="payment_method" value="paystack">

        <!-- Displayed card (no selection needed) -->
        <div class="card border-0 shadow-sm p-3 bg-light">
            <div class="d-flex align-items-center">
                <div class="me-3 text-primary">
                    <i class="fas fa-credit-card fs-2"></i>
                </div>
                <div>
                    <h6 class="fw-semibold mb-1">Online Payment</h6>
                    <small class="text-muted">
                        Pay ₦<span id="resale-fee-display">{{ config('services.app.resale_fee', 300) }}</span> via Paystack
                    </small>
                    <div class="badge bg-success ms-2">Secure & Instant</div>
                </div>
            </div>
        </div>
    </div>
</div>
 <div class="row">
                    <div class="col-12 mb-3">
                        <label for="notes" class="form-label fw-medium">
                            Notes <small class="text-muted">(Optional)</small>
                        </label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3" 
                                  placeholder="Any additional information about this resale">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
<!-- Resale Payment Summary -->
<!--<div class="alert alert-info mb-4 shadow-sm">-->
<!--    <h6 class="alert-heading mb-3">-->
<!--        <i class="fas fa-info-circle me-2"></i> Resale Payment Summary-->
<!--    </h6>-->
<!--    <div class="row">-->
<!--        <div class="col-md-6 mb-2">-->
<!--            <strong>New Sale Amount:</strong> ₦<span id="new-sale-amount">0.00</span>-->
<!--            <small class="text-muted d-block">For your records only</small>-->
<!--        </div>-->
<!--        <div class="col-md-6 mb-2">-->
<!--            <strong>Resale Processing Fee:</strong> ₦<span id="resale-processing-fee">{{ config('services.app.resale_fee', 300) }}</span>-->
<!--            <small class="text-warning d-block">Amount to be charged</small>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!-- Enhanced Resale Payment Summary -->
<div class="alert alert-primary mb-4 shadow-sm">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h6 class="alert-heading mb-3">
                <i class="fas fa-credit-card me-2"></i> Ready to Process Resale
            </h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <strong>New Sale Amount:</strong> ₦<span id="new-sale-amount">0.00</span>
                    <small class="text-muted d-block">make you collected directly from the customer</small>
                </div>
                <div class="col-md-6 mb-2">
                    <strong>Processing Fee:</strong> ₦<span id="resale-processing-fee">{{ config('services.app.resale_fee', 300) }}</span>
                    <small class="text-primary d-block">Amount to be charged via Paystack</small>
                </div>
            </div>
        </div>
        <div class="text-center">
            <i class="fab fa-cc-visa fs-2 text-primary"></i>
            <i class="fab fa-cc-mastercard fs-2 text-warning ms-2"></i>
            <div class="badge bg-success mt-2">Secure Payment</div>
        </div>
    </div>
</div>

<!-- Payment Method Selection for Resale
<div class="row mb-4">
    <div class="col-12">
        <h6 class="fw-bold mb-3">
            <i class="fas fa-credit-card me-2"></i>Payment Method
        </h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="payment-option">
                    <input type="radio" 
                           class="btn-check" 
                           name="payment_method" 
                           id="paystack_resale" 
                           value="paystack" 
                           checked>
                    <label class="btn btn-outline-primary w-100 h-100 p-3" for="paystack_resale">
                        <i class="fas fa-credit-card fs-3 mb-2 d-block"></i>
                        <h6 class="fw-medium mb-1">Online Payment</h6>
                        <small class="text-muted">Pay ₦<span id="resale-fee-display">{{ config('services.app.resale_fee', 300) }}</span> via Paystack</small>
                        <div class="badge bg-success mt-2">Secure & Instant</div>
                    </label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="payment-option">
                    <input type="radio" 
                           class="btn-check" 
                           name="payment_method" 
                           id="offline_resale" 
                           value="offline">
                    <label class="btn btn-outline-secondary w-100 h-100 p-3" for="offline_resale">
                        <i class="fas fa-money-bill-wave fs-3 mb-2 d-block"></i>
                        <h6 class="fw-medium mb-1">Offline Payment</h6>
                        <small class="text-muted">Cash or Bank Transfer</small>
                        <div class="badge bg-warning mt-2">Manual Process</div>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
 -->
<!-- Resale Payment Summary -->
<!--<div class="alert alert-info mb-4">-->
<!--    <h6 class="alert-heading">-->
<!--        <i class="fas fa-info-circle me-2"></i>Resale Payment Summary-->
<!--    </h6>-->
<!--    <div class="row">-->
<!--        <div class="col-md-6">-->
<!--            <strong>New Sale Amount:</strong> ₦<span id="new-sale-amount">0.00</span>-->
<!--            <small class="text-muted d-block">For your records only</small>-->
<!--        </div>-->
<!--        <div class="col-md-6">-->
<!--            <strong>Resale Processing Fee:</strong> ₦<span id="resale-processing-fee">{{ config('services.app.resale_fee', 300) }}</span>-->
<!--            <small class="text-danger d-block">Amount to be charged</small>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!--<div class="step-navigation">-->
<!--    <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">-->
<!--        <i class="fas fa-arrow-left me-2"></i>Back-->
<!--    </button>-->
<!--    <button type="submit" class="btn btn-step btn-success" id="submitBtn">-->
<!--        <i class="fas fa-check me-2"></i>-->
<!--        <span id="resale-button-text">Pay & Process Resale</span>-->
<!--        <span class="spinner-border spinner-border-sm ms-2 d-none" id="submitSpinner"></span>-->
<!--    </button>-->
<!--</div>-->
<div class="step-navigation">
    <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">
        <i class="fas fa-arrow-left me-2"></i>Back
    </button>
    <button type="submit" class="btn btn-success btn-lg px-4" id="processResaleBtn">
        <i class="fas fa-sync-alt me-2"></i>Process Resale & Pay ₦{{ config('services.app.resale_fee', 300) }}
        <span class="spinner-border spinner-border-sm ms-2 d-none" id="resaleSpinner"></span>
    </button>
</div>



            </div>
        </form>
    </div>
</div>

<!-- Phone Details Popup -->
<div class="phone-popup" id="phonePopup">
    <div class="popup-content">
        <button class="popup-close" onclick="closePhonePopup()">&times;</button>
        <div class="text-center mb-4">
            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
            <h4 class="fw-bold text-success mt-2">Phone Verified!</h4>
        </div>
        
        <div class="phone-details" id="phoneDetailsPopup">
            <!-- Phone details populated by JavaScript -->
        </div>
        
        <div class="text-center mt-4">
            <button type="button" class="btn btn-step" onclick="continueToCustomerStep()">
                Continue to Customer Details <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>
</div>

<!-- Forgot Resale Code Modal -->
<div class="modal fade" id="forgotCodeModal" tabindex="-1" aria-labelledby="forgotCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotCodeModalLabel">
                    <i class="fas fa-key me-2"></i>Forgot Resale Code
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Security Notice -->
                <div class="security-notice">
                    <h6 class="fw-bold mb-2">
                        <i class="fas fa-shield-alt me-2"></i>Security Notice
                    </h6>
                    <p class="mb-0 small">
                        For security purposes, we'll help you find your resale code using your phone's serial number and email. 
                        You'll receive a hint to help you remember your complete code.
                    </p>
                </div>
                
                <!-- Forgot Code Form -->
                <form id="forgotCodeForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="forgot_serial" class="form-label fw-medium">
                                Phone Serial Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="forgot_serial" 
                                   name="phone_serial_number" 
                                   placeholder="Enter phone serial number"
                                   required>
                            <small class="text-muted">Same as entered above</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="forgot_email" class="form-label fw-medium">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="forgot_email" 
                                   name="customer_email" 
                                   placeholder="Enter email from purchase"
                                   required
                                   >
                            <small class="text-muted">Email used during purchase</small>
                        </div>
                        <!-- phone number -->
                           <div class="col-md-6 mb-3">
                            <label for="forgot_email" class="form-label fw-medium">
                                Phone Number <span class="text-success">optional</span>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="phone_number" 
                                   name="customer_phone number" 
                                   placeholder="Enter phone number from purchase"
                                   Disabled
                                   >
                            <small class="text-muted">phone number used during purchase</small>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-search me-2"></i>Find My Code
                            <span class="spinner-border spinner-border-sm ms-2 d-none" id="forgotCodeSpinner"></span>
                        </button>
                    </div>
                </form>
                
                <!-- Success Message (Hidden by default) -->
                <div class="email-sent-success" id="emailSentSuccess">
                    <i class="fas fa-check-circle text-success fs-1 mb-3"></i>
                    <h5 class="text-success">Found Your Code!</h5>
                    <p class="mb-3">
                        Your resale code hint: <span class="masked-code" id="displayMaskedCode">***</span>
                    </p>
                    <p class="small text-muted">
                        Use this hint to remember your complete resale code, then return to the form above to continue.
                    </p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="fas fa-arrow-left me-2"></i>Back to Resale Form
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    let originalReceiptData = null;
    
    // Setup event listeners
    setupEventListeners();
    
    // Initialize progress bar
    updateProgressBar();
    
    function setupEventListeners() {
        // Form submission
        document.getElementById('resaleForm').addEventListener('submit', handleFormSubmit);
        
        // Real-time validation
        setupRealTimeValidation();
        
        // Phone number formatting
        document.getElementById('new_customer_phone').addEventListener('input', formatPhoneNumber);
    const newResaleCode = document.getElementById('new_resale_code');
    const newResaleCodeConfirm = document.getElementById('new_resale_code_confirmation');
        // New resale code confirmation
document.getElementById('new_resale_code_confirmation').addEventListener('input', checkNewResaleCodeMatch);
document.getElementById('new_resale_code').addEventListener('input', checkNewResaleCodeMatch);

// New resale code strength
document.getElementById('new_resale_code').addEventListener('input', updateNewResaleCodeStrength);
   
    }
    
    function setupRealTimeValidation() {
        const inputs = document.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearValidationError(this);
                validateField(this);
            });
            
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    }
    
    function clearValidationError(input) {
        input.classList.remove('is-invalid', 'is-valid');
        const feedback = input.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.style.display = 'none';
        }
    }
    
  function validateField(input) {
    let isValid = true;
    const value = input.value ? input.value.trim() : '';
    
    // Clear previous validation state
    input.classList.remove('is-invalid', 'is-valid');
    
    if (input.hasAttribute('required') && !value) {
        isValid = false;
        showFieldError(input, 'This field is required');
    } else if (input.type === 'email' && value && !isValidEmail(value)) {
        isValid = false;
        showFieldError(input, 'Please enter a valid email address');
    } else if (input.type === 'tel' && value && !isValidPhone(value)) {
        isValid = false;
        showFieldError(input, 'Please enter a valid phone number');
    } else if (input.id === 'new_resale_code' && value) {
        // Validate new resale code strength
        if (value.length < 4) {
            isValid = false;
            showFieldError(input, 'Resale code must be at least 4 characters');
        } else if (value.length > 20) {
            isValid = false;
            showFieldError(input, 'Resale code must not exceed 20 characters');
        } else if (!/^[A-Za-z0-9@#$%^&*()_+\-=\[\]{}|;':",./<>?]+$/.test(value)) {
            isValid = false;
            showFieldError(input, 'Resale code contains invalid characters');
        } else {
            // Valid resale code
            isValid = true;
        }
    } else if (input.id === 'new_resale_code_confirmation' && value) {
        // Validate resale code confirmation match
        const originalCode = document.getElementById('new_resale_code');
        if (originalCode && value !== originalCode.value) {
            isValid = false;
            showFieldError(input, 'Resale codes do not match');
        } else {
            isValid = true;
        }
    } else if (input.id === 'new_amount' && value) {
        // Validate amount
        const amount = parseFloat(value);
        if (isNaN(amount) || amount <= 0) {
            isValid = false;
            showFieldError(input, 'Please enter a valid amount greater than 0');
        } else {
            isValid = true;
        }
    } else if (value || input.hasAttribute('required')) {
        // Field has value or is required - mark as valid
        isValid = true;
    }
    
    // Apply visual feedback
    if (isValid && value) {
        input.classList.add('is-valid');
        // Clear any error messages
        const errorDiv = input.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    } else if (!isValid) {
        input.classList.add('is-invalid');
    }
    
    return isValid;
}
    
    function showFieldError(input, message) {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        
        let feedback = input.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.classList.add('invalid-feedback');
            input.parentNode.insertBefore(feedback, input.nextSibling);
        }
        feedback.textContent = message;
        feedback.style.display = 'block';
    }
    
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    function isValidPhone(phone) {
        const cleaned = phone.replace(/\D/g, '');
        return cleaned.length >= 10 && cleaned.length <= 15;
    }
    
    function formatPhoneNumber(event) {
        let value = event.target.value.replace(/\D/g, '');
        
        if (value.length > 0) {
            if (value.length <= 11) {
                if (value.startsWith('0')) {
                    value = value.replace(/(\d{4})(\d{3})(\d{4})/, '$1 $2 $3');
                } else {
                    value = value.replace(/(\d{3})(\d{3})(\d{4})/, '+234 $1 $2 $3');
                }
            }
        }
        
        event.target.value = value;
    }
    
    function showStep(step) {
        console.log('Showing step:', step); // Debug log
        
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
            content.style.display = 'none'; // Force hide
        });
        
        // Show target step
        const targetStep = document.querySelector(`.step-content[data-step="${step}"]`);
        if (targetStep) {
            targetStep.classList.add('active');
            targetStep.style.display = 'block'; // Force show
            console.log('Step', step, 'is now visible'); // Debug log
        } else {
            console.error('Step not found:', step); // Debug log
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
        updateProgressBar();
    }
    
    function updateProgressBar() {
        const progressBar = document.getElementById('progressBar');
        const progress = ((currentStep - 1) / 2) * 100; // 3 steps total, so divide by 2
        progressBar.style.width = progress + '%';
    }
    
    function validateCurrentStep() {
        const currentStepElement = document.querySelector(`.step-content[data-step="${currentStep}"]`);
        const requiredFields = currentStepElement.querySelectorAll('input[required], select[required]');
        let hasErrors = false;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                showFieldError(field, 'This field is required');
                hasErrors = true;
            } else {
                validateField(field);
                if (field.classList.contains('is-invalid')) {
                    hasErrors = true;
                }
            }
        });
        
        return !hasErrors;
    }
    
    function handleFormSubmit(event) {
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('submitSpinner');
    
    // Get all visible required fields only (not from hidden steps)
    const visibleRequiredFields = document.querySelectorAll('.step-content.active input[required], .step-content.active select[required], input[required]:not([style*="display: none"]), select[required]:not([style*="display: none"])');
    
    // Get all required fields that should be filled by step 3
    const allRequiredFields = [
        'original_serial_number',
        'resale_code', 
        'new_customer_name',
        'new_customer_phone',
        'new_customer_email',
        'new_amount',
        'payment_status',
        'new_resale_code',
        'new_resale_code_confirmation'
    ];
    
    let hasErrors = false;
    let firstErrorField = null;
    
    console.log('=== FORM VALIDATION DEBUG ===');
    
    // Check each required field by ID
    allRequiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (!field) {
            console.log(`Field not found: ${fieldId}`);
            return;
        }
        
        const value = field.value ? field.value.trim() : '';
        console.log(`Field ${fieldId}: "${value}" (length: ${value.length})`);
        
        // Skip validation for fields in step 1 as they should already be validated
        if (fieldId === 'original_serial_number' || fieldId === 'resale_code') {
            if (!value) {
                console.log(`Step 1 field ${fieldId} is empty - this shouldn't happen`);
                hasErrors = true;
                if (!firstErrorField) firstErrorField = field;
            }
            return;
        }
        
        // Validate field
        let fieldValid = true;
        
        if (!value) {
            fieldValid = false;
            showFieldError(field, 'This field is required');
            console.log(`${fieldId} is empty`);
        } else {
            // Additional validations
            if (fieldId === 'new_customer_email' && !isValidEmail(value)) {
                fieldValid = false;
                showFieldError(field, 'Please enter a valid email address');
                console.log(`${fieldId} has invalid email format`);
            } else if (fieldId === 'new_customer_phone' && !isValidPhone(value)) {
                fieldValid = false;
                showFieldError(field, 'Please enter a valid phone number');
                console.log(`${fieldId} has invalid phone format`);
            } else if (fieldId === 'new_amount') {
                const amount = parseFloat(value);
                if (isNaN(amount) || amount <= 0) {
                    fieldValid = false;
                    showFieldError(field, 'Please enter a valid amount greater than 0');
                    console.log(`${fieldId} has invalid amount: ${amount}`);
                }
            } else if (fieldId === 'new_resale_code') {
                if (value.length < 4) {
                    fieldValid = false;
                    showFieldError(field, 'Resale code must be at least 4 characters');
                    console.log(`${fieldId} is too short: ${value.length} characters`);
                } else if (!/^[A-Za-z0-9@#$%^&*()_+\-=\[\]{}|;':",./<>?]+$/.test(value)) {
                    fieldValid = false;
                    showFieldError(field, 'Resale code contains invalid characters');
                    console.log(`${fieldId} has invalid characters`);
                }
            } else if (fieldId === 'new_resale_code_confirmation') {
                const originalCode = document.getElementById('new_resale_code').value;
                if (value !== originalCode) {
                    fieldValid = false;
                    showFieldError(field, 'Resale codes do not match');
                    console.log(`${fieldId} doesn't match original code`);
                }
            }
            
            if (fieldValid) {
                // Clear any previous errors
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                const errorDiv = field.parentNode.querySelector('.invalid-feedback');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            }
        }
        
        if (!fieldValid) {
            hasErrors = true;
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
            if (!firstErrorField) {
                firstErrorField = field;
            }
        }
    });
    
    console.log(`Total errors: ${hasErrors ? 'YES' : 'NO'}`);
    console.log('=== END VALIDATION DEBUG ===');
    
    if (hasErrors) {
        event.preventDefault();
        showToast('Please fill all required fields correctly', 'error');
        
        // Scroll to first error field
        if (firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Find which step the error field is in and show that step
            const errorStep = firstErrorField.closest('.step-content');
            if (errorStep) {
                const stepNumber = parseInt(errorStep.getAttribute('data-step'));
                if (stepNumber !== currentStep) {
                    showStep(stepNumber);
                }
            }
            
            setTimeout(() => {
                firstErrorField.focus();
            }, 500);
        }
        return;
    }
    
    // All validation passed
    console.log('Form validation passed - submitting');
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating Receipt...';
    if (spinner) {
        spinner.classList.remove('d-none');
    }
    
    // Allow form submission
    setTimeout(() => {
        // Let the form submit naturally
        console.log('Form submitted successfully');
    }, 500);
}
    
    function showPhonePopup(receipt) {
        const popup = document.getElementById('phonePopup');
        const details = document.getElementById('phoneDetailsPopup');
        
        // Safe property access
        const receiptNumber = receipt.receipt_number || 'N/A';
        const phoneName = receipt.phone_name || 'Unknown Phone';
        const phoneColor = receipt.phone_color || 'Unknown Color';
        const phoneSerial = receipt.phone_serial_number || 'Unknown Serial';
        const customerName = receipt.customer_name || 'Unknown Customer';
        const originalDate = receipt.original_date || 'Unknown Date';
        const amount = receipt.original_amount || receipt.amount || 0;
        const formattedAmount = parseFloat(amount).toLocaleString('en-NG', {minimumFractionDigits: 2});
        
        details.innerHTML = `
            <div class="phone-detail-row">
                <span class="phone-detail-label">Receipt Number:</span>
                <span>${receiptNumber}</span>
            </div>
            <div class="phone-detail-row">
                <span class="phone-detail-label">Phone Model:</span>
                <span>${phoneName}</span>
            </div>
            <div class="phone-detail-row">
                <span class="phone-detail-label">Color:</span>
                <span>${phoneColor}</span>
            </div>
            <div class="phone-detail-row">
                <span class="phone-detail-label">Price:</span>
                <span>₦${formattedAmount}</span>
            </div>
            <div class="phone-detail-row">
                <span class="phone-detail-label">Customer:</span>
                <span>${customerName}</span>
            </div>
            <div class="phone-detail-row">
                <span class="phone-detail-label">Sale Date:</span>
                <span>${originalDate}</span>
            </div>
        `;
        
        popup.classList.add('show');
    }
    
    // Fixed continue function
    window.continueToCustomerStep = function() {
        console.log('Continue to customer step called'); // Debug log
        closePhonePopup();
        setTimeout(() => {
            showStep(2);
        }, 300); // Small delay to ensure popup closes first
    };
    
    window.closePhonePopup = function() {
        console.log('Closing phone popup'); // Debug log
        document.getElementById('phonePopup').classList.remove('show');
    };
    
    window.nextStep = function() {
        if (validateCurrentStep()) {
            if (currentStep < 3) {
                showStep(currentStep + 1);
            }
        } else {
            showToast('Please fill all required fields correctly', 'error');
        }
    };
    
    window.previousStep = function() {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    };
    
    window.openForgotCodeModal = function() {
        const serialNumber = document.getElementById('original_serial_number').value;
        if (serialNumber) {
            document.getElementById('forgot_serial').value = serialNumber;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('forgotCodeModal'));
        modal.show();
    };
    
    // Handle forgot code form submission - FIXED VERSION
// Handle forgot code form submission - CORRECTED VERSION
    const forgotCodeForm = document.getElementById('forgotCodeForm');
    if (forgotCodeForm) {
        forgotCodeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const spinner = document.getElementById('forgotCodeSpinner');
            const formData = new FormData(this); // This is correct here
            
            // Show loading state
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
            
            // Use the correct forgot resale code route
            fetch('{{ route("receipt.forgot-resale-code") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData // Use FormData for the forgot code request
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    document.getElementById('forgotCodeForm').style.display = 'none';
                    const successDiv = document.getElementById('emailSentSuccess');
                    successDiv.style.display = 'block';
                    
                    // Extract masked code from the message or create one
                    const message = data.message || '';
                    const maskedCodeMatch = message.match(/([A-Za-z0-9]\*+[A-Za-z0-9])/);
                    if (maskedCodeMatch) {
                        document.getElementById('displayMaskedCode').textContent = maskedCodeMatch[1];
                    } else {
                        // Create a generic masked code display
                        document.getElementById('displayMaskedCode').textContent = 'T***5';
                    }
                    
                    showToast('Resale code reminder sent to email!', 'success');
                } else {
                    showToast(data.message || 'Failed to send reminder. Please check your details.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to send reminder. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
            });
        });
    }
    
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `custom-toast ${type}`;
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
                <span>${message}</span>
                <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 100);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }
        }, 5000);
    }
});
// Helper validation functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    // Remove any non-digit characters for validation
    const cleanPhone = phone.replace(/\D/g, '');
    // Check if it's between 10-15 digits (international format)
    return cleanPhone.length >= 10 && cleanPhone.length <= 15;
}

function showFieldError(field, message) {
    field.classList.add('is-invalid');
    field.classList.remove('is-valid');
    
    // Find or create error message element
    let errorDiv = field.parentNode.querySelector('.invalid-feedback');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        field.parentNode.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}
function toggleNewResaleCodeVisibility() {
    const input = document.getElementById('new_resale_code');
    const icon = document.getElementById('new-resale-code-eye');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

function checkNewResaleCodeMatch() {
    const code = document.getElementById('new_resale_code').value;
    const confirmation = document.getElementById('new_resale_code_confirmation').value;
    const matchDiv = document.getElementById('newResaleCodeMatch');
    const mismatchDiv = document.getElementById('newResaleCodeMismatch');
    const confirmInput = document.getElementById('new_resale_code_confirmation');
    
    if (confirmation.length === 0) {
        matchDiv.classList.add('d-none');
        mismatchDiv.classList.add('d-none');
        confirmInput.classList.remove('is-valid', 'is-invalid');
        return;
    }
    
    if (code === confirmation && code.length >= 4) {
        matchDiv.classList.remove('d-none');
        mismatchDiv.classList.add('d-none');
        confirmInput.classList.add('is-valid');
        confirmInput.classList.remove('is-invalid');
    } else {
        matchDiv.classList.add('d-none');
        mismatchDiv.classList.remove('d-none');
        confirmInput.classList.add('is-invalid');
        confirmInput.classList.remove('is-valid');
    }
}

function updateNewResaleCodeStrength() {
    const code = document.getElementById('new_resale_code').value;
    const strengthSpan = document.getElementById('new_resale_code_strength');
    const input = document.getElementById('new_resale_code');
    
    let strength = 0;
    let strengthText = 'Weak';
    let strengthClass = 'text-danger';
    
    // Length check
    if (code.length >= 8) strength++;
    if (code.length >= 12) strength++;
    
    // Character variety checks
    if (/[a-z]/.test(code)) strength++;
    if (/[A-Z]/.test(code)) strength++;
    if (/[0-9]/.test(code)) strength++;
    if (/[^A-Za-z0-9]/.test(code)) strength++;
    
    if (strength >= 5) {
        strengthText = 'Very Strong';
        strengthClass = 'text-success';
        input.classList.add('is-valid');
        input.classList.remove('is-invalid');
    } else if (strength >= 4) {
        strengthText = 'Strong';
        strengthClass = 'text-success';
        input.classList.add('is-valid');
        input.classList.remove('is-invalid');
    } else if (strength >= 3) {
        strengthText = 'Medium';
        strengthClass = 'text-warning';
        input.classList.remove('is-valid', 'is-invalid');
    } else if (code.length >= 4) {
        strengthText = 'Weak';
        strengthClass = 'text-warning';
        input.classList.remove('is-valid', 'is-invalid');
    } else {
        strengthText = 'Too Short';
        strengthClass = 'text-danger';
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
    }
    
    strengthSpan.textContent = strengthText;
    strengthSpan.className = `strength-indicator ${strengthClass}`;
}

// backup
// New resale code functions
function toggleNewResaleCodeVisibility() {
    const input = document.getElementById('new_resale_code');
    const icon = document.getElementById('new-resale-code-eye');
    
    if (input && icon) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }
}

function checkNewResaleCodeMatch() {
    const code = document.getElementById('new_resale_code');
    const confirmation = document.getElementById('new_resale_code_confirmation');
    const matchDiv = document.getElementById('newResaleCodeMatch');
    const mismatchDiv = document.getElementById('newResaleCodeMismatch');
    
    if (!code || !confirmation || !matchDiv || !mismatchDiv) return;
    
    if (confirmation.value.length === 0) {
        matchDiv.classList.add('d-none');
        mismatchDiv.classList.add('d-none');
        confirmation.classList.remove('is-valid', 'is-invalid');
        return;
    }
    
    if (code.value === confirmation.value && code.value.length >= 4) {
        matchDiv.classList.remove('d-none');
        mismatchDiv.classList.add('d-none');
        confirmation.classList.add('is-valid');
        confirmation.classList.remove('is-invalid');
    } else {
        matchDiv.classList.add('d-none');
        mismatchDiv.classList.remove('d-none');
        confirmation.classList.add('is-invalid');
        confirmation.classList.remove('is-valid');
    }
}

function updateNewResaleCodeStrength() {
    const code = document.getElementById('new_resale_code');
    const strengthSpan = document.getElementById('new_resale_code_strength');
    
    if (!code || !strengthSpan) return;
    
    let strength = 0;
    let strengthText = 'Weak';
    let strengthClass = 'text-danger';
    
    const value = code.value;
    
    // Length check
    if (value.length >= 8) strength++;
    if (value.length >= 12) strength++;
    
    // Character variety checks
    if (/[a-z]/.test(value)) strength++;
    if (/[A-Z]/.test(value)) strength++;
    if (/[0-9]/.test(value)) strength++;
    if (/[^A-Za-z0-9]/.test(value)) strength++;
    
    if (strength >= 5) {
        strengthText = 'Very Strong';
        strengthClass = 'text-success';
    } else if (strength >= 4) {
        strengthText = 'Strong';
        strengthClass = 'text-success';
    } else if (strength >= 3) {
        strengthText = 'Medium';
        strengthClass = 'text-warning';
    } else if (value.length >= 4) {
        strengthText = 'Weak';
        strengthClass = 'text-warning';
    } else if (value.length > 0) {
        strengthText = 'Too Short';
        strengthClass = 'text-danger';
    }
    
    strengthSpan.textContent = strengthText;
    strengthSpan.className = `strength-indicator ${strengthClass}`;
}

// Make functions globally available
window.toggleNewResaleCodeVisibility = toggleNewResaleCodeVisibility;
window.checkNewResaleCodeMatch = checkNewResaleCodeMatch;
window.updateNewResaleCodeStrength = updateNewResaleCodeStrength;
// Global functions - defined outside DOMContentLoaded to ensure they're available immediately
window.verifyAndProceed = function() {
    const serialNumber = document.getElementById('original_serial_number').value.trim();
    const resaleCode = document.getElementById('resale_code').value.trim().toUpperCase();
    const spinner = document.getElementById('verifySpinner');
    const verifyBtn = document.querySelector('.btn-step');
    
    if (!serialNumber || !resaleCode) {
        showToast('Please enter both phone serial number and resale code', 'error');
        return;
    }
    
    // Show loading state
    if (spinner) {
        spinner.classList.remove('d-none');
    }
    if (verifyBtn) {
        verifyBtn.disabled = true;
        verifyBtn.innerHTML = 'Verifying... <i class="fas fa-spinner fa-spin ms-2"></i>';
    }
    
    // Make AJAX call to verify phone
    fetch('{{ route("receipt.search-resale") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            serial_number: serialNumber,
            resale_code: resaleCode
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Store the original receipt data globally
            window.originalReceiptData = data.receipt;
            showPhonePopup(data.receipt);
            showToast('Phone verified successfully!', 'success');
        } else {
            showToast(data.message || 'Phone not found. Please check your details.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Verification failed. Please try again.', 'error');
    })
    .finally(() => {
        // Reset button state
        if (spinner) {
            spinner.classList.add('d-none');
        }
        if (verifyBtn) {
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = 'Proceed <i class="fas fa-arrow-right ms-2"></i>';
        }
    });
};

window.continueToCustomerStep = function() {
    console.log('Continue to customer step called');
    closePhonePopup();
    setTimeout(() => {
        showStep(2);
    }, 300);
};

window.closePhonePopup = function() {
    console.log('Closing phone popup');
    document.getElementById('phonePopup').classList.remove('show');
};

window.nextStep = function() {
    if (validateCurrentStep()) {
        if (currentStep < 3) {
            showStep(currentStep + 1);
        }
    } else {
        showToast('Please fill all required fields correctly', 'error');
    }
};

window.previousStep = function() {
    if (currentStep > 1) {
        showStep(currentStep - 1);
    }
};

window.openForgotCodeModal = function() {
    const serialNumber = document.getElementById('original_serial_number').value;
    if (serialNumber) {
        document.getElementById('forgot_serial').value = serialNumber;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('forgotCodeModal'));
    modal.show();
};

window.toggleNewResaleCodeVisibility = function() {
    const input = document.getElementById('new_resale_code');
    const icon = document.getElementById('new-resale-code-eye');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
};

// Helper functions that might be called globally
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 100);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }
    }, 5000);
}

function showPhonePopup(receipt) {
    const popup = document.getElementById('phonePopup');
    const details = document.getElementById('phoneDetailsPopup');
    
    // Safe property access
    const receiptNumber = receipt.receipt_number || 'N/A';
    const phoneName = receipt.phone_name || 'Unknown Phone';
    const phoneColor = receipt.phone_color || 'Unknown Color';
    const phoneSerial = receipt.phone_serial_number || 'Unknown Serial';
    const customerName = receipt.customer_name || 'Unknown Customer';
    const originalDate = receipt.original_date || 'Unknown Date';
    const amount = receipt.original_amount || receipt.amount || 0;
    const formattedAmount = parseFloat(amount).toLocaleString('en-NG', {minimumFractionDigits: 2});
    
    details.innerHTML = `
        <div class="phone-detail-row">
            <span class="phone-detail-label">Receipt Number:</span>
            <span>${receiptNumber}</span>
        </div>
        <div class="phone-detail-row">
            <span class="phone-detail-label">Phone Model:</span>
            <span>${phoneName}</span>
        </div>
        <div class="phone-detail-row">
            <span class="phone-detail-label">Color:</span>
            <span>${phoneColor}</span>
        </div>
        <div class="phone-detail-row">
            <span class="phone-detail-label">Price:</span>
            <span>₦${formattedAmount}</span>
        </div>
        <div class="phone-detail-row">
            <span class="phone-detail-label">Customer:</span>
            <span>${customerName}</span>
        </div>
        <div class="phone-detail-row">
            <span class="phone-detail-label">Sale Date:</span>
            <span>${originalDate}</span>
        </div>
    `;
    
    popup.classList.add('show');
}


// Update resale payment display
// document.addEventListener('DOMContentLoaded', function() {
//     const paystackRadio = document.getElementById('paystack_resale');
//     const offlineRadio = document.getElementById('offline_resale');
//     const buttonText = document.getElementById('button-text');
//     const newAmountInput = document.getElementById('new_amount');
//     const newSaleAmountDisplay = document.getElementById('new-sale-amount');

//     // Update new sale amount display
//     if (newAmountInput) {
//         newAmountInput.addEventListener('input', function() {
//             const amount = parseFloat(this.value) || 0;
//             newSaleAmountDisplay.textContent = amount.toFixed(2);
//         });
//     }

//     // Update button text based on payment method
//     function updateButtonText() {
//         if (paystackRadio.checked) {
//             buttonText.textContent = 'Pay & Process Resale';
//         } else {
//             buttonText.textContent = 'Process Resale';
//         }
//     }

//     if (paystackRadio) paystackRadio.addEventListener('change', updateButtonText);
//     if (offlineRadio) offlineRadio.addEventListener('change', updateButtonText);
    
//     updateButtonText(); // Initial call
// });




// Add this script for resale payment method switching
// document.addEventListener('DOMContentLoaded', function() {
//     const paystackRadio = document.getElementById('paystack_resale');
//     const offlineRadio = document.getElementById('offline_resale');
//     const buttonText = document.getElementById('resale-button-text');
//     const newAmountInput = document.getElementById('new_amount');
//     const newSaleAmountDisplay = document.getElementById('new-sale-amount');

//     // Update new sale amount display
//     if (newAmountInput) {
//         newAmountInput.addEventListener('input', function() {
//             const amount = parseFloat(this.value) || 0;
//             newSaleAmountDisplay.textContent = amount.toFixed(2);
//         });
        
//         // Initial update
//         const amount = parseFloat(newAmountInput.value) || 0;
//         newSaleAmountDisplay.textContent = amount.toFixed(2);
//     }

//     // Update button text based on payment method
//     function updateResaleButtonText() {
//         if (paystackRadio && paystackRadio.checked) {
//             buttonText.textContent = 'Pay & Process Resale';
//         } else {
//             buttonText.textContent = 'Process Resale';
//         }
//     }

//     if (paystackRadio) paystackRadio.addEventListener('change', updateResaleButtonText);
//     if (offlineRadio) offlineRadio.addEventListener('change', updateResaleButtonText);
    
//     updateResaleButtonText(); // Initial call
// });
// Enhanced resale form handling
document.addEventListener('DOMContentLoaded', function() {
    const resaleForm = document.getElementById('resaleForm');
    const processBtn = document.getElementById('processResaleBtn');
    const spinner = document.getElementById('resaleSpinner');
    const newAmountInput = document.getElementById('new_amount');
    const newSaleAmountDisplay = document.getElementById('new-sale-amount');

    // Update new sale amount display in real-time
    if (newAmountInput && newSaleAmountDisplay) {
        newAmountInput.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            newSaleAmountDisplay.textContent = amount.toFixed(2);
        });
        
        // Initialize display
        const initialAmount = parseFloat(newAmountInput.value) || 0;
        newSaleAmountDisplay.textContent = initialAmount.toFixed(2);
    }

    // Handle form submission with loading state
    if (resaleForm) {
        resaleForm.addEventListener('submit', function(e) {
            // Show loading state
            if (processBtn) {
                processBtn.disabled = true;
                if (spinner) spinner.classList.remove('d-none');
                processBtn.innerHTML = '<i class="fas fa-sync-alt me-2"></i>Processing... <span class="spinner-border spinner-border-sm ms-2"></span>';
            }
        });
    }

    // Ensure payment method is always set to paystack
    const paymentMethodInput = document.querySelector('input[name="payment_method"]');
    if (paymentMethodInput) {
        paymentMethodInput.value = 'paystack';
    }
});
</script>
@endpush
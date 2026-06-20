@extends('layouts.app')

@section('title', 'System Settings')
@section('page-title', 'System Settings')
@section('page-description', 'Configure payment settings and system preferences')

@section('content')
<div class="row">
    <!-- Payment Settings -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-credit-card me-2"></i>Payment Settings
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.update-payment') }}" id="paymentSettingsForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Receipt Generation Fee -->
                        <div class="col-md-6 mb-3">
                            <label for="receipt_generation_fee" class="form-label fw-medium">
                                Receipt Generation Fee (₦) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('receipt_generation_fee') is-invalid @enderror" 
                                   id="receipt_generation_fee" 
                                   name="receipt_generation_fee" 
                                   value="{{ old('receipt_generation_fee', config('services.app.receipt_generation_fee', 500)) }}"
                                   min="1"
                                   step="0.01"
                                   required>
                            <small class="text-muted">
                                This amount will be charged via Paystack for each receipt generated
                            </small>
                            @error('receipt_generation_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Resale Fee -->
                        <div class="col-md-6 mb-3">
                            <label for="resale_fee" class="form-label fw-medium">
                                Resale Processing Fee (₦) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('resale_fee') is-invalid @enderror" 
                                   id="resale_fee" 
                                   name="resale_fee" 
                                   value="{{ old('resale_fee', config('services.app.resale_fee', 300)) }}"
                                   min="1"
                                   step="0.01"
                                   required>
                            <small class="text-muted">
                                Fee charged for processing phone resales
                            </small>
                            @error('resale_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Paystack Public Key -->
                        <div class="col-md-6 mb-3">
                            <label for="paystack_public_key" class="form-label fw-medium">
                                Paystack Public Key <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('paystack_public_key') is-invalid @enderror" 
                                   id="paystack_public_key" 
                                   name="paystack_public_key" 
                                   value="{{ old('paystack_public_key', config('services.paystack.public_key')) }}" 
                                   placeholder="pk_test_..."
                                   required>
                            @error('paystack_public_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Paystack Secret Key -->
                        <div class="col-md-6 mb-3">
                            <label for="paystack_secret_key" class="form-label fw-medium">
                                Paystack Secret Key <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control @error('paystack_secret_key') is-invalid @enderror" 
                                       id="paystack_secret_key" 
                                       name="paystack_secret_key" 
                                       value="{{ old('paystack_secret_key', config('services.paystack.secret_key')) }}" 
                                       placeholder="sk_test_..."
                                       required>
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleSecretKey()">
                                    <i class="fas fa-eye" id="secret-key-eye"></i>
                                </button>
                            </div>
                            @error('paystack_secret_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
<hr class="my-4">
<h6 class="fw-bold text-secondary mb-3">
    <i class="fas fa-university me-2"></i>Bank Transfer Settings
</h6>

<div class="row">
    <!-- Bank Name -->
    <div class="col-md-6 mb-3">
        <label for="bank_name" class="form-label fw-medium">
            Bank Name <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control @error('bank_name') is-invalid @enderror" 
               id="bank_name" 
               name="bank_name" 
               value="{{ old('bank_name', config('services.bank.name', 'First Bank of Nigeria')) }}" 
               placeholder="e.g., First Bank of Nigeria"
               required>
        @error('bank_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Account Number -->
    <div class="col-md-6 mb-3">
        <label for="bank_account_number" class="form-label fw-medium">
            Account Number <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control @error('bank_account_number') is-invalid @enderror" 
               id="bank_account_number" 
               name="bank_account_number" 
               value="{{ old('bank_account_number', config('services.bank.account_number', '1234567890')) }}" 
               placeholder="e.g., 1234567890"
               pattern="[0-9]{10}"
               maxlength="10"
               required>
        <small class="text-muted">10-digit account number</small>
        @error('bank_account_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <!-- Account Name -->
    <div class="col-md-6 mb-3">
        <label for="bank_account_name" class="form-label fw-medium">
            Account Name <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control @error('bank_account_name') is-invalid @enderror" 
               id="bank_account_name" 
               name="bank_account_name" 
               value="{{ old('bank_account_name', config('services.bank.account_name', 'M-RIGHT DIGITAL SERVICES')) }}" 
               placeholder="e.g., M-RIGHT DIGITAL SERVICES"
               style="text-transform: uppercase;"
               required>
        <small class="text-muted">Account name as it appears on bank statement</small>
        @error('bank_account_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Bank Code (optional) -->
    <div class="col-md-6 mb-3">
        <label for="bank_code" class="form-label fw-medium">
            Bank Code <small class="text-muted">(Optional)</small>
        </label>
        <input type="text" 
               class="form-control @error('bank_code') is-invalid @enderror" 
               id="bank_code" 
               name="bank_code" 
               value="{{ old('bank_code', config('services.bank.code', '011')) }}" 
               placeholder="e.g., 011"
               maxlength="3">
        <small class="text-muted">3-digit bank code for transfers</small>
        @error('bank_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <!-- Transfer Instructions -->
    <div class="col-12 mb-3">
        <label for="bank_instructions" class="form-label fw-medium">
            Transfer Instructions
        </label>
        <textarea class="form-control @error('bank_instructions') is-invalid @enderror" 
                  id="bank_instructions" 
                  name="bank_instructions" 
                  rows="3"
                  placeholder="Instructions for customers making bank transfers">{{ old('bank_instructions', config('services.bank.instructions', 'Please use the payment reference as your transaction narration and send proof of payment to our support team.')) }}</textarea>
        <small class="text-muted">Instructions shown to customers for bank transfers</small>
        @error('bank_instructions')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
                    <!-- Test/Live Mode Toggle -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="paystack_live_mode" 
                                       name="paystack_live_mode" 
                                       value="1"
                                       {{ old('paystack_live_mode', config('services.paystack.live_mode')) ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="paystack_live_mode">
                                    Live Mode (Use live Paystack keys)
                                </label>
                                <small class="text-muted d-block">
                                    ⚠️ Only enable this in production with live Paystack keys
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Changes take effect immediately for new transactions
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Payment Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Current Settings Display -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Current Settings
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="border p-3 rounded text-center">
                            <i class="fas fa-receipt text-primary fs-3 mb-2"></i>
                            <h6 class="fw-medium">Receipt Fee</h6>
                            <span class="text-success fw-bold fs-5">
                               ₦{{ number_format(config('services.app.receipt_generation_fee', 500), 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border p-3 rounded text-center">
                            <i class="fas fa-sync-alt text-info fs-3 mb-2"></i>
                            <h6 class="fw-medium">Resale Fee</h6>
                            <span class="text-success fw-bold fs-5">
                                
                                ₦{{ number_format(config('services.app.resale_fee', 300), 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border p-3 rounded text-center">
                            <i class="fas fa-shield-alt text-warning fs-3 mb-2"></i>
                            <h6 class="fw-medium">Paystack Mode</h6>
                            <span class="badge {{ config('services.paystack.live_mode') ? 'bg-success' : 'bg-warning' }}">
                                {{ config('services.paystack.live_mode') ? 'Live' : 'Test' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border p-3 rounded text-center">
                            <i class="fas fa-key text-secondary fs-3 mb-2"></i>
                            <h6 class="fw-medium">API Keys</h6>
                            <span class="badge bg-{{ config('services.paystack.public_key') ? 'success' : 'danger' }}">
                                {{ config('services.paystack.public_key') ? 'Configured' : 'Missing' }}
                            </span>
                        </div>
                    </div>
                        <div class="col-md-3">
        <div class="border p-3 rounded text-center">
            <i class="fas fa-university text-primary fs-3 mb-2"></i>
            <h6 class="fw-medium">Bank Account</h6>
            <small class="text-muted d-block">{{ config('services.bank.name') }}</small>
            <span class="badge bg-success">{{ config('services.bank.account_number') }}</span>
        </div>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSecretKey() {
    const input = document.getElementById('paystack_secret_key');
    const icon = document.getElementById('secret-key-eye');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection
@extends('layouts.app')

@section('title', 'Request Payout')
@section('page-title', 'Request Commission Payout')
@section('page-description', 'Submit a request to withdraw your commission earnings')

@push('styles')
<style>
    .balance-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .form-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #667eea;
    }
    
    .info-card {
        background: #e3f2fd;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #2196f3;
    }
    
    .amount-display {
        font-size: 2.5rem;
        font-weight: bold;
    }
    
    .amount-input {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
    }
    
    .bank-verification {
        background: #fff3e0;
        border-radius: 8px;
        padding: 1rem;
        border-left: 4px solid #ff9800;
    }
    
    .commission-breakdown {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <!-- Available Balance -->
            <div class="balance-card">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center">
                        <div class="amount-display">₦{{ number_format($availableBalance, 2) }}</div>
                        <div class="h5 mb-0">Available for Payout</div>
                        <small class="opacity-75">Your current commission balance</small>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="h4 mb-1">₦{{ number_format($commissionStats['total_commission_earned'], 2) }}</div>
                        <div class="h6 mb-0">Total Earned</div>
                        <small class="opacity-75">₦{{ number_format($commissionStats['total_paid_out'], 2) }} previously paid</small>
                    </div>
                </div>
            </div>

            <!-- Important Information -->
            <div class="info-card">
                <h5><i class="fas fa-info-circle me-2"></i>Payout Information</h5>
                <ul class="mb-0">
                    <li><strong>Minimum payout amount:</strong> ₦100</li>
                    <li><strong>Maximum payout amount:</strong> Your available balance (₦{{ number_format($availableBalance, 2) }})</li>
                    <li><strong>Processing time:</strong> 1-3 business days after approval</li>
                    <li><strong>Bank transfer fees:</strong> May apply based on your bank</li>
                    <li><strong>Restriction:</strong> Only one pending request allowed at a time</li>
                </ul>
            </div>

            <form method="POST" action="{{ route('shop.payouts.store') }}" id="payoutForm">
                @csrf
                
                <!-- Payout Amount -->
                <div class="form-section">
                    <h5><i class="fas fa-money-check-alt me-2"></i>Payout Amount</h5>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label">Amount to Request *</label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" class="form-control amount-input @error('amount_requested') is-invalid @enderror" 
                                       name="amount_requested" 
                                       value="{{ old('amount_requested') }}" 
                                       min="100" 
                                       max="{{ $availableBalance }}" 
                                       step="0.01" 
                                       id="amountInput"
                                       required>
                            </div>
                            @error('amount_requested')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Enter amount between ₦100 and ₦{{ number_format($availableBalance, 2) }}
                            </small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quick Select</label>
                            <div class="d-grid gap-2">
                                @if($availableBalance >= 1000)
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setAmount(1000)">
                                        ₦1,000
                                    </button>
                                @endif
                                @if($availableBalance >= 5000)
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setAmount(5000)">
                                        ₦5,000
                                    </button>
                                @endif
                                @if($availableBalance >= 10000)
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setAmount(10000)">
                                        ₦10,000
                                    </button>
                                @endif
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="setAmount({{ $availableBalance }})">
                                    All (₦{{ number_format($availableBalance, 0) }})
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reason for Payout -->
                <div class="form-section">
                    <h5><i class="fas fa-comment-alt me-2"></i>Reason for Payout</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Describe why you need this payout *</label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" 
                                  name="reason" 
                                  rows="3" 
                                  maxlength="500" 
                                  required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Be specific about your reason (business expenses, personal needs, etc.)
                        </small>
                    </div>
                </div>

                <!-- Bank Details -->
                <div class="form-section">
                    <h5><i class="fas fa-university me-2"></i>Bank Account Details</h5>
                    
                    <div class="bank-verification mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt text-warning me-2"></i>
                            <div>
                                <strong>Bank Verification Required</strong>
                                <div class="small">Ensure your bank details are accurate to avoid payment delays.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bank Name *</label>
                            <select class="form-select @error('bank_name') is-invalid @enderror" name="bank_name" required>
                                <option value="">Select Your Bank</option>
                                <option value="Access Bank" {{ old('bank_name') === 'Access Bank' ? 'selected' : '' }}>Access Bank</option>
                                <option value="Citibank" {{ old('bank_name') === 'Citibank' ? 'selected' : '' }}>Citibank</option>
                                <option value="Ecobank" {{ old('bank_name') === 'Ecobank' ? 'selected' : '' }}>Ecobank</option>
                                <option value="Fidelity Bank" {{ old('bank_name') === 'Fidelity Bank' ? 'selected' : '' }}>Fidelity Bank</option>
                                <option value="First Bank" {{ old('bank_name') === 'First Bank' ? 'selected' : '' }}>First Bank</option>
                                <option value="First City Monument Bank" {{ old('bank_name') === 'First City Monument Bank' ? 'selected' : '' }}>First City Monument Bank</option>
                                <option value="Guaranty Trust Bank" {{ old('bank_name') === 'Guaranty Trust Bank' ? 'selected' : '' }}>Guaranty Trust Bank</option>
                                <option value="Heritage Bank" {{ old('bank_name') === 'Heritage Bank' ? 'selected' : '' }}>Heritage Bank</option>
                                <option value="Keystone Bank" {{ old('bank_name') === 'Keystone Bank' ? 'selected' : '' }}>Keystone Bank</option>
                                <option value="Polaris Bank" {{ old('bank_name') === 'Polaris Bank' ? 'selected' : '' }}>Polaris Bank</option>
                                <option value="Providus Bank" {{ old('bank_name') === 'Providus Bank' ? 'selected' : '' }}>Providus Bank</option>
                                <option value="Stanbic IBTC Bank" {{ old('bank_name') === 'Stanbic IBTC Bank' ? 'selected' : '' }}>Stanbic IBTC Bank</option>
                                <option value="Standard Chartered" {{ old('bank_name') === 'Standard Chartered' ? 'selected' : '' }}>Standard Chartered</option>
                                <option value="Sterling Bank" {{ old('bank_name') === 'Sterling Bank' ? 'selected' : '' }}>Sterling Bank</option>
                                <option value="Union Bank" {{ old('bank_name') === 'Union Bank' ? 'selected' : '' }}>Union Bank</option>
                                <option value="United Bank for Africa" {{ old('bank_name') === 'United Bank for Africa' ? 'selected' : '' }}>United Bank for Africa</option>
                                <option value="Unity Bank" {{ old('bank_name') === 'Unity Bank' ? 'selected' : '' }}>Unity Bank</option>
                                <option value="Wema Bank" {{ old('bank_name') === 'Wema Bank' ? 'selected' : '' }}>Wema Bank</option>
                                <option value="Zenith Bank" {{ old('bank_name') === 'Zenith Bank' ? 'selected' : '' }}>Zenith Bank</option>
                            </select>
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Number *</label>
                            <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                   name="account_number" 
                                   value="{{ old('account_number') }}" 
                                   maxlength="20" 
                                   pattern="[0-9]+" 
                                   required>
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter your 10-digit account number</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Account Name *</label>
                        <input type="text" class="form-control @error('account_name') is-invalid @enderror" 
                               name="account_name" 
                               value="{{ old('account_name') }}" 
                               maxlength="255" 
                               required>
                        @error('account_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Account name as it appears on your bank statement</small>
                    </div>
                </div>

                <!-- Commission Breakdown -->
                <div class="commission-breakdown">
                    <h5><i class="fas fa-chart-line me-2"></i>Commission Summary</h5>
                    
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="h5 text-primary">₦{{ number_format($commissionStats['total_commission_earned'], 2) }}</div>
                            <small class="text-muted">Total Earned</small>
                        </div>
                        <div class="col-md-3">
                            <div class="h5 text-success">₦{{ number_format($commissionStats['total_paid_out'], 2) }}</div>
                            <small class="text-muted">Previously Paid</small>
                        </div>
                        <div class="col-md-3">
                            <div class="h5 text-warning">₦{{ number_format($availableBalance, 2) }}</div>
                            <small class="text-muted">Available Balance</small>
                        </div>
                        <div class="col-md-3">
                            <div class="h5 text-info" id="remainingBalance">₦{{ number_format($availableBalance, 2) }}</div>
                            <small class="text-muted">After Payout</small>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('shop.payouts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Payouts
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-undo me-2"></i>Reset Form
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Payout Request
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
const availableBalance = {{ $availableBalance }};

function setAmount(amount) {
    document.getElementById('amountInput').value = amount;
    updateRemainingBalance();
}

function updateRemainingBalance() {
    const amountInput = document.getElementById('amountInput');
    const remainingBalance = document.getElementById('remainingBalance');
    const amount = parseFloat(amountInput.value) || 0;
    const remaining = availableBalance - amount;
    
    remainingBalance.textContent = '₦' + remaining.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    // Update color based on remaining amount
    remainingBalance.className = 'h5 text-' + (remaining >= 0 ? 'info' : 'danger');
}

// Update remaining balance when amount changes
document.getElementById('amountInput').addEventListener('input', updateRemainingBalance);

// Form validation
document.getElementById('payoutForm').addEventListener('submit', function(e) {
    const amount = parseFloat(document.getElementById('amountInput').value);
    const submitBtn = document.getElementById('submitBtn');
    
    // Validate amount
    if (amount < 100) {
        e.preventDefault();
        alert('Minimum payout amount is ₦100.');
        return;
    }
    
    if (amount > availableBalance) {
        e.preventDefault();
        alert('Amount exceeds your available balance.');
        return;
    }
    
    // Confirm submission
    if (!confirm(`Are you sure you want to request a payout of ₦${amount.toLocaleString()}?`)) {
        e.preventDefault();
        return;
    }
    
    // Disable submit button to prevent double submission
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
});

// Bank account number validation
document.querySelector('input[name="account_number"]').addEventListener('input', function(e) {
    // Remove any non-digit characters
    this.value = this.value.replace(/\D/g, '');
    
    // Limit to 10 digits for most Nigerian banks
    if (this.value.length > 10) {
        this.value = this.value.substring(0, 10);
    }
});

// Account name validation (remove special characters)
document.querySelector('input[name="account_name"]').addEventListener('input', function(e) {
    // Allow only letters, spaces, and common punctuation
    this.value = this.value.replace(/[^a-zA-Z\s\-\.\']/g, '');
});

// Initialize remaining balance
updateRemainingBalance();
</script>
@endpush
@endsection
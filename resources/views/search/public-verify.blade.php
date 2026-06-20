@extends('layouts.app')

@section('title', 'Public Receipt Verification')
@section('page-title', 'Verify Receipt')
@section('page-description', 'Verify the authenticity of any M-right digital receipt')

@push('styles')
<style>
.verify-hero {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 20px;
    padding: 3rem 2rem;
    color: white;
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
    overflow: hidden;
}

.verify-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.verification-container {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 10;
    margin-top: -50px;
}

.verification-status {
    text-align: center;
    padding: 2rem;
}

.status-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
}

.status-icon.valid {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.status-icon.invalid {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.verification-details {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 2rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 500;
    color: #6c757d;
}

.detail-value {
    font-weight: 600;
    color: #343a40;
}

.security-features {
    background: #e7f3ff;
    border: 1px solid #b3d9ff;
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 2rem;
}

.security-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
}

.security-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #28a745;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 0.8rem;
}

.resale-history {
    margin-top: 2rem;
}

.history-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.history-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.2rem;
}

.history-icon.sale {
    background: #28a745;
    color: white;
}

.history-icon.resale {
    background: #17a2b8;
    color: white;
}

.anti-theft-panel {
    background: linear-gradient(135deg, #ff6b6b, #ffa500);
    color: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 2rem;
    text-align: center;
}

.verification-form {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.form-control-lg {
    padding: 1rem 1.5rem;
    font-size: 1.1rem;
    border-radius: 10px;
}

@media (max-width: 768px) {
    .verify-hero {
        padding: 2rem 1rem;
        margin-bottom: 2rem;
    }
    
    .verification-container {
        margin-top: -30px;
        padding: 1.5rem;
    }
    
    .detail-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>
@endpush

@section('content')
<!-- Verification Hero Section -->
<div class="verify-hero">
    <h1 class="display-5 fw-bold mb-3">Receipt Verification</h1>
    <p class="lead mb-0">Verify the authenticity and ownership of any M-right digital receipt</p>
</div>

<div class="verification-container">
    @if(isset($receipt) && $receipt)
        <!-- Receipt Found - Show Verification Results -->
        <div class="verification-status">
            <div class="status-icon {{ $basic_info['is_valid'] ? 'valid' : 'invalid' }}">
                <i class="fas {{ $basic_info['is_valid'] ? 'fa-check' : 'fa-times' }}"></i>
            </div>
            
            <h3 class="fw-bold mb-2">
                @if($basic_info['is_valid'])
                    <span class="text-success">Receipt Verified</span>
                @else
                    <span class="text-danger">Receipt Invalid</span>
                @endif
            </h3>
            
            <p class="text-muted mb-4">
                @if($basic_info['is_valid'])
                    This receipt is authentic and valid in our system.
                @else
                    This receipt could not be verified or has been cancelled.
                @endif
            </p>
        </div>

        @if($basic_info['is_valid'])
            <!-- Receipt Details -->
            <div class="verification-details">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Receipt Information
                </h5>
                
                <div class="detail-item">
                    <span class="detail-label">Receipt Number</span>
                    <span class="detail-value text-primary">{{ $receipt->receipt_number }}</span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Phone Model</span>
                    <span class="detail-value">{{ $basic_info['phone_name'] }}</span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Shop Name</span>
                    <span class="detail-value">{{ $basic_info['shop_name'] }}</span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Amount Paid</span>
                    <span class="detail-value text-success">₦{{ number_format($basic_info['amount'], 2) }}</span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Date Generated</span>
                    <span class="detail-value">{{ $basic_info['date_generated'] }}</span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Serial Number</span>
                    <span class="detail-value">{{ $receipt->phone_serial_number }}</span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Phone Color</span>
                    <span class="detail-value">{{ $receipt->phone_color }}</span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Customer Name</span>
                    <span class="detail-value">{{ $receipt->customer_name }}</span>
                </div>
            </div>

            <!-- Security Features -->
            <div class="security-features">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-shield-alt me-2 text-primary"></i>
                    Security Features
                </h5>
                
                <div class="security-item">
                    <div class="security-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <strong>Digital Signature:</strong> Receipt digitally signed and verified
                    </div>
                </div>
                
                <div class="security-item">
                    <div class="security-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <strong>Shop Verification:</strong> {{ $receipt->shop && $receipt->shop->approved ? 'Shop is verified and approved' : 'Shop verification pending' }}
                    </div>
                </div>
                
                <div class="security-item">
                    <div class="security-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <strong>Timestamp Valid:</strong> Receipt creation timestamp verified
                    </div>
                </div>
                
                <div class="security-item">
                    <div class="security-icon">
                        <i class="fas {{ $anti_theft_enabled ? 'fa-check' : 'fa-times' }}"></i>
                    </div>
                    <div>
                        <strong>Anti-Theft Protection:</strong> 
                        @if($anti_theft_enabled)
                            <span class="text-success">Enabled</span>
                        @else
                            <span class="text-warning">Not Enabled</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($anti_theft_enabled)
                <!-- Anti-Theft Status -->
                <div class="anti-theft-panel">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-shield-alt me-2"></i>
                        Anti-Theft Protection Active
                    </h5>
                    <p class="mb-3">
                        This phone is protected by M-right's anti-theft system. If reported stolen, 
                        this device can be tracked and identified.
                    </p>
                    <div class="row text-center">
                        <div class="col-4">
                            <i class="fas fa-eye fs-4 mb-2"></i>
                            <small class="d-block">Trackable</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-database fs-4 mb-2"></i>
                            <small class="d-block">Recorded</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-bell fs-4 mb-2"></i>
                            <small class="d-block">Alerts</small>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($resale_history) && count($resale_history) > 1)
                <!-- Resale History -->
                <div class="resale-history">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-history me-2 text-info"></i>
                        Ownership History
                    </h5>
                    
                    @foreach($resale_history as $index => $history)
                        <div class="history-item">
                            <div class="history-icon {{ $history['type'] === 'original_sale' ? 'sale' : 'resale' }}">
                                <i class="fas {{ $history['type'] === 'original_sale' ? 'fa-shopping-cart' : 'fa-sync-alt' }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    {{ $history['type'] === 'original_sale' ? 'Original Sale' : 'Resale' }}
                                    @if($loop->last)
                                        <span class="badge bg-primary ms-2">Current</span>
                                    @endif
                                </h6>
                                <p class="mb-1"><strong>Receipt:</strong> {{ $history['receipt_number'] }}</p>
                                <p class="mb-1"><strong>Shop:</strong> {{ $history['shop'] }}</p>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($history['date'])->format('M j, Y') }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button class="btn btn-primary btn-lg" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Verification
                    </button>
                    <a href="{{ route('search.index') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-search me-2"></i>Search Another Receipt
                    </a>
                </div>
            </div>
        @endif

    @elseif(isset($error))
        <!-- Error State -->
        <div class="verification-status">
            <div class="status-icon invalid">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h3 class="fw-bold mb-2 text-danger">Verification Failed</h3>
            <p class="text-muted mb-4">{{ $error }}</p>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="{{ route('search.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-search me-2"></i>Try Another Search
                </a>
                <a href="{{ route('help.customer') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-question-circle me-2"></i>Get Help
                </a>
            </div>
        </div>

    @else
        <!-- Default Verification Form -->
        <div class="verification-form">
            <div class="text-center mb-4">
                <i class="fas fa-shield-alt text-success mb-3" style="font-size: 3rem;"></i>
                <h4 class="fw-bold mb-2">Verify Receipt Authenticity</h4>
                <p class="text-muted">Enter a receipt number to verify its authenticity and view details</p>
            </div>

            <form action="{{ route('public.verify', '') }}" method="GET" id="verificationForm">
                <div class="mb-4">
                    <label for="receiptNumber" class="form-label fw-medium">Receipt Number</label>
                    <input type="text" 
                           class="form-control form-control-lg" 
                           id="receiptNumber" 
                           name="receipt_number"
                           placeholder="Enter receipt number (e.g., MR-TEC20250731001)"
                           required>
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Receipt numbers follow the format: MR-XXX20YYYYMMDD###
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-shield-alt me-2"></i>Verify Receipt
                        <span class="spinner-border spinner-border-sm ms-2 d-none" id="verifySpinner"></span>
                    </button>
                </div>
            </form>

            <!-- Help Section -->
            <div class="mt-4 text-center">
                <h6 class="fw-bold mb-3">How to Find Your Receipt Number</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded-3 border">
                            <i class="fas fa-receipt text-primary fs-3 mb-2"></i>
                            <h6 class="fw-medium">Physical Receipt</h6>
                            <small class="text-muted">Check the top of your printed receipt</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded-3 border">
                            <i class="fas fa-envelope text-info fs-3 mb-2"></i>
                            <h6 class="fw-medium">Email Receipt</h6>
                            <small class="text-muted">Look in your email for the digital receipt</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded-3 border">
                            <i class="fas fa-sms text-success fs-3 mb-2"></i>
                            <h6 class="fw-medium">SMS Receipt</h6>
                            <small class="text-muted">Check your text messages</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Additional Information -->
<div class="row mt-5">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-shield-check me-2 text-success"></i>
                    Why Verify Receipts?
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Confirm phone authenticity
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Verify ownership records
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Check anti-theft status
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success me-2"></i>
                        Track resale history
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-question-circle me-2 text-info"></i>
                    Need Help?
                </h6>
            </div>
            <div class="card-body">
                <p class="card-text mb-3">
                    Can't find your receipt number or having verification issues?
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('help.customer') }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-book me-1"></i>Help Guide
                    </a>
                    <a href="{{ route('help.contact') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-envelope me-1"></i>Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('verificationForm');
    const spinner = document.getElementById('verifySpinner');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const receiptNumber = document.getElementById('receiptNumber').value.trim();
            
            if (!receiptNumber) {
                e.preventDefault();
                alert('Please enter a receipt number.');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
            
            // Update form action with receipt number
            this.action = `{{ route('public.verify', '') }}/${encodeURIComponent(receiptNumber)}`;
        });
    }
    
    // Format receipt number input
    const receiptInput = document.getElementById('receiptNumber');
    if (receiptInput) {
        receiptInput.addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase();
            
            // Remove any characters that aren't letters, numbers, or hyphens
            value = value.replace(/[^A-Z0-9-]/g, '');
            
            e.target.value = value;
        });
        
        receiptInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                this.dispatchEvent(new Event('input'));
            }, 10);
        });
    }
});

// Print verification function
function printVerification() {
    window.print();
}

// Share verification result
function shareVerification() {
    if (navigator.share) {
        navigator.share({
            title: 'M-right Receipt Verification',
            text: 'Receipt has been verified as authentic',
            url: window.location.href
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('Verification link copied to clipboard', 'success');
        });
    }
}
</script>
@endpush
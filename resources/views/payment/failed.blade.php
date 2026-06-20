@extends('layouts.app')

@section('title', 'Payment Failed')
@section('page-title', 'Payment Failed')
@section('page-description', 'There was an issue processing your payment')

@push('styles')
<style>
.failed-container {
    max-width: 600px;
    margin: 2rem auto;
    text-align: center;
}

.failed-card {
    background: white;
    border-radius: 20px;
    padding: 3rem 2rem;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.failed-card::before {
    content: '';
    position: absolute;
    top: -50px;
    left: -50px;
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #dc3545, #e83e8c);
    border-radius: 50%;
    opacity: 0.1;
}

.failed-card::after {
    content: '';
    position: absolute;
    bottom: -30px;
    right: -30px;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #dc3545, #e83e8c);
    border-radius: 50%;
    opacity: 0.05;
}

.failed-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #dc3545, #e83e8c);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    font-size: 3rem;
    color: white;
    position: relative;
    z-index: 2;
}

.payment-details {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 2rem;
    margin: 2rem 0;
    text-align: left;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #dee2e6;
}

.detail-row:last-child {
    border-bottom: none;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.troubleshooting {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 12px;
    padding: 1.5rem;
    margin: 2rem 0;
    text-align: left;
}

.troubleshooting h6 {
    color: #856404;
    margin-bottom: 1rem;
}

.troubleshooting ul {
    margin-bottom: 0;
    color: #856404;
}

.troubleshooting li {
    margin-bottom: 0.5rem;
}

.help-section {
    background: #e1ecf4;
    border: 1px solid #bee5eb;
    border-radius: 12px;
    padding: 1.5rem;
    margin: 2rem 0;
    text-align: left;
}

@media (max-width: 768px) {
    .failed-card {
        padding: 2rem 1.5rem;
        margin: 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
@endpush

@section('content')
<div class="failed-container">
    <div class="failed-card">
        <div class="failed-icon">
            <i class="fas fa-times"></i>
        </div>
        
        <h2 class="fw-bold text-danger mb-3">Payment Failed</h2>
        <p class="text-muted mb-4">
            We're sorry, but there was an issue processing your payment. Don't worry, you haven't been charged.
        </p>
        
        @if($payment)
        <div class="payment-details">
            <h6 class="fw-bold mb-3">Payment Details</h6>
            
            <div class="detail-row">
                <span>Receipt Number:</span>
                <span class="fw-bold">{{ $payment->receipt->receipt_number }}</span>
            </div>
            
            <div class="detail-row">
                <span>Payment Reference:</span>
                <span>{{ $payment->reference }}</span>
            </div>
            
        <div class="detail-row">
    <span>Payment Method:</span>
    <span>{{ ucfirst($payment->payment_method ?? 'Paystack') }}</span>
</div>

<div class="detail-row">
    <span>Attempted Amount:</span>
    <span>₦{{ number_format($payment->amount, 2) }}</span>
</div>

<div class="detail-row">
    <span>Failure Time:</span>
    <span>{{ $payment->failed_at ? $payment->failed_at->format('M d, Y \a\t g:i A') : ($payment->updated_at ? $payment->updated_at->format('M d, Y \a\t g:i A') : 'Just now') }}</span>
</div>

<div class="detail-row">
    <span>Status:</span>
    <span class="badge bg-danger">{{ ucfirst($payment->status ?? 'Failed') }}</span>
</div>

@if($payment->failure_reason)
<div class="detail-row">
    <span>Failure Reason:</span>
    <span class="text-danger">{{ $payment->failure_reason }}</span>
</div>
@endif
        </div>
        @endif
        
        <!-- <div class="action-buttons">
            @if($payment && $payment->canBeRetried())
            <form method="POST" action="{{ route('payment.retry', $payment) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-redo me-2"></i>Try Again
                </button>
            </form>
            @else
            <a href="{{ route('payment.show', $payment->receipt ?? '#') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-redo me-2"></i>Try Again
            </a>
            @endif
            
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-home me-2"></i>Dashboard
            </a>
            
            <a href="{{ route('help.contact') }}" class="btn btn-outline-info btn-lg">
                <i class="fas fa-headset me-2"></i>Get Help
            </a>
        </div> -->
        <div class="action-buttons">
    @if($payment && $payment->status !== 'successful')
    <a href="{{ route('payment.show', $payment->receipt) }}" class="btn btn-primary btn-lg">
        <i class="fas fa-redo me-2"></i>Try Again
    </a>
    @endif
    
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
        <i class="fas fa-home me-2"></i>Dashboard
    </a>
    
    <a href="mailto:support@mright.com.ng?subject=Payment Issue - {{ $payment->reference ?? 'Payment Failed' }}" class="btn btn-outline-info btn-lg">
        <i class="fas fa-headset me-2"></i>Get Help
    </a>
</div>
    </div>
    
    <!-- Troubleshooting Tips -->
    <div class="troubleshooting">
        <h6><i class="fas fa-tools me-2"></i>Common Solutions</h6>
        <ul>
            <li>Check that your card has sufficient funds</li>
            <li>Verify your card details are entered correctly</li>
            <li>Ensure your internet connection is stable</li>
            <li>Try using a different payment method</li>
            <li>Contact your bank if the issue persists</li>
        </ul>
    </div>
    
    <!-- Help Section -->
    <div class="help-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="mb-2"><i class="fas fa-question-circle me-2"></i>Need Additional Help?</h6>
                <p class="mb-0 text-muted">
                    Our support team is available 24/7 to help resolve payment issues.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('help.contact') }}" class="btn btn-info">
                    <i class="fas fa-envelope me-2"></i>Contact Support
                </a>
            </div>
        </div>
    </div>
    
    @if($payment)
    <!-- Receipt Information -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="fas fa-mobile-alt me-2"></i>Receipt Information
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Customer:</strong> {{ $payment->receipt->customer_name }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $payment->receipt->customer_phone }}</p>
                    <p class="mb-0"><strong>Shop:</strong> {{ $payment->receipt->shop->shop_name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Device:</strong> {{ $payment->receipt->phone_name }}</p>
                    <p class="mb-1"><strong>Color:</strong> {{ $payment->receipt->phone_color }}</p>
                    <p class="mb-0"><strong>Serial:</strong> {{ $payment->receipt->phone_serial_number }}</p>
                </div>
            </div>
            
            <div class="alert alert-info mt-3 mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> Your receipt is still valid. You can complete the payment later or use a different payment method.
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus on retry button if available
    const retryButton = document.querySelector('.btn-primary');
    if (retryButton) {
        setTimeout(() => {
            retryButton.focus();
        }, 1000);
    }
    
    // Track payment failure for analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'payment_failed', {
            'payment_method': '{{ $payment->payment_method ?? "unknown" }}',
            'amount': {{ $payment->amount ?? 0 }},
            'currency': '{{ $payment->currency ?? "NGN" }}'
        });
    }
});
</script>
@endpush
@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <!-- Success Icon -->
                    <div class="mb-4">
                        <div class="success-icon mx-auto mb-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="text-success mb-2">Payment Successful!</h2>
                        <p class="text-muted mb-4">Your payment has been processed successfully.</p>
                    </div>

                    <!-- Payment Details -->
                    @if($payment)
                    <div class="payment-details bg-light rounded p-4 mb-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <small class="text-muted d-block">Receipt Number</small>
                                <strong>{{ $payment->receipt->receipt_number ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted d-block">Amount Paid</small>
                                <strong>₦{{ number_format($payment->amount, 2) }}</strong>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted d-block">Payment Reference</small>
                                <strong>{{ $payment->gateway_reference }}</strong>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted d-block">Transaction Date</small>
                                <strong>{{ $payment->paid_at ? $payment->paid_at->format('M d, Y - h:i A') : $payment->created_at->format('M d, Y - h:i A') }}</strong>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Success Message -->
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>What happens next?</strong><br>
                        • A receipt has been generated and sent to customer's email. You also have a copy in your record.<br>
                        • Your payment has been confirmed and recorded<br>
                        • You can download or print your receipt anytime
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                        @if($payment && $payment->receipt)
                        <a href="{{ route('receipt.show', $payment->receipt) }}" class="btn btn-primary">
                            <i class="fas fa-receipt me-2"></i>View Receipt
                        </a>
                        <a href="{{ route('receipt.download', $payment->receipt) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-download me-2"></i>Download Receipt
                        </a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Back to Dashboard
                        </a>
                    </div>

                    <!-- Additional Info -->
                    <div class="mt-4 pt-4 border-top">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Your payment was processed securely through Paystack
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-icon {
    animation: successPulse 2s ease-in-out;
}

@keyframes successPulse {
    0% {
        transform: scale(0.5);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.payment-details {
    border-left: 4px solid #28a745;
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Track successful payment for analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'purchase', {
            'transaction_id': '{{ $payment->gateway_reference ?? "" }}',
            'value': {{ $payment->amount ?? 0 }},
            'currency': '{{ $payment->currency ?? "NGN" }}'
        });
    }

    // Auto-focus on receipt button
    const receiptButton = document.querySelector('.btn-primary');
    if (receiptButton) {
        setTimeout(() => {
            receiptButton.focus();
        }, 1000);
    }

    // Show celebration effect
    if (window.confetti) {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 }
        });
    }
});
</script>
@endpush
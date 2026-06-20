@extends('layouts.app')

@section('title', 'Secure Payment')
@section('page-title', 'Complete Payment')
@section('page-description', 'Secure payment for receipt #' . $receipt->receipt_number)

@push('styles')
<style>
.payment-container {
    max-width: 900px;
    margin: 0 auto;
}

.payment-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.receipt-summary {
    background: linear-gradient(135deg, #0d8abc, #4dabf7);
    color: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.payment-methods {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin: 2rem 0;
}

.payment-method {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.payment-method:hover {
    border-color: #0d8abc;
    box-shadow: 0 4px 15px rgba(13, 138, 188, 0.1);
}

.payment-method.selected {
    border-color: #0d8abc;
    background: linear-gradient(135deg, #0d8abc, #4dabf7);
    color: white;
    transform: translateY(-2px);
}

.payment-method-icon {
    width: 60px;
    height: 60px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: #0d8abc;
    transition: all 0.3s ease;
}

.payment-method.selected .payment-method-icon {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.amount-breakdown {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.breakdown-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #dee2e6;
}

.breakdown-item:last-child {
    border-bottom: none;
    font-weight: bold;
    font-size: 1.1rem;
    color: #0d8abc;
}

.breakdown-item.total {
    background: #e3f2fd;
    margin: 0 -1.5rem -1.5rem;
    padding: 1rem 1.5rem;
    border-radius: 0 0 8px 8px;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-content {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    max-width: 300px;
}

@media (max-width: 768px) {
    .payment-card {
        padding: 1.5rem;
    }
    
    .receipt-summary {
        padding: 1.5rem;
    }
    
    .payment-methods {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="payment-container">
    <!-- Receipt Summary -->
    <div class="receipt-summary">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="fw-bold mb-2">
                    <i class="fas fa-lock me-2"></i>
                    Secure Payment for Receipt #{{ $receipt->receipt_number }}
                </h4>
                <p class="mb-1"><strong>Customer:</strong> {{ $receipt->customer_name }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $receipt->phone_name }} ({{ $receipt->phone_color }})</p>
                <p class="mb-0"><strong>Shop:</strong> {{ $receipt->shop->shop_name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-4 text-md-end">
                <h2 class="fw-bold mb-0">₦{{ number_format($receipt->amount, 2) }}</h2>
                <small>Item Value (For Records)</small>
            </div>
        </div>
    </div>

    <!-- Payment Card -->
    <div class="payment-card">
        <h5 class="fw-bold mb-4 text-center">
            <i class="fas fa-credit-card me-2"></i>Complete Your Payment
        </h5>

        <!-- FIXED: Amount Breakdown - Only Service Fee Charged -->
        <div class="amount-breakdown">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-info-circle me-2"></i>Payment Breakdown
            </h6>
            <div class="breakdown-item">
                <span>Item Amount:</span>
                <span class="text-muted">₦{{ number_format($receipt->amount, 2) }}</span>
            </div>
            <small class="text-muted d-block mb-2">↳ For your records only - NOT charged</small>
            
            <div class="breakdown-item">
                <span>Service Fee:</span>
                <span class="text-warning">₦{{ number_format($receipt->service_fee, 2) }}</span>
            </div>
            <small class="text-warning d-block mb-3">↳ This is what you will pay</small>
            
            <div class="breakdown-item total">
                <span><strong>Total Payment:</strong></span>
                <span class="text-success"><strong>₦{{ number_format($receipt->service_fee, 2) }}</strong></span>
            </div>
        </div>

        <!-- Payment Methods -->
       <!-- Simplified Payment Section - Skip Method Selection -->
        <div class="text-center mb-4">
            <div class="payment-method-icon" style="width: 80px; height: 80px; margin: 0 auto;">
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-visa ms-2"></i>
            </div>
            <h4 class="mt-3 mb-2">Secure Online Payment</h4>
            <p class="text-muted">Pay securely with your debit/credit card via Paystack</p>
            <div class="badge bg-success fs-6 px-3 py-2">256-bit SSL Encrypted</div>
        </div>

        <!-- Auto-submit form -->
        <form id="paymentForm" action="{{ route('payment.initialize', $receipt) }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $receipt->customer_email }}">
            <input type="hidden" name="phone" value="{{ $receipt->customer_phone }}">
            
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5" id="payNowBtn">
                    <i class="fas fa-credit-card me-2"></i>Pay Now - ₦{{ number_format($receipt->service_fee, 2) }}
                    <span class="spinner-border spinner-border-sm ms-2 d-none" id="paymentSpinner"></span>
                </button>
            </div>
            
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Your payment is secured by Paystack
                </small>
            </div>
        </form>
        <!-- Back Button -->
        <div class="text-center mt-4">
            <a href="{{ route('receipt.show', $receipt) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Receipt
            </a>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Processing...</span>
        </div>
        <h6>Processing Payment</h6>
        <p class="text-muted small mb-0">Please wait while we process your payment...</p>
    </div>
</div>

<!-- Bank Transfer Modal -->
<div class="modal fade" id="bankTransferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bank Transfer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bankTransferContent">
                <!-- Bank details will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.paystack.co/v1/inline.js"></script>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    const payBtn = document.getElementById('payNowBtn');
    const spinner = document.getElementById('paymentSpinner');
    
    // Optional: Auto-submit after 3 seconds (uncomment if desired)
    // setTimeout(() => {
    //     if (form) {
    //         payBtn.click();
    //     }
    // }, 3000);
    
    form.addEventListener('submit', function(e) {
        // Show loading state
        payBtn.disabled = true;
        spinner.classList.remove('d-none');
        payBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Processing... <span class="spinner-border spinner-border-sm ms-2"></span>';
    });
});
</script>
<!--<script>-->
<!--document.addEventListener('DOMContentLoaded', function() {-->
<!--    let selectedMethod = null;-->
<!--    const receiptId = {{ $receipt->id }};-->
<!--    const serviceFee = {{ $receipt->service_fee }};-->
<!--    const itemAmount = {{ $receipt->amount }};-->

    <!--// FIX: Get shop owner's email from shop->user->email-->
<!--    const shopOwnerEmail = '{{ $receipt->shop->user->email ?? config("mail.from.address") }}';-->

<!--    window.selectPaymentMethod = function(method) {-->
<!--        document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));-->
<!--        document.querySelector(`[data-method="${method}"]`).classList.add('selected');-->
<!--        selectedMethod = method;-->
<!--        document.getElementById('payButton').disabled = false;-->

<!--        const payButton = document.getElementById('payButton');-->
<!--        if (method === 'paystack') {-->
<!--            payButton.innerHTML = '<i class="fas fa-credit-card me-2"></i>Pay ₦{{ number_format($receipt->service_fee, 2) }}';-->
<!--        } else {-->
<!--            payButton.innerHTML = '<i class="fas fa-university me-2"></i>Get Bank Details';-->
<!--        }-->
<!--    };-->

<!--    window.processPayment = function() {-->
<!--        if (!selectedMethod) {-->
<!--            alert('Please select a payment method');-->
<!--            return;-->
<!--        }-->

<!--        if (!shopOwnerEmail) {-->
<!--            alert('Shop owner email is not configured.');-->
<!--            return;-->
<!--        }-->

<!--        if (selectedMethod === 'paystack') {-->
<!--            initializePaystackPayment(shopOwnerEmail);-->
<!--        } else {-->
<!--            showBankTransferDetails(shopOwnerEmail);-->
<!--        }-->
<!--    };-->

<!--    function initializePaystackPayment(email) {-->
<!--        showLoading();-->

<!--        fetch('{{ route("payment.initialize", $receipt) }}', {-->
<!--            method: 'POST',-->
<!--            headers: {-->
<!--                'Content-Type': 'application/json',-->
<!--                'X-CSRF-TOKEN': '{{ csrf_token() }}'-->
<!--            },-->
<!--            body: JSON.stringify({-->
                <!--email: email, // ✅ shop owner's email-->
<!--                phone: '{{ $receipt->customer_phone }}',-->
<!--                payment_method: 'paystack'-->
<!--            })-->
<!--        })-->
<!--        .then(response => response.json())-->
<!--        .then(data => {-->
<!--            hideLoading();-->
<!--            if (data.success) {-->
<!--                window.location.href = data.data.authorization_url;-->
<!--            } else {-->
<!--                alert('Payment initialization failed: ' + data.message);-->
<!--            }-->
<!--        })-->
<!--        .catch(error => {-->
<!--            hideLoading();-->
<!--            console.error('Payment error:', error);-->
<!--            alert('Payment initialization failed. Please try again.');-->
<!--        });-->
<!--    }-->

<!--    function showBankTransferDetails(email) {-->
<!--        const modal = new bootstrap.Modal(document.getElementById('bankTransferModal'));-->
<!--        const content = document.getElementById('bankTransferContent');-->
        
<!--        content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';-->
<!--        modal.show();-->

<!--        fetch('{{ route("payment.initialize", $receipt) }}', {-->
<!--            method: 'POST',-->
<!--            headers: {-->
<!--                'Content-Type': 'application/json',-->
<!--                'X-CSRF-TOKEN': '{{ csrf_token() }}'-->
<!--            },-->
<!--            body: JSON.stringify({-->
                <!--email: email, // ✅ shop owner's email-->
<!--                phone: '{{ $receipt->customer_phone }}',-->
<!--                payment_method: 'bank_transfer'-->
<!--            })-->
<!--        })-->
<!--        .then(response => response.json())-->
<!--        .then(data => {-->
<!--            if (data.success && data.bank_details) {-->
<!--                content.innerHTML = `-->
<!--                    <div class="bank-details">-->
<!--                        <h6 class="fw-bold mb-3">Transfer Details:</h6>-->
<!--                        <div class="alert alert-info">-->
<!--                            <p><strong>Bank:</strong> ${data.bank_details.bank_name}</p>-->
<!--                            <p><strong>Account Number:</strong> ${data.bank_details.account_number}</p>-->
<!--                            <p><strong>Account Name:</strong> ${data.bank_details.account_name}</p>-->
<!--                            <p><strong>Amount:</strong> ₦${data.bank_details.amount}</p>-->
<!--                            <p><strong>Reference:</strong> ${data.bank_details.reference}</p>-->
<!--                        </div>-->
<!--                        <div class="alert alert-warning">-->
<!--                            <small><i class="fas fa-info-circle me-1"></i>${data.bank_details.instructions}</small>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                `;-->
<!--            } else {-->
<!--                content.innerHTML = '<div class="alert alert-danger">Failed to generate bank details. Please try again.</div>';-->
<!--            }-->
<!--        })-->
<!--        .catch(error => {-->
<!--            content.innerHTML = '<div class="alert alert-danger">Error loading bank details. Please try again.</div>';-->
<!--        });-->
<!--    }-->

<!--    function showLoading() {-->
<!--        document.getElementById('loadingOverlay').style.display = 'flex';-->
<!--    }-->

<!--    function hideLoading() {-->
<!--        document.getElementById('loadingOverlay').style.display = 'none';-->
<!--    }-->
<!--});-->
<!--</script>-->

@endpush

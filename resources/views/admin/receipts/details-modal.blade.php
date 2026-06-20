<div class="receipt-details-modal">
    <!-- Receipt Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="receipt-icon me-3">
                    <i class="fas fa-receipt fa-3x text-primary"></i>
                </div>
                <div>
                    <h4 class="mb-1">Receipt #{{ $receipt->receipt_number }}</h4>
                    <p class="text-muted mb-1">
                        Generated on {{ $receipt->created_at->format('F j, Y \a\t g:i A') }}
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-clock me-1"></i>{{ $receipt->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 text-end">
            @php
                $status = $receipt->payment_gateway_status ?? 'pending';
                $statusColors = [
                    'successful' => 'success',
                    'pending' => 'warning', 
                    'failed' => 'danger',
                    'cancelled' => 'secondary'
                ];
                $statusIcons = [
                    'successful' => 'check-circle',
                    'pending' => 'clock',
                    'failed' => 'times-circle', 
                    'cancelled' => 'ban'
                ];
            @endphp
            
            <div class="mb-3">
                <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }} fs-6 px-3 py-2">
                    <i class="fas fa-{{ $statusIcons[$status] ?? 'question' }} me-2"></i>
                    {{ ucfirst($status) }}
                </span>
            </div>
            
            <div class="text-muted small">
                @if($receipt->transaction_reference)
                    <strong>Ref:</strong> {{ $receipt->transaction_reference }}
                @else
                    No transaction reference
                @endif
            </div>
        </div>
    </div>

    <!-- Receipt Details Tabs -->
    <ul class="nav nav-tabs mb-3" id="receiptDetailsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" 
                    data-bs-target="#basic" type="button" role="tab">
                <i class="fas fa-info-circle me-2"></i>Basic Details
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="customer-tab" data-bs-toggle="tab" 
                    data-bs-target="#customer" type="button" role="tab">
                <i class="fas fa-user me-2"></i>Customer Info
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payment-tab" data-bs-toggle="tab" 
                    data-bs-target="#payment" type="button" role="tab">
                <i class="fas fa-credit-card me-2"></i>Payment Details
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="shop-tab" data-bs-toggle="tab" 
                    data-bs-target="#shop" type="button" role="tab">
                <i class="fas fa-store me-2"></i>Shop Info
            </button>
        </li>
    </ul>

    <div class="tab-content" id="receiptDetailsTabContent">
        <!-- Basic Details -->
        <div class="tab-pane fade show active" id="basic" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Receipt Number:</td>
                            <td>
                                <code class="fs-6">{{ $receipt->receipt_number }}</code>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Amount:</td>
                            <td>
                                <span class="fs-5 fw-bold text-primary">
                                    ₦{{ number_format($receipt->amount ?? 0, 2) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Generated Date:</td>
                            <td>{{ $receipt->created_at->format('F j, Y g:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status:</td>
                            <td>
                                <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                    <i class="fas fa-{{ $statusIcons[$status] ?? 'question' }} me-1"></i>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                        @if($receipt->description)
                        <tr>
                            <td class="fw-bold text-muted">Description:</td>
                            <td>{{ $receipt->description }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-chart-pie me-2"></i>Receipt Analytics
                    </h6>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card bg-light">
                                <div class="card-body text-center p-3">
                                    <h5 class="text-primary mb-1">
                                        {{ $receipt->created_at->diffInDays(now()) }}
                                    </h5>
                                    <small class="text-muted">Days Old</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-light">
                                <div class="card-body text-center p-3">
                                    <h5 class="text-success mb-1">
                                        @if($status === 'successful')
                                            <i class="fas fa-check"></i>
                                        @elseif($status === 'pending')
                                            <i class="fas fa-clock"></i>
                                        @else
                                            <i class="fas fa-times"></i>
                                        @endif
                                    </h5>
                                    <small class="text-muted">Payment Status</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($receipt->notes)
                        <div class="mt-3">
                            <h6 class="fw-bold text-muted mb-2">Notes:</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0 small">{{ $receipt->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="tab-pane fade" id="customer" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-user me-2"></i>Customer Details
                    </h6>
                    
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Name:</td>
                            <td>{{ $receipt->customer_name ?: 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Email:</td>
                            <td>
                                @if($receipt->customer_email)
                                    <a href="mailto:{{ $receipt->customer_email }}" class="text-decoration-none">
                                        {{ $receipt->customer_email }}
                                    </a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Phone:</td>
                            <td>
                                @if($receipt->customer_phone)
                                    <a href="tel:{{ $receipt->customer_phone }}" class="text-decoration-none">
                                        {{ $receipt->customer_phone }}
                                    </a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                        </tr>
                        @if($receipt->customer_address)
                        <tr>
                            <td class="fw-bold text-muted">Address:</td>
                            <td>{{ $receipt->customer_address }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-history me-2"></i>Customer Activity
                    </h6>
                    
                    @if($receipt->user)
                        @php
                            $customerReceipts = $receipt->user->receipts()->count();
                            $customerTotalSpent = $receipt->user->receipts()
                                ->where('payment_gateway_status', 'successful')
                                ->sum('amount');
                        @endphp
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Total Receipts:</span>
                                            <strong class="text-primary">{{ $customerReceipts }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Total Spent:</span>
                                            <strong class="text-success">₦{{ number_format($customerTotalSpent, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Member Since:</span>
                                            <strong class="text-info">{{ $receipt->user->created_at->format('M Y') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash text-muted mb-3" style="font-size: 2rem;"></i>
                            <p class="text-muted">No registered customer account</p>
                            <small class="text-muted">This receipt was generated for a guest customer</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="tab-pane fade" id="payment" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-credit-card me-2"></i>Payment Information
                    </h6>
                    
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Amount:</td>
                            <td>
                                <span class="fs-4 fw-bold text-primary">
                                    ₦{{ number_format($receipt->amount ?? 0, 2) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Payment Method:</td>
                            <td>
                                @if($receipt->payment_method)
                                    <span class="badge bg-info">
                                        {{ ucfirst($receipt->payment_method) }}
                                    </span>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Payment Status:</td>
                            <td>
                                <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }} fs-6">
                                    <i class="fas fa-{{ $statusIcons[$status] ?? 'question' }} me-1"></i>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Transaction Reference:</td>
                            <td>
                                @if($receipt->transaction_reference)
                                    <code>{{ $receipt->transaction_reference }}</code>
                                @else
                                    <span class="text-muted">Not available</span>
                                @endif
                            </td>
                        </tr>
                        @if($receipt->payment_gateway_response)
                        <tr>
                            <td class="fw-bold text-muted">Gateway Response:</td>
                            <td>
                                <small class="text-muted">{{ $receipt->payment_gateway_response }}</small>
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-chart-line me-2"></i>Payment Timeline
                    </h6>
                    
                    <div class="payment-timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Receipt Generated</h6>
                                <p class="mb-0 text-muted small">
                                    {{ $receipt->created_at->format('M j, Y g:i A') }}
                                </p>
                            </div>
                        </div>
                        
                        @if($receipt->payment_initiated_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Payment Initiated</h6>
                                <p class="mb-0 text-muted small">
                                    {{ \Carbon\Carbon::parse($receipt->payment_initiated_at)->format('M j, Y g:i A') }}
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        @if($status === 'successful' && $receipt->payment_completed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Payment Completed</h6>
                                <p class="mb-0 text-muted small">
                                    {{ \Carbon\Carbon::parse($receipt->payment_completed_at)->format('M j, Y g:i A') }}
                                </p>
                            </div>
                        </div>
                        @elseif($status === 'failed')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Payment Failed</h6>
                                <p class="mb-0 text-muted small">
                                    {{ $receipt->updated_at->format('M j, Y g:i A') }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Shop Information -->
        <div class="tab-pane fade" id="shop" role="tabpanel">
            @if($receipt->shop)
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-store me-2"></i>Shop Details
                        </h6>
                        
                        <div class="d-flex align-items-center mb-3">
                            @if($receipt->shop->logo)
                                <img src="{{ Storage::url($receipt->shop->logo) }}" 
                                     class="rounded me-3" 
                                     style="width: 60px; height: 60px; object-fit: cover;" 
                                     alt="Shop Logo">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-store fa-lg text-muted"></i>
                                </div>
                            @endif
                            
                            <div>
                                <h5 class="mb-1">{{ $receipt->shop->shop_name }}</h5>
                                <p class="text-muted mb-0">{{ $receipt->shop->business_email }}</p>
                            </div>
                        </div>
                        
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted" width="40%">Owner:</td>
                                <td>{{ $receipt->shop->owner_full_name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Phone:</td>
                                <td>{{ $receipt->shop->business_phone_1 }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Location:</td>
                                <td>{{ $receipt->shop->state }}, {{ $receipt->shop->country }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Status:</td>
                                <td>
                                    @if($receipt->shop->approved)
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-chart-bar me-2"></i>Shop Performance
                        </h6>
                        
                        @php
                            $shopReceipts = $receipt->shop->receipts()->count();
                            $shopSuccessful = $receipt->shop->receipts()
                                ->where('payment_gateway_status', 'successful')->count();
                            $shopRevenue = $receipt->shop->receipts()
                                ->where('payment_gateway_status', 'successful')
                                ->sum('amount');
                        @endphp
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Total Receipts:</span>
                                            <strong class="text-primary">{{ $shopReceipts }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Successful Payments:</span>
                                            <strong class="text-success">{{ $shopSuccessful }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Total Revenue:</span>
                                            <strong class="text-warning">₦{{ number_format($shopRevenue, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.shops.show', $receipt->shop) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt me-1"></i>View Shop Details
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-store-slash text-muted mb-3" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mb-2">Shop Not Found</h5>
                    <p class="text-muted">The shop associated with this receipt may have been deleted.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="border-top pt-3 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Receipt ID: {{ $receipt->id }} • Last updated {{ $receipt->updated_at->diffForHumans() }}
                </small>
            </div>
            
            <div class="btn-group">
                @if($status === 'pending')
                    <button type="button" class="btn btn-warning" 
                            onclick="checkPaymentStatus({{ $receipt->id }})">
                        <i class="fas fa-sync-alt me-2"></i>Check Payment Status
                    </button>
                @endif
                
                <button type="button" class="btn btn-info" 
                        onclick="downloadReceipt({{ $receipt->id }})">
                    <i class="fas fa-download me-2"></i>Download PDF
                </button>
                
                @if($receipt->customer_email)
                    <button type="button" class="btn btn-outline-primary" 
                            onclick="resendReceipt({{ $receipt->id }})">
                        <i class="fas fa-envelope me-2"></i>Resend Email
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.receipt-details-modal .nav-tabs {
    border-bottom: 2px solid #dee2e6;
}

.receipt-details-modal .nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
}

.receipt-details-modal .nav-tabs .nav-link.active {
    color: #495057;
    border-bottom: 2px solid #007bff;
    background: none;
}

.payment-timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -1.75rem;
    top: 1.5rem;
    bottom: -1.5rem;
    width: 2px;
    background: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.timeline-content h6 {
    font-size: 0.9rem;
    color: #495057;
}
</style>

<script>
function resendReceipt(receiptId) {
    if (confirm('Are you sure you want to resend this receipt to the customer?')) {
        showLoading('Sending receipt...');
        
        fetch(`/admin/receipts/${receiptId}/resend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert('success', 'Receipt resent successfully!');
            } else {
                showAlert('error', data.message || 'Failed to resend receipt');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Failed to resend receipt');
        });
    }
}
</script>
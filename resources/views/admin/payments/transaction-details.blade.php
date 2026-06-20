<div class="transaction-details-modal">
    <!-- Transaction Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="transaction-icon me-3">
                    @php
                        $status = $transaction->payment_gateway_status ?? 'pending';
                        $iconColors = [
                            'successful' => 'success',
                            'pending' => 'warning',
                            'failed' => 'danger',
                            'refunded' => 'info'
                        ];
                        $icons = [
                            'successful' => 'check-circle',
                            'pending' => 'clock',
                            'failed' => 'times-circle',
                            'refunded' => 'undo'
                        ];
                    @endphp
                    <i class="fas fa-{{ $icons[$status] ?? 'question-circle' }} fa-3x text-{{ $iconColors[$status] ?? 'secondary' }}"></i>
                </div>
                <div>
                    <h4 class="mb-1">{{ $transaction->transaction_reference ?: 'No Reference' }}</h4>
                    <p class="text-muted mb-1">Receipt: {{ $transaction->receipt_number }}</p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar me-1"></i>
                        {{ $transaction->created_at->format('F j, Y \a\t g:i A') }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 text-end">
            <div class="mb-3">
                <span class="badge bg-{{ $iconColors[$status] ?? 'secondary' }} fs-6 px-3 py-2">
                    <i class="fas fa-{{ $icons[$status] ?? 'question' }} me-2"></i>
                    {{ ucfirst($status) }}
                </span>
            </div>
            
            <div class="text-muted">
                <h3 class="text-primary mb-0">₦{{ number_format($transaction->amount ?? 0, 2) }}</h3>
                <small>Transaction Amount</small>
            </div>
        </div>
    </div>

    <!-- Transaction Details Tabs -->
    <ul class="nav nav-tabs mb-3" id="transactionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" 
                    data-bs-target="#details" type="button" role="tab">
                <i class="fas fa-info-circle me-2"></i>Details
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="customer-tab" data-bs-toggle="tab" 
                    data-bs-target="#customer" type="button" role="tab">
                <i class="fas fa-user me-2"></i>Customer
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payment-tab" data-bs-toggle="tab" 
                    data-bs-target="#payment" type="button" role="tab">
                <i class="fas fa-credit-card me-2"></i>Payment Info
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" 
                    data-bs-target="#history" type="button" role="tab">
                <i class="fas fa-history me-2"></i>History
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Transaction Details -->
        <div class="tab-pane fade show active" id="details" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Transaction ID:</td>
                            <td>
                                <code>{{ $transaction->transaction_reference ?: 'Not assigned' }}</code>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Receipt Number:</td>
                            <td>
                                <strong>{{ $transaction->receipt_number }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Amount:</td>
                            <td>
                                <span class="fs-5 fw-bold text-primary">
                                    ₦{{ number_format($transaction->amount ?? 0, 2) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status:</td>
                            <td>
                                <span class="badge bg-{{ $iconColors[$status] ?? 'secondary' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Payment Method:</td>
                            <td>{{ ucfirst($transaction->payment_method ?: 'Not specified') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Created:</td>
                            <td>{{ $transaction->created_at->format('M j, Y g:i A') }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">Transaction Summary</h6>
                    
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h5 class="text-primary mb-1">
                                        {{ $transaction->created_at->diffInDays(now()) }}
                                    </h5>
                                    <small class="text-muted">Days Ago</small>
                                </div>
                                <div class="col-6">
                                    <h5 class="text-success mb-1">
                                        @if($status === 'successful')
                                            <i class="fas fa-check"></i>
                                        @elseif($status === 'pending')
                                            <i class="fas fa-clock"></i>
                                        @else
                                            <i class="fas fa-times"></i>
                                        @endif
                                    </h5>
                                    <small class="text-muted">Status</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($transaction->payment_gateway_response)
                        <div class="mt-3">
                            <h6 class="fw-bold text-muted mb-2">Gateway Response:</h6>
                            <div class="bg-light p-3 rounded">
                                <small>{{ $transaction->payment_gateway_response }}</small>
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
                    <h6 class="fw-bold text-primary mb-3">Customer Details</h6>
                    
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Name:</td>
                            <td>{{ $transaction->customer_name ?: 'Guest Customer' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Email:</td>
                            <td>
                                @if($transaction->customer_email)
                                    <a href="mailto:{{ $transaction->customer_email }}">
                                        {{ $transaction->customer_email }}
                                    </a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Phone:</td>
                            <td>
                                @if($transaction->customer_phone)
                                    <a href="tel:{{ $transaction->customer_phone }}">
                                        {{ $transaction->customer_phone }}
                                    </a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                        </tr>
                        @if($transaction->customer_address)
                        <tr>
                            <td class="fw-bold text-muted">Address:</td>
                            <td>{{ $transaction->customer_address }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                
                <div class="col-md-6">
                    @if($transaction->user)
                        <h6 class="fw-bold text-primary mb-3">Registered Customer</h6>
                        
                        @php
                            $customerStats = [
                                'total_transactions' => $transaction->user->receipts()->count(),
                                'total_spent' => $transaction->user->receipts()
                                    ->where('payment_gateway_status', 'successful')->sum('amount'),
                                'member_since' => $transaction->user->created_at->format('M Y')
                            ];
                        @endphp
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Total Transactions:</span>
                                            <strong>{{ $customerStats['total_transactions'] }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card border-success">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Total Spent:</span>
                                            <strong>₦{{ number_format($customerStats['total_spent'], 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Member Since:</span>
                                            <strong>{{ $customerStats['member_since'] }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash text-muted mb-3" style="font-size: 2rem;"></i>
                            <h6 class="text-muted">Guest Customer</h6>
                            <p class="text-muted mb-0">No registered account</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="tab-pane fade" id="payment" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">Payment Details</h6>
                    
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Amount:</td>
                            <td>
                                <span class="fs-4 fw-bold text-primary">
                                    ₦{{ number_format($transaction->amount ?? 0, 2) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Payment Method:</td>
                            <td>
                                @if($transaction->payment_method)
                                    <span class="badge bg-info">{{ ucfirst($transaction->payment_method) }}</span>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Gateway:</td>
                            <td>{{ ucfirst($transaction->payment_method ?: 'Unknown') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Reference:</td>
                            <td>
                                @if($transaction->transaction_reference)
                                    <code>{{ $transaction->transaction_reference }}</code>
                                @else
                                    <span class="text-muted">Not assigned</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status:</td>
                            <td>
                                <span class="badge bg-{{ $iconColors[$status] ?? 'secondary' }} fs-6">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    @if($transaction->shop)
                        <h6 class="fw-bold text-primary mb-3">Shop Information</h6>
                        
                        <div class="d-flex align-items-center mb-3">
                            @if($transaction->shop->logo)
                                <img src="{{ Storage::url($transaction->shop->logo) }}" 
                                     class="rounded me-3" 
                                     style="width: 50px; height: 50px; object-fit: cover;" 
                                     alt="Shop Logo">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-store text-muted"></i>
                                </div>
                            @endif
                            
                            <div>
                                <h6 class="mb-1">{{ $transaction->shop->shop_name }}</h6>
                                <small class="text-muted">{{ $transaction->shop->state }}, {{ $transaction->shop->country }}</small>
                            </div>
                        </div>
                        
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-2">
                                        <small class="text-muted">Shop Status</small>
                                        <div class="fw-bold">
                                            @if($transaction->shop->approved)
                                                <span class="text-success">Approved</span>
                                            @else
                                                <span class="text-warning">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-2">
                                        <small class="text-muted">Member Since</small>
                                        <div class="fw-bold">{{ $transaction->shop->created_at->format('M Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.shops.show', $transaction->shop) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-external-link-alt me-1"></i>View Shop
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="transaction-timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-primary"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Transaction Created</h6>
                        <p class="text-muted mb-0">Receipt generated and payment initiated</p>
                        <small class="text-muted">{{ $transaction->created_at->format('M j, Y g:i A') }}</small>
                    </div>
                </div>
                
                @if($transaction->payment_initiated_at)
                <div class="timeline-item">
                    <div class="timeline-marker bg-info"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Payment Gateway Called</h6>
                        <p class="text-muted mb-0">Payment request sent to gateway</p>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($transaction->payment_initiated_at)->format('M j, Y g:i A') }}</small>
                    </div>
                </div>
                @endif
                
                @if($status === 'successful')
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Payment Successful</h6>
                        <p class="text-muted mb-0">Payment completed successfully</p>
                        <small class="text-muted">{{ $transaction->updated_at->format('M j, Y g:i A') }}</small>
                    </div>
                </div>
                @elseif($status === 'failed')
                <div class="timeline-item">
                    <div class="timeline-marker bg-danger"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Payment Failed</h6>
                        <p class="text-muted mb-0">{{ $transaction->payment_gateway_response ?: 'Payment could not be processed' }}</p>
                        <small class="text-muted">{{ $transaction->updated_at->format('M j, Y g:i A') }}</small>
                    </div>
                </div>
                @elseif($status === 'refunded')
                <div class="timeline-item">
                    <div class="timeline-marker bg-warning"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Payment Refunded</h6>
                        <p class="text-muted mb-0">Refund processed successfully</p>
                        <small class="text-muted">{{ $transaction->updated_at->format('M j, Y g:i A') }}</small>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="border-top pt-3 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted">
                    Transaction ID: {{ $transaction->id }} • Last updated {{ $transaction->updated_at->diffForHumans() }}
                </small>
            </div>
            
            <div class="btn-group">
                @if($status === 'successful')
                    <button type="button" class="btn btn-warning" 
                            onclick="initiateRefund({{ $transaction->id }})">
                        <i class="fas fa-undo me-2"></i>Initiate Refund
                    </button>
                @endif
                
                @if($status === 'pending')
                    <button type="button" class="btn btn-info" 
                            onclick="checkStatus({{ $transaction->id }})">
                        <i class="fas fa-sync-alt me-2"></i>Check Status
                    </button>
                @endif
                
                <button type="button" class="btn btn-outline-primary" 
                        onclick="window.open('{{ route('admin.receipts.index') }}?search={{ $transaction->receipt_number }}', '_blank')">
                    <i class="fas fa-receipt me-2"></i>View Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.transaction-timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -1.75rem;
    top: 1.5rem;
    bottom: -2rem;
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
</style>
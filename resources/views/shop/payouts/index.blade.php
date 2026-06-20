@extends('layouts.app')

@section('title', 'My Payouts')
@section('page-title', 'Commission Payouts')
@section('page-description', 'Manage your commission earnings and payout requests')

@push('styles')
<style>
    .balance-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #667eea;
        height: 100%;
    }
    
    .payout-card {
        transition: transform 0.2s;
        border-radius: 8px;
        border-left: 4px solid #e9ecef;
    }
    
    .payout-card.pending {
        border-left-color: #ffc107;
    }
    
    .payout-card.approved {
        border-left-color: #28a745;
    }
    
    .payout-card.paid {
        border-left-color: #007bff;
    }
    
    .payout-card.rejected {
        border-left-color: #dc3545;
    }
    
    .payout-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .commission-breakdown {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .amount-display {
        font-size: 2rem;
        font-weight: bold;
    }
    
    .status-badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Balance Overview -->
    <div class="balance-card">
        <div class="row align-items-center">
            <div class="col-md-4 text-center">
                <div class="amount-display">₦{{ number_format($commissionStats['available_balance'], 2) }}</div>
                <div class="h5 mb-0">Available Balance</div>
                <small class="opacity-75">Commission earnings ready for payout</small>
            </div>
            <div class="col-md-4 text-center">
                <div class="h3 mb-1">₦{{ number_format($commissionStats['total_commission_earned'], 2) }}</div>
                <div class="h6 mb-0">Total Earned</div>
                <small class="opacity-75">From {{ $commissionStats['successful_receipts'] }} receipts</small>
            </div>
            <div class="col-md-4 text-center">
                <div class="h3 mb-1">₦{{ number_format($commissionStats['total_paid_out'], 2) }}</div>
                <div class="h6 mb-0">Total Received</div>
                <small class="opacity-75">Successfully paid out</small>
            </div>
        </div>
        
        @if(auth()->user()->canRequestPayout())
            <div class="text-center mt-4">
                <a href="{{ route('shop.payouts.create') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-money-check-alt me-2"></i>Request Payout
                </a>
            </div>
        @else
            <div class="text-center mt-4">
                <button type="button" class="btn btn-outline-light" disabled>
                    <i class="fas fa-info-circle me-2"></i>
                    @if($commissionStats['pending_requests'] > 0)
                        You have a pending payout request
                    @elseif($commissionStats['available_balance'] <= 0)
                        No available balance for payout
                    @else
                        Payout requests temporarily unavailable
                    @endif
                </button>
            </div>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-warning mb-1">{{ $payoutStats['pending_requests'] }}</h3>
                        <div class="text-muted">Pending Requests</div>
                    </div>
                    <div class="text-warning opacity-50">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-success mb-1">{{ $payoutStats['approved_requests'] }}</h3>
                        <div class="text-muted">Approved</div>
                    </div>
                    <div class="text-success opacity-50">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-primary mb-1">{{ $payoutStats['paid_requests'] }}</h3>
                        <div class="text-muted">Completed</div>
                    </div>
                    <div class="text-primary opacity-50">
                        <i class="fas fa-money-check-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-info mb-1">{{ $payoutStats['total_requests'] }}</h3>
                        <div class="text-muted">Total Requests</div>
                    </div>
                    <div class="text-info opacity-50">
                        <i class="fas fa-list fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Breakdown -->
    <div class="commission-breakdown">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5><i class="fas fa-chart-pie me-2"></i>Commission Breakdown</h5>
                <p class="text-muted mb-0">Detailed view of your earnings and payouts</p>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-outline-primary" onclick="loadCommissionBreakdown()">
                    <i class="fas fa-eye me-2"></i>View Details
                </button>
                <a href="{{ route('shop.payouts.export') }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-download me-2"></i>Export History
                </a>
            </div>
        </div>
    </div>

    <!-- Actions & Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fas fa-money-check-alt me-2"></i>Payout History
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    @if(auth()->user()->canRequestPayout())
                        <a href="{{ route('shop.payouts.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>New Payout Request
                        </a>
                    @endif
                </div>
            </div>
            
            <!-- Filters -->
            <div class="row mt-3">
                <div class="col-md-3">
                    <form method="GET" id="filterForm">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                </div>
                <div class="col-md-3">
                        <input type="date" name="date_from" class="form-control" 
                               value="{{ request('date_from') }}" 
                               onchange="this.form.submit()" 
                               placeholder="From Date">
                </div>
                <div class="col-md-3">
                        <input type="date" name="date_to" class="form-control" 
                               value="{{ request('date_to') }}" 
                               onchange="this.form.submit()" 
                               placeholder="To Date">
                    </form>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-info w-100" onclick="checkPayoutEligibility()">
                        <i class="fas fa-check me-2"></i>Check Eligibility
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Requests -->
    @if($payoutRequests->count() > 0)
        <div class="row">
            @foreach($payoutRequests as $payout)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card payout-card {{ $payout->status }} h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $payout->request_number }}</h6>
                            <span class="status-badge bg-{{ $payout->status === 'pending' ? 'warning' : ($payout->status === 'approved' ? 'success' : ($payout->status === 'paid' ? 'primary' : 'danger')) }}">
                                {{ $payout->status_display }}
                            </span>
                        </div>
                        
                        <div class="card-body">
                            <!-- Amount Info -->
                            <div class="text-center mb-3">
                                <div class="h4 text-primary mb-1">
                                    ₦{{ number_format($payout->amount_requested, 2) }}
                                </div>
                                @if($payout->amount_approved && $payout->amount_approved != $payout->amount_requested)
                                    <small class="text-success">
                                        Approved: ₦{{ number_format($payout->amount_approved, 2) }}
                                    </small>
                                @endif
                            </div>
                            
                            <!-- Bank Details -->
                            <div class="mb-3">
                                <small class="text-muted d-block">Bank Details:</small>
                                <div class="small">
                                    <strong>{{ $payout->bank_name }}</strong><br>
                                    {{ $payout->account_name }}<br>
                                    {{ $payout->account_number }}
                                </div>
                            </div>
                            
                            <!-- Request Date & Status Info -->
                            <div class="mb-3">
                                <small class="text-muted d-block">Requested:</small>
                                <div class="small">{{ $payout->created_at->format('M d, Y \a\t g:i A') }}</div>
                                <div class="small text-muted">{{ $payout->created_at->diffForHumans() }}</div>
                            </div>
                            
                            <!-- Status Specific Info -->
                            @if($payout->approved_at)
                                <div class="mb-3">
                                    <small class="text-success d-block">Approved:</small>
                                    <div class="small">{{ $payout->approved_at->format('M d, Y \a\t g:i A') }}</div>
                                    @if($payout->approvedBy)
                                        <div class="small text-muted">by {{ $payout->approvedBy->name }}</div>
                                    @endif
                                </div>
                            @endif
                            
                            @if($payout->paid_at)
                                <div class="mb-3">
                                    <small class="text-primary d-block">Paid:</small>
                                    <div class="small">{{ $payout->paid_at->format('M d, Y \a\t g:i A') }}</div>
                                    @if($payout->payment_reference)
                                        <div class="small">Ref: <code>{{ $payout->payment_reference }}</code></div>
                                    @endif
                                </div>
                            @endif
                            
                            @if($payout->rejection_reason)
                                <div class="mb-3">
                                    <small class="text-danger d-block">Rejection Reason:</small>
                                    <p class="small text-danger mb-0">{{ $payout->rejection_reason }}</p>
                                </div>
                            @endif
                            
                            <!-- Processing Time -->
                            <div class="row text-center small">
                                <div class="col">
                                    <span class="text-muted">Days Ago</span><br>
                                    <strong>{{ $payout->days_since_request }}</strong>
                                </div>
                                <div class="col">
                                    <span class="text-muted">Available Then</span><br>
                                    <strong>₦{{ number_format($payout->commission_balance, 0) }}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <a href="{{ route('shop.payouts.show', $payout) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $payoutRequests->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-money-check-alt text-muted mb-3" style="font-size: 4rem;"></i>
            <h4 class="text-muted">No Payout Requests Yet</h4>
            <p class="text-muted">Start generating receipts to earn commissions and request payouts.</p>
            @if(auth()->user()->canRequestPayout())
                <a href="{{ route('shop.payouts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create First Payout Request
                </a>
            @else
                <div class="alert alert-info d-inline-block">
                    <i class="fas fa-info-circle me-2"></i>
                    Earn commissions by generating successful receipts, then request payouts here.
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Commission Breakdown Modal -->
<div class="modal fade" id="commissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Commission Breakdown</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="commissionLoading" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading commission details...</p>
                </div>
                <div id="commissionContent" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadCommissionBreakdown() {
    const modal = new bootstrap.Modal(document.getElementById('commissionModal'));
    modal.show();
    
    fetch('/shop/payouts/commission-breakdown')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayCommissionBreakdown(data.breakdown);
            } else {
                showCommissionError('Failed to load commission breakdown.');
            }
        })
        .catch(error => {
            showCommissionError('An error occurred while loading the breakdown.');
        });
}

function displayCommissionBreakdown(breakdown) {
    const loading = document.getElementById('commissionLoading');
    const content = document.getElementById('commissionContent');
    
    loading.style.display = 'none';
    content.style.display = 'block';
    
    let html = `
        <div class="row mb-4">
            <div class="col-md-4 text-center">
                <h4 class="text-primary">₦${breakdown.total_commission.toLocaleString()}</h4>
                <small class="text-muted">Total Earned</small>
            </div>
            <div class="col-md-4 text-center">
                <h4 class="text-success">₦${breakdown.total_paid_out.toLocaleString()}</h4>
                <small class="text-muted">Total Paid Out</small>
            </div>
            <div class="col-md-4 text-center">
                <h4 class="text-warning">₦${breakdown.available_balance.toLocaleString()}</h4>
                <small class="text-muted">Available Balance</small>
            </div>
        </div>
    `;
    
    if (breakdown.recent_receipts && breakdown.recent_receipts.length > 0) {
        html += `
            <h6>Recent Commission Earnings</h6>
            <div class="table-responsive mb-4">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Receipt</th>
                            <th>Amount</th>
                            <th>Commission</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        breakdown.recent_receipts.forEach(receipt => {
            html += `
                <tr>
                    <td>${receipt.receipt_number}</td>
                    <td>₦${receipt.amount.toLocaleString()}</td>
                    <td>₦${receipt.commission_amount.toLocaleString()}</td>
                    <td>${new Date(receipt.created_at).toLocaleDateString()}</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
    }
    
    if (breakdown.recent_payouts && breakdown.recent_payouts.length > 0) {
        html += `
            <h6>Recent Payouts</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Request</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        breakdown.recent_payouts.forEach(payout => {
            const statusClass = payout.status === 'paid' ? 'success' : 'warning';
            html += `
                <tr>
                    <td>${payout.request_number}</td>
                    <td>₦${payout.amount_approved.toLocaleString()}</td>
                    <td><span class="badge bg-${statusClass}">${payout.status}</span></td>
                    <td>${new Date(payout.created_at).toLocaleDateString()}</td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
    }
    
    content.innerHTML = html;
}

function showCommissionError(message) {
    const loading = document.getElementById('commissionLoading');
    loading.innerHTML = `<p class="text-danger">${message}</p>`;
}

function checkPayoutEligibility() {
    fetch('/shop/payouts/check-eligibility')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const eligibility = data.eligibility;
                let message = `Available Balance: ₦${eligibility.available_balance.toLocaleString()}\n`;
                message += `Minimum Amount: ₦${eligibility.minimum_amount}\n`;
                message += `Pending Requests: ${eligibility.pending_requests}\n\n`;
                
                if (eligibility.can_request) {
                    message += 'You can request a payout!';
                    if (confirm(message + '\n\nWould you like to create a payout request now?')) {
                        window.location.href = '/shop/payouts/create';
                    }
                } else {
                    message += 'Reasons you cannot request payout:\n';
                    eligibility.reasons.forEach(reason => {
                        message += `• ${reason}\n`;
                    });
                    alert(message);
                }
            }
        })
        .catch(error => {
            alert('Failed to check eligibility. Please try again.');
        });
}
</script>
@endpush
@endsection
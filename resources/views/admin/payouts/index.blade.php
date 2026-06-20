@extends('layouts.app')

@section('title', 'Payout Management')
@section('page-title', 'Payout Management')
@section('page-description', 'Manage shop owner commission payout requests')

@push('styles')
<style>
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #667eea;
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
    
    .amount-display {
        font-size: 1.25rem;
        font-weight: bold;
    }
    
    .status-badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
    }
    
    .priority-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    
    .priority-high {
        background: #dc3545;
    }
    
    .priority-medium {
        background: #ffc107;
    }
    
    .priority-low {
        background: #28a745;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-warning mb-1">{{ $stats['pending_requests'] }}</h3>
                        <div class="text-muted">Pending Requests</div>
                        <small class="text-warning">₦{{ number_format($stats['total_pending_amount'], 2) }}</small>
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
                        <h3 class="text-success mb-1">{{ $stats['approved_requests'] }}</h3>
                        <div class="text-muted">Approved Requests</div>
                        <small class="text-success">₦{{ number_format($stats['total_approved_amount'], 2) }}</small>
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
                        <h3 class="text-primary mb-1">{{ $stats['paid_requests'] }}</h3>
                        <div class="text-muted">Paid Requests</div>
                        <small class="text-primary">₦{{ number_format($stats['total_paid_amount'], 2) }}</small>
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
                        <h3 class="text-info mb-1">{{ $stats['recent_requests'] }}</h3>
                        <div class="text-muted">Recent (30 days)</div>
                        <small class="text-muted">{{ $stats['total_requests'] }} total</small>
                    </div>
                    <div class="text-info opacity-50">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fas fa-money-check-alt me-2"></i>Payout Requests
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.payouts.export') }}">All Requests</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.payouts.export', 'pending') }}">Pending Only</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.payouts.export', 'approved') }}">Approved Only</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.payouts.export', 'paid') }}">Paid Only</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="refreshStatistics()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                </div>
            </div>
            
            <!-- Advanced Filters -->
            <div class="row mt-3">
                <div class="col-md-2">
                    <form method="GET" id="filterForm">
                        <select name="status" class="form-select" onchange="submitFilters()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                </div>
                <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" 
                               value="{{ request('date_from') }}" 
                               onchange="submitFilters()" 
                               placeholder="From Date">
                </div>
                <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" 
                               value="{{ request('date_to') }}" 
                               onchange="submitFilters()" 
                               placeholder="To Date">
                </div>
                <div class="col-md-2">
                        <input type="number" name="amount_min" class="form-control" 
                               value="{{ request('amount_min') }}" 
                               placeholder="Min Amount" 
                               onchange="submitFilters()">
                </div>
                <div class="col-md-4">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control" 
                                   placeholder="Search by name, email, request number..." 
                                   value="{{ request('search') }}">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Requests -->
    @if($payoutRequests->count() > 0)
        <!-- Bulk Actions -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <form id="bulkActionForm" method="POST" action="{{ route('admin.payouts.bulk-action') }}">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    Select All
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <select name="action" class="form-select form-select-sm" required>
                                        <option value="">Choose Action</option>
                                        <option value="approve">Bulk Approve</option>
                                        <option value="reject">Bulk Reject</option>
                                        <option value="mark_paid">Mark as Paid</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="bulk_reason" class="form-control form-control-sm" 
                                           placeholder="Reason/Reference (optional)">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-check me-1"></i>Execute Action
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payout Requests Grid -->
        <div class="row">
            @foreach($payoutRequests as $payout)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card payout-card {{ $payout->status }} h-100" style="position: relative;">
                        <!-- Priority Indicator -->
                        @php
                            $priority = 'low';
                            if ($payout->status === 'pending' && $payout->amount_requested >= 10000) $priority = 'high';
                            elseif ($payout->status === 'pending' && $payout->amount_requested >= 5000) $priority = 'medium';
                        @endphp
                        <div class="priority-indicator priority-{{ $priority }}"></div>
                        
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input payout-checkbox" type="checkbox" 
                                       name="payout_ids[]" value="{{ $payout->id }}" 
                                       form="bulkActionForm">
                            </div>
                            <div class="text-center flex-grow-1">
                                <h6 class="mb-0">{{ $payout->request_number }}</h6>
                                <small class="text-muted">{{ $payout->created_at->diffForHumans() }}</small>
                            </div>
                            <span class="status-badge bg-{{ $payout->status === 'pending' ? 'warning' : ($payout->status === 'approved' ? 'success' : ($payout->status === 'paid' ? 'primary' : 'danger')) }}">
                                {{ $payout->status_display }}
                            </span>
                        </div>
                        
                        <div class="card-body">
                            <!-- Shop Owner Info -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar bg-primary text-white rounded-circle me-3" 
                                     style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    {{ strtoupper(substr($payout->user->first_name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $payout->user->name }}</h6>
                                    <small class="text-muted">{{ $payout->shop ? $payout->shop->shop_name : 'No shop' }}</small>
                                </div>
                            </div>
                            
                            <!-- Amount Info -->
                            <div class="text-center mb-3">
                                <div class="amount-display text-primary">
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
                            
                            <!-- Request Reason -->
                            @if($payout->reason)
                                <div class="mb-3">
                                    <small class="text-muted d-block">Reason:</small>
                                    <p class="small mb-0">{{ Str::limit($payout->reason, 60) }}</p>
                                </div>
                            @endif
                            
                            <!-- Status Specific Info -->
                            @if($payout->rejection_reason)
                                <div class="mb-3">
                                    <small class="text-danger d-block">Rejection Reason:</small>
                                    <p class="small text-danger mb-0">{{ Str::limit($payout->rejection_reason, 60) }}</p>
                                </div>
                            @endif
                            
                            @if($payout->payment_reference)
                                <div class="mb-3">
                                    <small class="text-muted d-block">Payment Reference:</small>
                                    <code class="small">{{ $payout->payment_reference }}</code>
                                </div>
                            @endif
                            
                            <!-- Processing Info -->
                            <div class="row text-center small">
                                <div class="col">
                                    <span class="text-muted">Days Ago</span><br>
                                    <strong>{{ $payout->days_since_request }}</strong>
                                </div>
                                <div class="col">
                                    <span class="text-muted">Balance</span><br>
                                    <strong>₦{{ number_format($payout->commission_balance, 0) }}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('admin.payouts.show', $payout) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                
                                @if($payout->canBeApproved())
                                    <button type="button" class="btn btn-outline-success btn-sm" 
                                            onclick="quickApprove({{ $payout->id }})">
                                        <i class="fas fa-check me-1"></i>Approve
                                    </button>
                                @elseif($payout->canBeProcessed())
                                    <button type="button" class="btn btn-outline-primary btn-sm" 
                                            onclick="quickMarkPaid({{ $payout->id }})">
                                        <i class="fas fa-money-check me-1"></i>Mark Paid
                                    </button>
                                @endif
                                
                                @if($payout->canBeRejected())
                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                            onclick="quickReject({{ $payout->id }})">
                                        <i class="fas fa-times me-1"></i>Reject
                                    </button>
                                @endif
                            </div>
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
            <h4 class="text-muted">No Payout Requests Found</h4>
            <p class="text-muted">Shop owner payout requests will appear here when submitted.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Filter management
function submitFilters() {
    document.getElementById('filterForm').submit();
}

// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.payout-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Quick actions
function quickApprove(payoutId) {
    const amount = prompt('Enter approved amount (leave empty for full amount):');
    if (amount !== null) {
        performQuickAction(payoutId, 'approve', { amount_approved: amount });
    }
}

function quickReject(payoutId) {
    const reason = prompt('Enter rejection reason:');
    if (reason && reason.trim()) {
        performQuickAction(payoutId, 'reject', { rejection_reason: reason });
    }
}

function quickMarkPaid(payoutId) {
    const reference = prompt('Enter payment reference:');
    if (reference && reference.trim()) {
        performQuickAction(payoutId, 'mark-paid', { payment_reference: reference });
    }
}

function performQuickAction(payoutId, action, data) {
    fetch(`/admin/payouts/${payoutId}/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Action failed: ' + data.message);
        }
    })
    .catch(error => {
        alert('An error occurred while performing the action.');
    });
}

// Refresh statistics
function refreshStatistics() {
    fetch('/admin/payouts/statistics')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update statistics cards
                location.reload();
            }
        })
        .catch(error => {
            console.error('Failed to refresh statistics:', error);
        });
}

// Bulk action form validation
document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    const selectedCheckboxes = document.querySelectorAll('.payout-checkbox:checked');
    const action = document.querySelector('select[name="action"]').value;
    
    if (selectedCheckboxes.length === 0) {
        e.preventDefault();
        alert('Please select at least one payout request.');
        return;
    }
    
    if (!action) {
        e.preventDefault();
        alert('Please select an action.');
        return;
    }
    
    if (!confirm(`Are you sure you want to ${action} ${selectedCheckboxes.length} payout request(s)?`)) {
        e.preventDefault();
    }
});
</script>
@endpush
@endsection
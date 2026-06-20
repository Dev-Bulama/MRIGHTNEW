@extends('layouts.union')

@section('breadcrumb')
    <li class="breadcrumb-item active">Payouts</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Payout Management</h1>
            <p class="text-muted">Monitor payout requests from your assigned locations</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshData()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Requests</h6>
                            <h3 class="mb-0 text-primary">{{ $stats['total_requests'] ?? 0 }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-money-check-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Pending</h6>
                            <h3 class="mb-0 text-warning">{{ $stats['pending_requests'] ?? 0 }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Approved</h6>
                            <h3 class="mb-0 text-success">{{ $stats['approved_requests'] ?? 0 }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Amount</h6>
                            <h3 class="mb-0 text-info">₦{{ number_format($stats['total_amount_requested'] ?? 0) }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-naira-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('union.payouts.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Shop owner name...">
                </div>

                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>

                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payouts Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Payout Requests</h5>
        </div>
        <div class="card-body p-0">
            @if($payouts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Shop Owner</th>
                                <th>Shop Details</th>
                                <th>Amount Requested</th>
                                <th>Status</th>
                                <th>Request Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payouts as $payout)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $payout->user->name ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $payout->user->email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($payout->user && $payout->user->shop)
                                            <strong>{{ $payout->user->shop->shop_name }}</strong><br>
                                            <small class="text-muted">{{ $payout->user->shop->state }}, {{ $payout->user->shop->local_government }}</small>
                                        @else
                                            <span class="text-muted">No shop details</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>₦{{ number_format($payout->amount_requested, 2) }}</strong>
                                        @if($payout->amount_approved && $payout->amount_approved != $payout->amount_requested)
                                            <br><small class="text-success">Approved: ₦{{ number_format($payout->amount_approved, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'paid' => 'primary',
                                                'rejected' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$payout->status] ?? 'secondary' }}">
                                            {{ ucfirst($payout->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $payout->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $payout->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="viewDetails({{ $payout->id }})" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer">
                    {{ $payouts->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-money-check-alt text-muted mb-3" style="font-size: 4rem;"></i>
                    <h5 class="text-muted">No Payout Requests Found</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                            No payout requests match your current filters.
                        @else
                            No payout requests have been made in your assigned locations yet.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('union.payouts.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}
</style>

<script>
function refreshData() {
    location.reload();
}

function viewDetails(payoutId) {
    // Implement payout details modal or redirect
    alert('View details for payout ID: ' + payoutId);
}
</script>
@endsection
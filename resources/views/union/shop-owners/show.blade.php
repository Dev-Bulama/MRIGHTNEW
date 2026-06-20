@extends('layouts.union')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('union.shop-owners.index') }}">Shop Owners</a></li>
    <li class="breadcrumb-item active">{{ $shopOwner->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $shopOwner->name }}</h1>
            <p class="text-muted">Shop Owner Details & Performance</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('union.shop-owners.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <button class="btn btn-primary" onclick="refreshStats()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Shop Owner Overview -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Shop Owner Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-large mx-auto mb-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 class="mb-1">{{ $shopOwner->name }}</h5>
                        <span class="badge bg-{{ $shopOwner->status == 'active' ? 'success' : 'secondary' }} mb-2">
                            {{ ucfirst($shopOwner->status) }}
                        </span>
                    </div>

                    <div class="info-group">
                        <div class="info-item">
                            <label>Email:</label>
                            <span>{{ $shopOwner->email }}</span>
                        </div>
                        <div class="info-item">
                            <label>Phone:</label>
                            <span>{{ $shopOwner->phone_number }}</span>
                        </div>
                        @if($shopOwner->secondary_phone)
                            <div class="info-item">
                                <label>Secondary Phone:</label>
                                <span>{{ $shopOwner->secondary_phone }}</span>
                            </div>
                        @endif
                        <div class="info-item">
                            <label>Registration Date:</label>
                            <span>{{ $shopOwner->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="info-item">
                            <label>Last Login:</label>
                            <span>
                                @if($shopOwner->last_login_at)
                                    {{ $shopOwner->last_login_at->diffForHumans() }}
                                @else
                                    Never
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <label>Account Status:</label>
                            <span>
                                @if($shopOwner->account_activated_at)
                                    <span class="badge bg-success">Activated</span>
                                @else
                                    <span class="badge bg-warning">Pending Activation</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Shop Information -->
            @if($shopOwner->shop)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-store me-2"></i>Shop Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <div class="info-item">
                                        <label>Shop Name:</label>
                                        <span>{{ $shopOwner->shop->shop_name }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Business Type:</label>
                                        <span>{{ $shopOwner->shop->business_type ?? 'Not specified' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>State:</label>
                                        <span>{{ $shopOwner->shop->state }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>LGA:</label>
                                        <span>{{ $shopOwner->shop->local_government }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <div class="info-item">
                                        <label>Business Address:</label>
                                        <span>{{ $shopOwner->shop->business_address }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Shop Status:</label>
                                        <span>
                                            @if($shopOwner->shop->approved)
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-warning">Pending Approval</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <label>Registration:</label>
                                        <span>{{ $shopOwner->shop->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card mb-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-store text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">No Shop Registered</h5>
                        <p class="text-muted">This shop owner hasn't registered their shop yet.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Performance Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card primary">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Total Receipts</h6>
                        <h3 class="mb-0">{{ $stats['total_receipts'] ?? 0 }}</h3>
                        <small class="opacity-75">All time</small>
                    </div>
                    <div>
                        <i class="fas fa-receipt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card success">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Successful</h6>
                        <h3 class="mb-0">{{ $stats['successful_receipts'] ?? 0 }}</h3>
                        <small class="opacity-75">
                            {{ $performanceMetrics['success_rate'] ?? 0 }}% success rate
                        </small>
                    </div>
                    <div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card warning">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Total Revenue</h6>
                        <h3 class="mb-0">₦{{ number_format($stats['total_revenue'] ?? 0) }}</h3>
                        <small class="opacity-75">
                            Avg: ₦{{ number_format($performanceMetrics['avg_receipt_amount'] ?? 0) }}
                        </small>
                    </div>
                    <div>
                        <i class="fas fa-naira-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card info">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Commission</h6>
                        <h3 class="mb-0">₦{{ number_format($stats['total_commission'] ?? 0) }}</h3>
                        <small class="opacity-75">
                            Available: ₦{{ number_format($stats['available_balance'] ?? 0) }}
                        </small>
                    </div>
                    <div>
                        <i class="fas fa-percent fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Performance Metrics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="metric-item">
                        <div class="d-flex justify-content-between">
                            <span>Success Rate:</span>
                            <strong>{{ $performanceMetrics['success_rate'] ?? 0 }}%</strong>
                        </div>
                        <div class="progress mt-1 mb-3">
                            <div class="progress-bar bg-success" style="width: {{ $performanceMetrics['success_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="metric-item">
                        <div class="d-flex justify-content-between">
                            <span>Average Receipt Amount:</span>
                            <strong>₦{{ number_format($performanceMetrics['avg_receipt_amount'] ?? 0) }}</strong>
                        </div>
                    </div>

                    <div class="metric-item">
                        <div class="d-flex justify-content-between">
                            <span>Days Since Registration:</span>
                            <strong>{{ $performanceMetrics['registration_days'] ?? 0 }} days</strong>
                        </div>
                    </div>

                    @if($performanceMetrics['days_since_last_receipt'])
                        <div class="metric-item">
                            <div class="d-flex justify-content-between">
                                <span>Last Receipt:</span>
                                <strong>{{ $performanceMetrics['days_since_last_receipt'] }} days ago</strong>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-calendar me-2"></i>Monthly & Current Stats
                    </h6>
                </div>
                <div class="card-body">
                    <div class="metric-item">
                        <div class="d-flex justify-content-between">
                            <span>This Month Receipts:</span>
                            <strong>{{ $stats['monthly_receipts'] ?? 0 }}</strong>
                        </div>
                    </div>

                    <div class="metric-item">
                        <div class="d-flex justify-content-between">
                            <span>This Month Revenue:</span>
                            <strong>₦{{ number_format($stats['monthly_revenue'] ?? 0) }}</strong>
                        </div>
                    </div>

                    <div class="metric-item">
                        <div class="d-flex justify-content-between">
                            <span>Pending Payouts:</span>
                            <strong>{{ $stats['pending_payouts'] ?? 0 }}</strong>
                        </div>
                    </div>

                    <div class="metric-item">
                        <div class="d-flex justify-content-between">
                            <span>Total Payout Requests:</span>
                            <strong>{{ $stats['payout_requests'] ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Receipts -->
    @if(isset($shopOwner->receipts) && $shopOwner->receipts->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-receipt me-2"></i>Recent Receipts
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shopOwner->receipts->take(10) as $receipt)
                                <tr>
                                    <td>
                                        <strong>{{ $receipt->receipt_number }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $receipt->customer_name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $receipt->customer_phone ?? $receipt->customer_email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>₦{{ number_format($receipt->amount) }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'paid' => 'success',
                                                'partial' => 'warning',
                                                'pending' => 'secondary',
                                                'successful' => 'success',
                                                'failed' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$receipt->payment_status ?? 'pending'] ?? 'secondary' }}">
                                            {{ ucfirst($receipt->payment_status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $receipt->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $receipt->created_at->format('h:i A') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-receipt text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="text-muted">No Receipts Yet</h5>
                <p class="text-muted">This shop owner hasn't generated any receipts yet.</p>
            </div>
        </div>
    @endif
</div>

<style>
.avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.info-group {
    space-y: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item label {
    font-weight: 600;
    color: #495057;
    margin: 0;
    min-width: 120px;
}

.info-item span {
    text-align: right;
    color: #212529;
}

.metric-item {
    margin-bottom: 1rem;
}

.metric-item:last-child {
    margin-bottom: 0;
}

.stats-card {
    background: linear-gradient(135deg, var(--card-color, #007bff) 0%, var(--card-color-dark, #0056b3) 100%);
    color: white;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.stats-card.primary { --card-color: #007bff; --card-color-dark: #0056b3; }
.stats-card.success { --card-color: #28a745; --card-color-dark: #1e7e34; }
.stats-card.warning { --card-color: #ffc107; --card-color-dark: #d39e00; }
.stats-card.info { --card-color: #17a2b8; --card-color-dark: #117a8b; }
</style>

<script>
function refreshStats() {
    // Add loading indicator
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
    button.disabled = true;
    
    // Simulate refresh (you can replace with actual AJAX call)
    setTimeout(() => {
        location.reload();
    }, 1000);
}
</script>
@endsection
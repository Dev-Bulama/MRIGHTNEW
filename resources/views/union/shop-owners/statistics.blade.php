@extends('layouts.union')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('union.shop-owners.index') }}">Shop Owners</a></li>
    <li class="breadcrumb-item active">{{ $user->name }} - Statistics</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Shop Owner Statistics</h1>
            <p class="text-muted">Performance overview for {{ $user->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('union.shop-owners.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Shop Owners
            </a>
        </div>
    </div>

    <!-- Shop Owner Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="text-center">
                        <div class="avatar-circle mb-2" style="width: 80px; height: 80px; margin: 0 auto;">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-10">
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    
                    @if($user->shop)
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Shop Name:</strong><br>
                                {{ $user->shop->shop_name }}
                            </div>
                            <div class="col-md-4">
                                <strong>Location:</strong><br>
                                {{ $user->shop->state }}, {{ $user->shop->local_government }}
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong><br>
                                <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Receipts</h6>
                            <h3 class="mb-0 text-primary">{{ $stats['total_receipts'] }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-receipt fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Total Revenue</h6>
                            <h3 class="mb-0 text-success">₦{{ number_format($stats['total_revenue']) }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-naira-sign fa-2x"></i>
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
                            <h6 class="text-muted mb-1">This Month</h6>
                            <h3 class="mb-0 text-info">{{ $stats['this_month_receipts'] }}</h3>
                            <small class="text-muted">₦{{ number_format($stats['this_month_revenue']) }}</small>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-calendar fa-2x"></i>
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
                            <h6 class="text-muted mb-1">Success Rate</h6>
                            <h3 class="mb-0 text-warning">
                                {{ $stats['total_receipts'] > 0 ? round(($stats['successful_receipts'] / $stats['total_receipts']) * 100, 1) : 0 }}%
                            </h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Receipts -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Receipts</h5>
                </div>
                <div class="card-body">
                    @if($recentReceipts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                    @foreach($recentReceipts as $receipt)
                                        <tr>
                                            <td><strong>{{ $receipt->receipt_number }}</strong></td>
                                            <td>{{ $receipt->customer_name }}</td>
                                            <td>₦{{ number_format($receipt->amount) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $receipt->payment_status == 'paid' ? 'success' : ($receipt->payment_status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($receipt->payment_status) }}
                                                </span>
                                            </td>
                                            <td>{{ $receipt->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-receipt text-muted mb-3" style="font-size: 3rem;"></i>
                            <h6 class="text-muted">No Receipts Yet</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Performance Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Successful Receipts:</span>
                        <strong class="text-success">{{ $stats['successful_receipts'] }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Pending Receipts:</span>
                        <strong class="text-warning">{{ $stats['pending_receipts'] }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Failed Receipts:</span>
                        <strong class="text-danger">{{ $stats['failed_receipts'] }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Payouts:</span>
                        <strong>₦{{ number_format($totalPayouts) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}
</style>
@endsection
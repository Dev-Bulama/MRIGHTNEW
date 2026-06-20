@extends('layouts.app')

@section('title', 'Performance Dashboard')
@section('page-title', 'Performance Analytics')
@section('page-description', 'Track your shop performance and analytics')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-2">
                <i class="fas fa-chart-line text-primary me-2"></i>
                Performance Dashboard
            </h1>
            <p class="text-muted mb-0">
                Track your shop's performance and growth metrics
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="btn-group" role="group">
                <a href="{{ route('performance.export') }}" class="btn btn-outline-primary">
                    <i class="fas fa-download me-1"></i>Export Data
                </a>
                <!-- <a href="{{ route('receipt.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Generate Receipt
                </a> -->
            </div>
        </div>
    </div>

    <!-- Monthly Performance -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                        This Month Performance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-4 col-md-4 mb-3">
                            <div class="performance-metric">
                                <div class="d-flex align-items-center">
                                    <div class="metric-icon bg-primary">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="metric-value">{{ number_format($monthlyStats['receipts']) }}</h3>
                                        <p class="metric-label mb-0">Receipts Generated</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-4 col-md-4 mb-3">
                            <div class="performance-metric">
                                <div class="d-flex align-items-center">
                                    <div class="metric-icon bg-success">
                                        <i class="fas fa-naira-sign"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="metric-value">₦{{ number_format($monthlyStats['revenue'], 2) }}</h3>
                                        <p class="metric-label mb-0">Total Revenue</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-4 col-md-4 mb-3">
                            <div class="performance-metric">
                                <div class="d-flex align-items-center">
                                    <div class="metric-icon bg-info">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="metric-value">{{ number_format($monthlyStats['customers']) }}</h3>
                                        <p class="metric-label mb-0">Unique Customers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Yearly Performance -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-success me-2"></i>
                        Year {{ now()->year }} Performance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-4 col-md-4 mb-3">
                            <div class="performance-metric">
                                <div class="d-flex align-items-center">
                                    <div class="metric-icon bg-warning">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="metric-value">{{ number_format($yearlyStats['receipts']) }}</h3>
                                        <p class="metric-label mb-0">Total Receipts</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-4 col-md-4 mb-3">
                            <div class="performance-metric">
                                <div class="d-flex align-items-center">
                                    <div class="metric-icon bg-danger">
                                        <i class="fas fa-naira-sign"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="metric-value">₦{{ number_format($yearlyStats['revenue'], 2) }}</h3>
                                        <p class="metric-label mb-0">Annual Revenue</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-4 col-md-4 mb-3">
                            <div class="performance-metric">
                                <div class="d-flex align-items-center">
                                    <div class="metric-icon bg-secondary">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="metric-value">{{ number_format($yearlyStats['customers']) }}</h3>
                                        <p class="metric-label mb-0">Total Customers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history text-info me-2"></i>
                        Recent Receipts Activity
                    </h5>
                    <a href="{{ route('receipt.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-list me-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentReceipts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Generated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentReceipts as $receipt)
                                        <tr>
                                            <td>
                                                <strong>{{ $receipt->receipt_number }}</strong>
                                            </td>
                                            <td>{{ $receipt->customer_name }}</td>
                                            <td>{{ $receipt->phone_name }}</td>
                                            <td>
                                                <strong class="text-success">₦{{ number_format($receipt->amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColor = match($receipt->payment_gateway_status) {
                                                        'successful' => 'success',
                                                        'pending' => 'warning',
                                                        'failed' => 'danger',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusColor }}">
                                                    {{ ucfirst($receipt->payment_gateway_status ?? $receipt->payment_status) }}
                                                </span>
                                            </td>
                                            <td>{{ $receipt->created_at->format('M j, Y') }}</td>
                                            <td>
                                                <a href="{{ route('receipt.show', $receipt) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Performance Data</h5>
                            <p class="text-muted">Generate some receipts to see your performance metrics.</p>
                            <a href="{{ route('receipt.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Generate Your First Receipt
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.performance-metric {
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    height: 100%;
}

.metric-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.metric-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: #333;
}

.metric-label {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 500;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 10px 10px 0 0 !important;
}

@media (max-width: 768px) {
    .metric-value {
        font-size: 1.5rem;
    }
    
    .metric-icon {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .performance-metric {
        padding: 1rem;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-group .btn {
        width: 100%;
    }
}
</style>
@endsection
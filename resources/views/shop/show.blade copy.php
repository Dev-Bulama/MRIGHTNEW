@extends('layouts.app')

@section('title', 'Shop Profile - ' . $shop->shop_name)
@section('page-title', 'Shop Management')
@section('page-description', 'View and manage your shop profile and performance')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-2">
                <i class="fas fa-store text-primary me-2"></i>
                {{ $shop->shop_name }}
            </h1>
            <p class="text-muted mb-0">
                Shop Profile & Performance Overview
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="btn-group" role="group">
                <a href="{{ route('shop.edit', $shop) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i>Edit Profile
                </a>
                <a href="{{ route('receipt.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Generate Receipt
                </a>
            </div>
        </div>
    </div>

    <!-- Shop Status Alert -->
    <div class="row mb-4">
        <div class="col-12">
            @if($shop->approved)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Shop Approved!</strong> Your shop is active and you can generate receipts for customers.
                    <small class="text-muted d-block mt-1">
                        Approved on {{ $shop->approved_at ? $shop->approved_at->format('M j, Y g:i A') : 'N/A' }}
                    </small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @else
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-clock me-2"></i>
                    <strong>Approval Pending!</strong> Your shop profile is under review by admin team.
                    <small class="text-muted d-block mt-1">
                        Submitted on {{ $shop->created_at->format('M j, Y g:i A') }}
                    </small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Shop Performance Stats -->
    @if($shop->approved)
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card dashboard-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="dashboard-icon bg-primary">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="card-subtitle text-muted mb-1">Total Receipts</h6>
                                <h3 class="card-title mb-0">{{ number_format($stats['total_receipts']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card dashboard-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="dashboard-icon bg-success">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="card-subtitle text-muted mb-1">This Month</h6>
                                <h3 class="card-title mb-0">{{ number_format($stats['monthly_receipts']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card dashboard-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="dashboard-icon bg-warning">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="card-subtitle text-muted mb-1">This Year</h6>
                                <h3 class="card-title mb-0">{{ number_format($stats['yearly_receipts']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card dashboard-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="dashboard-icon bg-info">
                                <i class="fas fa-naira-sign"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="card-subtitle text-muted mb-1">Commission Earned</h6>
                                <h3 class="card-title mb-0">₦{{ number_format($stats['total_earnings'], 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Shop Information -->
    <div class="row">
        <!-- Business Details -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-building text-primary me-2"></i>
                        Business Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($shop->logo)
                            <div class="col-12 text-center mb-3">
                                <img src="{{ $shop->logo_url }}" alt="{{ $shop->shop_name }}" 
                                     class="img-fluid rounded" style="max-height: 100px;">
                            </div>
                        @endif
                    </div>
                    
                    <dl class="row">
                        <dt class="col-sm-4">Shop Name:</dt>
                        <dd class="col-sm-8">{{ $shop->shop_name }}</dd>
                        
                        <dt class="col-sm-4">Owner:</dt>
                        <dd class="col-sm-8">{{ $shop->owner_full_name }}</dd>
                        
                        <dt class="col-sm-4">Business Email:</dt>
                        <dd class="col-sm-8">
                            <a href="mailto:{{ $shop->business_email }}" class="text-decoration-none">
                                {{ $shop->business_email }}
                            </a>
                        </dd>
                        
                        <dt class="col-sm-4">Primary Phone:</dt>
                        <dd class="col-sm-8">
                            <a href="tel:{{ $shop->business_phone_1 }}" class="text-decoration-none">
                                {{ $shop->business_phone_1 }}
                            </a>
                        </dd>
                        
                        @if($shop->business_phone_2)
                            <dt class="col-sm-4">Secondary Phone:</dt>
                            <dd class="col-sm-8">
                                <a href="tel:{{ $shop->business_phone_2 }}" class="text-decoration-none">
                                    {{ $shop->business_phone_2 }}
                                </a>
                            </dd>
                        @endif
                        
                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($shop->approved)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Approved
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="fas fa-clock me-1"></i>Pending Approval
                                </span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Location & Contact -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt text-success me-2"></i>
                        Location & Contact
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Address:</dt>
                        <dd class="col-sm-8">{{ $shop->business_address }}</dd>
                        
                        <dt class="col-sm-4">Local Government:</dt>
                        <dd class="col-sm-8">{{ $shop->local_government }}</dd>
                        
                        <dt class="col-sm-4">State:</dt>
                        <dd class="col-sm-8">{{ $shop->state }}</dd>
                        
                        <dt class="col-sm-4">Country:</dt>
                        <dd class="col-sm-8">{{ $shop->country }}</dd>
                        
                        <dt class="col-sm-4">Registered:</dt>
                        <dd class="col-sm-8">
                            {{ $shop->created_at->format('M j, Y') }}
                            <br>
                            <small class="text-muted">{{ $shop->created_at->diffForHumans() }}</small>
                        </dd>
                    </dl>
                    
                    @if($shop->terms_and_conditions)
                        <div class="mt-3">
                            <h6 class="fw-bold">Terms & Conditions:</h6>
                            <p class="text-muted small">{{ $shop->terms_and_conditions }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Receipts -->
    @if($shop->approved && isset($stats['recent_receipts']) && $stats['recent_receipts']->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history text-info me-2"></i>
                            Recent Receipts
                        </h5>
                        <a href="{{ route('receipt.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-1"></i>View All
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['recent_receipts'] as $receipt)
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
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    @if($shop->approved)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt text-warning me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <a href="{{ route('receipt.create') }}" class="quick-action-card card text-decoration-none h-100">
                                    <div class="card-body text-center p-3">
                                        <i class="fas fa-plus-circle text-primary fs-1 mb-2"></i>
                                        <h6 class="fw-medium mb-0">New Receipt</h6>
                                        <small class="text-muted">Generate receipt</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="{{ route('receipt.resale') }}" class="quick-action-card card text-decoration-none h-100">
                                    <div class="card-body text-center p-3">
                                        <i class="fas fa-sync-alt text-info fs-1 mb-2"></i>
                                        <h6 class="fw-medium mb-0">Update Resale</h6>
                                        <small class="text-muted">Mark as resale</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="{{ route('search.index') }}" class="quick-action-card card text-decoration-none h-100">
                                    <div class="card-body text-center p-3">
                                        <i class="fas fa-search text-warning fs-1 mb-2"></i>
                                        <h6 class="fw-medium mb-0">Search Phone</h6>
                                        <small class="text-muted">Verify receipt</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="{{ route('performance.index') }}" class="quick-action-card card text-decoration-none h-100">
                                    <div class="card-body text-center p-3">
                                        <i class="fas fa-chart-line text-success fs-1 mb-2"></i>
                                        <h6 class="fw-medium mb-0">Performance</h6>
                                        <small class="text-muted">View analytics</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.dashboard-card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 10px;
    transition: transform 0.2s ease;
}

.dashboard-card:hover {
    transform: translateY(-2px);
}

.dashboard-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.quick-action-card {
    border: 2px solid transparent;
    transition: all 0.3s ease;
    text-decoration: none !important;
}

.quick-action-card:hover {
    border-color: #007bff;
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
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

dl.row dt {
    font-weight: 600;
    color: #495057;
}

dl.row dd {
    color: #6c757d;
}

.btn-group .btn {
    border-radius: 5px !important;
    margin: 0 2px;
}

@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-group .btn {
        width: 100%;
        margin: 0;
    }
    
    .dashboard-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }
    
    .quick-action-card .fs-1 {
        font-size: 2rem !important;
    }
}

@media (max-width: 576px) {
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>
@endsection
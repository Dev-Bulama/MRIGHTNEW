@extends('layouts.app')

@section('title', 'User Details')
@section('page-title', 'User Details')
@section('page-description', 'View and manage user information')

@section('content')

<!-- Include admin alerts -->
@include('partials.alerts')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">User Details</h1>
            <p class="text-muted">View and manage user information</p>
        </div>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>
    </div>

    <div class="row">
        <!-- User Information -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>User Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-large mx-auto mb-3">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'secondary' }} mb-2">
                            {{ ucfirst($user->status) }}
                        </span>
                        <div>
                            <span class="badge bg-primary">{{ ucfirst($user->user_type) }}</span>
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="info-item">
                            <label>Email:</label>
                            <span>{{ $user->email }}</span>
                        </div>
                        <div class="info-item">
                            <label>Phone:</label>
                            <span>{{ $user->phone_number ?? 'Not provided' }}</span>
                        </div>
                        @if($user->secondary_phone)
                            <div class="info-item">
                                <label>Secondary Phone:</label>
                                <span>{{ $user->secondary_phone }}</span>
                            </div>
                        @endif
                        <div class="info-item">
                            <label>Registration:</label>
                            <span>{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="info-item">
                            <label>Last Login:</label>
                            <span>
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->diffForHumans() }}
                                @else
                                    Never
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <label>Email Verified:</label>
                            <span>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-warning">Unverified</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 d-grid gap-2">
                        @if($user->status === 'active')
                            <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100" 
                                        onclick="return confirm('Are you sure you want to suspend this user?')">
                                    <i class="fas fa-user-slash me-2"></i>Suspend User
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-user-check me-2"></i>Activate User
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-edit me-2"></i>Edit User
                        </a>
                        
                        <a href="{{ route('admin.users.contact', $user) }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-envelope me-2"></i>Send Message
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance & Details -->
        <div class="col-lg-8">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-primary">{{ $stats['total_receipts'] ?? 0 }}</h3>
                            <small class="text-muted">Total Receipts</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-success">{{ $stats['successful_receipts'] ?? 0 }}</h3>
                            <small class="text-muted">Successful</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-info">₦{{ number_format($stats['total_revenue'] ?? 0) }}</h3>
                            <small class="text-muted">Total Revenue</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="text-warning">{{ $stats['monthly_receipts'] ?? 0 }}</h3>
                            <small class="text-muted">This Month</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shop Information -->
            @if($user->shop)
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
                                        <span>{{ $user->shop->shop_name }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Business Type:</label>
                                        <span>{{ $user->shop->business_type ?? 'Not specified' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>State:</label>
                                        <span>{{ $user->shop->state }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>LGA:</label>
                                        <span>{{ $user->shop->local_government }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <div class="info-item">
                                        <label>Address:</label>
                                        <span>{{ $user->shop->business_address }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Shop Status:</label>
                                        <span>
                                            @if($user->shop->approved)
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <label>Registered:</label>
                                        <span>{{ $user->shop->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Union Information (if applicable) -->
            @if($user->user_type === 'union')
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>Union Assignment
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Assigned States:</strong>
                                <div class="mt-1">
                                    @if($user->assigned_states)
                                        @foreach($user->assigned_states as $state)
                                            <span class="badge bg-primary me-1">{{ $state }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">None assigned</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <strong>Assigned LGAs:</strong>
                                <div class="mt-1">
                                    @if($user->assigned_lgas)
                                        @foreach($user->assigned_lgas as $lga)
                                            <span class="badge bg-info me-1">{{ $lga }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">All LGAs in assigned states</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($user->assigned_at)
                            <div class="mt-3">
                                <small class="text-muted">
                                    Assigned on {{ $user->assigned_at->format('M d, Y') }}
                                    @if($user->assignedBy)
                                        by {{ $user->assignedBy->name }}
                                    @endif
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Recent Activity -->
            @if($user->receipts && $user->receipts->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>Recent Receipts
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
                                    @foreach($user->receipts->take(5) as $receipt)
                                        <tr>
                                            <td><strong>{{ $receipt->receipt_number }}</strong></td>
                                            <td>{{ $receipt->customer_name ?? 'N/A' }}</td>
                                            <td>₦{{ number_format($receipt->amount) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $receipt->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($receipt->payment_status ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td>{{ $receipt->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
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
    align-items: flex-start;
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
    flex: 1;
}
</style>
@endsection
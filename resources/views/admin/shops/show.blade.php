@extends('layouts.app')

@section('title', 'Shop Details - ' . $shop->shop_name)
@section('page-title', 'Shop Details')
@section('page-description', 'Detailed view of ' . $shop->shop_name)

@push('styles')
<style>
    .shop-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .shop-logo {
        width: 100px;
        height: 100px;
        border-radius: 15px;
        border: 4px solid rgba(255,255,255,0.2);
        object-fit: cover;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        height: 100%;
    }
    
    .stat-item {
        text-align: center;
        padding: 1rem;
        border-radius: 10px;
        background: #f8f9fa;
        margin-bottom: 1rem;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .recent-activity-item {
        padding: 1rem;
        border-left: 4px solid #007bff;
        margin-bottom: 1rem;
        background: #f8f9fa;
        border-radius: 0 8px 8px 0;
    }
    
    .action-button {
        min-width: 120px;
        margin: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Shop Header -->
    <div class="shop-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    @if($shop->logo)
                        <img src="{{ Storage::url($shop->logo) }}" 
                             class="shop-logo me-4" alt="Shop Logo">
                    @else
                        <div class="shop-logo me-4 bg-light bg-opacity-25 d-flex align-items-center justify-content-center">
                            <i class="fas fa-store fa-3x text-white"></i>
                        </div>
                    @endif
                    
                    <div>
                        <h1 class="mb-2">{{ $shop->shop_name }}</h1>
                        <p class="mb-1 opacity-75">
                            <i class="fas fa-envelope me-2"></i>{{ $shop->business_email }}
                        </p>
                        <p class="mb-1 opacity-75">
                            <i class="fas fa-phone me-2"></i>{{ $shop->business_phone_1 }}
                        </p>
                        <p class="mb-0 opacity-75">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $shop->state }}, {{ $shop->country }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 text-end">
                <div class="mb-3">
                    @if($shop->approved)
                        <span class="badge bg-success fs-5 px-3 py-2">
                            <i class="fas fa-check me-2"></i>Approved
                        </span>
                    @elseif($shop->status === 'rejected')
                        <span class="badge bg-danger fs-5 px-3 py-2">
                            <i class="fas fa-times me-2"></i>Rejected
                        </span>
                    @else
                        <span class="badge bg-warning fs-5 px-3 py-2">
                            <i class="fas fa-clock me-2"></i>Pending
                        </span>
                    @endif
                </div>
                
                <div>
                    <small class="opacity-75">
                        <i class="fas fa-calendar me-1"></i>
                        Registered {{ $shop->created_at->format('M j, Y') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Quick Actions</h5>
                        
                        <div class="btn-group flex-wrap">
                            @if(!$shop->approved && $shop->status !== 'rejected')
                                <button type="button" class="btn btn-success action-button" 
                                        onclick="approveShop({{ $shop->id }})">
                                    <i class="fas fa-check me-2"></i>Approve Shop
                                </button>
                                <button type="button" class="btn btn-danger action-button" 
                                        onclick="rejectShop({{ $shop->id }})">
                                    <i class="fas fa-times me-2"></i>Reject Shop
                                </button>
                            @elseif($shop->approved)
                                <button type="button" class="btn btn-warning action-button" 
                                        onclick="suspendShop({{ $shop->id }})">
                                    <i class="fas fa-pause me-2"></i>Suspend Shop
                                </button>
                            @else
                                <button type="button" class="btn btn-info action-button" 
                                        onclick="reinstateShop({{ $shop->id }})">
                                    <i class="fas fa-undo me-2"></i>Reinstate Shop
                                </button>
                            @endif
                            
                            <button type="button" class="btn btn-outline-primary action-button" 
                                    onclick="contactOwner()">
                                <i class="fas fa-envelope me-2"></i>Contact Owner
                            </button>
                            
                            <button type="button" class="btn btn-outline-secondary action-button" 
                                    onclick="exportShopData()">
                                <i class="fas fa-download me-2"></i>Export Data
                            </button>
                            
                            <a href="{{ route('admin.shops.index') }}" class="btn btn-outline-dark action-button">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shop Information Grid -->
    <div class="row">
        <!-- Basic Information -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="info-card">
                <h5 class="text-primary mb-3">
                    <i class="fas fa-info-circle me-2"></i>Basic Information
                </h5>
                
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="fw-bold text-muted" width="40%">Shop Name:</td>
                        <td>{{ $shop->shop_name }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Owner:</td>
                        <td>{{ $shop->owner_full_name }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Email:</td>
                        <td>{{ $shop->business_email }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Phone:</td>
                        <td>{{ $shop->business_phone_1 }}</td>
                    </tr>
                    @if($shop->business_phone_2)
                    <tr>
                        <td class="fw-bold text-muted">Alt. Phone:</td>
                        <td>{{ $shop->business_phone_2 }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="fw-bold text-muted">Registered:</td>
                        <td>{{ $shop->created_at->format('M j, Y g:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Location Information -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="info-card">
                <h5 class="text-success mb-3">
                    <i class="fas fa-map-marker-alt me-2"></i>Location Details
                </h5>
                
                <table class="table table-borderless mb-3">
                    <tr>
                        <td class="fw-bold text-muted" width="40%">Country:</td>
                        <td>{{ $shop->country }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">State:</td>
                        <td>{{ $shop->state }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">LGA:</td>
                        <td>{{ $shop->local_government }}</td>
                    </tr>
                </table>
                
                <div class="mb-0">
                    <p class="fw-bold text-muted mb-2">Full Address:</p>
                    <address class="mb-0 text-muted">
                        {{ $shop->business_address }}
                    </address>
                </div>
            </div>
        </div>

        <!-- Owner Account Information -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="info-card">
                <h5 class="text-info mb-3">
                    <i class="fas fa-user me-2"></i>Owner Account
                </h5>
                
                @if($shop->user)
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Name:</td>
                            <td>{{ $shop->user->first_name }} {{ $shop->user->last_name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Email:</td>
                            <td>{{ $shop->user->email }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Phone:</td>
                            <td>{{ $shop->user->phone_number ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">User Type:</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ ucwords(str_replace('_', ' ', $shop->user->user_type)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status:</td>
                            <td>
                                @if($shop->user->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($shop->user->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Joined:</td>
                            <td>{{ $shop->user->created_at->format('M j, Y') }}</td>
                        </tr>
                    </table>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-user-slash text-muted mb-2" style="font-size: 2rem;"></i>
                        <p class="text-muted mb-0">Owner account not found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Performance Statistics -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="info-card">
                <h5 class="text-warning mb-4">
                    <i class="fas fa-chart-bar me-2"></i>Performance Statistics
                </h5>
                
                @php
                    $totalReceipts = $shop->receipts()->count();
                    $thisMonthReceipts = $shop->receipts()->whereMonth('created_at', now()->month)->count();
                    $lastMonthReceipts = $shop->receipts()->whereMonth('created_at', now()->subMonth()->month)->count();
                    $thisYearReceipts = $shop->receipts()->whereYear('created_at', now()->year)->count();
                    $avgPerMonth = $totalReceipts > 0 ? round($totalReceipts / max(1, $shop->created_at->diffInMonths(now()) + 1), 1) : 0;
                    
                    $growthRate = 0;
                    if($lastMonthReceipts > 0) {
                        $growthRate = round((($thisMonthReceipts - $lastMonthReceipts) / $lastMonthReceipts) * 100, 1);
                    } elseif($thisMonthReceipts > 0) {
                        $growthRate = 100;
                    }
                @endphp
                
                <div class="row">
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="stat-item">
                            <div class="stat-number text-primary">{{ $totalReceipts }}</div>
                            <div class="text-muted small">Total Receipts</div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="stat-item">
                            <div class="stat-number text-success">{{ $thisMonthReceipts }}</div>
                            <div class="text-muted small">This Month</div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="stat-item">
                            <div class="stat-number text-info">{{ $thisYearReceipts }}</div>
                            <div class="text-muted small">This Year</div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="stat-item">
                            <div class="stat-number text-warning">{{ $avgPerMonth }}</div>
                            <div class="text-muted small">Monthly Avg</div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="stat-item">
                            <div class="stat-number {{ $growthRate >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $growthRate >= 0 ? '+' : '' }}{{ $growthRate }}%
                            </div>
                            <div class="text-muted small">Growth Rate</div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 col-6">
                        <div class="stat-item">
                            <div class="stat-number text-secondary">₦0</div>
                            <div class="text-muted small">Total Earnings</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Additional Information -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-8 mb-4">
            <div class="info-card">
                <h5 class="text-purple mb-3">
                    <i class="fas fa-history me-2"></i>Recent Activity
                </h5>
                
                @php
                    $recentReceipts = $shop->receipts()
                        ->with('user')
                        ->latest()
                        ->take(10)
                        ->get();
                @endphp
                
                @if($recentReceipts->count() > 0)
                    <div style="max-height: 400px; overflow-y: auto;">
                        @foreach($recentReceipts as $receipt)
                            <div class="recent-activity-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-receipt text-primary me-2"></i>
                                            Receipt Generated
                                        </h6>
                                        <p class="mb-1">
                                            Receipt #<strong>{{ $receipt->receipt_number }}</strong>
                                            @if($receipt->customer_name)
                                                for <strong>{{ $receipt->customer_name }}</strong>
                                            @endif
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $receipt->created_at->format('M j, Y g:i A') }}
                                            ({{ $receipt->created_at->diffForHumans() }})
                                        </small>
                                    </div>
                                    
                                    <span class="badge bg-success">Completed</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mb-2">No Recent Activity</h5>
                        <p class="text-muted">This shop hasn't generated any receipts yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Additional Information -->
        <div class="col-lg-4 mb-4">
            <div class="info-card">
                <h5 class="text-dark mb-3">
                    <i class="fas fa-file-alt me-2"></i>Additional Information
                </h5>
                
                @if($shop->terms_and_conditions)
                    <div class="mb-4">
                        <h6 class="fw-bold text-muted mb-2">Terms & Conditions:</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0 small">{{ $shop->terms_and_conditions }}</p>
                        </div>
                    </div>
                @endif
                
                <!-- System Information -->
                <div class="mb-3">
                    <h6 class="fw-bold text-muted mb-2">System Information:</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Shop ID:</td>
                            <td><code>{{ $shop->id }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Created:</td>
                            <td>{{ $shop->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Updated:</td>
                            <td>{{ $shop->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @if($shop->approved_at)
                        <tr>
                            <td class="text-muted">Approved:</td>
                            <td>{{ \Carbon\Carbon::parse($shop->approved_at)->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                
                <!-- Quick Stats -->
                <div>
                    <h6 class="fw-bold text-muted mb-2">Quick Stats:</h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-primary bg-opacity-10 p-2 rounded text-center">
                                <small class="text-primary fw-bold">{{ $shop->created_at->diffInDays(now()) }}</small>
                                <br>
                                <small class="text-muted">Days Active</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-success bg-opacity-10 p-2 rounded text-center">
                                <small class="text-success fw-bold">{{ $totalReceipts }}</small>
                                <br>
                                <small class="text-muted">Receipts</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Shop management functions
function approveShop(shopId) {
    if (confirm('Are you sure you want to approve this shop?')) {
        performShopAction(shopId, 'approve', 'Approving shop...');
    }
}

function rejectShop(shopId) {
    const reason = prompt('Please provide a reason for rejection (optional):');
    if (reason !== null) {
        performShopAction(shopId, 'reject', 'Rejecting shop...', { reason: reason });
    }
}

function suspendShop(shopId) {
    if (confirm('Are you sure you want to suspend this shop?')) {
        performShopAction(shopId, 'suspend', 'Suspending shop...');
    }
}

function reinstateShop(shopId) {
    if (confirm('Are you sure you want to reinstate this shop?')) {
        performShopAction(shopId, 'reinstate', 'Reinstating shop...');
    }
}

function performShopAction(shopId, action, loadingMessage, extraData = {}) {
    showLoading(loadingMessage);
    
    const url = `/admin/shops/${shopId}/${action}`;
    const data = {
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        ...extraData
    };
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message || 'Operation failed');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'An error occurred. Please try again.');
        console.error('Error:', error);
    });
}

function contactOwner() {
    // Implementation for contacting shop owner
    showAlert('info', 'Contact owner functionality coming soon!');
}

function exportShopData() {
    // Implementation for exporting shop data
    showAlert('info', 'Export functionality coming soon!');
}

// Utility functions
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

function showLoading(message) {
    let loader = document.getElementById('globalLoader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'globalLoader';
        loader.innerHTML = `
            <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                 style="background: rgba(0,0,0,0.5); z-index: 10000;">
                <div class="bg-white p-4 rounded text-center">
                    <div class="spinner-border text-primary mb-3"></div>
                    <p class="mb-0" id="loadingMessage">${message}</p>
                </div>
            </div>
        `;
        document.body.appendChild(loader);
    } else {
        document.getElementById('loadingMessage').textContent = message;
        loader.style.display = 'block';
    }
}

function hideLoading() {
    const loader = document.getElementById('globalLoader');
    if (loader) {
        loader.style.display = 'none';
    }
}
</script>
@endpush
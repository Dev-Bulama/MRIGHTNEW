@extends('layouts.app')

@section('title', 'Shop Profile - ' . $shop->shop_name)
@section('page-title', 'Shop Management')
@section('page-description', 'View and manage your shop profile and performance')

@push('styles')
<style>
.shop-profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 2rem;
}

.shop-logo {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 15px;
    border: 4px solid rgba(255,255,255,0.2);
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #0d8abc;
    margin-bottom: 0.5rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin-bottom: 1rem;
}

.recent-receipts-table {
    max-height: 400px;
    overflow-y: auto;
}

.quick-action-btn {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    display: block;
    height: 100%;
}

.quick-action-btn:hover {
    border-color: #0d8abc;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(13, 138, 188, 0.15);
    color: #0d8abc;
}

.shop-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

@media (max-width: 768px) {
    .shop-logo {
        width: 80px;
        height: 80px;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .shop-info-grid {
        grid-template-columns: 1fr;
    }
}
.disabled-link {
  pointer-events: none; /* Prevents all pointer events on the element */
  cursor: default; /* Changes the cursor to indicate it's not clickable */
  opacity: 0.6; /* Optional: Visually dim the link to show it's disabled */
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Enhanced Shop Profile Header -->
    <div class="shop-profile-header">
        <div class="p-4">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    @if($shop->logo)
                        <img src="{{ Storage::url($shop->logo) }}" 
                             alt="{{ $shop->shop_name }}" 
                             class="shop-logo">
                    @else
                        <div class="shop-logo d-flex align-items-center justify-content-center bg-white bg-opacity-20 mx-auto">
                            <i class="fas fa-store fs-1 text-white"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-7">
                    <h1 class="h2 fw-bold mb-2">{{ $shop->shop_name }}</h1>
                    <div class="shop-info-grid text-white-50">
                        <div>
                            <i class="fas fa-user me-2"></i>{{ $shop->owner_full_name }}
                        </div>
                        <div>
                            <i class="fas fa-envelope me-2"></i>{{ $shop->business_email }}
                        </div>
                        <div>
                            <i class="fas fa-phone me-2"></i>{{ $shop->business_phone_1 }}
                        </div>
                        <div>
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $shop->local_government }}, {{ $shop->state }}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    @if($shop->approved)
                        <div class="badge bg-success fs-6 p-2 mb-3">
                            <i class="fas fa-check-circle me-1"></i>Active Shop
                        </div>
                        <div class="d-grid">
                            <small class="text-white-50">Approved on</small>
                            <span class="fw-medium">{{ $shop->approved_at ? $shop->approved_at->format('M j, Y') : 'N/A' }}</span>
                        </div>
                    @else
                        <div class="badge bg-warning fs-6 p-2 mb-3">
                            <i class="fas fa-clock me-1"></i>Pending Approval
                        </div>
                        <div class="d-grid">
                            <small class="text-white-50">Submitted on</small>
                            <span class="fw-medium">{{ $shop->created_at->format('M j, Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Shop Status Alerts -->
    @if(!$shop->approved)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-clock me-3 mt-1"></i>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading">Shop Approval Pending</h5>
                            <p class="mb-3">Your shop profile is currently under review by our admin team. You'll be notified once it's approved.</p>
                            <div class="d-grid gap-2 d-md-flex">
                                <a href="{{ route('shop.edit', $shop) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit me-1"></i>Edit Profile
                                </a>
                                <a href="{{ route('help.setup') }}" class="btn btn-warning">
                                    <i class="fas fa-question-circle me-1"></i>Setup Guide
                                </a>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Enhanced Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-number" data-stat="total_receipts">{{ $stats['total_receipts'] ?? 0 }}</div>
                <p class="text-muted mb-0 fw-medium">Total Receipts</p>
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>All time
                </small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-calendar-month"></i>
                </div>
                <div class="stat-number" data-stat="monthly_receipts">{{ $stats['monthly_receipts'] ?? 0 }}</div>
                <p class="text-muted mb-0 fw-medium">This Month</p>
                <small class="text-info">
                    <i class="fas fa-calendar me-1"></i>{{ now()->format('M Y') }}
                </small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-calendar-year"></i>
                </div>
                <div class="stat-number" data-stat="yearly_receipts">{{ $stats['yearly_receipts'] ?? 0 }}</div>
                <p class="text-muted mb-0 fw-medium">This Year</p>
                <small class="text-warning">
                    <i class="fas fa-chart-line me-1"></i>{{ now()->format('Y') }}
                </small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-naira-sign"></i>
                </div>
                <div class="stat-number" data-stat="total_earnings">₦{{ number_format($stats['total_earnings'] ?? 0, 2) }}</div>
                <p class="text-muted mb-0 fw-medium">Total Earnings</p>
                <small class="text-success">
                    <i class="fas fa-coins me-1"></i>Commission earned
                </small>
            </div>
        </div>
    </div>

    <!-- Enhanced Quick Actions -->
    @if($shop->approved)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt me-2 text-primary"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- <div class="col-lg-3 col-md-6">
                                <a href="{{ route('receipt.create') }}" class="quick-action-btn">
                                    <div class="text-center">
                                        <i class="fas fa-plus-circle text-primary fs-1 mb-3"></i>
                                        <h6 class="fw-medium mb-1">Generate Receipt</h6>
                                        <small class="text-muted">Create new digital receipt</small>
                                    </div>
                                </a>
                            </div> -->
                            <div class="col-lg-6 col-md-6">
                                <a href="{{ route('receipt.index') }}" class="quick-action-btn">
                                    <div class="text-center">
                                        <i class="fas fa-list text-success fs-1 mb-3"></i>
                                        <h6 class="fw-medium mb-1">View Receipts</h6>
                                        <small class="text-muted">Manage all receipts</small>
                                    </div>
                                </a>
                            </div>
                            <!-- <div class="col-lg-3 col-md-6">
                                <a href="{{ route('receipt.resale') }}" class="quick-action-btn">
                                    <div class="text-center">
                                        <i class="fas fa-sync-alt text-info fs-1 mb-3"></i>
                                        <h6 class="fw-medium mb-1">Update Resale</h6>
                                        <small class="text-muted">Transfer ownership</small>
                                    </div>
                                </a>
                            </div> -->
                            <div class="col-lg-6 col-md-6">
                                <a href="{{ route('performance.index') }}" class="quick-action-btn">
                                    <div class="text-center">
                                        <i class="fas fa-chart-line text-warning fs-1 mb-3"></i>
                                        <h6 class="fw-medium mb-1">Performance</h6>
                                        <small class="text-muted">Analytics & reports</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Shop Information & Recent Receipts -->
    <div class="row">
        <!-- Shop Details -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>Shop Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium text-muted">Business Address</label>
                        <p class="mb-0">{{ $shop->business_address }}</p>
                    </div>
                    
                    @if($shop->business_phone_2)
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted">Secondary Phone</label>
                            <p class="mb-0">{{ $shop->business_phone_2 }}</p>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium text-muted">Registration Date</label>
                        <p class="mb-0">{{ $shop->created_at->format('F j, Y g:i A') }}</p>
                    </div>
                    
                    @if($shop->terms_and_conditions)
                        <div class="mb-3">
                            <label class="form-label fw-medium text-muted">Terms & Conditions</label>
                            <p class="mb-0 small">{{ Str::limit($shop->terms_and_conditions, 150) }}</p>
                        </div>
                    @endif
                    
                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('shop.edit', $shop) }}" class="btn btn-outline-primary disabled-link">
                            <i class="fas fa-edit me-2"></i>Edit Shop Profile
                        </a>
                        @if($shop->approved)
                            <!-- <a href="{{ route('receipt.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Generate Receipt
                            </a> -->
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Receipts -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2 text-primary"></i>Recent Receipts
                    </h5>
                    <a href="{{ route('receipt.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body recent-receipts-table">
                    @if(isset($stats['recent_receipts']) && $stats['recent_receipts']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Customer</th>
                                        <th>Phone Model</th>
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
                                                <span class="fw-medium">{{ $receipt->receipt_number }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-medium">{{ $receipt->customer_name }}</span>
                                                    <small class="text-muted">{{ $receipt->customer_phone }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span>{{ $receipt->phone_name }}</span>
                                                    <small class="text-muted">{{ $receipt->phone_serial_number }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">₦{{ number_format($receipt->amount, 2) }}</span>
                                            </td>
                                            <td>
                                                @if($receipt->payment_status === 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($receipt->payment_status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($receipt->payment_status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $receipt->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                           <div class="btn-group btn-group-sm">
    <a href="{{ route('receipt.show', $receipt) }}" 
       class="btn btn-outline-primary btn-sm" 
       title="View Receipt">
        <i class="fas fa-eye"></i>
    </a>
    @if(method_exists('App\Http\Controllers\ReceiptController', 'download'))
        <a href="{{ route('receipt.download', $receipt) }}" 
           class="btn btn-outline-success btn-sm" 
           title="Download PDF">
            <i class="fas fa-download"></i>
        </a>
    @else
        <button class="btn btn-outline-success btn-sm" 
                onclick="showToast('Download feature coming soon!', 'info')" 
                title="Download PDF">
            <i class="fas fa-download"></i>
        </button>
    @endif
</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="text-muted">No receipts generated yet</h5>
                            <p class="text-muted mb-3">Start generating digital receipts for your customers</p>
                            @if($shop->approved)
                                <a href="{{ route('receipt.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Generate First Receipt
                                </a>
                            @else
                                <p class="text-muted"><em>Shop must be approved before generating receipts</em></p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($shop->approved)
        <!-- Additional Actions Row -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs me-2 text-primary"></i>Shop Management
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <!-- <a href="{{ route('search.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>Search Phone Database
                            </a> -->
                            <a href="{{ route('performance.index') }}" class="btn btn-outline-success">
                                <i class="fas fa-chart-bar me-2"></i>View Performance Analytics
                            </a>
                            <a href="{{ route('receipt.index') }}" class="btn btn-outline-info">
                                <i class="fas fa-list me-2"></i>Manage All Receipts
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-question-circle me-2 text-primary"></i>Need Help?
                        </h5>
                    </div>
                    <!--<div class="card-body">-->
                    <!--    <p class="text-muted mb-3">Get assistance with shop management and receipt generation</p>-->
                    <!--    <div class="d-grid gap-2">-->
                            <!-- <a href="{{ route('help.setup') }}" class="btn btn-outline-warning">
                    <!--            <i class="fas fa-book me-2"></i>Setup Guide-->
                    <!--        </a> -->-->
                    <!--        <a href="{{ route('help.contact') }}" class="btn btn-outline-secondary">-->
                    <!--            <i class="fas fa-headset me-2"></i>Contact Support-->
                    <!--        </a>-->
                    <!--    </div>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh shop stats every 3 minutes
    setInterval(refreshShopStats, 180000);
    
    // Add copy functionality for shop details
    document.querySelectorAll('[data-copy]').forEach(element => {
        element.addEventListener('click', function() {
            navigator.clipboard.writeText(this.dataset.copy);
            showToast('Copied to clipboard!', 'success');
        });
    });
    
    // Smooth scroll for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

function refreshShopStats() {
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.stats) {
            Object.keys(data.stats).forEach(key => {
                const element = document.querySelector(`[data-stat="${key}"]`);
                if (element) {
                    // Animate counter update
                    const currentValue = parseInt(element.textContent.replace(/[^\d]/g, ''));
                    const newValue = data.stats[key];
                    if (currentValue !== newValue) {
                        animateCounter(element, currentValue, newValue);
                    }
                }
            });
        }
    })
    .catch(error => console.error('Error refreshing stats:', error));
}

function animateCounter(element, start, end) {
    const duration = 1000;
    const increment = (end - start) / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        
        if (element.textContent.includes('₦')) {
            element.textContent = '₦' + Math.floor(current).toLocaleString();
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 16);
}

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}
</script>
@endpush
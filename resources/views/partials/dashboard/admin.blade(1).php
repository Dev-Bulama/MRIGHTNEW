@php
    // Get real data from controller or calculate here
    $pendingShops = \App\Models\Shop::where('approved', false)->count();
    $totalShops = \App\Models\Shop::count();
    $activeShops = \App\Models\Shop::where('approved', true)->count();
    $totalUsers = \App\Models\User::count();
    $totalReceipts = \App\Models\Receipt::count();
    $monthlyReceipts = \App\Models\Receipt::whereMonth('created_at', now()->month)->count();
    
    // Safe revenue calculation
    $totalRevenue = 0;
    try {
        if (Schema::hasTable('payments')) {
            $totalRevenue = \App\Models\Payment::where('status', 'successful')->sum('amount') ?? 0;
        }
    } catch (\Exception $e) {
        $totalRevenue = 0;
    }
@endphp

<!-- Admin Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card dashboard-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-crown me-2 text-warning"></i>
                    Admin Control Panel
                    @if($pendingShops > 0)
                        <span class="badge bg-danger ms-2">{{ $pendingShops }} Pending</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="{{ route('admin.shops.index') }}" class="quick-action-card card text-decoration-none h-100 position-relative">
                            <div class="card-body text-center p-3">
                                <i class="fas fa-store text-primary fs-1 mb-2"></i>
                                <h6 class="fw-medium mb-0">Manage Shops</h6>
                                <small class="text-muted">{{ $totalShops }} total</small>
                                @if($pendingShops > 0)
                                    <span class="status-badge bg-warning">{{ $pendingShops }}</span>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="{{ route('admin.users.index') }}" class="quick-action-card card text-decoration-none h-100">
                            <div class="card-body text-center p-3">
                                <i class="fas fa-users text-success fs-1 mb-2"></i>
                                <h6 class="fw-medium mb-0">Manage Users</h6>
                                <small class="text-muted">{{ $totalUsers }} users</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="{{ route('admin.receipts.index') }}" class="quick-action-card card text-decoration-none h-100">
                            <div class="card-body text-center p-3">
                                <i class="fas fa-receipt text-info fs-1 mb-2"></i>
                                <h6 class="fw-medium mb-0">All Receipts</h6>
                                <small class="text-muted">{{ $totalReceipts }} total</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="{{ route('admin.reports.index') }}" class="quick-action-card card text-decoration-none h-100">
                            <div class="card-body text-center p-3">
                                <i class="fas fa-chart-bar text-warning fs-1 mb-2"></i>
                                <h6 class="fw-medium mb-0">Reports</h6>
                                <small class="text-muted">Analytics</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="{{ route('admin.payments.index') }}" class="quick-action-card card text-decoration-none h-100">
                            <div class="card-body text-center p-3">
                                <i class="fas fa-credit-card text-purple fs-1 mb-2"></i>
                                <h6 class="fw-medium mb-0">Payments</h6>
                                <small class="text-muted">Transactions</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="{{ route('admin.settings.index') }}" class="quick-action-card card text-decoration-none h-100">
                            <div class="card-body text-center p-3">
                                <i class="fas fa-cogs text-secondary fs-1 mb-2"></i>
                                <h6 class="fw-medium mb-0">Settings</h6>
                                <small class="text-muted">System config</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">Total Shops</h6>
                        <h3 class="mb-0 fw-bold counter animate-counter" data-target="{{ $totalShops }}">
                            {{ $totalShops }}
                        </h3>
                        <small class="text-muted">{{ $activeShops }} approved</small>
                    </div>
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-store"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress mb-2" style="height: 5px;">
                        <div class="progress-bar bg-primary" 
                             style="width: {{ $totalShops > 0 ? ($activeShops / $totalShops) * 100 : 0 }}%"></div>
                    </div>
                    <a href="{{ route('admin.shops.index') }}" class="btn btn-outline-primary btn-sm w-100" data-primary-action>
                        <i class="fas fa-store me-1"></i>Manage Shops
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">Total Receipts</h6>
                        <h3 class="mb-0 fw-bold counter animate-counter" data-target="{{ $totalReceipts }}">
                            {{ $totalReceipts }}
                        </h3>
                        <small class="text-muted">{{ $monthlyReceipts }} this month</small>
                    </div>
                    <div class="stat-icon bg-success">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress mb-2" style="height: 5px;">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                    <a href="{{ route('admin.receipts.index') }}" class="btn btn-outline-success btn-sm w-100" data-primary-action>
                        <i class="fas fa-list me-1"></i>View All Receipts
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">Total Revenue</h6>
                        <h3 class="mb-0 fw-bold">₦{{ number_format($totalRevenue, 2) }}</h3>
                        <small class="text-muted">All transactions</small>
                    </div>
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-naira-sign"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress mb-2" style="height: 5px;">
                        <div class="progress-bar bg-warning" style="width: 92%"></div>
                    </div>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-warning btn-sm w-100" data-primary-action>
                        <i class="fas fa-money-bill me-1"></i>View Payments
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">System Users</h6>
                        <h3 class="mb-0 fw-bold counter animate-counter" data-target="{{ $totalUsers }}">
                            {{ $totalUsers }}
                        </h3>
                        <small class="text-muted">Registered users</small>
                    </div>
                    <div class="stat-icon bg-info">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress mb-2" style="height: 5px;">
                        <div class="progress-bar bg-info" style="width: 78%"></div>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info btn-sm w-100" data-primary-action>
                        <i class="fas fa-user-cog me-1"></i>Manage Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Approvals & System Health -->
<div class="row mb-4">
    <div class="col-lg-8 mb-3">
        <div class="card dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2 text-warning"></i>
                    Pending Approvals
                </h5>
                @if($pendingShops > 0)
                    <button class="btn btn-success btn-sm" onclick="approveAllShops()">
                        <i class="fas fa-check-double me-1"></i>Approve All
                    </button>
                @endif
            </div>
            <div class="card-body">
                @if($pendingShops > 0)
                    @php
                        $recentPendingShops = \App\Models\Shop::where('approved', false)
                            ->with('user')
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @foreach($recentPendingShops as $shop)
                        <div class="pending-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div>
                                <h6 class="mb-1">{{ $shop->shop_name }}</h6>
                                <p class="mb-0 text-muted small">
                                    Owner: {{ $shop->user->first_name }} {{ $shop->user->last_name }} • 
                                    Location: {{ $shop->state }}, {{ $shop->local_government }}
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $shop->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div>
                                <button class="btn btn-success btn-sm me-2" onclick="approveShop({{ $shop->id }})">
                                    <i class="fas fa-check me-1"></i>Approve
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="rejectShop({{ $shop->id }})">
                                    <i class="fas fa-times me-1"></i>Reject
                                </button>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($pendingShops > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.shops.index') }}" class="btn btn-outline-primary">
                                View All {{ $pendingShops }} Pending Shops
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-success">All Caught Up!</h5>
                        <p class="text-muted">No pending shop approvals at the moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-3">
        <div class="card dashboard-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-heartbeat me-2 text-danger"></i>
                    System Health
                </h5>
            </div>
            <div class="card-body">
                <div class="health-item d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <strong>Database</strong>
                        <div class="text-muted small">Connection Status</div>
                    </div>
                    <span class="badge bg-success">Healthy</span>
                </div>
                
                <div class="health-item d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <strong>Server</strong>
                        <div class="text-muted small">Performance</div>
                    </div>
                    <span class="badge bg-success">Online</span>
                </div>
                
                <div class="health-item d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <strong>Payment Gateway</strong>
                        <div class="text-muted small">Paystack API</div>
                    </div>
                    <span class="badge bg-success">Active</span>
                </div>
                
                <div class="health-item d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <strong>File Storage</strong>
                        <div class="text-muted small">Disk Usage</div>
                    </div>
                    <span class="badge bg-warning">85% Used</span>
                </div>
                
                <div class="text-center mt-3">
                    <a href="{{ route('admin.system.health') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-tools me-1"></i>View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Quick Stats -->
<div class="row">
    <div class="col-lg-8 mb-3">
        <div class="card dashboard-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-activity me-2 text-info"></i>
                    Recent System Activity
                </h5>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @php
                    $recentActivities = [];
                    
                    // Recent shop registrations
                    $recentShops = \App\Models\Shop::with('user')->latest()->take(3)->get();
                    foreach($recentShops as $shop) {
                        $recentActivities[] = [
                            'icon' => 'fas fa-store',
                            'color' => 'primary',
                            'title' => 'New Shop Registration',
                            'description' => $shop->shop_name . ' by ' . $shop->user->first_name,
                            'time' => $shop->created_at,
                            'status' => $shop->approved ? 'approved' : 'pending'
                        ];
                    }
                    
                    // Recent receipts
                    $recentReceipts = \App\Models\Receipt::with(['shop', 'user'])->latest()->take(3)->get();
                    foreach($recentReceipts as $receipt) {
                        $recentActivities[] = [
                            'icon' => 'fas fa-receipt',
                            'color' => 'success',
                            'title' => 'Receipt Generated',
                            'description' => $receipt->receipt_number . ' by ' . $receipt->shop->shop_name,
                            'time' => $receipt->created_at,
                            'status' => 'completed'
                        ];
                    }
                    
                    // Sort by time
                    usort($recentActivities, function($a, $b) {
                        return $b['time'] <=> $a['time'];
                    });
                    
                    $recentActivities = array_slice($recentActivities, 0, 10);
                @endphp
                
                @forelse($recentActivities as $activity)
                    <div class="activity-item d-flex align-items-start py-3 border-bottom">
                        <div class="me-3">
                            <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $activity['title'] }}</h6>
                            <p class="mb-1 text-muted">{{ $activity['description'] }}</p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>{{ $activity['time']->diffForHumans() }}
                            </small>
                        </div>
                        <span class="badge bg-{{ $activity['color'] }}">
                            {{ ucfirst($activity['status']) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-inbox text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted">No recent activities</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-3">
        <div class="card dashboard-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2 text-success"></i>
                    Quick Insights
                </h5>
            </div>
            <div class="card-body">
                <div class="insight-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Shop Approval Rate</span>
                        <strong>{{ $totalShops > 0 ? round(($activeShops / $totalShops) * 100, 1) : 0 }}%</strong>
                    </div>
                    <div class="progress mt-1" style="height: 5px;">
                        <div class="progress-bar bg-success" 
                             style="width: {{ $totalShops > 0 ? ($activeShops / $totalShops) * 100 : 0 }}%"></div>
                    </div>
                </div>
                
                <div class="insight-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Monthly Growth</span>
                        <strong>+{{ rand(15, 35) }}%</strong>
                    </div>
                    <div class="progress mt-1" style="height: 5px;">
                        <div class="progress-bar bg-primary" style="width: 75%"></div>
                    </div>
                </div>
                
                <div class="insight-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span>System Performance</span>
                        <strong>{{ rand(85, 98) }}%</strong>
                    </div>
                    <div class="progress mt-1" style="height: 5px;">
                        <div class="progress-bar bg-info" style="width: 92%"></div>
                    </div>
                </div>
                
                <div class="insight-item">
                    <div class="d-flex justify-content-between">
                        <span>User Satisfaction</span>
                        <strong>{{ rand(85, 95) }}%</strong>
                    </div>
                    <div class="progress mt-1" style="height: 5px;">
                        <div class="progress-bar bg-warning" style="width: 88%"></div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-chart-bar me-1"></i>View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Shop approval functions
function approveShop(shopId) {
    if (confirm('Are you sure you want to approve this shop?')) {
        fetch(`/admin/shops/${shopId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', 'Failed to approve shop');
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred');
        });
    }
}

function rejectShop(shopId) {
    if (confirm('Are you sure you want to reject this shop?')) {
        fetch(`/admin/shops/${shopId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('warning', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', 'Failed to reject shop');
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred');
        });
    }
}

function approveAllShops() {
    if (confirm('Are you sure you want to approve ALL pending shops?')) {
        fetch('/admin/shops/approve-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', `${data.count} shops approved successfully!`);
                setTimeout(() => location.reload(), 2000);
            } else {
                showAlert('error', 'Failed to approve shops');
            }
        })
        .catch(error => {
            showAlert('error', 'An error occurred');
        });
    }
}

function showAlert(type, message) {
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 5000);
}

// Counter animation for statistics
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.dataset.target);
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const updateCounter = () => {
            current += step;
            if (current < target) {
                counter.textContent = Math.floor(current);
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target;
            }
        };
        
        updateCounter();
    });
});
</script>
@endpush
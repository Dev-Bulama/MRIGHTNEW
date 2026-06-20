@php
    $shop = auth()->user()->shop;
    $isShopActive = $shop && $shop->isActive();
    $currentMonthReceipts = auth()->user()->currentMonthReceipts();
    $currentYearReceipts = auth()->user()->currentYearReceipts();
    $totalEarnings = auth()->user()->totalEarnings;
@endphp

@if(!$isShopActive)
    <!-- Shop Setup Required -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-store me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        @if(!$shop)
                            <h5 class="alert-heading">Setup Your Shop Profile</h5>
                            <p class="mb-3">You need to create your shop profile before you can start generating digital receipts for your customers.</p>
                            <div class="d-grid gap-2 d-md-flex">
                                <a href="{{ route('shop.create') }}" class="btn btn-warning">
                                    <i class="fas fa-plus me-2"></i>Create Shop Profile
                                </a>
                                <a href="{{ route('help.setup') }}" class="btn btn-outline-warning">
                                    <i class="fas fa-question-circle me-2"></i>Need Help?
                                </a>
                            </div>
                        @else
                            <h5 class="alert-heading">Shop Approval Pending</h5>
                            <p class="mb-3">Your shop profile is currently under review by our admin team. You'll be notified once it's approved.</p>
                            <div class="d-grid gap-2 d-md-flex">
                                <a href="{{ route('shop.show', $shop) }}" class="btn btn-warning">
                                    <i class="fas fa-eye me-2"></i>View Shop Profile
                                </a>
                                <a href="{{ route('shop.edit', $shop) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-edit me-2"></i>Edit Profile
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
@else
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2 text-warning"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 quick-actions">
                        <div class="col-md-3 col-6">
                            <a href="{{ route('receipt.create') }}" class="quick-action-card card text-decoration-none h-100 position-relative">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-plus-circle text-primary fs-1 mb-2"></i>
                                    <h6 class="fw-medium mb-0">New Receipt</h6>
                                    <small class="text-muted">Generate receipt</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('receipt.resale') }}" class="quick-action-card card text-decoration-none h-100 position-relative">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-sync-alt text-info fs-1 mb-2"></i>
                                    <h6 class="fw-medium mb-0">Update Resale</h6>
                                    <small class="text-muted">Transfer ownership</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('search.index') }}" class="quick-action-card card text-decoration-none h-100 position-relative">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-search text-success fs-1 mb-2"></i>
                                    <h6 class="fw-medium mb-0">Search Phone</h6>
                                    <small class="text-muted">Find receipt</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="{{ route('receipt.index') }}" class="quick-action-card card text-decoration-none h-100 position-relative">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-list text-secondary fs-1 mb-2"></i>
                                    <h6 class="fw-medium mb-0">My Receipts</h6>
                                    <small class="text-muted">View all</small>
                                    @if(auth()->user()->receipts->count() > 0)
                                        <span class="status-badge bg-primary">{{ auth()->user()->receipts->count() }}</span>
                                    @endif
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Statistics Overview - Clean Stats Only -->
<div class="row mb-4">
    <!-- This Month Stats -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">This Month</h6>
                        <h3 class="mb-0 fw-bold counter animate-counter" data-target="{{ $currentMonthReceipts->count() }}">
                            {{ $currentMonthReceipts->count() }}
                        </h3>
                        <small class="text-muted">Receipts Generated</small>
                    </div>
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- This Year Stats -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">This Year</h6>
                        <h3 class="mb-0 fw-bold counter animate-counter" data-target="{{ $currentYearReceipts->count() }}">
                            {{ $currentYearReceipts->count() }}
                        </h3>
                        <small class="text-muted">Total Receipts</small>
                    </div>
                    <div class="stat-icon bg-success">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Earnings Stats -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">Earnings</h6>
                        <h3 class="mb-0 fw-bold counter animate-counter" data-target="{{ $totalEarnings }}">
                            ₦{{ number_format($totalEarnings, 2) }}
                        </h3>
                        <small class="text-muted">Total Revenue</small>
                    </div>
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-naira-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Trend Graph Placeholder -->
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">Performance</h6>
                        <h3 class="mb-0 fw-bold counter animate-counter">
                            95%
                        </h3>
                        <small class="text-muted">Success Rate</small>
                    </div>
                    <div class="stat-icon bg-info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($isShopActive)
    <!-- Charts and Analytics -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-3">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Receipt Trends
                    </h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="chartPeriod" id="week" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="week">Week</label>
                        
                        <input type="radio" class="btn-check" name="chartPeriod" id="month" autocomplete="off">
                        <label class="btn btn-outline-primary" for="month">Month</label>
                        
                        <input type="radio" class="btn-check" name="chartPeriod" id="year" autocomplete="off">
                        <label class="btn btn-outline-primary" for="year">Year</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="receiptTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-3">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-percentage me-2 text-success"></i>
                        Performance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">Success Rate</span>
                            <span class="fw-bold text-success">95%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 95%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Shop Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-store me-2 text-info"></i>
                        My Shop Information
                    </h5>
                    <a href="{{ route('shop.edit', $shop) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit Shop
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <div class="shop-logo text-center">
                                <img src="{{ $shop->logo_url }}" 
                                     alt="{{ $shop->shop_name }}" 
                                     class="img-fluid rounded-3 border"
                                     style="max-width: 80px; max-height: 80px;">
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-dark mb-1">{{ $shop->shop_name }}</h6>
                                    <p class="text-muted mb-2">{{ $shop->owner_full_name }}</p>
                                    <p class="small text-muted mb-0">{{ $shop->formatted_address }}</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Contact</small>
                                    <p class="mb-1">{{ $shop->business_phone_1 }}</p>
                                    @if($shop->business_phone_2)
                                        <p class="mb-1">{{ $shop->business_phone_2 }}</p>
                                    @endif
                                    <p class="small text-muted mb-0">{{ $shop->business_email }}</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Status</small>
                                    <span class="badge bg-success mb-2">Active</span>
                                    <p class="small text-muted mb-0">
                                        Approved: {{ $shop->approved_at->format('M j, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
// Shop owner specific dashboard interactions
document.addEventListener('DOMContentLoaded', function() {
    // Chart period switching
    const chartPeriodInputs = document.querySelectorAll('input[name="chartPeriod"]');
    chartPeriodInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateReceiptChart(this.id);
        });
    });
    
    // Update shop information periodically
    setInterval(updateShopStats, 300000); // Every 5 minutes
});

function updateReceiptChart(period) {
    // Mock data for different periods
    const chartData = {
        week: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            data: [5, 8, 12, 6, 9, 15, 10]
        },
        month: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            data: [35, 42, 38, 45]
        },
        year: {
            labels: ['Q1', 'Q2', 'Q3', 'Q4'],
            data: [120, 150, 140, 180]
        }
    };
    
    const chart = Chart.getChart('receiptTrendChart');
    if (chart) {
        chart.data.labels = chartData[period].labels;
        chart.data.datasets[0].data = chartData[period].data;
        chart.update();
    }
}

function updateShopStats() {
    fetch('{{ route("dashboard.shop-stats") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update statistics
        updateCounters(data);
    })
    .catch(error => {
        console.error('Error updating shop stats:', error);
    });
}

function updateCounters(data) {
    Object.keys(data).forEach(key => {
        const counter = document.querySelector(`[data-stat="${key}"]`);
        if (counter) {
            const currentValue = parseInt(counter.textContent);
            const newValue = parseInt(data[key]);
            
            if (currentValue !== newValue) {
                animateCounterUpdate(counter, currentValue, newValue);
            }
        }
    });
}

function animateCounterUpdate(element, from, to) {
    const duration = 1000;
    const increment = (to - from) / (duration / 16);
    let current = from;
    
    const update = () => {
        current += increment;
        
        if ((increment > 0 && current < to) || (increment < 0 && current > to)) {
            element.textContent = Math.floor(current);
            requestAnimationFrame(update);
        } else {
            element.textContent = to;
            // Flash effect for new values
            element.style.background = 'rgba(13, 138, 188, 0.1)';
            setTimeout(() => {
                element.style.background = '';
            }, 300);
        }
    };
    
    update();
}
</script>
@endpush
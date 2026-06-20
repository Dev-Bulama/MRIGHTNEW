@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Overview of your M-right digital receipt activities')

@push('styles')
<style>

    .stats-number {
    font-size: 1.25rem; /* Customize as needed */
}

.stats-label {
    font-size: 0.875rem;
    font-weight: 500;
}

.dashboard-card {
    transition: all 0.3s ease;
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.5rem;
    color: white;
}

.chart-container {
    position: relative;
    height: 300px;
}

.animate-counter {
    animation: countUp 2s ease-out;
}

@keyframes countUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.quick-action-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
}

.quick-action-card:hover {
    border-color: var(--primary-color);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(13, 138, 188, 0.15);
}

.stats-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.stats-card .card-body {
    padding: 2rem;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    line-height: 1;
}

.stats-label {
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.border-left-primary {
    border-left: 5px solid #4e73df!important;
}

.border-left-success {
    border-left: 5px solid #1cc88a!important;
}

.border-left-info {
    border-left: 5px solid #36b9cc!important;
}

.border-left-warning {
    border-left: 5px solid #f6c23e!important;
}

.receipt-trend-chart {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    overflow: hidden;
}

.receipt-trend-chart .card-header {
    background: rgba(255, 255, 255, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.receipt-trend-chart .card-header h6 {
    color: white;
    font-weight: 600;
}

.chart-area {
    position: relative;
    height: 320px;
    padding: 20px;
}
</style>
@endpush

@section('content')
<div class="dashboard-content fade-in">
    <!-- Welcome Section -->
    <div class="welcome-section mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h3 fw-bold text-dark mb-2">
                    Welcome back, {{ auth()->user()->first_name }}! 👋
                </h1>
                <p class="text-muted mb-0">
                    @if(auth()->user()->isShopOwner())
                        @if(auth()->user()->shop && auth()->user()->shop->isActive())
                            <!-- Manage your digital receipts and track your business performance. -->
                        @else
                            Complete your shop setup to start generating digital receipts.
                        @endif
                    @elseif(auth()->user()->isAdmin())
                        Monitor system activities and manage shop approvals.
                    @else
                        View and manage your phone receipt records.
                    @endif
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    {{ now()->format('l, F j, Y') }}
                </small>
            </div>
        </div>
    </div>
    
    @if(auth()->user()->isShopOwner())
        @if(auth()->user()->shop && auth()->user()->shop->isActive())
            <!-- Shop Owner Dashboard - Clean Stats Only -->
            
            <!-- Receipt Trend Chart -->
            <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <div class="card receipt-trend-chart shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold">Receipt Trend Chart</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="receiptTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="mb-3 text-gray-800">Quick Actions</h5>
                </div>
                <div class="col-lg-6 col-md-6 mb-3">
                    <div class="card quick-action-card h-100" onclick="window.location.href='{{ route('receipt.create') }}'">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-primary mx-auto mb-3">
                                <i class="fas fa-plus"></i>
                            </div>
                            <h6 class="font-weight-bold">New Phone Receipt</h6>
                            <p class="text-muted small mb-0">New Phone/London Used</p>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card quick-action-card h-100" onclick="window.location.href='{{ route('search.index') }}'">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-success mx-auto mb-3">
                                <i class="fas fa-search"></i>
                            </div>
                            <h6 class="font-weight-bold">Search Receipts</h6>
                            <p class="text-muted small mb-0">Find phone records</p>
                        </div>
                    </div>
                </div> -->
                <div class="col-lg-6 col-md-6 mb-3">
                    <div class="card quick-action-card h-100" onclick="window.location.href='{{ route('receipt.resale') }}'">
                        <div class="card-body text-center">
                            <div class="stat-icon bg-info mx-auto mb-3">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <h6 class="font-weight-bold">Resale Phone</h6>
                            <p class="text-muted small mb-0">Change of ownership</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <!-- This Month Stats -->
             <!-- This Month Stats -->
<div class="col-xl-4 col-md-6 mb-4">
    <div class="card stats-card border-left-primary shadow h-100">
        <div class="card-body py-3 px-3">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="stats-label text-primary mb-1 small">This Month</div>
                    <div class="stats-number text-gray-800 h5 mb-0 font-weight-bold animate-counter">
                        {{ $monthlyReceipts ?? 0 }}
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-calendar fa-lg text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- This Year Stats -->
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card stats-card border-left-success shadow h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="stats-label text-success mb-1">This Year</div>
                                    <div class="stats-number text-gray-800 animate-counter">
                                        {{ $yearlyReceipts ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Earnings Stats -->
              <!-- Earnings Stats -->
<div class="col-xl-4 col-md-8 mb-4">
    <div class="card stats-card border-left-info shadow h-100">
        <div class="card-body py-3 px-3">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="stats-label text-info mb-1 small">Earnings</div>
                      <small class="text-muted"> ₦400 commission per receipt generated</small>
                    <div class="stats-number text-gray-800 h5 mb-0 font-weight-bold animate-counter">
                        ₦{{ number_format($totalEarnings ?? 0, 2) }}
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-dollar-sign fa-lg text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- Receipt Trend Stats -->
                <!-- <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card border-left-warning shadow h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="stats-label text-warning mb-1">Receipt Trend</div>
                                    <div class="stats-number text-gray-800 animate-counter">
                                        @php
                                            $currentMonth = $monthlyReceipts ?? 0;
                                            $lastMonth = auth()->user()->receipts()->whereMonth('created_at', now()->subMonth()->month)->count();
                                            $trendPercentage = $lastMonth > 0 ? round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1) : ($currentMonth > 0 ? 100 : 0);
                                        @endphp
                                        {{ $trendPercentage > 0 ? '+' : '' }}{{ $trendPercentage }}%
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

        @else
            <!-- Shop Setup Required -->
            <div class="row">
                <div class="col-12">
                    <div class="card dashboard-card">
                        <div class="card-body text-center py-5">
                            <div class="stat-icon bg-warning mx-auto mb-4">
                                <i class="fas fa-store"></i>
                            </div>
                            <h4 class="font-weight-bold mb-3">Complete Shop Setup</h4>
                            <p class="text-muted mb-4">
                                Set up your shop profile to start generating digital receipts and managing your business.
                            </p>
                            <a href="{{ route('shop.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Setup Shop Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    
    @elseif(auth()->user()->isAdmin())
        <!-- Admin Dashboard -->
        <div class="row">
            <!-- Admin Stats -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stats-label text-primary mb-1">Total Shops</div>
                                <div class="stats-number text-gray-800">{{ $totalShops ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-store fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stats-label text-success mb-1">Active Shops</div>
                                <div class="stats-number text-gray-800">{{ $activeShops ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stats-label text-warning mb-1">Pending Approval</div>
                                <div class="stats-number text-gray-800">{{ $pendingShops ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stats-label text-info mb-1">Total Users</div>
                                <div class="stats-number text-gray-800">{{ $totalUsers ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="mb-3 text-gray-800">Admin Actions</h5>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card quick-action-card h-100" onclick="window.location.href='{{ route('admin.shops.index') }}'">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-primary mx-auto mb-3">
                            <i class="fas fa-store"></i>
                        </div>
                        <h6 class="font-weight-bold">Manage Shops</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card quick-action-card h-100" onclick="window.location.href='{{ route('admin.users.index') }}'">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-success mx-auto mb-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <h6 class="font-weight-bold">Manage Users</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card quick-action-card h-100" onclick="window.location.href='{{ route('admin.receipts.index') }}'">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-info mx-auto mb-3">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <h6 class="font-weight-bold">View Receipts</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card quick-action-card h-100" onclick="window.location.href='{{ route('admin.reports.index') }}'">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-warning mx-auto mb-3">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h6 class="font-weight-bold">Reports</h6>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Customer Dashboard -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-body text-center py-5">
                        <div class="stat-icon bg-info mx-auto mb-4">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4 class="font-weight-bold mb-3">Verify Phone Receipt</h4>
                        <p class="text-muted mb-4">
                            Search and verify phone receipts in our system.
                        </p>
                        <a href="{{ route('search.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Search Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(auth()->user()->isShopOwner() && auth()->user()->shop && auth()->user()->shop->isActive())
        // Initialize Receipt Trend Chart
        initializeReceiptTrendChart();
    @endif
    
    // Counter animation
    animateCounters();
});

function initializeReceiptTrendChart() {
    const ctx = document.getElementById('receiptTrendChart');
    if (!ctx) return;
    
    // Sample data - replace with actual data from backend
    const chartData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Receipts Generated',
            data: [12, 19, 15, 25, 22, 30, 35, 28, 32, 38, 42, 45],
            borderColor: 'rgba(255, 255, 255, 0.8)',
            backgroundColor: 'rgba(255, 255, 255, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: 'white',
            pointBorderColor: 'rgba(255, 255, 255, 0.8)',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    };

    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.2)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.8)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.2)'
                    },
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.8)'
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: 'white',
                    hoverBorderColor: 'rgba(255, 255, 255, 0.8)'
                }
            }
        }
    });
}

function animateCounters() {
    const counters = document.querySelectorAll('.animate-counter');
    
    counters.forEach(counter => {
        const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            if (counter.textContent.includes('₦')) {
                counter.textContent = '₦' + Math.floor(current).toLocaleString('en-NG', {minimumFractionDigits: 2});
            } else if (counter.textContent.includes('%')) {
                counter.textContent = (current >= 0 ? '+' : '') + Math.floor(current) + '%';
            } else {
                counter.textContent = Math.floor(current).toLocaleString();
            }
        }, 16);
    });
}

// Auto-refresh dashboard data every 5 minutes
setInterval(function() {
    refreshDashboardData();
}, 300000);

function refreshDashboardData() {
    // Optional: Add AJAX call to refresh dashboard data
    console.log('Dashboard data refreshed at:', new Date().toLocaleTimeString());
}
</script>
@endpush
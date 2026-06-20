@extends('layouts.union')

@section('breadcrumb')
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Performance Reports</h1>
            <p class="text-muted">Analytics and insights for your assigned locations</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshData()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
            <button class="btn btn-success" onclick="exportReport()">
                <i class="fas fa-download me-2"></i>Export Report
            </button>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card primary">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Total Receipts</h6>
                        <h3 class="mb-0">{{ $performanceData['total_receipts'] ?? 0 }}</h3>
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
                        <h3 class="mb-0">{{ $performanceData['successful_receipts'] ?? 0 }}</h3>
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
                        <h3 class="mb-0">₦{{ number_format($performanceData['total_revenue'] ?? 0) }}</h3>
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
                        <h6 class="mb-1">This Month</h6>
                        <h3 class="mb-0">{{ $performanceData['this_month_receipts'] ?? 0 }}</h3>
                    </div>
                    <div>
                        <i class="fas fa-calendar fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Monthly Trends Chart -->
        <div class="col-lg-8 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Monthly Trends (Last 6 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="col-lg-4 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Performance Summary
                    </h5>
                </div>
                <div class="card-body">
                    @if($performanceData['total_receipts'] > 0)
                        @php
                            $successRate = round(($performanceData['successful_receipts'] / $performanceData['total_receipts']) * 100, 1);
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Success Rate:</span>
                                <strong>{{ $successRate }}%</strong>
                            </div>
                            <div class="progress mt-1">
                                <div class="progress-bar bg-success" style="width: {{ $successRate }}%"></div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Average Revenue per Receipt:</span>
                            <strong>₦{{ $performanceData['total_receipts'] > 0 ? number_format($performanceData['total_revenue'] / $performanceData['total_receipts']) : '0' }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Monthly Growth:</span>
                            <strong class="text-success">+12%</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-trophy me-2"></i>Top Performing Shop Owners (This Month)
            </h5>
        </div>
        <div class="card-body p-0">
            @if($topPerformers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Shop Owner</th>
                                <th>Shop Details</th>
                                <th>Monthly Receipts</th>
                                <th>Monthly Revenue</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topPerformers as $index => $performer)
                                <tr>
                                    <td>
                                        @if($index < 3)
                                            @php
                                                $badges = ['🥇', '🥈', '🥉'];
                                            @endphp
                                            <span style="font-size: 1.5rem;">{{ $badges[$index] }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $performer['user']->name }}</strong><br>
                                                <small class="text-muted">{{ $performer['user']->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($performer['user']->shop)
                                            <strong>{{ $performer['user']->shop->shop_name }}</strong><br>
                                            <small class="text-muted">{{ $performer['user']->shop->state }}, {{ $performer['user']->shop->local_government }}</small>
                                        @else
                                            <span class="text-muted">No shop details</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $performer['monthly_receipts'] }}</strong>
                                    </td>
                                    <td>
                                        <strong>₦{{ number_format($performer['monthly_revenue']) }}</strong>
                                    </td>
                                    <td>
                                        @if($performer['monthly_receipts'] >= 20)
                                            <span class="badge bg-success">Excellent</span>
                                        @elseif($performer['monthly_receipts'] >= 10)
                                            <span class="badge bg-primary">Good</span>
                                        @elseif($performer['monthly_receipts'] >= 5)
                                            <span class="badge bg-warning">Average</span>
                                        @else
                                            <span class="badge bg-secondary">Below Average</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-trophy text-muted mb-3" style="font-size: 4rem;"></i>
                    <h5 class="text-muted">No Performance Data Yet</h5>
                    <p class="text-muted">Performance data will appear here once shop owners start generating receipts.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
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

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}
</style>

<script>
function refreshData() {
    location.reload();
}

function exportReport() {
    window.open('{{ route("union.dashboard.export") }}', '_blank');
}

// Initialize chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
    
    const monthlyData = @json($monthlyTrends);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Receipts Generated',
                data: monthlyData.map(item => item.receipts),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Receipts'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Performance Trend'
                }
            }
        }
    });
});
</script>
@endsection
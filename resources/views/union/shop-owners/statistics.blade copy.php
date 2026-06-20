@extends('layouts.union')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('union.shop-owners.index') }}">Shop Owners</a></li>
    <li class="breadcrumb-item active">Statistics</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Shop Owner Performance Statistics</h1>
            <p class="text-muted">Detailed performance metrics and analytics</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshStats()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
            <button class="btn btn-success" onclick="exportChart()">
                <i class="fas fa-download me-2"></i>Export
            </button>
            <button class="btn btn-outline-secondary" onclick="window.close()">
                <i class="fas fa-times me-2"></i>Close
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading performance data...</p>
    </div>

    <!-- Statistics Content -->
    <div id="statisticsContent" style="display: none;">
        <!-- Performance Overview -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card primary">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Daily Average</h6>
                            <h3 class="mb-0" id="dailyAverage">-</h3>
                            <small class="opacity-75">Receipts per day</small>
                        </div>
                        <div>
                            <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Peak Day</h6>
                            <h3 class="mb-0" id="peakDay">-</h3>
                            <small class="opacity-75">Highest receipts</small>
                        </div>
                        <div>
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Trend</h6>
                            <h3 class="mb-0" id="trendIndicator">-</h3>
                            <small class="opacity-75">30-day trend</small>
                        </div>
                        <div>
                            <i class="fas fa-trending-up fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card info">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Success Rate</h6>
                            <h3 class="mb-0" id="avgSuccessRate">-</h3>
                            <small class="opacity-75">Average success</small>
                        </div>
                        <div>
                            <i class="fas fa-percentage fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Daily Receipts Chart -->
            <div class="col-lg-8 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-area me-2"></i>Daily Receipts (Last 30 Days)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyReceiptsChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Performance -->
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Monthly Trends
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Rate Trend -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>Success Rate Trend (Weekly)
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="successRateChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Summary Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>Performance Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="performanceTable">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Daily Avg</th>
                                <th>Weekly Avg</th>
                                <th>Monthly Total</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Receipts Generated</strong></td>
                                <td id="dailyReceipts">-</td>
                                <td id="weeklyReceipts">-</td>
                                <td id="monthlyReceipts">-</td>
                                <td id="receiptsTrend">-</td>
                            </tr>
                            <tr>
                                <td><strong>Revenue Generated</strong></td>
                                <td id="dailyRevenue">-</td>
                                <td id="weeklyRevenue">-</td>
                                <td id="monthlyRevenue">-</td>
                                <td id="revenueTrend">-</td>
                            </tr>
                            <tr>
                                <td><strong>Success Rate</strong></td>
                                <td id="dailySuccess">-</td>
                                <td id="weeklySuccess">-</td>
                                <td id="monthlySuccess">-</td>
                                <td id="successTrend">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Error State -->
    <div id="errorState" style="display: none;" class="text-center py-5">
        <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 4rem;"></i>
        <h4 class="text-danger">Failed to Load Statistics</h4>
        <p class="text-muted">There was an error loading the performance data.</p>
        <button class="btn btn-primary" onclick="loadStatistics()">
            <i class="fas fa-retry me-2"></i>Try Again
        </button>
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

.trend-up { color: #28a745; }
.trend-down { color: #dc3545; }
.trend-stable { color: #6c757d; }
</style>

<script>
let dailyChart, monthlyChart, successChart;

// Load statistics when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
});

function loadStatistics() {
    const shopOwnerId = window.location.pathname.split('/')[3]; // Extract shop owner ID from URL
    
    document.getElementById('loadingState').style.display = 'block';
    document.getElementById('statisticsContent').style.display = 'none';
    document.getElementById('errorState').style.display = 'none';

    fetch(`/union/shop-owners/${shopOwnerId}/statistics`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayStatistics(data.performance_data);
            } else {
                showError();
            }
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
            showError();
        });
}

function displayStatistics(data) {
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('statisticsContent').style.display = 'block';
    
    // Update summary cards
    updateSummaryCards(data);
    
    // Create charts
    createDailyReceiptsChart(data.daily_receipts);
    createMonthlyChart(data.monthly_performance);
    createSuccessRateChart(data.success_rate_trend);
    
    // Update performance table
    updatePerformanceTable(data);
}

function updateSummaryCards(data) {
    const dailyReceipts = data.daily_receipts || [];
    
    // Calculate daily average
    const totalReceipts = dailyReceipts.reduce((sum, day) => sum + parseInt(day.count), 0);
    const dailyAvg = dailyReceipts.length > 0 ? Math.round(totalReceipts / dailyReceipts.length) : 0;
    document.getElementById('dailyAverage').textContent = dailyAvg;
    
    // Find peak day
    const peakDay = dailyReceipts.reduce((max, day) => 
        parseInt(day.count) > parseInt(max.count || 0) ? day : max, {count: 0});
    document.getElementById('peakDay').textContent = peakDay.count || 0;
    
    // Calculate trend (simple: compare first week vs last week)
    const firstWeek = dailyReceipts.slice(0, 7);
    const lastWeek = dailyReceipts.slice(-7);
    const firstAvg = firstWeek.reduce((sum, day) => sum + parseInt(day.count), 0) / 7;
    const lastAvg = lastWeek.reduce((sum, day) => sum + parseInt(day.count), 0) / 7;
    const trendPercent = firstAvg > 0 ? ((lastAvg - firstAvg) / firstAvg * 100).toFixed(1) : 0;
    
    const trendElement = document.getElementById('trendIndicator');
    if (trendPercent > 0) {
        trendElement.innerHTML = `<i class="fas fa-arrow-up"></i> ${trendPercent}%`;
        trendElement.className = 'trend-up';
    } else if (trendPercent < 0) {
        trendElement.innerHTML = `<i class="fas fa-arrow-down"></i> ${Math.abs(trendPercent)}%`;
        trendElement.className = 'trend-down';
    } else {
        trendElement.innerHTML = `<i class="fas fa-minus"></i> 0%`;
        trendElement.className = 'trend-stable';
    }
    
    // Average success rate
    const successRates = data.success_rate_trend || [];
    const avgSuccess = successRates.length > 0 ? 
        (successRates.reduce((sum, week) => sum + parseFloat(week.success_rate), 0) / successRates.length).toFixed(1) : 0;
    document.getElementById('avgSuccessRate').textContent = avgSuccess + '%';
}

function createDailyReceiptsChart(dailyData) {
    const ctx = document.getElementById('dailyReceiptsChart').getContext('2d');
    
    if (dailyChart) {
        dailyChart.destroy();
    }
    
    dailyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyData.map(item => new Date(item.date).toLocaleDateString()),
            datasets: [{
                label: 'Receipts',
                data: dailyData.map(item => parseInt(item.count)),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Revenue (₦)',
                data: dailyData.map(item => parseFloat(item.total_amount)),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                yAxisID: 'y1'
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
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Revenue (₦)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Daily Performance Trend'
                }
            }
        }
    });
}

function createMonthlyChart(monthlyData) {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    
    if (monthlyChart) {
        monthlyChart.destroy();
    }
    
    monthlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Receipts',
                data: monthlyData.map(item => item.receipts),
                backgroundColor: 'rgba(0, 123, 255, 0.8)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function createSuccessRateChart(successData) {
    const ctx = document.getElementById('successRateChart').getContext('2d');
    
    if (successChart) {
        successChart.destroy();
    }
    
    successChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: successData.map(item => item.week),
            datasets: [{
                label: 'Success Rate (%)',
                data: successData.map(item => parseFloat(item.success_rate)),
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
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
                    max: 100,
                    title: {
                        display: true,
                        text: 'Success Rate (%)'
                    }
                }
            }
        }
    });
}

function updatePerformanceTable(data) {
    // This would calculate and display performance metrics in the table
    // For now, just show placeholder values
    document.getElementById('dailyReceipts').textContent = '12';
    document.getElementById('weeklyReceipts').textContent = '84';
    document.getElementById('monthlyReceipts').textContent = '360';
    document.getElementById('receiptsTrend').innerHTML = '<i class="fas fa-arrow-up text-success"></i> +15%';
    
    document.getElementById('dailyRevenue').textContent = '₦450K';
    document.getElementById('weeklyRevenue').textContent = '₦3.2M';
    document.getElementById('monthlyRevenue').textContent = '₦13.5M';
    document.getElementById('revenueTrend').innerHTML = '<i class="fas fa-arrow-up text-success"></i> +8%';
    
    document.getElementById('dailySuccess').textContent = '89%';
    document.getElementById('weeklySuccess').textContent = '87%';
    document.getElementById('monthlySuccess').textContent = '91%';
    document.getElementById('successTrend').innerHTML = '<i class="fas fa-arrow-up text-success"></i> +3%';
}

function showError() {
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('statisticsContent').style.display = 'none';
    document.getElementById('errorState').style.display = 'block';
}

function refreshStats() {
    loadStatistics();
}

function exportChart() {
    alert('Export functionality coming soon!');
}
</script>
@endsection
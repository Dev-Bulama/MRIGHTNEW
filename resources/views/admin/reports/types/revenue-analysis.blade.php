<!-- Summary Metrics -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="metric-card">
            <div class="metric-number text-primary">₦{{ number_format($data['summary']['total_revenue'], 2) }}</div>
            <div class="text-muted">Total Revenue</div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="metric-card">
            <div class="metric-number text-success">{{ number_format($data['summary']['total_transactions']) }}</div>
            <div class="text-muted">Total Transactions</div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="metric-card">
            <div class="metric-number text-info">₦{{ number_format($data['summary']['avg_transaction_value'], 2) }}</div>
            <div class="text-muted">Avg Transaction Value</div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="metric-card">
            <div class="metric-number text-warning">{{ number_format($data['growth_analysis']['growth_rate'] ?? 0, 1) }}%</div>
            <div class="text-muted">Growth Rate</div>
        </div>
    </div>
</div>

<!-- Revenue by Period Chart -->
<div class="chart-container">
    <h5 class="mb-3">Revenue Trends</h5>
    <canvas id="revenueChart" height="100"></canvas>
</div>

<!-- Revenue by Shop -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="data-table">
            <div class="table-header p-3 bg-light">
                <h5 class="mb-0">Top Performing Shops</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Rank</th>
                            <th>Shop Name</th>
                            <th>Revenue</th>
                            <th>Transactions</th>
                            <th>Avg Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['revenue_by_shop'] as $index => $shop)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $shop->shop->shop_name ?? 'Unknown Shop' }}</strong>
                                    <br><small class="text-muted">{{ $shop->shop->state ?? 'N/A' }}</small>
                                </td>
                                <td>₦{{ number_format($shop->total_revenue, 2) }}</td>
                                <td>{{ $shop->transaction_count }}</td>
                                <td>₦{{ number_format($shop->total_revenue / max(1, $shop->transaction_count), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="data-table">
            <div class="table-header p-3 bg-light">
                <h5 class="mb-0">Revenue by Payment Method</h5>
            </div>
            <div class="p-3">
                @foreach($data['revenue_by_method'] as $method)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong>{{ ucfirst($method->payment_method ?: 'Other') }}</strong>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">₦{{ number_format($method->total_revenue, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue trend chart
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueData = @json($data['revenue_by_period']);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: Object.keys(revenueData),
        datasets: [{
            label: 'Revenue',
            data: Object.values(revenueData).map(item => item.revenue),
            borderColor: 'rgb(102, 126, 234)',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Revenue Over Time'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₦' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endpush
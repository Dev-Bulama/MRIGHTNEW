@extends('layouts.app')

@section('title', 'System Reports')
@section('page-title', 'System Reports & Analytics')
@section('page-description', 'Generate comprehensive reports and analytics')

@push('styles')
<style>
    .reports-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .report-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        height: 100%;
        border: 2px solid transparent;
        cursor: pointer;
    }
    
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: #667eea;
    }
    
    .report-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        margin: 0 auto 1rem;
    }
    
    .quick-stats {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-item {
        text-align: center;
        padding: 1rem;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .report-category {
        margin-bottom: 3rem;
    }
    
    .category-header {
        border-bottom: 3px solid #667eea;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Reports Header -->
    <div class="reports-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">
                    <i class="fas fa-chart-line me-3"></i>System Reports & Analytics
                </h1>
                <p class="mb-0 opacity-75">
                    Generate detailed reports, analyze system performance, and gain insights into your digital receipt platform.
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex flex-column align-items-end">
                    <h3 class="mb-1">{{ date('M Y') }}</h3>
                    <p class="mb-0 opacity-75">Current Reporting Period</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Statistics -->
    <div class="quick-stats">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="stat-item">
                    <div class="stat-number text-primary">{{ $quickStats['total_reports'] ?? 0 }}</div>
                    <div class="text-muted">Reports Generated</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="stat-item">
                    <div class="stat-number text-success">{{ $quickStats['active_shops'] ?? 0 }}</div>
                    <div class="text-muted">Active Shops</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="stat-item">
                    <div class="stat-number text-info">{{ $quickStats['monthly_receipts'] ?? 0 }}</div>
                    <div class="text-muted">Monthly Receipts</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-item">
                    <div class="stat-number text-warning">₦{{ number_format($quickStats['monthly_revenue'] ?? 0, 0) }}</div>
                    <div class="text-muted">Monthly Revenue</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Analytics Reports -->
    <div class="report-category">
        <div class="category-header">
            <h3 class="text-primary mb-0">
                <i class="fas fa-chart-bar me-2"></i>Business Analytics
            </h3>
        </div>
        
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="report-card" onclick="generateReport('revenue-analysis')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5 class="text-center mb-2">Revenue Analysis</h5>
                    <p class="text-muted text-center mb-3">Detailed revenue breakdown by period, shop, and payment method</p>
                    <div class="text-center">
                        <span class="badge bg-primary">{{ $reportCounts['revenue'] ?? 0 }} reports</span>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="report-card" onclick="generateReport('shop-performance')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                        <i class="fas fa-store"></i>
                    </div>
                    <h5 class="text-center mb-2">Shop Performance</h5>
                    <p class="text-muted text-center mb-3">Individual shop metrics, rankings, and performance comparisons</p>
                    <div class="text-center">
                        <span class="badge bg-success">{{ $reportCounts['shop_performance'] ?? 0 }} reports</span>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="report-card" onclick="generateReport('customer-insights')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="text-center mb-2">Customer Insights</h5>
                    <p class="text-muted text-center mb-3">Customer behavior, retention, and transaction patterns</p>
                    <div class="text-center">
                        <span class="badge bg-info">{{ $reportCounts['customer_insights'] ?? 0 }} reports</span>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="report-card" onclick="generateReport('payment-trends')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h5 class="text-center mb-2">Payment Trends</h5>
                    <p class="text-muted text-center mb-3">Payment method preferences, success rates, and gateway performance</p>
                    <div class="text-center">
                        <span class="badge bg-warning">{{ $reportCounts['payment_trends'] ?? 0 }} reports</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Reports -->
    <div class="report-category">
        <div class="category-header">
            <h3 class="text-success mb-0">
                <i class="fas fa-cogs me-2"></i>Operational Reports
            </h3>
        </div>
        
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="report-card" onclick="generateReport('system-health')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h5 class="text-center mb-2">System Health</h5>
                    <p class="text-muted text-center mb-3">Server performance, uptime, error rates, and system metrics</p>
                    <div class="text-center">
                        <span class="badge bg-success">{{ $reportCounts['system_health'] ?? 0 }} reports</span>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="report-card" onclick="generateReport('user-activity')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                        <i class="fas fa-user-chart"></i>
                    </div>
                    <h5 class="text-center mb-2">User Activity</h5>
                    <p class="text-muted text-center mb-3">Login patterns, feature usage, and user engagement metrics</p>
                    <div class="text-center">
                        <span class="badge bg-info">{{ $reportCounts['user_activity'] ?? 0 }} reports</span>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="report-card" onclick="generateReport('receipt-analytics')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h5 class="text-center mb-2">Receipt Analytics</h5>
                    <p class="text-muted text-center mb-3">Receipt generation patterns, completion rates, and trends</p>
                    <div class="text-center">
                        <span class="badge bg-primary">{{ $reportCounts['receipt_analytics'] ?? 0 }} reports</span>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="report-card" onclick="generateReport('geographic-analysis')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h5 class="text-center mb-2">Geographic Analysis</h5>
                    <p class="text-muted text-center mb-3">Location-based insights, regional performance, and market penetration</p>
                    <div class="text-center">
                        <span class="badge bg-warning">{{ $reportCounts['geographic'] ?? 0 }} reports</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compliance & Audit Reports -->
    <div class="report-category">
        <div class="category-header">
            <h3 class="text-danger mb-0">
                <i class="fas fa-shield-alt me-2"></i>Compliance & Audit
            </h3>
        </div>
        
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="report-card" onclick="generateReport('audit-trail')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h5 class="text-center mb-2">Audit Trail</h5>
                    <p class="text-muted text-center mb-3">Complete activity logs, user actions, and system changes</p>
                    <div class="text-center">
                        <span class="badge bg-danger">{{ $reportCounts['audit'] ?? 0 }} reports</span>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="report-card" onclick="generateReport('compliance-check')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <h5 class="text-center mb-2">Compliance Check</h5>
                    <p class="text-muted text-center mb-3">Regulatory compliance status and requirement fulfillment</p>
                    <div class="text-center">
                        <span class="badge bg-success">{{ $reportCounts['compliance'] ?? 0 }} reports</span>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="report-card" onclick="generateReport('security-report')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h5 class="text-center mb-2">Security Report</h5>
                    <p class="text-muted text-center mb-3">Security incidents, failed logins, and threat analysis</p>
                    <div class="text-center">
                        <span class="badge bg-warning">{{ $reportCounts['security'] ?? 0 }} reports</span>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="report-card" onclick="generateReport('financial-reconciliation')">
                    <div class="report-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <h5 class="text-center mb-2">Financial Reconciliation</h5>
                    <p class="text-muted text-center mb-3">Payment reconciliation, discrepancies, and financial accuracy</p>
                    <div class="text-center">
                        <span class="badge bg-info">{{ $reportCounts['financial'] ?? 0 }} reports</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="report-category">
        <div class="category-header">
            <h3 class="text-dark mb-0">
                <i class="fas fa-clock me-2"></i>Recent Reports
            </h3>
        </div>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Latest Generated Reports</h5>
                <button class="btn btn-outline-primary btn-sm" onclick="refreshRecentReports()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
            <div class="card-body">
                @if(!empty($recentReports))
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Report Type</th>
                                    <th>Generated By</th>
                                    <th>Date Range</th>
                                    <th>Generated On</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentReports as $report)
                                    <tr>
                                        <td>
                                            <strong>{{ $report['title'] }}</strong>
                                            <br><small class="text-muted">{{ $report['description'] }}</small>
                                        </td>
                                        <td>{{ $report['generated_by'] }}</td>
                                        <td>{{ $report['date_range'] }}</td>
                                        <td>{{ $report['generated_on'] }}</td>
                                        <td>
                                            <span class="badge bg-{{ $report['status'] === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($report['status']) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="viewReport({{ $report['id'] }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success" onclick="downloadReport({{ $report['id'] }})">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="deleteReport({{ $report['id'] }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mb-2">No Reports Generated Yet</h5>
                        <p class="text-muted mb-4">Start by generating your first report from the categories above.</p>
                        <button class="btn btn-primary" onclick="generateReport('revenue-analysis')">
                            <i class="fas fa-plus me-2"></i>Generate First Report
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Report Generation Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalTitle">Generate Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="reportModalContent">
                <!-- Report generation form will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateReport(reportType) {
    showLoading('Loading report options...');
    
    fetch(`/admin/reports/generate/${reportType}`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                document.getElementById('reportModalTitle').textContent = data.title;
                document.getElementById('reportModalContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('reportModal')).show();
            } else {
                showAlert('error', data.message || 'Failed to load report options');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Failed to load report options');
        });
}

function viewReport(reportId) {
    window.open(`/admin/reports/view/${reportId}`, '_blank');
}

function downloadReport(reportId) {
    showLoading('Preparing download...');
    
    fetch(`/admin/reports/download/${reportId}`)
        .then(response => {
            hideLoading();
            if (response.ok) {
                return response.blob();
            }
            throw new Error('Download failed');
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `report-${reportId}.pdf`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            showAlert('success', 'Report downloaded successfully!');
        })
        .catch(error => {
            showAlert('error', 'Download failed. Please try again.');
        });
}

function deleteReport(reportId) {
    if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
        showLoading('Deleting report...');
        
        fetch(`/admin/reports/delete/${reportId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert('success', 'Report deleted successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.message || 'Failed to delete report');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Failed to delete report');
        });
    }
}

function refreshRecentReports() {
    showLoading('Refreshing reports...');
    location.reload();
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
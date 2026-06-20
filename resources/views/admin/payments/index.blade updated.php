@extends('layouts.app')

@section('title', 'Payment Management')
@section('page-title', 'Payment Management')
@section('page-description', 'Monitor transactions, manage refunds, and analyze payment performance')

@push('styles')
<style>
    .payment-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .payment-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-left: 4px solid;
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .payment-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .payment-card.success { border-left-color: #28a745; }
    .payment-card.warning { border-left-color: #ffc107; }
    .payment-card.danger { border-left-color: #dc3545; }
    .payment-card.info { border-left-color: #17a2b8; }
    
    .transaction-status {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .transaction-row:hover {
        background-color: #f8f9fa;
    }
    
    .gateway-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        border-radius: 10px;
    }
    
    .amount-display {
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .filter-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .payment-method-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: white;
        margin-right: 1rem;
    }
    
    .trend-indicator {
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .trend-up { color: #28a745; }
    .trend-down { color: #dc3545; }
    .trend-neutral { color: #6c757d; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Payment Header -->
    <div class="payment-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">
                    <i class="fas fa-credit-card me-3"></i>Payment Management
                </h1>
                <p class="mb-0 opacity-75">
                    Monitor all transactions, manage payment disputes, and analyze payment gateway performance.
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex flex-column align-items-end">
                    <h3 class="mb-1">₦{{ number_format($totalVolume ?? 0, 2) }}</h3>
                    <p class="mb-0 opacity-75">Total Transaction Volume</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="payment-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-success mb-2">{{ $stats['successful'] ?? 0 }}</h3>
                        <p class="text-muted mb-1">Successful Payments</p>
                        <div class="trend-indicator trend-up">
                            <i class="fas fa-arrow-up me-1"></i>+12.5% from last month
                        </div>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="payment-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-warning mb-2">{{ $stats['pending'] ?? 0 }}</h3>
                        <p class="text-muted mb-1">Pending Payments</p>
                        <div class="trend-indicator trend-neutral">
                            <i class="fas fa-minus me-1"></i>No change
                        </div>
                    </div>
                    <i class="fas fa-clock fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="payment-card danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-danger mb-2">{{ $stats['failed'] ?? 0 }}</h3>
                        <p class="text-muted mb-1">Failed Payments</p>
                        <div class="trend-indicator trend-down">
                            <i class="fas fa-arrow-down me-1"></i>-3.2% from last month
                        </div>
                    </div>
                    <i class="fas fa-times-circle fa-2x text-danger opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="payment-card info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-info mb-2">{{ $stats['refunded'] ?? 0 }}</h3>
                        <p class="text-muted mb-1">Refunded</p>
                        <div class="trend-indicator trend-down">
                            <i class="fas fa-arrow-down me-1"></i>-8.1% from last month
                        </div>
                    </div>
                    <i class="fas fa-undo fa-2x text-info opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Gateway Performance -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Payment Gateway Performance
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="gatewayPerformanceChart" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-pie-chart me-2"></i>Payment Methods
                    </h5>
                </div>
                <div class="card-body">
                    <div class="payment-methods">
                        @foreach($paymentMethods as $method)
                            <div class="d-flex align-items-center mb-3">
                                <div class="payment-method-icon" style="background: {{ $method['color'] }};">
                                    <i class="{{ $method['icon'] }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $method['name'] }}</h6>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">{{ $method['count'] }} transactions</small>
                                        <small class="fw-bold">{{ $method['percentage'] }}%</small>
                                    </div>
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar" 
                                             style="width: {{ $method['percentage'] }}%; background: {{ $method['color'] }};"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search Transactions</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" 
                           placeholder="Transaction ID, customer..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="successful" {{ request('status') === 'successful' ? 'selected' : '' }}>Successful</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Gateway</label>
                <select class="form-select" name="gateway">
                    <option value="">All Gateways</option>
                    <option value="paystack" {{ request('gateway') === 'paystack' ? 'selected' : '' }}>Paystack</option>
                    <option value="flutterwave" {{ request('gateway') === 'flutterwave' ? 'selected' : '' }}>Flutterwave</option>
                    <option value="bank_transfer" {{ request('gateway') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" class="form-control" name="date_from" 
                       value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" class="form-control" name="date_to" 
                       value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Recent Transactions
                @if($transactions->total() > 0)
                    <span class="badge bg-primary ms-2">{{ $transactions->total() }}</span>
                @endif
            </h5>
            
            <div class="d-flex gap-2">
                <button class="btn btn-success btn-sm" onclick="reconcilePayments()">
                    <i class="fas fa-sync-alt me-1"></i>Reconcile
                </button>
                
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportTransactions('excel')">
                            <i class="fas fa-file-excel me-2"></i>Excel Report
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportTransactions('pdf')">
                            <i class="fas fa-file-pdf me-2"></i>PDF Report
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportTransactions('csv')">
                            <i class="fas fa-file-csv me-2"></i>CSV Data
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Transaction Details</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Gateway</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr class="transaction-row">
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $transaction->transaction_reference ?: 'N/A' }}</h6>
                                            <small class="text-muted">Receipt: {{ $transaction->receipt_number }}</small>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div>
                                            <strong>{{ $transaction->customer_name ?: 'Guest' }}</strong>
                                            @if($transaction->customer_email)
                                                <br><small class="text-muted">{{ $transaction->customer_email }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="amount-display text-primary">
                                            ₦{{ number_format($transaction->amount ?? 0, 2) }}
                                        </div>
                                    </td>
                                    
                                    <td>
                                        @if($transaction->payment_method)
                                            <span class="gateway-badge bg-info text-white">
                                                {{ ucfirst($transaction->payment_method) }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @php
                                            $status = $transaction->payment_gateway_status ?? 'pending';
                                            $statusColors = [
                                                'successful' => 'success',
                                                'pending' => 'warning',
                                                'failed' => 'danger',
                                                'refunded' => 'info',
                                                'cancelled' => 'secondary'
                                            ];
                                            $statusIcons = [
                                                'successful' => 'check-circle',
                                                'pending' => 'clock',
                                                'failed' => 'times-circle',
                                                'refunded' => 'undo',
                                                'cancelled' => 'ban'
                                            ];
                                        @endphp
                                        
                                        <span class="transaction-status bg-{{ $statusColors[$status] ?? 'secondary' }} text-white">
                                            <i class="fas fa-{{ $statusIcons[$status] ?? 'question' }} me-1"></i>
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <div>
                                            <strong>{{ $transaction->created_at->format('M d, Y') }}</strong>
                                            <br><small class="text-muted">{{ $transaction->created_at->format('g:i A') }}</small>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="btn-group-vertical" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                    onclick="viewTransaction({{ $transaction->id }})" 
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            @if($status === 'successful')
                                                <button type="button" class="btn btn-outline-warning btn-sm" 
                                                        onclick="initiateRefund({{ $transaction->id }})" 
                                                        title="Refund">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            @endif
                                            
                                            @if($status === 'pending')
                                                <button type="button" class="btn btn-outline-info btn-sm" 
                                                        onclick="checkStatus({{ $transaction->id }})" 
                                                        title="Check Status">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} 
                            of {{ $transactions->total() }} transactions
                        </div>
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-credit-card text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">No Transactions Found</h4>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'status', 'gateway', 'date_from', 'date_to']))
                            No transactions match your current filters. Try adjusting your search criteria.
                        @else
                            No payment transactions have been processed yet.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status', 'gateway', 'date_from', 'date_to']))
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transactionModalContent">
                <!-- Transaction details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gateway Performance Chart
const ctx = document.getElementById('gatewayPerformanceChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Paystack', 'Flutterwave', 'Bank Transfer', 'Others'],
        datasets: [{
            label: 'Success Rate %',
            data: [95.2, 92.8, 88.5, 85.1],
            backgroundColor: [
                'rgba(102, 126, 234, 0.8)',
                'rgba(40, 167, 69, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(108, 117, 125, 0.8)'
            ],
            borderColor: [
                'rgb(102, 126, 234)',
                'rgb(40, 167, 69)',
                'rgb(255, 193, 7)',
                'rgb(108, 117, 125)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});

// Payment management functions
function viewTransaction(transactionId) {
    showLoading('Loading transaction details...');
    
    fetch(`/admin/payments/${transactionId}/details`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                document.getElementById('transactionModalContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('transactionModal')).show();
            } else {
                showAlert('error', 'Failed to load transaction details');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Failed to load transaction details');
        });
}

function initiateRefund(transactionId) {
    if (confirm('Are you sure you want to initiate a refund for this transaction?')) {
        showLoading('Processing refund...');
        
        fetch(`/admin/payments/${transactionId}/refund`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert('success', 'Refund initiated successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.message || 'Failed to initiate refund');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Failed to initiate refund');
        });
    }
}

function checkStatus(transactionId) {
    showLoading('Checking payment status...');
    
    fetch(`/admin/payments/${transactionId}/check-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('success', `Status updated: ${data.status}`);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message || 'Failed to check status');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'Failed to check status');
    });
}

function reconcilePayments() {
    if (confirm('This will reconcile all pending payments with the payment gateways. Continue?')) {
        showLoading('Reconciling payments...');
        
        fetch('/admin/payments/reconcile', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert('success', `Reconciliation completed: ${data.updated_count} transactions updated`);
                setTimeout(() => location.reload(), 2000);
            } else {
                showAlert('error', data.message || 'Reconciliation failed');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Reconciliation failed');
        });
    }
}

function exportTransactions(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    
    showLoading('Generating export...');
    
    fetch(`{{ route('admin.payments.index') }}?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        hideLoading();
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Export failed');
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `payments-export-${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        showAlert('success', 'Export completed successfully!');
    })
    .catch(error => {
        showAlert('error', 'Export failed. Please try again.');
    });
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
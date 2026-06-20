@extends('layouts.app')

@section('title', 'Receipt Management')
@section('page-title', 'Receipt Analytics & Management')
@section('page-description', 'Monitor and manage all system receipts')

@push('styles')
<style>
    .receipt-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-left: 4px solid;
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .stat-card.success { border-left-color: #28a745; }
    .stat-card.warning { border-left-color: #ffc107; }
    .stat-card.danger { border-left-color: #dc3545; }
    .stat-card.info { border-left-color: #17a2b8; }
    
    .receipt-status {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .receipt-row:hover {
        background-color: #f8f9fa;
    }
    
    .filter-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .receipt-number {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        color: #495057;
    }
    
    .amount-display {
        font-weight: 700;
        font-size: 1.1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Receipt Analytics Header -->
    <div class="receipt-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">
                    <i class="fas fa-receipt me-3"></i>Receipt Management
                </h1>
                <p class="mb-0 opacity-75">
                    Monitor receipt generation, track payment statuses, and analyze system performance.
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex flex-column align-items-end">
                    <h3 class="mb-1">{{ $stats['total'] ?? 0 }}</h3>
                    <p class="mb-0 opacity-75">Total Receipts Generated</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-success mb-2">{{ $stats['successful'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Successful Payments</p>
                        <small class="text-success">
                            {{ $stats['total'] > 0 ? round(($stats['successful'] / $stats['total']) * 100, 1) : 0 }}% Success Rate
                        </small>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-warning mb-2">{{ $stats['pending'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Pending Payments</p>
                        <small class="text-warning">Awaiting Confirmation</small>
                    </div>
                    <i class="fas fa-clock fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-danger mb-2">{{ $stats['failed'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Failed Payments</p>
                        <small class="text-danger">
                            {{ $stats['total'] > 0 ? round(($stats['failed'] / $stats['total']) * 100, 1) : 0 }}% Failed
                        </small>
                    </div>
                    <i class="fas fa-times-circle fa-2x text-danger opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-info mb-2">₦{{ number_format($totalRevenue ?? 0, 2) }}</h3>
                        <p class="text-muted mb-0">Total Revenue</p>
                        <small class="text-info">This Month: ₦{{ number_format($monthlyRevenue ?? 0, 2) }}</small>
                    </div>
                    <i class="fas fa-naira-sign fa-2x text-info opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.receipts.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search Receipts</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" 
                           placeholder="Receipt number, customer..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Payment Status</label>
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="successful" {{ request('status') === 'successful' ? 'selected' : '' }}>Successful</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
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
            
            <div class="col-md-2">
                <label class="form-label">Shop</label>
                <select class="form-select" name="shop_id">
                    <option value="">All Shops</option>
                    @foreach(\App\Models\Shop::approved()->get() as $shop)
                        <option value="{{ $shop->id }}" {{ request('shop_id') == $shop->id ? 'selected' : '' }}>
                            {{ $shop->shop_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.receipts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Receipts List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>All Receipts
                @if($receipts->total() > 0)
                    <span class="badge bg-primary ms-2">{{ $receipts->total() }}</span>
                @endif
            </h5>
            
            <div class="d-flex gap-2">
                <button class="btn btn-success btn-sm" onclick="refreshData()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportReceipts('excel')">
                            <i class="fas fa-file-excel me-2"></i>Excel Report
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportReceipts('pdf')">
                            <i class="fas fa-file-pdf me-2"></i>PDF Report
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportReceipts('csv')">
                            <i class="fas fa-file-csv me-2"></i>CSV Data
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($receipts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Receipt Details</th>
                                <th>Customer Information</th>
                                <th>Shop</th>
                                <th>Amount & Payment</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipts as $receipt)
                                <tr class="receipt-row">
                                    <td>
                                        <div>
                                            <h6 class="mb-1 receipt-number">#{{ $receipt->receipt_number }}</h6>
                                            <small class="text-muted">
                                                @if($receipt->transaction_reference)
                                                    Ref: {{ $receipt->transaction_reference }}
                                                @else
                                                    No reference
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div>
                                            <strong>{{ $receipt->customer_name ?: 'N/A' }}</strong>
                                            @if($receipt->customer_email)
                                                <br><small class="text-muted">{{ $receipt->customer_email }}</small>
                                            @endif
                                            @if($receipt->customer_phone)
                                                <br><small class="text-muted">{{ $receipt->customer_phone }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td>
                                        @if($receipt->shop)
                                            <div>
                                                <strong>{{ $receipt->shop->shop_name }}</strong>
                                                <br><small class="text-muted">{{ $receipt->shop->state }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Shop not found</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        <div>
                                            <div class="amount-display text-primary">
                                                ₦{{ number_format($receipt->amount ?? 0, 2) }}
                                            </div>
                                            <small class="text-muted">
                                                @if($receipt->payment_method)
                                                    via {{ ucfirst($receipt->payment_method) }}
                                                @else
                                                    Payment method not specified
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        @php
                                            $status = $receipt->payment_gateway_status ?? 'pending';
                                            $statusColors = [
                                                'successful' => 'success',
                                                'pending' => 'warning',
                                                'failed' => 'danger',
                                                'cancelled' => 'secondary'
                                            ];
                                            $statusIcons = [
                                                'successful' => 'check-circle',
                                                'pending' => 'clock',
                                                'failed' => 'times-circle',
                                                'cancelled' => 'ban'
                                            ];
                                        @endphp
                                        
                                        <span class="receipt-status bg-{{ $statusColors[$status] ?? 'secondary' }} text-white">
                                            <i class="fas fa-{{ $statusIcons[$status] ?? 'question' }} me-1"></i>
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <div>
                                            <strong>{{ $receipt->created_at->format('M d, Y') }}</strong>
                                            <br><small class="text-muted">{{ $receipt->created_at->format('g:i A') }}</small>
                                            <br><small class="text-muted">{{ $receipt->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="btn-group-vertical" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                    onclick="viewReceipt({{ $receipt->id }})" 
                                                    title="View Receipt">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <button type="button" class="btn btn-outline-info btn-sm" 
                                                    onclick="downloadReceipt({{ $receipt->id }})" 
                                                    title="Download PDF">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            
                                            @if($receipt->payment_gateway_status === 'pending')
                                                <button type="button" class="btn btn-outline-warning btn-sm" 
                                                        onclick="checkPaymentStatus({{ $receipt->id }})" 
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
                            Showing {{ $receipts->firstItem() ?? 0 }} to {{ $receipts->lastItem() ?? 0 }} 
                            of {{ $receipts->total() }} receipts
                        </div>
                        {{ $receipts->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-receipt text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">No Receipts Found</h4>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to', 'shop_id']))
                            No receipts match your current filters. Try adjusting your search criteria.
                        @else
                            No receipts have been generated yet. Receipts will appear here once shops start generating them.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status', 'date_from', 'date_to', 'shop_id']))
                        <a href="{{ route('admin.receipts.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Receipt Details Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Receipt Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="receiptModalContent">
                <!-- Receipt details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="downloadReceiptFromModal()">
                    <i class="fas fa-download me-2"></i>Download PDF
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentReceiptId = null;

function viewReceipt(receiptId) {
    currentReceiptId = receiptId;
    showLoading('Loading receipt details...');
    
    fetch(`/admin/receipts/${receiptId}/details`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                document.getElementById('receiptModalContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('receiptModal')).show();
            } else {
                showAlert('error', 'Failed to load receipt details');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Failed to load receipt details');
        });
}

function downloadReceipt(receiptId) {
    showLoading('Generating PDF...');
    
    fetch(`/admin/receipts/${receiptId}/download`)
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
            a.style.display = 'none';
            a.href = url;
            a.download = `receipt-${receiptId}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            showAlert('success', 'Receipt downloaded successfully!');
        })
        .catch(error => {
            showAlert('error', 'Download failed. Please try again.');
        });
}

function downloadReceiptFromModal() {
    if (currentReceiptId) {
        downloadReceipt(currentReceiptId);
    }
}

function checkPaymentStatus(receiptId) {
    showLoading('Checking payment status...');
    
    fetch(`/admin/receipts/${receiptId}/check-status`, {
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
            showAlert('success', `Payment status updated: ${data.status}`);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message || 'Failed to check payment status');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'Failed to check payment status');
    });
}

function refreshData() {
    showLoading('Refreshing data...');
    location.reload();
}

function exportReceipts(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    
    showLoading('Generating export...');
    
    fetch(`{{ route('admin.receipts.index') }}?${params.toString()}`, {
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
        a.download = `receipts-export-${new Date().toISOString().split('T')[0]}.${format}`;
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

// Auto-refresh data every 2 minutes for real-time updates
setInterval(function() {
    if (!document.hidden) {
        // Only refresh if page is visible
        console.log('Auto-refreshing receipt data...');
        // You can implement a silent refresh here without reloading the page
    }
}, 120000);
</script>
@endpush
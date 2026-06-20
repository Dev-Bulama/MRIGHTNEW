@extends('layouts.app')

@section('title', 'Payment Overview')
@section('page-title', 'Payment Overview')
@section('page-description', 'Manage your shop payment transactions and history')

@push('styles')
<style>
.stats-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stats-card.success { border-left-color: #28a745; }
.stats-card.warning { border-left-color: #ffc107; } 
.stats-card.info { border-left-color: #17a2b8; }
.stats-card.primary { border-left-color: #007bff; }

.table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.btn-group .btn {
    margin: 0 1px;
}

.filter-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .btn-group-vertical .btn {
        margin-bottom: 4px;
    }
    
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
    
    .stats-card {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white border-bottom">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">
                                <i class="fas fa-money-check-alt me-2 text-primary"></i>Payment Management
                            </h5>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="printTable()">
                                    <i class="fas fa-print me-1"></i>Print
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="exportTable()">
                                    <i class="fas fa-file-excel me-1"></i>Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @php
                        $userReceipts = auth()->user()->receipts()->with('shop')->latest()->get();
                        $totalRevenue = $userReceipts->where('payment_status', 'paid')->sum('amount');
                        $pendingPayments = $userReceipts->where('payment_status', 'pending')->count();
                        $partialPayments = $userReceipts->where('payment_status', 'partial')->count();
                        $paidReceipts = $userReceipts->where('payment_status', 'paid')->count();
                    @endphp
                    
                    <!-- Enhanced Statistics -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card stats-card success h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-naira-sign fa-2x text-success mb-2"></i>
                                    <h3 class="mb-0">₦{{ number_format($totalRevenue, 2) }}</h3>
                                    <p class="mb-0 text-muted">Total Revenue</p>
                                    <small class="text-success">{{ $paidReceipts }} paid receipt{{ $paidReceipts !== 1 ? 's' : '' }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card stats-card warning h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                    <h3 class="mb-0">{{ $pendingPayments }}</h3>
                                    <p class="mb-0 text-muted">Pending Payments</p>
                                    <small class="text-warning">Awaiting payment</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card stats-card info h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-pie fa-2x text-info mb-2"></i>
                                    <h3 class="mb-0">{{ $partialPayments }}</h3>
                                    <p class="mb-0 text-muted">Part Payments</p>
                                    <small class="text-info">Partially paid</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card stats-card primary h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-receipt fa-2x text-primary mb-2"></i>
                                    <h3 class="mb-0">{{ $userReceipts->count() }}</h3>
                                    <p class="mb-0 text-muted">Total Receipts</p>
                                    <small class="text-primary">All transactions</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($userReceipts->count() > 0)
                        <!-- Filter Section -->
                        <div class="filter-section">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="statusFilter" class="form-label">Payment Status</label>
                                    <select class="form-select" id="statusFilter" onchange="filterTable()">
                                        <option value="">All Statuses</option>
                                        <option value="paid">Paid</option>
                                        <option value="partial">Part Payment</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="searchFilter" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="searchFilter" placeholder="Receipt #, customer name..." onkeyup="filterTable()">
                                </div>
                                <div class="col-md-2">
                                    <label for="dateFrom" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="dateFrom" onchange="filterTable()">
                                </div>
                                <div class="col-md-2">
                                    <label for="dateTo" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="dateTo" onchange="filterTable()">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-secondary w-100" onclick="clearFilters()" title="Clear Filters">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Payment Table -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="paymentsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Device</th>
                                        <th>Amount</th>
                                        <th>Payment Status</th>
                                        <th>Date</th>
                                        <th width="200" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userReceipts as $receipt)
                                        <tr id="receipt-{{ $receipt->id }}" 
                                            data-status="{{ $receipt->payment_status }}" 
                                            data-customer="{{ strtolower($receipt->customer_name) }}" 
                                            data-receipt="{{ strtolower($receipt->receipt_number) }}"
                                            data-date="{{ $receipt->created_at->format('Y-m-d') }}">
                                            <td>
                                                <div class="fw-bold text-primary">{{ $receipt->receipt_number }}</div>
                                                @if($receipt->receipt_type === 'resale')
                                                    <span class="badge bg-info bg-opacity-10 text-info">Resale</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $receipt->customer_name }}</div>
                                                @if($receipt->customer_email)
                                                    <small class="text-muted">{{ $receipt->customer_email }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="tel:{{ $receipt->customer_phone }}" class="text-decoration-none">
                                                    {{ $receipt->customer_phone }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ $receipt->phone_name ?? 'N/A' }}</div>
                                                @if($receipt->phone_color)
                                                    <small class="text-muted">{{ $receipt->phone_color }}</small>
                                                @endif
                                            </td>
                                            <td class="fw-bold">₦{{ number_format($receipt->amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $receipt->payment_status === 'paid' ? 'success' : ($receipt->payment_status === 'partial' ? 'info' : 'warning') }}" 
                                                      id="status-badge-{{ $receipt->id }}">
                                                    {{ $receipt->payment_status === 'paid' ? 'Paid' : ($receipt->payment_status === 'partial' ? 'Part Payment' : 'Pending') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ $receipt->created_at->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $receipt->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <!-- Mobile View -->
                                                <div class="d-md-none">
                                                    <div class="btn-group-vertical w-100">
                                                        @if($receipt->payment_status !== 'paid')
                                                            <button type="button" 
                                                                    class="btn btn-success btn-sm mb-1" 
                                                                    onclick="updatePaymentStatus({{ $receipt->id }}, 'paid')"
                                                                    title="Mark as Paid">
                                                                <i class="fas fa-check me-1"></i>Mark Paid
                                                            </button>
                                                        @endif
                                                        
                                                        @if($receipt->payment_status !== 'partial')
                                                            <button type="button" 
                                                                    class="btn btn-info btn-sm mb-1" 
                                                                    onclick="updatePaymentStatus({{ $receipt->id }}, 'partial')"
                                                                    title="Mark as Part Payment">
                                                                <i class="fas fa-clock me-1"></i>Part Payment
                                                            </button>
                                                        @endif
                                                        
                                                        @if($receipt->payment_status !== 'pending')
                                                            <button type="button" 
                                                                    class="btn btn-warning btn-sm mb-1" 
                                                                    onclick="updatePaymentStatus({{ $receipt->id }}, 'pending')"
                                                                    title="Mark as Pending">
                                                                <i class="fas fa-hourglass-half me-1"></i>Pending
                                                            </button>
                                                        @endif
                                                        
                                                        <a href="{{ route('receipt.show', $receipt) }}" 
                                                           class="btn btn-outline-primary btn-sm" 
                                                           title="View Receipt">
                                                            <i class="fas fa-eye me-1"></i>View Receipt
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- Desktop View -->
                                                <div class="d-none d-md-flex justify-content-center">
                                                    <div class="btn-group" role="group">
                                                        @if($receipt->payment_status !== 'paid')
                                                            <button type="button" 
                                                                    class="btn btn-success btn-sm" 
                                                                    onclick="updatePaymentStatus({{ $receipt->id }}, 'paid')"
                                                                    title="Mark as Paid"
                                                                    data-bs-toggle="tooltip">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @endif
                                                        
                                                        @if($receipt->payment_status !== 'partial')
                                                            <button type="button" 
                                                                    class="btn btn-info btn-sm" 
                                                                    onclick="updatePaymentStatus({{ $receipt->id }}, 'partial')"
                                                                    title="Mark as Part Payment"
                                                                    data-bs-toggle="tooltip">
                                                                <i class="fas fa-clock"></i>
                                                            </button>
                                                        @endif
                                                        
                                                        @if($receipt->payment_status !== 'pending')
                                                            <button type="button" 
                                                                    class="btn btn-warning btn-sm" 
                                                                    onclick="updatePaymentStatus({{ $receipt->id }}, 'pending')"
                                                                    title="Mark as Pending"
                                                                    data-bs-toggle="tooltip">
                                                                <i class="fas fa-hourglass-half"></i>
                                                            </button>
                                                        @endif
                                                        
                                                        <a href="{{ route('receipt.show', $receipt) }}" 
                                                           class="btn btn-outline-primary btn-sm" 
                                                           title="View Receipt"
                                                           data-bs-toggle="tooltip">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Table Footer -->
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <small class="text-muted">
                                Showing <span id="visibleCount">{{ $userReceipts->count() }}</span> of {{ $userReceipts->count() }} receipt{{ $userReceipts->count() !== 1 ? 's' : '' }}
                            </small>
                            <small class="text-muted">
                                Last updated: {{ $userReceipts->first()->updated_at ?? now() }}
                            </small>
                        </div>

                        <!-- Success/Error Messages -->
                        <div id="payment-messages"></div>

                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <i class="fas fa-receipt text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h4 class="text-muted">No Transactions Yet</h4>
                            <p class="text-muted mb-4">Start generating receipts to see payment transactions here.</p>
                            <a href="{{ route('receipt.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Generate First Receipt
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updatePaymentStatus(receiptId, status) {
    // Show loading state
    const row = document.getElementById(`receipt-${receiptId}`);
    const buttons = row.querySelectorAll('button[onclick]');
    
    // Show confirmation dialog
    const statusText = status === 'paid' ? 'Paid in Full' : (status === 'partial' ? 'Part Payment' : 'Pending');
    if (!confirm(`Are you sure you want to mark this receipt as "${statusText}"?`)) {
        return;
    }
    
    buttons.forEach(btn => btn.disabled = true);
    
    // Show loading message
    showMessage('Updating payment status...', 'info');
    
    fetch(`/receipts/${receiptId}/update-payment-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            payment_status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update badge
            const badge = document.getElementById(`status-badge-${receiptId}`);
            const badgeText = status === 'paid' ? 'Paid' : (status === 'partial' ? 'Part Payment' : 'Pending');
            const badgeClass = status === 'paid' ? 'bg-success' : (status === 'partial' ? 'bg-info' : 'bg-warning');
            
            badge.textContent = badgeText;
            badge.className = `badge ${badgeClass}`;
            
            // Update row data attribute
            row.setAttribute('data-status', status);
            
            // Show success message
            showMessage(data.message, 'success');
            
            // Refresh page after 3 seconds to update statistics and button states
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            showMessage(data.message || 'Failed to update payment status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while updating payment status', 'error');
    })
    .finally(() => {
        // Re-enable buttons
        buttons.forEach(btn => btn.disabled = false);
    });
}

function showMessage(message, type) {
    const messageContainer = document.getElementById('payment-messages');
    const alertClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-info');
    
    messageContainer.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show mt-3" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-triangle' : 'info-circle')} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = messageContainer.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

function filterTable() {
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    const table = document.getElementById('paymentsTable');
    const rows = table.getElementsByTagName('tr');
    let visibleCount = 0;
    
    for (let i = 1; i < rows.length; i++) { // Start from 1 to skip header
        const row = rows[i];
        const status = row.getAttribute('data-status');
        const customer = row.getAttribute('data-customer');
        const receipt = row.getAttribute('data-receipt');
        const date = row.getAttribute('data-date');
        
        let showRow = true;
        
        // Status filter
        if (statusFilter && status !== statusFilter) {
            showRow = false;
        }
        
        // Search filter
        if (searchFilter && !customer.includes(searchFilter) && !receipt.includes(searchFilter)) {
            showRow = false;
        }
        
        // Date filters
        if (dateFrom && date < dateFrom) {
            showRow = false;
        }
        
        if (dateTo && date > dateTo) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    }
    
    // Update visible count
    document.getElementById('visibleCount').textContent = visibleCount;
}

function clearFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('searchFilter').value = '';
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    filterTable();
}

function printTable() {
    const printContent = document.getElementById('paymentsTable').outerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Payment Report - {{ auth()->user()->shop->shop_name ?? 'Shop' }}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    @media print {
                        .btn, .btn-group { display: none !important; }
                        th:last-child, td:last-child { display: none !important; }
                        body { font-size: 12px; }
                        .table { margin-bottom: 0; }
                    }
                </style>
            </head>
            <body class="p-4">
                <div class="text-center mb-4">
                    <h2>Payment Report</h2>
                    <p class="text-muted">{{ auth()->user()->shop->shop_name ?? 'Shop Name' }} - Generated on ${new Date().toLocaleDateString()}</p>
                </div>
                ${printContent}
                <div class="mt-3 text-center">
                    <small class="text-muted">This report was generated automatically by M-right Digital Receipt System</small>
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function exportTable() {
    const table = document.getElementById('paymentsTable');
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    // Headers
    const headers = ['Receipt #', 'Customer', 'Phone', 'Device', 'Amount', 'Status', 'Date'];
    csv.push(headers.join(','));
    
    // Data rows (only visible ones)
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        if (row.style.display !== 'none') {
            const cols = row.querySelectorAll('td');
            
            if (cols.length > 0) {
                let csvRow = [];
                csvRow.push(`"${cols[0].textContent.trim().replace(/"/g, '""')}"`);
                csvRow.push(`"${cols[1].textContent.trim().replace(/"/g, '""')}"`);
                csvRow.push(`"${cols[2].textContent.trim()}"`);
                csvRow.push(`"${cols[3].textContent.trim().replace(/"/g, '""')}"`);
                csvRow.push(`"${cols[4].textContent.trim()}"`);
                csvRow.push(`"${cols[5].textContent.trim()}"`);
                csvRow.push(`"${cols[6].textContent.trim().replace(/"/g, '""')}"`);
                csv.push(csvRow.join(','));
            }
        }
    }
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `payment_report_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showMessage('Payment report exported successfully!', 'success');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>
@endpush
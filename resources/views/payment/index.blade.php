@extends('layouts.app')

@section('title', 'Payment Overview')
@section('page-title', 'Payment Overview')
@section('page-description', 'Manage and track payment status for all receipts')

@push('styles')
<style>
.payment-stats-card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.payment-stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.payment-table th {
    background-color: #f8f9fc;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    padding: 1rem;
}

.payment-table td {
    padding: 1rem;
    vertical-align: middle;
    border-top: 1px solid #e3e6f0;
}

.status-update-btn {
    transition: all 0.3s ease;
}

.status-update-btn:hover {
    transform: scale(1.05);
}

.receipt-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.bulk-actions {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.filter-card {
    background: #f8f9fc;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Payment Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card payment-stats-card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Paid
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['paid'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card payment-stats-card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Part Payment
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['partial'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card payment-stats-card border-left-secondary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['pending'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card payment-stats-card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₦{{ number_format($stats['total_revenue'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulkActions" style="display: none;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-1 font-weight-bold">Bulk Actions</h6>
                <small><span id="selectedCount">0</span> receipt(s) selected</small>
            </div>
            <div>
                <button type="button" class="btn btn-light btn-sm me-2" onclick="updateBulkStatus('paid')">
                    <i class="fas fa-check me-1"></i>Mark as Paid
                </button>
                <button type="button" class="btn btn-warning btn-sm me-2" onclick="updateBulkStatus('partial')">
                    <i class="fas fa-clock me-1"></i>Part Payment
                </button>
                <button type="button" class="btn btn-secondary btn-sm me-2" onclick="updateBulkStatus('pending')">
                    <i class="fas fa-hourglass me-1"></i>Mark as Pending
                </button>
                <button type="button" class="btn btn-outline-light btn-sm" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i>Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('payments.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="payment_status" class="form-label">Payment Status</label>
                <select class="form-select" name="payment_status" id="payment_status">
                    <option value="">All Status</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Part Payment</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" name="search" id="search" 
                       value="{{ request('search') }}" placeholder="Receipt number, customer name...">
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-refresh"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Payment Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Payment Overview</h6>
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleSelectAll()">
                    <i class="fas fa-check-square me-1"></i>Select All
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table payment-table mb-0">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                            </th>
                            <th>Receipt</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipts as $receipt)
                        <tr>
                            <td>
                                <input type="checkbox" class="receipt-checkbox" value="{{ $receipt->id }}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $receipt->receipt_number }}</div>
                                <small class="text-muted">{{ $receipt->phone_name }}</small>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $receipt->customer_name }}</div>
                                <small class="text-muted">{{ $receipt->customer_email ?? 'No email' }}</small>
                            </td>
                            <td>{{ $receipt->customer_phone }}</td>
                            <td class="font-weight-bold">₦{{ number_format($receipt->amount, 2) }}</td>
                            <td>
                                <span class="payment-status-badge" data-receipt-id="{{ $receipt->id }}">
                                    @if($receipt->payment_status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($receipt->payment_status == 'partial')
                                        <span class="badge bg-warning">Part Payment</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div>{{ $receipt->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $receipt->created_at->format('g:i A') }}</small>
                            </td>
                            <td>
                                <div class="receipt-actions">
                                    <button type="button" class="btn btn-sm btn-outline-primary status-update-btn" 
                                            onclick="showStatusUpdateModal({{ $receipt->id }}, '{{ $receipt->payment_status }}', '{{ $receipt->receipt_number }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="{{ route('receipt.view', $receipt->id) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-receipt fa-3x mb-3"></i>
                                    <p>No receipts found</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($receipts->hasPages())
        <div class="card-footer">
            {{ $receipts->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Payment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusUpdateForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Receipt</label>
                        <input type="text" class="form-control" id="modalReceiptNumber" readonly>
                        <input type="hidden" id="modalReceiptId">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Status</label>
                        <select class="form-select" id="modalPaymentStatus" required>
                            <option value="paid">Paid</option>
                            <option value="partial">Part Payment</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="modalNotes" rows="3" 
                                  placeholder="Add any notes about this payment status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Selected receipt IDs for bulk actions
let selectedReceipts = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateBulkActions();
});

// Toggle select all
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.receipt-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkActions();
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.receipt-checkbox:checked');
    selectedReceipts = Array.from(checkboxes).map(cb => cb.value);
    
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    if (selectedReceipts.length > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = selectedReceipts.length;
    } else {
        bulkActions.style.display = 'none';
    }
}

// Clear selection
function clearSelection() {
    document.querySelectorAll('.receipt-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAllCheckbox').checked = false;
    updateBulkActions();
}

// Show status update modal
function showStatusUpdateModal(receiptId, currentStatus, receiptNumber) {
    document.getElementById('modalReceiptId').value = receiptId;
    document.getElementById('modalReceiptNumber').value = receiptNumber;
    document.getElementById('modalPaymentStatus').value = currentStatus;
    document.getElementById('modalNotes').value = '';
    
    new bootstrap.Modal(document.getElementById('statusUpdateModal')).show();
}

// Handle status update form submission
document.getElementById('statusUpdateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const receiptId = document.getElementById('modalReceiptId').value;
    const paymentStatus = document.getElementById('modalPaymentStatus').value;
    const notes = document.getElementById('modalNotes').value;
    
    updatePaymentStatus(receiptId, paymentStatus, notes);
});

// Update payment status
function updatePaymentStatus(receiptId, paymentStatus, notes = '') {
    const submitBtn = document.querySelector('#statusUpdateModal button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
    
    fetch(`/payments/update-status/${receiptId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            payment_status: paymentStatus,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the status badge in the table
            const statusBadge = document.querySelector(`[data-receipt-id="${receiptId}"]`);
            if (statusBadge) {
                statusBadge.innerHTML = data.status_badge;
            }
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal')).hide();
            
            // Show success message
            showToast('Payment status updated successfully!', 'success');
            
            // Optionally refresh the page to update stats
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Failed to update status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Bulk status update
function updateBulkStatus(newStatus) {
    if (selectedReceipts.length === 0) {
        showToast('Please select receipts to update', 'warning');
        return;
    }
    
    if (!confirm(`Are you sure you want to update ${selectedReceipts.length} receipt(s) to ${newStatus}?`)) {
        return;
    }
    
    fetch('/payments/bulk-update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            receipt_ids: selectedReceipts,
            payment_status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Failed to update status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 4000);
}
</script>
@endpush
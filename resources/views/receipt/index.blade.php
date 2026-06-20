@extends('layouts.app')

@section('title', 'My Receipts')
@section('page-title', 'My Receipts')
@section('page-description', 'Manage all your generated digital receipts')

@push('styles')
<style>
.receipts-hero {
    background: linear-gradient(135deg, #0d8abc 0%, #4dabf7 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.receipts-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: #0d8abc;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: white;
}

.stat-icon.total { background: linear-gradient(135deg, #0d8abc, #4dabf7); }
.stat-icon.active { background: linear-gradient(135deg, #28a745, #20c997); }
.stat-icon.paid { background: linear-gradient(135deg, #28a745, #20c997); }
.stat-icon.pending { background: linear-gradient(135deg, #ffc107, #fd7e14); }

.filters-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.receipt-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border-left: 4px solid #0d8abc;
}

.receipt-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.receipt-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.receipt-number {
    font-size: 1.2rem;
    font-weight: 600;
    color: #0d8abc;
    font-family: 'Courier New', monospace;
}

.receipt-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.receipt-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.info-group {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
}

.info-label {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 500;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-weight: 600;
    color: #343a40;
}

.receipt-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .receipts-hero {
        padding: 1.5rem;
    }
    
    .stats-cards {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .receipt-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .receipt-info {
        grid-template-columns: 1fr;
    }
    
    .filter-form {
        grid-template-columns: 1fr;
    }
    
    .receipt-actions {
        flex-direction: column;
    }
}

.table-view .table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.table-view .table th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
}

.view-toggle {
    background: white;
    border-radius: 8px;
    padding: 0.5rem;
    margin-bottom: 1rem;
    border: 1px solid #e9ecef;
}

.view-toggle-btn {
    background: none;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.3s ease;
    color: #6c757d;
}

.view-toggle-btn.active {
    background: #0d8abc;
    color: white;
}
</style>
@endpush

@section('content')
<!-- Hero Section -->
<div class="receipts-hero">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold mb-2">My Receipts</h1>
            <p class="lead mb-0">Manage and track all your generated digital receipts</p>
        </div>
        <div class="col-md-4 text-center">
            <i class="fas fa-receipt" style="font-size: 4rem; opacity: 0.3;"></i>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-cards">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-receipt"></i>
        </div>
        <h3 class="mb-1 fw-bold">{{ $stats['total'] }}</h3>
        <p class="text-muted mb-0">Total Receipts</p>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon active">
            <i class="fas fa-check-circle"></i>
        </div>
        <h3 class="mb-1 fw-bold">{{ $stats['active'] }}</h3>
        <p class="text-muted mb-0">Active</p>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon paid">
            <i class="fas fa-credit-card"></i>
        </div>
        <h3 class="mb-1 fw-bold">{{ $stats['paid'] }}</h3>
        <p class="text-muted mb-0">Paid</p>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <h3 class="mb-1 fw-bold">{{ $stats['pending'] }}</h3>
        <p class="text-muted mb-0">Pending Payment</p>
    </div>
</div>

<!-- Filters Section -->
<div class="filters-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            <i class="fas fa-filter me-2"></i>Filter Receipts
        </h5>
        <div class="view-toggle">
            <button class="view-toggle-btn active" id="cardViewBtn" onclick="switchView('card')">
                <i class="fas fa-th-large me-1"></i>Cards
            </button>
            <button class="view-toggle-btn" id="tableViewBtn" onclick="switchView('table')">
                <i class="fas fa-table me-1"></i>Table
            </button>
        </div>
    </div>
    
    <form method="GET" action="{{ route('receipt.index') }}" id="filterForm">
        <div class="filter-form">
            <div>
                <label class="form-label fw-medium">Search</label>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Receipt #, customer, phone..."
                       value="{{ request('search') }}">
            </div>
            
            <div>
                <label class="form-label fw-medium">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="void" {{ request('status') === 'void' ? 'selected' : '' }}>Void</option>
                </select>
            </div>
            
            <div>
                <label class="form-label fw-medium">Payment</label>
                <select class="form-select" name="payment_status">
                    <option value="">All Payments</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                </select>
            </div>
            
            <div>
                <label class="form-label fw-medium">From Date</label>
                <input type="date" 
                       class="form-control" 
                       name="date_from" 
                       value="{{ request('date_from') }}">
            </div>
            
            <div>
                <label class="form-label fw-medium">To Date</label>
                <input type="date" 
                       class="form-control" 
                       name="date_to" 
                       value="{{ request('date_to') }}">
            </div>
            
            <div>
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('receipt.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Action Bar -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <p class="mb-0 text-muted">
            @if($receipts->total() > 0)
                Showing {{ $receipts->firstItem() }} to {{ $receipts->lastItem() }} of {{ $receipts->total() }} receipts
            @else
                No receipts found
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('receipt.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Generate New Receipt
        </a>
        @if($receipts->total() > 0)
            <button class="btn btn-outline-success" onclick="exportReceipts()">
                <i class="fas fa-download me-1"></i>Export
            </button>
        @endif
    </div>
</div>

<!-- Receipts Display -->
@if($receipts->total() > 0)
    <!-- Card View (Default) -->
    <div id="cardView">
        @foreach($receipts as $receipt)
            <div class="receipt-card">
                <div class="receipt-header">
                    <div>
                        <div class="receipt-number">{{ $receipt->receipt_number }}</div>
                        <small class="text-muted">{{ $receipt->created_at->format('M j, Y \a\t g:i A') }}</small>
                    </div>
                    <div class="receipt-badges">
                        @if($receipt->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @elseif($receipt->status === 'cancelled')
                            <span class="badge bg-danger">Cancelled</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($receipt->status) }}</span>
                        @endif
                        
                        @if($receipt->payment_status === 'paid')
                            <span class="badge bg-success">Paid</span>
                        @elseif($receipt->payment_status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($receipt->payment_status === 'partial')
                            <span class="badge bg-info">Partial</span>
                        @else
                            <span class="badge bg-danger">{{ ucfirst($receipt->payment_status) }}</span>
                        @endif
                        
                        @if($receipt->receipt_type === 'resale')
                            <span class="badge bg-info">Resale</span>
                        @endif
                        
                        @if($receipt->enable_antitheft)
                            <span class="badge bg-warning">
                                <i class="fas fa-shield-alt me-1"></i>Protected
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="receipt-info">
                    <div class="info-group">
                        <div class="info-label">Customer</div>
                        <div class="info-value">{{ $receipt->customer_name }}</div>
                        <small class="text-muted">{{ $receipt->customer_phone }}</small>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Phone Details</div>
                        <div class="info-value">{{ $receipt->phone_name }}</div>
                        <small class="text-muted">{{ $receipt->phone_color }} • SN: {{ $receipt->phone_serial_number }}</small>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Amount</div>
                        <div class="info-value text-success">₦{{ number_format($receipt->amount, 2) }}</div>
                        <small class="text-muted">Service fee: ₦{{ number_format($receipt->service_fee ?? 0, 2) }}</small>
                    </div>
                    
                    <div class="info-group">
                        <div class="info-label">Generated</div>
                        <div class="info-value">{{ $receipt->created_at->diffForHumans() }}</div>
                        <small class="text-muted">{{ $receipt->created_at->format('g:i A') }}</small>
                    </div>
                </div>
                
                <div class="receipt-actions">
                    <a href="{{ route('receipt.view', $receipt) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>View
                    </a>
                    <a href="{{ route('receipt.download', $receipt) }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-download me-1"></i>Download
                    </a>
                    <button class="btn btn-outline-info btn-sm" onclick="printReceipt({{ $receipt->id }})">
                        <i class="fas fa-print me-1"></i>Print
                    </button>
                    @if($receipt->payment_status === 'pending')
                        <button class="btn btn-outline-warning btn-sm" onclick="retryPayment({{ $receipt->id }})">
                            <i class="fas fa-redo me-1"></i>Retry Payment
                        </button>
                    @endif
                    @if($receipt->status === 'active' && $receipt->receipt_type === 'sale')
                        <a href="{{ route('receipt.resale') }}?parent={{ $receipt->id }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-sync-alt me-1"></i>Mark as Resale
                        </a>
                    @endif
                    <div class="dropdown d-inline">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('receipt.show', $receipt) }}">
                                    <i class="fas fa-info-circle me-2"></i>Details
                                </a>
                            </li>
                            @if($receipt->customer_email)
                                <li>
                                    <a class="dropdown-item" href="#" onclick="resendEmail({{ $receipt->id }})">
                                        <i class="fas fa-envelope me-2"></i>Resend Email
                                    </a>
                                </li>
                            @endif
                            @if($receipt->status === 'active')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <!-- <a class="dropdown-item text-danger" href="#" onclick="cancelReceipt({{ $receipt->id }})">
                                        <i class="fas fa-times me-2"></i>Cancel Receipt
                                    </a> -->
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Table View (Hidden by default) -->
    <div id="tableView" class="table-view d-none">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipts as $receipt)
                        <tr>
                            <td>
                                <div class="fw-medium text-primary">{{ $receipt->receipt_number }}</div>
                                @if($receipt->receipt_type === 'resale')
                                    <span class="badge bg-info mt-1">Resale</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-medium">{{ $receipt->customer_name }}</div>
                                <small class="text-muted">{{ $receipt->customer_phone }}</small>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $receipt->phone_name }}</div>
                                <small class="text-muted">{{ $receipt->phone_color }}</small>
                                @if($receipt->enable_antitheft)
                                    <br><span class="badge bg-warning mt-1">
                                        <i class="fas fa-shield-alt"></i>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-success">₦{{ number_format($receipt->amount, 0) }}</span>
                            </td>
                            <td>
                                @if($receipt->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($receipt->status === 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($receipt->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($receipt->payment_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($receipt->payment_status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-info">{{ ucfirst($receipt->payment_status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $receipt->created_at->format('M j, Y') }}</div>
                                <small class="text-muted">{{ $receipt->created_at->format('g:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('receipt.view', $receipt) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('receipt.download', $receipt) }}" class="btn btn-outline-success">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('receipt.show', $receipt) }}">Details</a></li>
                                            @if($receipt->customer_email)
                                                <li><a class="dropdown-item" href="#" onclick="resendEmail({{ $receipt->id }})">Resend Email</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $receipts->links() }}
    </div>
    
@else
    <!-- Empty State -->
    <div class="empty-state">
        <i class="fas fa-receipt"></i>
        <h4 class="fw-bold mb-3">No Receipts Found</h4>
        <p class="mb-4">
            @if(request()->hasAny(['search', 'status', 'payment_status', 'date_from', 'date_to']))
                No receipts match your current filters. Try adjusting your search criteria.
            @else
                You haven't generated any receipts yet. Create your first digital receipt to get started.
            @endif
        </p>
        
        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ route('receipt.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Generate First Receipt
            </a>
            @if(request()->hasAny(['search', 'status', 'payment_status', 'date_from', 'date_to']))
                <a href="{{ route('receipt.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear Filters
                </a>
            @endif
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Setup auto-submit on filter changes
    setupFilterAutoSubmit();
    
    // Load saved view preference
    loadViewPreference();
});

function setupFilterAutoSubmit() {
    const form = document.getElementById('filterForm');
    const selects = form.querySelectorAll('select');
    const dateInputs = form.querySelectorAll('input[type="date"]');
    
    selects.forEach(select => {
        select.addEventListener('change', () => {
            form.submit();
        });
    });
    
    dateInputs.forEach(input => {
        input.addEventListener('change', () => {
            form.submit();
        });
    });
    
    // Search input with debounce
    const searchInput = form.querySelector('input[name="search"]');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            form.submit();
        }, 1000);
    });
}

function switchView(viewType) {
    const cardView = document.getElementById('cardView');
    const tableView = document.getElementById('tableView');
    const cardBtn = document.getElementById('cardViewBtn');
    const tableBtn = document.getElementById('tableViewBtn');
    
    if (viewType === 'table') {
        cardView.classList.add('d-none');
        tableView.classList.remove('d-none');
        cardBtn.classList.remove('active');
        tableBtn.classList.add('active');
        
        localStorage.setItem('receipts_view_preference', 'table');
    } else {
        cardView.classList.remove('d-none');
        tableView.classList.add('d-none');
        cardBtn.classList.add('active');
        tableBtn.classList.remove('active');
        
        localStorage.setItem('receipts_view_preference', 'card');
    }
}

function loadViewPreference() {
    const savedView = localStorage.getItem('receipts_view_preference');
    if (savedView === 'table') {
        switchView('table');
    }
}

function printReceipt(receiptId) {
    const printUrl = `{{ route('receipt.view', '') }}/${receiptId}`;
    const printWindow = window.open(printUrl, '_blank');
    
    printWindow.onload = function() {
        printWindow.print();
    };
}

function retryPayment(receiptId) {
    if (confirm('Do you want to retry the payment for this receipt?')) {
        fetch(`{{ route('payment.retry', '') }}/${receiptId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Payment retry initiated successfully', 'success');
                if (data.payment_url) {
                    window.open(data.payment_url, '_blank');
                }
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Error retrying payment', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error retrying payment', 'danger');
        });
    }
}

function resendEmail(receiptId) {
    if (confirm('Resend receipt email to customer?')) {
        fetch(`{{ route('api.receipt.resend-email', '') }}/${receiptId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Email sent successfully!', 'success');
            } else {
                showToast('Failed to send email', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error sending email', 'danger');
        });
    }
}

function cancelReceipt(receiptId) {
    if (confirm('Are you sure you want to cancel this receipt? This action cannot be undone.')) {
        fetch(`{{ route('receipt.update-status', '') }}/${receiptId}`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: 'cancelled' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Receipt cancelled successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Error cancelling receipt', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error cancelling receipt', 'danger');
        });
    }
}

function exportReceipts() {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('export', 'csv');
    
    const link = document.createElement('a');
    link.href = currentUrl.toString();
    link.download = `receipts_export_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showToast('Exporting receipts...', 'info');
}

// Bulk actions
function selectAllReceipts() {
    const checkboxes = document.querySelectorAll('.receipt-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllReceipts');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const selectedCount = document.querySelectorAll('.receipt-checkbox:checked').length;
    const bulkActions = document.getElementById('bulkActions');
    
    if (selectedCount > 0) {
        bulkActions.classList.remove('d-none');
        bulkActions.querySelector('.selected-count').textContent = selectedCount;
    } else {
        bulkActions.classList.add('d-none');
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + N - New receipt
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        window.location.href = '{{ route("receipt.create") }}';
    }
    
    // Ctrl/Cmd + F - Focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.querySelector('input[name="search"]').focus();
    }
    
    // V - Switch view
    if (e.key === 'v' && !e.ctrlKey && !e.metaKey) {
        const currentView = document.getElementById('cardView').classList.contains('d-none') ? 'table' : 'card';
        switchView(currentView === 'table' ? 'card' : 'table');
    }
});
</script>
@endpush
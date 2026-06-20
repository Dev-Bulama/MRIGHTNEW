@extends('layouts.app')

@section('title', 'Payment Management')
@section('page-title', 'Payment Management')
@section('page-description', 'Monitor and manage payment transactions')

@push('styles')
<style>
.payment-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    border-left: 4px solid;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-card.success { border-left-color: #28a745; }
.stat-card.warning { border-left-color: #ffc107; }
.stat-card.danger { border-left-color: #dc3545; }
.stat-card.info { border-left-color: #17a2b8; }

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
}

.payment-filters {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.payments-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.table th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
    padding: 1rem;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
}

.payment-method-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.payment-method-badge i {
    margin-right: 0.5rem;
}

.method-paystack {
    background: linear-gradient(135deg, #0d8abc, #4dabf7);
    color: white;
}

.method-bank {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.method-cash {
    background: linear-gradient(135deg, #6c757d, #adb5bd);
    color: white;
}

.payment-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.8rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-action:hover {
    transform: translateY(-1px);
}

.anti-theft-status {
    padding: 2rem;
    background: linear-gradient(135deg, #17a2b8, #20c997);
    color: white;
    border-radius: 12px;
    margin-bottom: 2rem;
}

.api-status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.api-status-indicator.online {
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
}

.api-status-indicator.offline {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

@media (max-width: 768px) {
    .payment-stats {
        grid-template-columns: 1fr;
    }
    
    .payment-actions {
        flex-direction: column;
    }
    
    .table-responsive {
        font-size: 0.9rem;
    }
}
</style>
@endpush

@section('content')
<!-- Payment Statistics -->
<div class="payment-stats">
    <div class="stat-card success">
        <div class="stat-number text-success">₦{{ number_format(125000000, 2) }}</div>
        <div class="stat-label">Total Revenue</div>
        <small class="text-muted">All successful payments</small>
    </div>
    
    <div class="stat-card info">
        <div class="stat-number text-info">2,847</div>
        <div class="stat-label">Successful Payments</div>
        <small class="text-muted">This month: 423</small>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-number text-warning">156</div>
        <div class="stat-label">Pending Payments</div>
        <small class="text-muted">Awaiting processing</small>
    </div>
    
    <div class="stat-card danger">
        <div class="stat-number text-danger">89</div>
        <div class="stat-label">Failed Payments</div>
        <small class="text-muted">Last 7 days</small>
    </div>
</div>

<!-- Anti-theft API Status -->
<div class="anti-theft-status">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h5 class="mb-2">
                <i class="fas fa-shield-alt me-2"></i>
                Anti-theft Integration Status
            </h5>
            <p class="mb-0">Real-time phone theft monitoring and alerts system</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="api-status-indicator online" id="antiTheftStatus">
                <div class="status-dot"></div>
                API Online
            </div>
            <br>
            <small class="text-light opacity-75">Last checked: <span id="lastChecked">Just now</span></small>
        </div>
    </div>
</div>

<!-- Payment Filters -->
<div class="payment-filters">
    <form method="GET" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="successful" {{ request('status') == 'successful' ? 'selected' : '' }}>Successful</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        
        <div class="col-md-3">
            <label class="form-label">Payment Method</label>
            <select name="method" class="form-select">
                <option value="">All Methods</option>
                <option value="paystack" {{ request('method') == 'paystack' ? 'selected' : '' }}>Paystack</option>
                <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
            </select>
        </div>
        
        <div class="col-md-2">
            <label class="form-label">From Date</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        
        <div class="col-md-2">
            <label class="form-label">To Date</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-filter me-1"></i>Filter
            </button>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-undo me-1"></i>Reset
            </a>
        </div>
    </form>
</div>

<!-- Payments Table -->
<div class="payments-table">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Payment Info</th>
                    <th>Customer</th>
                    <th>Receipt</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Sample payment rows - in production, this would be populated from database -->
                <tr>
                    <td>
                        <div>
                            <strong>PAY-ABC123456</strong>
                            <br><small class="text-muted">Gateway: pk_test_***4567</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <strong>John Doe</strong>
                            <br><small class="text-muted">john@example.com</small>
                            <br><small class="text-muted">+234 803 123 4567</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <strong>MR-TEC20240731001</strong>
                            <br><small class="text-muted">iPhone 14 Pro Max</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <strong>₦850,000.00</strong>
                            <br><small class="text-muted">Fee: ₦17,000.00</small>
                        </div>
                    </td>
                    <td>
                        <span class="payment-method-badge method-paystack">
                            <i class="fas fa-credit-card"></i>Paystack
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-success">Successful</span>
                        <br><small class="text-muted">2 mins ago</small>
                    </td>
                    <td>
                        <div>
                            <strong>{{ now()->format('M d, Y') }}</strong>
                            <br><small class="text-muted">{{ now()->format('g:i A') }}</small>
                        </div>
                    </td>
                    <td>
                        <div class="payment-actions">
                            <button class="btn btn-action btn-outline-primary" onclick="viewPayment('PAY-ABC123456')" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-action btn-outline-success" onclick="downloadReceipt('MR-TEC20240731001')" title="Download Receipt">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="btn btn-action btn-outline-warning" onclick="refundPayment('PAY-ABC123456')" title="Process Refund">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <div>
                            <strong>PAY-DEF789012</strong>
                            <br><small class="text-muted">Bank Transfer</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <strong>Jane Smith</strong>
                            <br><small class="text-muted">jane@example.com</small>
                            <br><small class="text-muted">+234 805 987 6543</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <strong>MR-TEC20240731002</strong>
                            <br><small class="text-muted">Samsung Galaxy S24</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <strong>₦650,000.00</strong>
                            <br><small class="text-muted">Fee: ₦13,000.00</small>
                        </div>
                    </td>
                    <td>
                        <span class="payment-method-badge method-bank">
                            <i class="fas fa-university"></i>Bank Transfer
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-warning">Pending</span>
                        <br><small class="text-muted">Awaiting verification</small>
                    </td>
                    <td>
                        <div>
                            <strong>{{ now()->subHours(2)->format('M d, Y') }}</strong>
                            <br><small class="text-muted">{{ now()->subHours(2)->format('g:i A') }}</small>
                        </div>
                    </td>
                    <td>
                        <div class="payment-actions">
                            <button class="btn btn-action btn-outline-primary" onclick="viewPayment('PAY-DEF789012')" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-action btn-outline-success" onclick="verifyBankTransfer('PAY-DEF789012')" title="Verify Transfer">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-action btn-outline-danger" onclick="cancelPayment('PAY-DEF789012')" title="Cancel Payment">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <div>
                            <strong>PAY-GHI345678</strong>
                            <br><small class="text-muted">Gateway: Declined</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <strong>Mike Johnson</strong>
                            <br><small class="text-muted">mike@example.com</small>
                            <br><small class="text-muted">+234 807 555 1234</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <strong>MR-TEC20240731003</strong>
                            <br><small class="text-muted">Google Pixel 8</small>
                        </div>
                    </td>
                    <td>
                        <div>
                            <strong>₦420,000.00</strong>
                            <br><small class="text-muted">Fee: ₦8,400.00</small>
                        </div>
                    </td>
                    <td>
                        <span class="payment-method-badge method-paystack">
                            <i class="fas fa-credit-card"></i>Paystack
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-danger">Failed</span>
                        <br><small class="text-muted">Insufficient funds</small>
                    </td>
                    <td>
                        <div>
                            <strong>{{ now()->subHours(5)->format('M d, Y') }}</strong>
                            <br><small class="text-muted">{{ now()->subHours(5)->format('g:i A') }}</small>
                        </div>
                    </td>
                    <td>
                        <div class="payment-actions">
                            <button class="btn btn-action btn-outline-primary" onclick="viewPayment('PAY-GHI345678')" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-action btn-outline-info" onclick="retryPayment('PAY-GHI345678')" title="Retry Payment">
                                <i class="fas fa-redo"></i>
                            </button>
                            <button class="btn btn-action btn-outline-secondary" onclick="contactCustomer('mike@example.com')" title="Contact Customer">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="p-3 border-top">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small class="text-muted">Showing 1 to 3 of 2,847 payments</small>
            </div>
            <div class="col-md-6">
                <nav aria-label="Payment pagination">
                    <ul class="pagination pagination-sm justify-content-end mb-0">
                        <li class="page-item disabled">
                            <span class="page-link">Previous</span>
                        </li>
                        <li class="page-item active">
                            <span class="page-link">1</span>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Card -->
<div class="card mt-4">
    <div class="card-header">
        <h6 class="card-title mb-0">
            <i class="fas fa-bolt me-2"></i>Quick Actions
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Payment Operations</h6>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="exportPayments()">
                        <i class="fas fa-file-export me-2"></i>Export Payment Data
                    </button>
                    <button class="btn btn-outline-info" onclick="bulkVerifyTransfers()">
                        <i class="fas fa-check-double me-2"></i>Bulk Verify Bank Transfers
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <h6>Anti-theft Operations</h6>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-warning" onclick="checkAntiTheftStatus()">
                        <i class="fas fa-shield-alt me-2"></i>Check API Status
                    </button>
                    <button class="btn btn-outline-danger" onclick="viewTheftReports()">
                        <i class="fas fa-exclamation-triangle me-2"></i>View Theft Reports
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check anti-theft API status on page load
    checkAntiTheftStatus();
    
    // Auto-refresh API status every 5 minutes
    setInterval(checkAntiTheftStatus, 300000);
    
    // Update last checked timestamp
    updateLastChecked();
    setInterval(updateLastChecked, 60000);
});

function viewPayment(paymentId) {
    // In production, this would open a modal or navigate to payment details page
    alert(`Viewing payment details for: ${paymentId}`);
}

function downloadReceipt(receiptNumber) {
    // In production, this would trigger receipt download
    window.open(`/receipt/${receiptNumber}/download`, '_blank');
}

function refundPayment(paymentId) {
    if (confirm('Are you sure you want to process a refund for this payment?')) {
        // In production, this would send refund request to payment gateway
        alert(`Processing refund for payment: ${paymentId}`);
    }
}

function verifyBankTransfer(paymentId) {
    if (confirm('Mark this bank transfer as verified?')) {
        // In production, this would update payment status
        alert(`Verifying bank transfer: ${paymentId}`);
    }
}

function cancelPayment(paymentId) {
    if (confirm('Are you sure you want to cancel this payment?')) {
        // In production, this would cancel the payment
        alert(`Cancelling payment: ${paymentId}`);
    }
}

function retryPayment(paymentId) {
    // In production, this would allow customer to retry payment
    alert(`Initiating payment retry for: ${paymentId}`);
}

function contactCustomer(email) {
    // In production, this would open email composer or support system
    window.location.href = `mailto:${email}?subject=Payment Inquiry - M-RIGHT Digital Services`;
}

function exportPayments() {
    // In production, this would generate and download payment export
    alert('Generating payment export... You will receive an email when ready.');
}

function bulkVerifyTransfers() {
    if (confirm('Verify all pending bank transfers? This action will mark all verified transfers as successful.')) {
        alert('Processing bulk verification...');
    }
}

function checkAntiTheftStatus() {
    // Simulate API status check
    const statusIndicator = document.getElementById('antiTheftStatus');
    
    // In production, this would make an actual API call
    fetch('/api/admin/antitheft/status')
        .then(response => response.json())
        .then(data => {
            if (data.online) {
                statusIndicator.className = 'api-status-indicator online';
                statusIndicator.innerHTML = '<div class="status-dot"></div>API Online';
            } else {
                statusIndicator.className = 'api-status-indicator offline';
                statusIndicator.innerHTML = '<div class="status-dot"></div>API Offline';
            }
        })
        .catch(error => {
            statusIndicator.className = 'api-status-indicator offline';
            statusIndicator.innerHTML = '<div class="status-dot"></div>API Offline';
        });
}

function viewTheftReports() {
    // In production, this would navigate to theft reports page
    alert('Navigating to theft reports dashboard...');
}

function updateLastChecked() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    document.getElementById('lastChecked').textContent = timeString;
}

// Real-time payment status updates (WebSocket simulation)
function simulateRealTimeUpdates() {
    // In production, this would use WebSockets or Server-Sent Events
    setInterval(() => {
        // Simulate random status updates
        const badges = document.querySelectorAll('.badge');
        // This is just a demo - in production, you'd receive real updates
    }, 10000);
}

// Initialize real-time updates
simulateRealTimeUpdates();
</script>
@endpush
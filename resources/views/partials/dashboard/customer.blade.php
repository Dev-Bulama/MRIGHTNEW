@php
    // For customers, we'll show receipts they can access via phone/email search
    $userReceipts = \App\Models\Receipt::where('customer_email', auth()->user()->email)
                                       ->orWhere('customer_phone', auth()->user()->phone_number)
                                       ->latest()
                                       ->take(5)
                                       ->get();
    $totalReceipts = \App\Models\Receipt::where('customer_email', auth()->user()->email)
                                        ->orWhere('customer_phone', auth()->user()->phone_number)
                                        ->count();
@endphp

<!-- Customer Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card dashboard-card bg-gradient-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-bold mb-2">Welcome to Phone Anti-Theft Digital Receipt System</h4>
                        <p class="mb-3 opacity-75">
                            Manage your phone purchase receipts and track ownership records securely. 
                            Search for receipts using your phone's serial number or your contact information.
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('search.index') }}" class="btn btn-light">
                                <i class="fas fa-search me-2"></i>Search My Receipts
                            </a>
                            <a href="{{ route('help.customer') }}" class="btn btn-outline-light">
                                <i class="fas fa-question-circle me-2"></i>How it Works
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fas fa-mobile-alt" style="font-size: 5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions for Customers -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card dashboard-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2 text-primary"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('search.index') }}" class="quick-action-card card text-decoration-none h-100">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-search text-primary fs-1 mb-3"></i>
                                <h6 class="fw-medium mb-2">Search Receipt</h6>
                                <p class="small text-muted mb-0">Find your phone receipt by serial number</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('search.verify') }}" class="quick-action-card card text-decoration-none h-100">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-shield-alt text-success fs-1 mb-3"></i>
                                <h6 class="fw-medium mb-2">Verify Ownership</h6>
                                <p class="small text-muted mb-0">Confirm your phone's authenticity</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('customer.receipts') }}" class="quick-action-card card text-decoration-none h-100">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-receipt text-info fs-1 mb-3"></i>
                                <h6 class="fw-medium mb-2">My Receipts</h6>
                                <p class="small text-muted mb-0">View all your purchase receipts</p>
                                @if($totalReceipts > 0)
                                    <span class="status-badge bg-primary">{{ $totalReceipts }}</span>
                                @endif
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('customer.report-issue') }}" class="quick-action-card card text-decoration-none h-100">
                            <div class="card-body text-center p-4">
                                <i class="fas fa-exclamation-triangle text-warning fs-1 mb-3"></i>
                                <h6 class="fw-medium mb-2">Report Issue</h6>
                                <p class="small text-muted mb-0">Report stolen or lost phone</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Statistics -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon bg-primary mx-auto mb-3">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3 class="mb-2 fw-bold counter" data-target="{{ $totalReceipts }}">{{ $totalReceipts }}</h3>
                <h6 class="text-muted mb-0">My Receipts</h6>
                <small class="text-muted">Total purchases</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon bg-success mx-auto mb-3">
                    <i class="fas fa-shield-check"></i>
                </div>
                @php
                    $verifiedPhones = \App\Models\Receipt::where('customer_email', auth()->user()->email)
                                                        ->orWhere('customer_phone', auth()->user()->phone_number)
                                                        ->where('enable_antitheft', true)
                                                        ->count();
                @endphp
                <h3 class="mb-2 fw-bold counter" data-target="{{ $verifiedPhones }}">{{ $verifiedPhones }}</h3>
                <h6 class="text-muted mb-0">Protected Phones</h6>
                <small class="text-muted">Anti-theft enabled</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon bg-info mx-auto mb-3">
                    <i class="fas fa-sync-alt"></i>
                </div>
                @php
                    $resaleCount = \App\Models\Receipt::where('customer_email', auth()->user()->email)
                                                     ->orWhere('customer_phone', auth()->user()->phone_number)
                                                     ->where('receipt_type', 'resale')
                                                     ->count();
                @endphp
                <h3 class="mb-2 fw-bold counter" data-target="{{ $resaleCount }}">{{ $resaleCount }}</h3>
                <h6 class="text-muted mb-0">Resale Records</h6>
                <small class="text-muted">Ownership transfers</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon bg-warning mx-auto mb-3">
                    <i class="fas fa-clock"></i>
                </div>
                @php
                    $recentReceipts = \App\Models\Receipt::where('customer_email', auth()->user()->email)
                                                        ->orWhere('customer_phone', auth()->user()->phone_number)
                                                        ->where('created_at', '>=', now()->subDays(30))
                                                        ->count();
                @endphp
                <h3 class="mb-2 fw-bold counter" data-target="{{ $recentReceipts }}">{{ $recentReceipts }}</h3>
                <h6 class="text-muted mb-0">This Month</h6>
                <small class="text-muted">Recent activity</small>
            </div>
        </div>
    </div>
</div>

<!-- Recent Receipts -->
<div class="row mb-4">
    <div class="col-lg-8 mb-3">
        <div class="card dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2 text-primary"></i>
                    My Recent Receipts
                </h5>
                @if($totalReceipts > 5)
                    <a href="{{ route('customer.receipts') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-list me-1"></i>View All
                    </a>
                @endif
            </div>
            <div class="card-body p-0">
                @if($userReceipts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Receipt #</th>
                                    <th>Phone Details</th>
                                    <th>Shop</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userReceipts as $receipt)
                                <tr>
                                    <td>
                                        <span class="fw-medium text-primary">{{ $receipt->receipt_number }}</span>
                                        @if($receipt->receipt_type === 'resale')
                                            <span class="badge bg-info ms-1">Resale</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <span class="fw-medium">{{ $receipt->phone_name }}</span>
                                            <small class="d-block text-muted">{{ $receipt->phone_color }}</small>
                                            <small class="d-block text-muted">SN: {{ $receipt->phone_serial_number }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $receipt->shop->shop_name }}</span>
                                        <small class="d-block text-muted">{{ $receipt->shop->state }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $receipt->created_at->format('M j, Y') }}</span>
                                        <small class="d-block text-muted">{{ $receipt->created_at->format('g:i A') }}</small>
                                    </td>
                                    <td>
                                        @if($receipt->enable_antitheft)
                                            <span class="badge bg-success">
                                                <i class="fas fa-shield-alt me-1"></i>Protected
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-minus me-1"></i>Basic
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('receipt.view', $receipt) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('receipt.download', $receipt) }}" 
                                               class="btn btn-outline-success">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-receipt text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">No Receipts Found</h6>
                        <p class="text-muted small mb-4">
                            No purchase receipts are associated with your email or phone number yet.
                        </p>
                        <a href="{{ route('search.index') }}" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Search for My Receipts
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Help & Information -->
    <div class="col-lg-4 mb-3">
        <div class="card dashboard-card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2 text-warning"></i>
                    Tips & Information
                </h5>
            </div>
            <div class="card-body">
                <div class="tip-item mb-3 p-3 bg-light rounded">
                    <h6 class="fw-medium text-primary mb-2">
                        <i class="fas fa-shield-alt me-2"></i>Protect Your Phone
                    </h6>
                    <p class="small text-muted mb-0">
                        Enable anti-theft protection when purchasing phones to help track ownership and deter theft.
                    </p>
                </div>
                
                <div class="tip-item mb-3 p-3 bg-light rounded">
                    <h6 class="fw-medium text-success mb-2">
                        <i class="fas fa-search me-2"></i>Find Your Receipt
                    </h6>
                    <p class="small text-muted mb-0">
                        Use your phone's serial number or your contact information to quickly locate your purchase receipt.
                    </p>
                </div>
                
                <div class="tip-item mb-3 p-3 bg-light rounded">
                    <h6 class="fw-medium text-info mb-2">
                        <i class="fas fa-sync-alt me-2"></i>Transfer Ownership
                    </h6>
                    <p class="small text-muted mb-0">
                        When selling your phone, the shop can update the receipt to transfer ownership to the new buyer.
                    </p>
                </div>
                
                <div class="text-center mt-3">
                    <a href="{{ route('help.customer') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-question-circle me-1"></i>Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Security Features -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card dashboard-card border-success">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lock me-2"></i>
                    Your Data Security
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-3 text-center">
                        <i class="fas fa-shield-alt text-success mb-3" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-medium">Secure Storage</h6>
                        <p class="small text-muted mb-0">Your data is encrypted and securely stored</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <i class="fas fa-user-shield text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-medium">Privacy Protected</h6>
                        <p class="small text-muted mb-0">Only you can access your receipt information</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <i class="fas fa-certificate text-warning mb-3" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-medium">Verified Shops</h6>
                        <p class="small text-muted mb-0">All shops are verified before approval</p>
                    </div>
                    <div class="col-md-3 text-center">
                        <i class="fas fa-headset text-info mb-3" style="font-size: 2.5rem;"></i>
                        <h6 class="fw-medium">24/7 Support</h6>
                        <p class="small text-muted mb-0">Get help whenever you need it</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    // Mock recent activities for customer
    $recentActivities = [
        [
            'title' => 'Receipt Accessed',
            'description' => 'You viewed receipt #MR-2025-XYZ789 for iPhone 14 Pro',
            'time' => '2 hours ago',
            'icon' => 'fa-eye',
            'color' => 'primary'
        ],
        [
            'title' => 'Account Updated',
            'description' => 'Your profile information was updated successfully',
            'time' => '1 day ago',
            'icon' => 'fa-user-edit',
            'color' => 'info'
        ],
        [
            'title' => 'New Receipt Available',
            'description' => 'A new receipt has been generated for your purchase',
            'time' => '3 days ago',
            'icon' => 'fa-receipt',
            'color' => 'success',
            'badge' => ['text' => 'New', 'color' => 'success']
        ]
    ];
@endphp

@push('scripts')
<script>
// Customer specific dashboard interactions
document.addEventListener('DOMContentLoaded', function() {
    // Setup customer help tooltips
    setupCustomerTooltips();
    
    // Auto-search if user has phone number
    checkAutoSearch();
});

function setupCustomerTooltips() {
    // Add helpful tooltips to customer interface
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function checkAutoSearch() {
    // If customer has phone number, offer to search for receipts
    @if(auth()->user()->phone_number && $totalReceipts === 0)
        setTimeout(() => {
            if (confirm('Would you like to search for receipts associated with your phone number?')) {
                window.location.href = '{{ route("search.index", ["auto_phone" => auth()->user()->phone_number]) }}';
            }
        }, 3000);
    @endif
}

// Quick receipt download
function downloadReceipt(receiptId) {
    const link = document.createElement('a');
    link.href = `{{ route('receipt.download', '') }}/${receiptId}`;
    link.target = '_blank';
    link.click();
}

// Verify phone ownership
function verifyOwnership(serialNumber) {
    window.location.href = `{{ route('search.verify') }}?serial=${encodeURIComponent(serialNumber)}`;
}

// Report phone issue
function reportPhoneIssue(receiptId) {
    window.location.href = `{{ route('customer.report-issue') }}?receipt_id=${receiptId}`;
}

// Enhanced search with customer context
function enhancedSearch() {
    const modal = new bootstrap.Modal(document.getElementById('searchModal'));
    modal.show();
}
</script>
@endpush

<!-- Search Modal for Customer -->
<div class="modal fade" id="searchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-search me-2"></i>Find My Receipt
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('search.index') }}" method="GET">
                    <div class="mb-3">
                        <label class="form-label">Search by:</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="search_type" id="serial" value="serial" checked>
                            <label class="btn btn-outline-primary" for="serial">Serial Number</label>
                            
                            <input type="radio" class="btn-check" name="search_type" id="phone" value="phone">
                            <label class="btn btn-outline-primary" for="phone">Phone Number</label>
                            
                            <input type="radio" class="btn-check" name="search_type" id="email" value="email">
                            <label class="btn btn-outline-primary" for="email">Email</label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <input type="text" class="form-control form-control-lg" name="query" 
                               placeholder="Enter search term..." required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Search Receipts
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@extends('layouts.app')

@section('title', 'Advanced Search Results')
@section('page-title', 'Search Results')
@section('page-description', 'Advanced search results with filtering options')

@push('styles')
<style>
.results-hero {
    background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
    border-radius: 15px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
}

.results-summary {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
    border-left: 4px solid #0d8abc;
}

.applied-filters {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
}

.filter-tag {
    display: inline-block;
    background: #e7f3ff;
    color: #0d8abc;
    border: 1px solid #b3d9ff;
    border-radius: 15px;
    padding: 0.25rem 0.75rem;
    font-size: 0.8rem;
    font-weight: 500;
    margin: 0.25rem;
}

.results-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.result-item {
    padding: 1.5rem;
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.3s ease;
}

.result-item:hover {
    background: #f8f9fa;
    transform: translateX(5px);
}

.result-item:last-child {
    border-bottom: none;
}

.result-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.receipt-number {
    font-size: 1.1rem;
    font-weight: 600;
    color: #0d8abc;
    text-decoration: none;
}

.receipt-number:hover {
    color: #0b7aa3;
    text-decoration: underline;
}

.result-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.detail-group {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
}

.detail-label {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 500;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-weight: 600;
    color: #343a40;
    word-break: break-word;
}

.result-badges {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}

.result-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.view-mode-toggle {
    background: white;
    border-radius: 8px;
    padding: 0.5rem;
    margin-bottom: 1rem;
    border: 1px solid #e9ecef;
}

.view-mode-btn {
    background: none;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.3s ease;
    color: #6c757d;
}

.view-mode-btn.active {
    background: #0d8abc;
    color: white;
}

.sort-options {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid #e9ecef;
}

.table-view .table {
    margin-bottom: 0;
}

.table-view .table th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
}

.table-view .table td {
    vertical-align: middle;
}

.pagination-wrapper {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 2rem;
    border: 1px solid #e9ecef;
}

.export-options {
    background: #e7f3ff;
    border: 1px solid #b3d9ff;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 2rem;
}

.no-results {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
}

.no-results i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .result-details {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .result-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .result-actions {
        flex-direction: column;
    }
    
    .results-hero {
        padding: 1.5rem;
    }
    
    .sort-options .row {
        flex-direction: column;
    }
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.result-item.highlight {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
}
</style>
@endpush

@section('content')
<!-- Results Hero Section -->
<div class="results-hero">
    <div class="row align-items-center">
        <div class="col-md-9">
            <h2 class="fw-bold mb-2">Search Results</h2>
            <p class="lead mb-0">
                Found {{ $results->total() }} result{{ $results->total() !== 1 ? 's' : '' }} 
                @if($results->total() > 0)
                    (showing {{ $results->firstItem() }}-{{ $results->lastItem() }})
                @endif
            </p>
        </div>
        <div class="col-md-3 text-center">
            <i class="fas fa-search-plus" style="font-size: 3rem; opacity: 0.3;"></i>
        </div>
    </div>
</div>

<!-- Applied Filters Summary -->
@if(!empty(array_filter($validated)))
    <div class="applied-filters">
        <h6 class="fw-bold mb-2">
            <i class="fas fa-filter me-2"></i>Applied Filters
        </h6>
        <div class="d-flex justify-content-between align-items-start flex-wrap">
            <div class="filter-tags">
                @foreach($validated as $key => $value)
                    @if($value && !in_array($key, ['_token', 'per_page']))
                        <span class="filter-tag">
                            <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> 
                            @if($key === 'shop_id')
                                {{ App\Models\Shop::find($value)->shop_name ?? $value }}
                            @elseif($key === 'enable_antitheft')
                                Enabled
                            @elseif(in_array($key, ['amount_from', 'amount_to']))
                                ₦{{ number_format($value) }}
                            @else
                                {{ $value }}
                            @endif
                        </span>
                    @endif
                @endforeach
            </div>
            <a href="{{ route('search.advanced') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-edit me-1"></i>Modify Search
            </a>
        </div>
    </div>
@endif

<!-- Export Options -->
@if($results->total() > 0)
    <div class="export-options">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="fw-bold mb-2">
                    <i class="fas fa-download me-2"></i>Export Results
                </h6>
                <p class="mb-0 small text-muted">Download search results in various formats</p>
            </div>
            <div class="col-md-4">
                <div class="btn-group w-100">
                    <button class="btn btn-outline-primary btn-sm" onclick="exportResults('pdf')">
                        <i class="fas fa-file-pdf me-1"></i>PDF
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="exportResults('excel')">
                        <i class="fas fa-file-excel me-1"></i>Excel
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="exportResults('csv')">
                        <i class="fas fa-file-csv me-1"></i>CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Sort and View Options -->
@if($results->total() > 0)
    <div class="sort-options">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <label class="form-label mb-0 fw-medium">Sort by:</label>
                    <select class="form-select form-select-sm" id="sortBy" onchange="applySorting()">
                        <option value="created_at_desc">Newest First</option>
                        <option value="created_at_asc">Oldest First</option>
                        <option value="amount_desc">Highest Amount</option>
                        <option value="amount_asc">Lowest Amount</option>
                        <option value="receipt_number_asc">Receipt Number A-Z</option>
                        <option value="customer_name_asc">Customer Name A-Z</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="view-mode-toggle float-md-end">
                    <button class="view-mode-btn active" id="cardViewBtn" onclick="switchView('card')">
                        <i class="fas fa-th-large me-1"></i>Cards
                    </button>
                    <button class="view-mode-btn" id="tableViewBtn" onclick="switchView('table')">
                        <i class="fas fa-table me-1"></i>Table
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Results Container -->
<div class="results-container" id="resultsContainer">
    @if($results->total() > 0)
        <!-- Card View (Default) -->
        <div id="cardView">
            @foreach($results as $receipt)
                <div class="result-item" data-receipt-id="{{ $receipt->id }}">
                    <div class="result-header">
                        <div class="flex-grow-1">
                            <a href="{{ route('receipt.show', $receipt) }}" class="receipt-number">
                                {{ $receipt->receipt_number }}
                            </a>
                            <div class="result-badges mt-2">
                                @if($receipt->receipt_type === 'resale')
                                    <span class="badge bg-info">Resale</span>
                                @else
                                    <span class="badge bg-success">Original Sale</span>
                                @endif
                                
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
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($receipt->payment_status) }}</span>
                                @endif
                                
                                @if($receipt->enable_antitheft)
                                    <span class="badge bg-warning">
                                        <i class="fas fa-shield-alt me-1"></i>Protected
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <h5 class="text-success mb-1">₦{{ number_format($receipt->amount, 2) }}</h5>
                            <small class="text-muted">{{ $receipt->created_at->format('M j, Y') }}</small>
                        </div>
                    </div>
                    
                    <div class="result-details">
                        <div class="detail-group">
                            <div class="detail-label">Customer</div>
                            <div class="detail-value">{{ $receipt->customer_name }}</div>
                            <small class="text-muted">{{ $receipt->customer_phone }}</small>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">Phone Details</div>
                            <div class="detail-value">{{ $receipt->phone_name }}</div>
                            <small class="text-muted">{{ $receipt->phone_color }} • SN: {{ $receipt->phone_serial_number }}</small>
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">Shop</div>
                            <div class="detail-value">{{ $receipt->shop->shop_name ?? 'N/A' }}</div>
                            @if($receipt->shop)
                                <small class="text-muted">{{ $receipt->shop->state }}, {{ $receipt->shop->local_government }}</small>
                            @endif
                        </div>
                        
                        <div class="detail-group">
                            <div class="detail-label">Generated By</div>
                            <div class="detail-value">{{ $receipt->user->name ?? 'System' }}</div>
                            <small class="text-muted">{{ $receipt->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    
                    <div class="result-actions">
                        <a href="{{ route('receipt.show', $receipt) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>View Details
                        </a>
                        <a href="{{ route('receipt.view', $receipt) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-receipt me-1"></i>View Receipt
                        </a>
                        <a href="{{ route('receipt.download', $receipt) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                        @if($receipt->enable_antitheft)
                            <button class="btn btn-outline-warning btn-sm" onclick="checkAntiTheftStatus({{ $receipt->id }})">
                                <i class="fas fa-shield-alt me-1"></i>Anti-Theft
                            </button>
                        @endif
                        @can('update', $receipt)
                            <button class="btn btn-outline-secondary btn-sm" onclick="updateReceiptStatus({{ $receipt->id }})">
                                <i class="fas fa-edit me-1"></i>Update
                            </button>
                        @endcan
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
                            <th width="15%">Receipt Number</th>
                            <th width="15%">Customer</th>
                            <th width="20%">Phone Details</th>
                            <th width="15%">Shop</th>
                            <th width="10%">Amount</th>
                            <th width="10%">Date</th>
                            <th width="10%">Status</th>
                            <th width="5%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $receipt)
                            <tr>
                                <td>
                                    <a href="{{ route('receipt.show', $receipt) }}" class="text-decoration-none fw-medium">
                                        {{ $receipt->receipt_number }}
                                    </a>
                                    @if($receipt->receipt_type === 'resale')
                                        <br><span class="badge bg-info mt-1">Resale</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $receipt->customer_name }}</div>
                                    <small class="text-muted">{{ $receipt->customer_phone }}</small>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $receipt->phone_name }}</div>
                                    <small class="text-muted">{{ $receipt->phone_color }}</small>
                                    <small class="d-block text-muted">SN: {{ $receipt->phone_serial_number }}</small>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $receipt->shop->shop_name ?? 'N/A' }}</div>
                                    @if($receipt->shop)
                                        <small class="text-muted">{{ $receipt->shop->state }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-success">₦{{ number_format($receipt->amount, 0) }}</span>
                                </td>
                                <td>
                                    <div>{{ $receipt->created_at->format('M j, Y') }}</div>
                                    <small class="text-muted">{{ $receipt->created_at->format('g:i A') }}</small>
                                </td>
                                <td>
                                    @if($receipt->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($receipt->status === 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($receipt->status) }}</span>
                                    @endif
                                    
                                    @if($receipt->enable_antitheft)
                                        <br><span class="badge bg-warning mt-1">
                                            <i class="fas fa-shield-alt"></i>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('receipt.show', $receipt) }}">
                                                    <i class="fas fa-eye me-2"></i>View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('receipt.download', $receipt) }}">
                                                    <i class="fas fa-download me-2"></i>Download
                                                </a>
                                            </li>
                                            @if($receipt->enable_antitheft)
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="checkAntiTheftStatus({{ $receipt->id }})">
                                                        <i class="fas fa-shield-alt me-2"></i>Anti-Theft Status
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="pagination-wrapper">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0 text-muted">
                        Showing {{ $results->firstItem() }} to {{ $results->lastItem() }} of {{ $results->total() }} results
                    </p>
                </div>
                <div>
                    {{ $results->links() }}
                </div>
            </div>
        </div>
        
    @else
        <!-- No Results Found -->
        <div class="no-results">
            <i class="fas fa-search-minus"></i>
            <h4 class="fw-bold mb-3">No Results Found</h4>
            <p class="mb-4">
                We couldn't find any receipts matching your search criteria. 
                Try adjusting your filters or search terms.
            </p>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="{{ route('search.advanced') }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Modify Search
                </a>
                <a href="{{ route('search.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-search me-2"></i>Simple Search
                </a>
                <button class="btn btn-outline-info" onclick="showSearchTips()">
                    <i class="fas fa-lightbulb me-2"></i>Search Tips
                </button>
            </div>
        </div>
    @endif
</div>

<!-- Search Tips Modal -->
<div class="modal fade" id="searchTipsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-lightbulb me-2 text-warning"></i>Search Tips
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary">Improve Your Search Results</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Use partial matches - you don't need complete information
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Try broader date ranges if searching by specific dates
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Use amount ranges instead of exact amounts
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Clear specific filters and search with general terms
                            </li>
                        </ul>
                    </div>
                    <div class="col-12">
                        <h6 class="fw-bold text-info">Common Search Scenarios</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn btn-outline-primary btn-sm w-100" onclick="applyQuickSearch('thisMonth')">
                                    This Month's Receipts
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-success btn-sm w-100" onclick="applyQuickSearch('highValue')">
                                    High Value Items
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-info btn-sm w-100" onclick="applyQuickSearch('protected')">
                                    Protected Phones
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-warning btn-sm w-100" onclick="applyQuickSearch('pending')">
                                    Pending Payments
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('help.guides') }}" class="btn btn-primary">
                    <i class="fas fa-book me-1"></i>Full Guide
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize results page
    initializeResultsPage();
    
    // Setup keyboard shortcuts
    setupKeyboardShortcuts();
    
    // Auto-refresh if needed
    setupAutoRefresh();
});

function initializeResultsPage() {
    // Apply any URL parameters for sorting
    const urlParams = new URLSearchParams(window.location.search);
    const sortBy = urlParams.get('sort');
    if (sortBy) {
        document.getElementById('sortBy').value = sortBy;
    }
    
    // Highlight results if search term is provided
    highlightSearchTerms();
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
        
        // Store preference
        localStorage.setItem('search_view_preference', 'table');
    } else {
        cardView.classList.remove('d-none');
        tableView.classList.add('d-none');
        cardBtn.classList.add('active');
        tableBtn.classList.remove('active');
        
        // Store preference
        localStorage.setItem('search_view_preference', 'card');
    }
}

function applySorting() {
    const sortBy = document.getElementById('sortBy').value;
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('sort', sortBy);
    
    // Show loading
    showLoading();
    
    // Redirect with new sort parameter
    window.location.href = currentUrl.toString();
}

function exportResults(format) {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('export', format);
    
    // Create temporary link to trigger download
    const link = document.createElement('a');
    link.href = currentUrl.toString();
    link.download = `search_results_${new Date().toISOString().split('T')[0]}.${format}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showToast(`Exporting results as ${format.toUpperCase()}...`, 'info');
}

function highlightSearchTerms() {
    // Get search terms from applied filters
    const filterTags = document.querySelectorAll('.filter-tag');
    const searchTerms = [];
    
    filterTags.forEach(tag => {
        const text = tag.textContent;
        if (text.includes(':')) {
            const value = text.split(':')[1].trim();
            if (value.length > 2) {
                searchTerms.push(value);
            }
        }
    });
    
    // Highlight terms in results (basic implementation)
    if (searchTerms.length > 0) {
        const resultItems = document.querySelectorAll('.result-item');
        resultItems.forEach(item => {
            searchTerms.forEach(term => {
                if (item.textContent.toLowerCase().includes(term.toLowerCase())) {
                    item.classList.add('highlight');
                }
            });
        });
    }
}

function checkAntiTheftStatus(receiptId) {
    fetch(`{{ route('api.receipt.status', '') }}/${receiptId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.anti_theft_enabled) {
            showToast(`Anti-theft status: ${data.anti_theft_status}`, 'info');
        } else {
            showToast('Anti-theft protection is not enabled for this device', 'warning');
        }
    })
    .catch(error => {
        console.error('Error checking anti-theft status:', error);
        showToast('Error checking anti-theft status', 'danger');
    });
}

function updateReceiptStatus(receiptId) {
    const newStatus = prompt('Enter new status (active, cancelled, void):');
    if (newStatus && ['active', 'cancelled', 'void'].includes(newStatus.toLowerCase())) {
        fetch(`{{ route('receipt.update-status', '') }}/${receiptId}`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: newStatus.toLowerCase() })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Receipt status updated successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Error updating receipt status', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error updating receipt status', 'danger');
        });
    }
}

function showSearchTips() {
    const modal = new bootstrap.Modal(document.getElementById('searchTipsModal'));
    modal.show();
}

function applyQuickSearch(type) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('searchTipsModal'));
    modal.hide();
    
    const baseUrl = '{{ route("search.advanced") }}';
    const today = new Date();
    let url = baseUrl + '?';
    
    switch (type) {
        case 'thisMonth':
            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            url += `date_from=${monthStart.toISOString().split('T')[0]}&date_to=${today.toISOString().split('T')[0]}`;
            break;
        case 'highValue':
            url += 'amount_from=100000'; // ₦100,000 and above
            break;
        case 'protected':
            url += 'enable_antitheft=1';
            break;
        case 'pending':
            url += 'payment_status=pending';
            break;
    }
    
    window.location.href = url;
}

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + / - Show search tips
        if ((e.ctrlKey || e.metaKey) && e.key === '/') {
            e.preventDefault();
            showSearchTips();
        }
        
        // Ctrl/Cmd + E - Export as CSV
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            exportResults('csv');
        }
        
        // V - Switch view mode
        if (e.key === 'v' && !e.ctrlKey && !e.metaKey) {
            const currentView = document.getElementById('cardView').classList.contains('d-none') ? 'table' : 'card';
            switchView(currentView === 'table' ? 'card' : 'table');
        }
    });
}

function setupAutoRefresh() {
    // Auto-refresh results every 5 minutes if user is idle
    let lastActivity = Date.now();
    let refreshTimer;
    
    function resetTimer() {
        lastActivity = Date.now();
        clearTimeout(refreshTimer);
        
        refreshTimer = setTimeout(() => {
            if (Date.now() - lastActivity >= 300000) { // 5 minutes
                if (confirm('Results may be outdated. Refresh the page?')) {
                    location.reload();
                }
            }
        }, 300000);
    }
    
    // Track user activity
    ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
        document.addEventListener(event, resetTimer, true);
    });
    
    resetTimer();
}

function showLoading() {
    const container = document.getElementById('resultsContainer');
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted">Updating results...</p>
        </div>
    `;
    
    container.style.position = 'relative';
    container.appendChild(overlay);
}

// Load user's view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('search_view_preference');
    if (savedView && savedView === 'table') {
        switchView('table');
    }
});
</script>
@endpush
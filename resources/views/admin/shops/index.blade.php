@extends('layouts.app')

@section('title', 'Manage Shops')
@section('page-title', 'Shop Management')
@section('page-description', 'Approve and manage registered shops')

@push('styles')
<style>
    .shop-card {
        transition: all 0.3s ease;
        border: 1px solid #e3e6f0;
    }
    
    .shop-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.65rem;
        border-radius: 50px;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .status-approved {
        background: #d4edda;
        color: #155724;
        border: 1px solid #a3cfbb;
    }
    
    .status-rejected {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f1aeb5;
    }
    
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .action-btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
    }
    
    .filter-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
    }
    
    .shop-logo {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #e3e6f0;
    }
    
    .bulk-actions {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        display: none;
    }
    
    .bulk-actions.show {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                        <p class="mb-0 opacity-75">Total Shops</p>
                    </div>
                    <i class="fas fa-store fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0 text-success">{{ $stats['approved'] ?? 0 }}</h3>
                            <p class="mb-0 text-muted">Approved</p>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0 text-warning">{{ $stats['pending'] ?? 0 }}</h3>
                            <p class="mb-0 text-muted">Pending</p>
                        </div>
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0 text-danger">{{ $stats['rejected'] ?? 0 }}</h3>
                            <p class="mb-0 text-muted">Rejected</p>
                        </div>
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.shops.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search Shops</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" 
                           placeholder="Shop name, owner, email..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Status Filter</label>
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Location</label>
                <select class="form-select" name="state">
                    <option value="">All States</option>
                    @foreach(['Lagos', 'Abuja', 'Kano', 'Rivers', 'Oyo', 'Kaduna', 'Plateau', 'Delta'] as $state)
                        <option value="{{ $state }}" {{ request('state') === $state ? 'selected' : '' }}>
                            {{ $state }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.shops.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulkActions">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>Bulk Actions:</strong>
                <span id="selectedCount">0</span> shops selected
            </div>
            <div>
                <button class="btn btn-success btn-sm me-2" onclick="bulkApproveSelected()">
                    <i class="fas fa-check me-1"></i>Approve Selected
                </button>
                <button class="btn btn-danger btn-sm me-2" onclick="bulkRejectSelected()">
                    <i class="fas fa-times me-1"></i>Reject Selected
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i>Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Shops List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-store me-2"></i>Registered Shops
                @if($shops->total() > 0)
                    <span class="badge bg-primary ms-2">{{ $shops->total() }}</span>
                @endif
            </h5>
            
            <div class="d-flex gap-2">
                @if($stats['pending'] > 0)
                    <button class="btn btn-success btn-sm" onclick="approveAllPending()">
                        <i class="fas fa-check-double me-1"></i>Approve All Pending
                    </button>
                @endif
                
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportShops('excel')">
                            <i class="fas fa-file-excel me-2"></i>Excel
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportShops('pdf')">
                            <i class="fas fa-file-pdf me-2"></i>PDF
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportShops('csv')">
                            <i class="fas fa-file-csv me-2"></i>CSV
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($shops->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Shop Details</th>
                                <th>Owner Information</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Registration Date</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shops as $shop)
                                <tr class="shop-row" data-shop-id="{{ $shop->id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input shop-checkbox" 
                                               value="{{ $shop->id }}">
                                    </td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($shop->logo)
                                                <img src="{{ Storage::url($shop->logo) }}" 
                                                     class="shop-logo me-3" alt="Logo">
                                            @else
                                                <div class="shop-logo me-3 bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-store text-muted"></i>
                                                </div>
                                            @endif
                                            
                                            <div>
                                                <h6 class="mb-1">{{ $shop->shop_name }}</h6>
                                                <p class="mb-0 text-muted small">{{ $shop->business_email }}</p>
                                                <p class="mb-0 text-muted small">{{ $shop->business_phone_1 }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <strong>{{ $shop->owner_full_name }}</strong>
                                        <br>
                                        <span class="text-muted small">{{ $shop->user->email ?? 'N/A' }}</span>
                                        <br>
                                        <span class="text-muted small">{{ $shop->user->phone_number ?? 'N/A' }}</span>
                                    </td>
                                    
                                    <td>
                                        <span class="fw-medium">{{ $shop->state }}</span>
                                        <br>
                                        <span class="text-muted small">{{ $shop->local_government }}</span>
                                        <br>
                                        <span class="text-muted small">{{ $shop->country }}</span>
                                    </td>
                                    
                                    <td>
                                        @if($shop->approved)
                                            <span class="status-badge status-approved">
                                                <i class="fas fa-check me-1"></i>Approved
                                            </span>
                                        @elseif($shop->status === 'rejected')
                                            <span class="status-badge status-rejected">
                                                <i class="fas fa-times me-1"></i>Rejected
                                            </span>
                                        @else
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        <span class="fw-medium">{{ $shop->created_at->format('M d, Y') }}</span>
                                        <br>
                                        <span class="text-muted small">{{ $shop->created_at->diffForHumans() }}</span>
                                    </td>
                                    
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                    onclick="viewShopDetails({{ $shop->id }})" 
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            @if(!$shop->approved && $shop->status !== 'rejected')
                                                <button type="button" class="btn btn-outline-success btn-sm" 
                                                        onclick="approveShop({{ $shop->id }})" 
                                                        title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="rejectShop({{ $shop->id }})" 
                                                        title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteShop({{ $shop->id }})" title="Delete Shop">
    <i class="fas fa-trash"></i> Delete
</button>
                                            @elseif($shop->approved)
                                                <button type="button" class="btn btn-outline-warning btn-sm" 
                                                        onclick="suspendShop({{ $shop->id }})" 
                                                        title="Suspend">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                                                                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteShop({{ $shop->id }})" title="Delete Shop">
    <i class="fas fa-trash"></i> Delete
</button>
                                                
                                            @else
                                                <button type="button" class="btn btn-outline-info btn-sm" 
                                                        onclick="reinstateShop({{ $shop->id }})" 
                                                        title="Reinstate">
                                                    <i class="fas fa-undo"></i>
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
                            Showing {{ $shops->firstItem() ?? 0 }} to {{ $shops->lastItem() ?? 0 }} 
                            of {{ $shops->total() }} results
                        </div>
                        {{ $shops->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-store text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">No Shops Found</h4>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'status', 'state']))
                            No shops match your current filters. Try adjusting your search criteria.
                        @else
                            No shops have been registered yet. Check back later for new registrations.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status', 'state']))
                        <a href="{{ route('admin.shops.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Shop Details Modal -->
<div class="modal fade" id="shopDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Shop Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="shopDetailsContent">
                <!-- Shop details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Shop management functions
function approveShop(shopId) {
    if (confirm('Are you sure you want to approve this shop?')) {
        performShopAction(shopId, 'approve', 'Approving shop...');
    }
}

function rejectShop(shopId) {
    const reason = prompt('Please provide a reason for rejection (optional):');
    if (reason !== null) {
        performShopAction(shopId, 'reject', 'Rejecting shop...', { reason: reason });
    }
}

function suspendShop(shopId) {
    if (confirm('Are you sure you want to suspend this shop?')) {
        performShopAction(shopId, 'suspend', 'Suspending shop...');
    }
}

function reinstateShop(shopId) {
    if (confirm('Are you sure you want to reinstate this shop?')) {
        performShopAction(shopId, 'reinstate', 'Reinstating shop...');
    }
}

function performShopAction(shopId, action, loadingMessage, extraData = {}) {
    showLoading(loadingMessage);
    
    const url = `/admin/shops/${shopId}/${action}`;
    const data = {
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        ...extraData
    };
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message || 'Operation failed');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'An error occurred. Please try again.');
        console.error('Error:', error);
    });
}

function viewShopDetails(shopId) {
    fetch(`/admin/shops/${shopId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('shopDetailsContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('shopDetailsModal')).show();
            } else {
                showAlert('error', 'Failed to load shop details');
            }
        })
        .catch(error => {
            showAlert('error', 'Failed to load shop details');
        });
}

function approveAllPending() {
    if (confirm('Are you sure you want to approve ALL pending shops?')) {
        showLoading('Approving all pending shops...');
        
        fetch('/admin/shops/approve-all', {
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
                showAlert('success', `${data.count} shops approved successfully!`);
                setTimeout(() => location.reload(), 2000);
            } else {
                showAlert('error', data.message || 'Failed to approve shops');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'An error occurred');
        });
    }
}

// Bulk selection functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const shopCheckboxes = document.querySelectorAll('.shop-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    selectAllCheckbox.addEventListener('change', function() {
        shopCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    shopCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.shop-checkbox:checked');
        const count = checkedBoxes.length;
        
        selectedCount.textContent = count;
        
        if (count > 0) {
            bulkActions.classList.add('show');
        } else {
            bulkActions.classList.remove('show');
        }
        
        // Update select all checkbox state
        selectAllCheckbox.indeterminate = count > 0 && count < shopCheckboxes.length;
        selectAllCheckbox.checked = count === shopCheckboxes.length;
    }
});

function bulkApproveSelected() {
    const selected = getSelectedShops();
    if (selected.length === 0) {
        showAlert('warning', 'Please select shops to approve');
        return;
    }
    
    if (confirm(`Are you sure you want to approve ${selected.length} selected shops?`)) {
        performBulkAction('approve', selected);
    }
}

function bulkRejectSelected() {
    const selected = getSelectedShops();
    if (selected.length === 0) {
        showAlert('warning', 'Please select shops to reject');
        return;
    }
    
    const reason = prompt('Please provide a reason for rejection (optional):');
    if (reason !== null) {
        performBulkAction('reject', selected, { reason: reason });
    }
}

function getSelectedShops() {
    return Array.from(document.querySelectorAll('.shop-checkbox:checked')).map(cb => cb.value);
}

function performBulkAction(action, shopIds, extraData = {}) {
    showLoading(`Processing ${shopIds.length} shops...`);
    
    fetch(`/admin/shops/bulk-${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            shop_ids: shopIds,
            ...extraData
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('error', data.message || 'Bulk operation failed');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'An error occurred');
    });
}

function clearSelection() {
    document.querySelectorAll('.shop-checkbox:checked').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    document.getElementById('bulkActions').classList.remove('show');
}

function exportShops(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    window.location.href = `{{ route('admin.shops.index') }}?${params.toString()}`;
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
    // Create or show loading overlay
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
// ADD THIS NEW FUNCTION
function deleteShop(shopId) {
    if (confirm('Are you sure you want to PERMANENTLY delete this shop and all its data? This action cannot be undone.')) {
        showLoading('Deleting shop...');
        
        fetch(`/admin/shops/${shopId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.message || 'Failed to delete shop');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'An error occurred while deleting shop');
        });
    }
}
// ADD THIS TO THE BULK ACTIONS
function bulkDeleteShops() {
    const checkboxes = document.querySelectorAll('.shop-checkbox:checked');
    const shopIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (shopIds.length === 0) {
        showAlert('warning', 'Please select shops to delete');
        return;
    }

    if (confirm(`Are you sure you want to PERMANENTLY delete ${shopIds.length} shops? This action cannot be undone.`)) {
        performBulkShopAction('delete', shopIds);
    }
}
</script>
@endpush
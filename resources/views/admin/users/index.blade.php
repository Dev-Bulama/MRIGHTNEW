@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')
@section('page-description', 'Manage system users, import accounts, and monitor user activity')

@push('styles')
<style>
    .users-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .user-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-left: 4px solid;
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .user-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .user-card.admin { border-left-color: #dc3545; }
    .user-card.shop-owner { border-left-color: #28a745; }
    .user-card.customer { border-left-color: #17a2b8; }
    .user-card.total { border-left-color: #6f42c1; }
    
    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e9ecef;
    }
    
    .user-status {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .filter-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .import-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .user-actions .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        margin: 0 0.1rem;
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
    
    .user-row.selected {
        background-color: #e3f2fd !important;
    }
    
    .user-phones {
        font-size: 0.85rem;
    }
    
    .user-phones .phone-number {
        display: inline-block;
        background: #f8f9fa;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        margin: 0.1rem;
        border: 1px solid #dee2e6;
    }
    
    .import-progress {
        display: none;
        margin-top: 1rem;
    }
    
    .import-results {
        display: none;
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Users Header -->
    <div class="users-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">
                    <i class="fas fa-users me-3"></i>User Management
                </h1>
                <p class="mb-0 opacity-75">
                    Manage system users, import new accounts, monitor activity, and control user permissions.
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex flex-column align-items-end">
                    <h3 class="mb-1">{{ $stats['total'] ?? 0 }}</h3>
                    <p class="mb-0 opacity-75">Total Users</p>
                </div>
            </div>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="user-card total">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-primary mb-2">{{ $stats['total'] ?? 0 }}</h3>
                        <p class="text-muted mb-1">Total Users</p>
                        <div class="small text-muted">
                            <i class="fas fa-arrow-up me-1"></i>+5.2% this month
                        </div>
                    </div>
                    <i class="fas fa-users fa-2x text-primary opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="user-card admin">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-danger mb-2">{{ $stats['admins'] ?? 0 }}</h3>
                        <p class="text-muted mb-1">Administrators</p>
                        <div class="small text-muted">
                            System administrators
                        </div>
                    </div>
                    <i class="fas fa-user-shield fa-2x text-danger opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="user-card shop-owner">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-success mb-2">{{ $stats['shop_owners'] ?? 0 }}</h3>
                        <p class="text-muted mb-1">Shop Owners</p>
                        <div class="small text-muted">
                            Business account holders
                        </div>
                    </div>
                    <i class="fas fa-store fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <!--<div class="user-card customer">-->
                <!--<div class="d-flex justify-content-between align-items-center">-->
                    <!--<div>-->
                    <!--    <h3 class="text-info mb-2">{{ $stats['customers'] ?? 0 }}</h3>-->
                    <!--    <p class="text-muted mb-1">Customers</p>-->
                    <!--    <div class="small text-muted">-->
                    <!--        Regular user accounts-->
                    <!--    </div>-->
                    <!--</div>-->
                <!--    <i class="fas fa-user fa-2x text-info opacity-50"></i>-->
                <!--</div>-->
            <!--</div>-->
        </div>
    </div>

    <!-- Import Users Section -->
    <div class="import-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-2">
                    <i class="fas fa-upload me-2"></i>Bulk User Import
                </h5>
                <p class="mb-0 opacity-75">
                    Import users from CSV/Excel files. System automatically detects duplicates and validates data.
                </p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-light" onclick="showImportModal()">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Import Users
                </button>
                <button class="btn btn-outline-light" onclick="downloadTemplate()">
                    <i class="fas fa-download me-2"></i>Template
                </button>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search Users</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" 
                           placeholder="Name, email, phone..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">User Type</label>
                <select class="form-select" name="type">
                    <option value="">All Types</option>
                    <option value="admin" {{ request('type') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="shop_owner" {{ request('type') === 'shop_owner' ? 'selected' : '' }}>Shop Owner</option>
                    <option value="customer" {{ request('type') === 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Date Range</label>
                <select class="form-select" name="date_range">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="year" {{ request('date_range') === 'year' ? 'selected' : '' }}>This Year</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Bar -->
    <div class="bulk-actions" id="bulkActionsBar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong id="selectedCount">0</strong> users selected
            </div>
            <div class="btn-group">
                <button class="btn btn-success btn-sm" onclick="bulkAction('activate')">
                    <i class="fas fa-check me-1"></i>Activate
                </button>
                <button class="btn btn-warning btn-sm" onclick="bulkAction('suspend')">
                    <i class="fas fa-pause me-1"></i>Suspend
                </button>
                <button class="btn btn-info btn-sm" onclick="bulkAction('send_welcome')">
                    <i class="fas fa-envelope me-1"></i>Send Welcome
                </button>
                <button class="btn btn-outline-danger btn-sm" onclick="bulkAction('delete')">
                    <i class="fas fa-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Users List
                @if($users->total() > 0)
                    <span class="badge bg-primary ms-2">{{ $users->total() }}</span>
                @endif
            </h5>
            
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" onclick="refreshUsers()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
                
                <div class="btn-group">
                    <button class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="exportUsers('excel')">
                            <i class="fas fa-file-excel me-2"></i>Excel Report
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportUsers('pdf')">
                            <i class="fas fa-file-pdf me-2"></i>PDF Report
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportUsers('csv')">
                            <i class="fas fa-file-csv me-2"></i>CSV Data
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" class="form-check-input" 
                                           id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>User Details</th>
                                <th>Contact Information</th>
                                <th>Type & Status</th>
                                <th>Activity</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="user-row" data-user-id="{{ $user->id }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input user-checkbox" 
                                               value="{{ $user->id }}" onchange="updateBulkActions()">
                                    </td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->avatar_url }}" 
                                                 alt="{{ $user->full_name }}" 
                                                 class="user-avatar me-3">
                                            <div>
                                                <h6 class="mb-1">{{ $user->full_name }}</h6>
                                                <small class="text-muted">
                                                    ID: {{ $user->id }} • 
                                                    Joined {{ $user->created_at->format('M d, Y') }}
                                                </small>
                                                @if($user->import_metadata)
                                                    <br><span class="badge bg-info badge-sm">Imported</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div>
                                            <strong>{{ $user->email }}</strong>
                                            <div class="user-phones mt-1">
                                                @foreach($user->phone_numbers as $phone)
                                                    <span class="phone-number">{{ $phone }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="mb-2">
                                            <span class="badge bg-{{ $user->user_type === 'admin' ? 'danger' : ($user->user_type === 'shop_owner' ? 'success' : 'info') }}">
                                                {{ $user->user_type_display }}
                                            </span>
                                        </div>
                                        <span class="user-status bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'suspended' ? 'danger' : 'warning') }} text-white">
                                            {{ $user->status_display }}
                                        </span>
                                    </td>
                                    
                                    <td>
                                        <div>
                                            @if($user->last_login_at)
                                                <strong>{{ $user->last_login_at->diffForHumans() }}</strong>
                                                <br><small class="text-muted">{{ $user->last_login_at->format('M d, Y g:i A') }}</small>
                                            @else
                                                <span class="text-muted">Never logged in</span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="user-actions">
                                            <button class="btn btn-outline-primary btn-sm" 
                                                    onclick="viewUser({{ $user->id }})" 
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <button class="btn btn-outline-success btn-sm" 
                                                    onclick="editUser({{ $user->id }})" 
                                                    title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            @if($user->status === 'active')
                                                <button class="btn btn-outline-warning btn-sm" 
                                                        onclick="suspendUser({{ $user->id }})" 
                                                        title="Suspend">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-outline-success btn-sm" 
                                                        onclick="activateUser({{ $user->id }})" 
                                                        title="Activate">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @endif
                                            
                                            <button class="btn btn-outline-info btn-sm" 
                                                    onclick="contactUser({{ $user->id }})" 
                                                    title="Send Message">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                            
                                            @if($user->user_type !== 'admin')
                                                <button class="btn btn-outline-danger btn-sm" 
                                                        onclick="deleteUser({{ $user->id }})" 
                                                        title="Delete User">
                                                    <i class="fas fa-trash"></i>
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
                            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} 
                            of {{ $users->total() }} users
                        </div>
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">No Users Found</h4>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'type', 'status', 'date_range']))
                            No users match your current filters. Try adjusting your search criteria.
                        @else
                            No users have been registered yet. Use the import feature to add users in bulk.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'type', 'status', 'date_range']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    @else
                        <button class="btn btn-primary" onclick="showImportModal()">
                            <i class="fas fa-upload me-2"></i>Import Users
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- User Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Import Instructions</h6>
                        <div class="alert alert-info">
                            <ul class="mb-0">
                                <li>Upload CSV or Excel files with user data</li>
                                <li>Required columns: <strong>name, email, phone_number</strong></li>
                                <li>Optional columns: <strong>secondary_phone, user_type</strong></li>
                                <li>System will auto-detect and skip duplicate users</li>
                                <li>Default password will be set to phone number if not provided</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Select File</label>
                        <input type="file" class="form-control" name="import_file" 
                               accept=".csv,.xlsx,.xls" required>
                        <small class="text-muted">Supported formats: CSV, Excel (.xlsx, .xls)</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Default User Type</label>
                            <select class="form-select" name="default_user_type">
                                <option value="customer">Customer</option>
                                <option value="shop_owner">Shop Owner</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Status</label>
                            <select class="form-select" name="default_status">
                                <option value="pending">Pending (Requires Activation)</option>
                                <option value="active">Active (Immediate Access)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="send_welcome_email" 
                               id="sendWelcomeEmail" checked>
                        <label class="form-check-label" for="sendWelcomeEmail">
                            Send welcome email to imported users
                        </label>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="skip_duplicates" 
                               id="skipDuplicates" checked>
                        <label class="form-check-label" for="skipDuplicates">
                            Skip duplicate users (recommended)
                        </label>
                    </div>
                </form>
                
                <!-- Import Progress -->
                <div class="import-progress">
                    <div class="d-flex align-items-center mb-2">
                        <div class="spinner-border spinner-border-sm me-2"></div>
                        <span>Processing import...</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
                
                <!-- Import Results -->
                <div class="import-results">
                    <div class="alert alert-success" id="importSuccess" style="display: none;">
                        <h6>Import Completed Successfully!</h6>
                        <ul id="successList"></ul>
                    </div>
                    
                    <div class="alert alert-warning" id="importWarnings" style="display: none;">
                        <h6>Import Completed with Warnings</h6>
                        <ul id="warningsList"></ul>
                    </div>
                    
                    <div class="alert alert-danger" id="importErrors" style="display: none;">
                        <h6>Import Failed</h6>
                        <ul id="errorsList"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="processImport()">
                    <i class="fas fa-upload me-2"></i>Start Import
                </button>
            </div>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<!--<div class="modal fade" id="userModal" tabindex="-1">-->
<!--    <div class="modal-dialog modal-lg">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header">-->
<!--                <h5 class="modal-title">User Details</h5>-->
<!--                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>-->
<!--            </div>-->
<!--            <div class="modal-body" id="userModalContent">-->
                <!-- User details will be loaded here -->
<!--            </div>-->
<!--            <div class="modal-footer">-->
<!--                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!-- User Details Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="userModalContent">
                <!-- User details will be loaded here -->
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
// Global variables
let selectedUsers = [];

// Import functionality
function showImportModal() {
    new bootstrap.Modal(document.getElementById('importModal')).show();
}

function downloadTemplate() {
    // Create CSV template
    const csvContent = [
        'name,email,phone_number,secondary_phone,user_type',
        'John Doe,john@example.com,08012345678,08087654321,customer',
        'Jane Smith,jane@example.com,08023456789,,shop_owner'
    ].join('\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'user-import-template.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function processImport() {
    const form = document.getElementById('importForm');
    const formData = new FormData(form);
    
    // Show progress
    document.querySelector('.import-progress').style.display = 'block';
    document.querySelector('.modal-footer').style.display = 'none';
    
    fetch('{{ route("admin.users.import") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        document.querySelector('.import-progress').style.display = 'none';
        document.querySelector('.import-results').style.display = 'block';
        
        if (data.success) {
            showImportResults(data);
            setTimeout(() => {
                location.reload();
            }, 3000);
        } else {
            showImportErrors(data);
        }
    })
    .catch(error => {
        document.querySelector('.import-progress').style.display = 'none';
        showAlert('error', 'Import failed. Please try again.');
    });
}

function showImportResults(data) {
    if (data.imported_count > 0) {
        document.getElementById('importSuccess').style.display = 'block';
        document.getElementById('successList').innerHTML = `
            <li>${data.imported_count} users imported successfully</li>
            <li>${data.activated_count} users activated and ready to use</li>
        `;
    }
    
    if (data.skipped_count > 0) {
        document.getElementById('importWarnings').style.display = 'block';
        document.getElementById('warningsList').innerHTML = `
            <li>${data.skipped_count} duplicate users were skipped</li>
        `;
    }
    
    if (data.failed_imports && data.failed_imports.length > 0) {
        document.getElementById('importWarnings').style.display = 'block';
        let warnings = document.getElementById('warningsList').innerHTML;
        data.failed_imports.forEach(failure => {
            warnings += `<li>Row ${failure.row}: ${failure.reason}</li>`;
        });
        document.getElementById('warningsList').innerHTML = warnings;
    }
}

function showImportErrors(data) {
    document.getElementById('importErrors').style.display = 'block';
    document.getElementById('errorsList').innerHTML = `<li>${data.message}</li>`;
}

// User actions
function viewUser(userId) {
    showLoading('Loading user details...');
    
    fetch(`/admin/users/${userId}/details`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                document.getElementById('userModalContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('userModal')).show();
            } else {
                showAlert('error', 'Failed to load user details');
            }
        })
        .catch(error => {
            hideLoading();
            showAlert('error', 'Failed to load user details');
        });
}

function editUser(userId) {
    window.open(`/admin/users/${userId}/edit`, '_blank');
}

function suspendUser(userId) {
    if (confirm('Are you sure you want to suspend this user? They will lose access to the system.')) {
        updateUserStatus(userId, 'suspend');
    }
}

function activateUser(userId) {
    updateUserStatus(userId, 'activate');
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        updateUserStatus(userId, 'delete');
    }
}

function contactUser(userId) {
    window.open(`/admin/users/${userId}/contact`, '_blank');
}

function updateUserStatus(userId, action) {
    showLoading(`${action.charAt(0).toUpperCase() + action.slice(1)}ing user...`);
    
    fetch(`/admin/users/${userId}/${action}`, {
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
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message || `Failed to ${action} user`);
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', `Failed to ${action} user`);
    });
}

// Bulk actions
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    selectedUsers = Array.from(checkboxes).map(cb => cb.value);
    
    const bulkBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');
    
    if (selectedUsers.length > 0) {
        bulkBar.classList.add('show');
        selectedCount.textContent = selectedUsers.length;
    } else {
        bulkBar.classList.remove('show');
    }
    
    // Update select all checkbox
    const allCheckboxes = document.querySelectorAll('.user-checkbox');
    const selectAll = document.getElementById('selectAll');
    selectAll.indeterminate = selectedUsers.length > 0 && selectedUsers.length < allCheckboxes.length;
    selectAll.checked = selectedUsers.length === allCheckboxes.length;
}

function bulkAction(action) {
    if (selectedUsers.length === 0) {
        showAlert('warning', 'Please select users first');
        return;
    }
    
    const actionText = {
        'activate': 'activate',
        'suspend': 'suspend',
        'send_welcome': 'send welcome emails to',
        'delete': 'delete'
    };
    
    if (confirm(`Are you sure you want to ${actionText[action]} ${selectedUsers.length} selected users?`)) {
        processBulkAction(action, selectedUsers);
    }
}

function processBulkAction(action, userIds) {
    showLoading(`Processing ${action} for ${userIds.length} users...`);
    
    fetch(`/admin/users/bulk-${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ user_ids: userIds })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('success', `${action} completed for ${data.affected_count} users`);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', data.message || `Bulk ${action} failed`);
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', `Bulk ${action} failed`);
    });
}

// Export functionality
function exportUsers(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    
    showLoading('Generating export...');
    
    fetch(`{{ route('admin.users.index') }}?${params.toString()}`, {
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
        a.download = `users-export-${new Date().toISOString().split('T')[0]}.${format}`;
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

function refreshUsers() {
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add row click handlers
    document.querySelectorAll('.user-row').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox' && !e.target.closest('.user-actions')) {
                const checkbox = this.querySelector('.user-checkbox');
                checkbox.checked = !checkbox.checked;
                updateBulkActions();
                
                if (checkbox.checked) {
                    this.classList.add('selected');
                } else {
                    this.classList.remove('selected');
                }
            }
        });
    });
});
</script>
@endpush
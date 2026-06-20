@extends('layouts.union')

@section('title', 'Pre-Approvals Management')
@section('page-title', 'Pre-Approvals Management')
@section('page-description', 'Manage pre-approved shop owners in your assigned locations')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Pre-Approval Management</h1>
            <p class="text-muted">Manage pre-approved registrations for your locations</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('union.pre-approvals.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Pre-Approval
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-2"></i>Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('union.pre-approvals.export') }}">
                        <i class="fas fa-file-csv me-2"></i>Export to CSV
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('union.pre-approvals.template') }}">
                        <i class="fas fa-file-download me-2"></i>Download Template
                    </a></li>
                </ul>
            </div>
            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-upload me-2"></i>Import CSV
            </button>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h6 class="mb-2">Import completed with some errors:</h6>
            <ul class="mb-0 small">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card primary">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Total Pre-Approvals</h6>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <div>
                        <i class="fas fa-list fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card warning">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Pending</h6>
                        <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                    </div>
                    <div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card success">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Used</h6>
                        <h3 class="mb-0">{{ $stats['used'] ?? 0 }}</h3>
                    </div>
                    <div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card danger">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">Expired</h6>
                        <h3 class="mb-0">{{ $stats['expired'] ?? 0 }}</h3>
                    </div>
                    <div>
                        <i class="fas fa-times-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('union.pre-approvals.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Name, email, phone...">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Used</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="state" class="form-label">State</label>
                        <select class="form-select" id="state" name="state">
                            <option value="">All States</option>
                            @if(isset($availableStates))
                                @foreach($availableStates as $state)
                                    <option value="{{ $state }}" {{ request('state') == $state ? 'selected' : '' }}>
                                        {{ $state }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('union.pre-approvals.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pre-Approvals Table -->
    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">Pre-Approved Users</h6>
        </div>
        <div class="card-body">
            @if($preApprovals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Contact Info</th>
                                <th>Location</th>
                                <th>Shop Info</th>
                                <th>Status</th>
                                <th>Approval Code</th>
                                <th>Created</th>
                                <th>Expires</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($preApprovals as $preApproval)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $preApproval->full_name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div><i class="fas fa-envelope text-muted me-1"></i> {{ $preApproval->email }}</div>
                                            <div><i class="fas fa-phone text-muted me-1"></i> {{ $preApproval->phone_number }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div><strong>{{ $preApproval->state }}</strong></div>
                                            <div class="text-muted">{{ $preApproval->local_government }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            @if($preApproval->shop_name)
                                                <div><strong>{{ $preApproval->shop_name }}</strong></div>
                                            @else
                                                <em class="text-muted">Not specified</em>
                                            @endif
                                            @if($preApproval->business_address)
                                                <div class="text-muted">{{ Str::limit($preApproval->business_address, 30) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $preApproval->status_color }}">
                                            {{ ucfirst($preApproval->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <code class="small">{{ $preApproval->approval_code }}</code>
                                    </td>
                                    <td class="small">
                                        {{ $preApproval->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="small">
                                        @if($preApproval->expires_at)
                                            {{ $preApproval->expires_at->format('M d, Y') }}
                                            @if($preApproval->expires_at->isPast() && $preApproval->status === 'pending')
                                                <div class="text-danger small">Expired</div>
                                            @endif
                                        @else
                                            <em class="text-muted">Never</em>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $preApprovals->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users text-muted mb-3" style="font-size: 3rem;"></i>
                    <h6 class="text-muted">No Pre-Approvals Yet</h6>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'state']))
                            No pre-approvals match your current filters.
                        @else
                            You haven't created any pre-approvals yet.
                        @endif
                    </p>
                    <div class="mt-3">
                        @if(request()->hasAny(['search', 'status', 'state']))
                            <a href="{{ route('union.pre-approvals.index') }}" class="btn btn-outline-primary me-2">
                                <i class="fas fa-times me-2"></i>Clear Filters
                            </a>
                        @endif
                        <a href="{{ route('union.pre-approvals.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Pre-Approval
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('union.pre-approvals.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Import Pre-Approvals</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" 
                               accept=".csv,.txt" required>
                        <div class="form-text">
                            Upload a CSV file with pre-approval data.
                            <a href="{{ route('union.pre-approvals.template') }}">Download template</a>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6 class="mb-2">CSV Format Requirements:</h6>
                        <ul class="mb-0 small">
                            <li><strong>Required columns:</strong> first_name, last_name, email, phone_number, state, local_government</li>
                            <li><strong>Optional columns:</strong> shop_name, business_address</li>
                            <li>States must be from your assigned locations</li>
                            <li>Email and phone must be unique</li>
                            <li>Max file size: 2MB</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stats-card {
    border-radius: 10px;
    padding: 1.5rem;
    color: white;
    margin-bottom: 1rem;
    transition: transform 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
}

.stats-card.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-card.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stats-card.success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stats-card.danger {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.table th {
    font-weight: 600;
    color: #6c757d;
    border-bottom: 2px solid #dee2e6;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush
@extends('layouts.union')

@section('breadcrumb')
    <li class="breadcrumb-item active">Pre-Approvals</li>
@endsection

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
            <form method="GET" action="{{ route('union.pre-approvals.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Name, email, or phone...">
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Used</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="state" class="form-label">State</label>
                    <select class="form-select" id="state" name="state">
                        <option value="">All States</option>
                        @if(auth()->user()->assigned_states)
                            @foreach(auth()->user()->assigned_states as $state)
                                <option value="{{ $state }}" {{ request('state') == $state ? 'selected' : '' }}>
                                    {{ $state }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pre-Approvals Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Pre-Approval List</h5>
        </div>
        <div class="card-body p-0">
            @if(isset($preApprovals) && $preApprovals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Applicant Details</th>
                                <th>Contact Info</th>
                                <th>Location</th>
                                <th>Shop Details</th>
                                <th>Status</th>
                                <th>Dates</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($preApprovals as $preApproval)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $preApproval->first_name }} {{ $preApproval->last_name }}</strong><br>
                                            <small class="text-muted">Code: {{ $preApproval->approval_code }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <small class="text-muted">Email:</small><br>
                                            <strong>{{ $preApproval->email }}</strong><br>
                                            <small class="text-muted">Phone:</small><br>
                                            <strong>{{ $preApproval->phone_number }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $preApproval->state }}</span><br>
                                        <small class="text-muted">{{ $preApproval->local_government }}</small>
                                    </td>
                                    <td>
                                        @if($preApproval->shop_name)
                                            <strong>{{ $preApproval->shop_name }}</strong><br>
                                            @if($preApproval->business_address)
                                                <small class="text-muted">{{ Str::limit($preApproval->business_address, 30) }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'used' => 'success',
                                                'expired' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$preApproval->status] ?? 'secondary' }}">
                                            {{ ucfirst($preApproval->status) }}
                                        </span>
                                        @if($preApproval->used_at)
                                            <br><small class="text-muted">
                                                Used: {{ $preApproval->used_at->format('M d, Y') }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">Created:</small><br>
                                        <strong>{{ $preApproval->created_at->format('M d, Y') }}</strong><br>
                                        @if($preApproval->expires_at)
                                            <small class="text-muted">Expires:</small><br>
                                            <strong class="{{ $preApproval->expires_at->isPast() ? 'text-danger' : '' }}">
                                                {{ $preApproval->expires_at->format('M d, Y') }}
                                            </strong>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-info" 
                                                    onclick="viewDetails('{{ $preApproval->id }}')" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($preApproval->status == 'pending')
                                                <button type="button" class="btn btn-outline-warning" 
                                                        onclick="resendCode('{{ $preApproval->id }}')" title="Resend Code">
                                                    <i class="fas fa-paper-plane"></i>
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
                    {{ $preApprovals->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-check text-muted mb-3" style="font-size: 4rem;"></i>
                    <h5 class="text-muted">No Pre-Approvals Found</h5>
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
                        <h6>CSV Format Requirements:</h6>
                        <ul class="mb-0">
                            <li>Include headers: first_name, last_name, email, phone_number, state, local_government</li>
                            <li>Optional: shop_name, business_address</li>
                            <li>State must be in your assigned locations</li>
                            <li>Maximum 1000 rows per upload</li>
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

<style>
.stats-card {
    background: linear-gradient(135deg, var(--card-color, #007bff) 0%, var(--card-color-dark, #0056b3) 100%);
    color: white;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.stats-card.primary { --card-color: #007bff; --card-color-dark: #0056b3; }
.stats-card.success { --card-color: #28a745; --card-color-dark: #1e7e34; }
.stats-card.warning { --card-color: #ffc107; --card-color-dark: #d39e00; }
.stats-card.danger { --card-color: #dc3545; --card-color-dark: #bd2130; }
</style>

<script>
function viewDetails(preApprovalId) {
    // You can implement a modal or redirect to details page
    alert('View details for pre-approval ID: ' + preApprovalId);
}

function resendCode(preApprovalId) {
    if (confirm('Are you sure you want to resend the approval code to this applicant?')) {
        // Implement resend functionality
        alert('Code resent for pre-approval ID: ' + preApprovalId);
    }
}
</script>
@endsection
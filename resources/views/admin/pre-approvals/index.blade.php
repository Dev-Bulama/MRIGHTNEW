@extends('layouts.app')

@section('title', 'Pre-Approval Management')
@section('page-title', 'Pre-Approval Management')
@section('page-description', 'Manage shop owner pre-approvals and registration validation')

@push('styles')
<style>
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #667eea;
    }
    
    .stats-card.pending { border-left-color: #ffc107; }
    .stats-card.used { border-left-color: #28a745; }
    .stats-card.expired { border-left-color: #dc3545; }
    
    .import-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-primary mb-1">{{ $stats['total'] }}</h3>
                        <div class="text-muted">Total Pre-Approvals</div>
                    </div>
                    <div class="text-primary opacity-50">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card pending">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-warning mb-1">{{ $stats['pending'] }}</h3>
                        <div class="text-muted">Pending Use</div>
                    </div>
                    <div class="text-warning opacity-50">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card used">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-success mb-1">{{ $stats['used'] }}</h3>
                        <div class="text-muted">Successfully Used</div>
                    </div>
                    <div class="text-success opacity-50">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card expired">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-danger mb-1">{{ $stats['expired'] }}</h3>
                        <div class="text-muted">Expired</div>
                    </div>
                    <div class="text-danger opacity-50">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Section -->
    <!-- Import Section -->
    <div class="import-section">
        <div class="row">
            <div class="col-md-8">
                <h4><i class="fas fa-upload me-2"></i>Import Pre-Approved Users</h4>
                <p class="text-muted mb-3">Upload CSV file with shop owner details for automatic approval during registration.</p>
                
                <form action="{{ route('admin.pre-approvals.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Select CSV File</label>
                            <input type="file" class="form-control" name="file" accept=".csv" required>
                            <small class="text-muted">Supported format: .csv only (Max: 10MB)</small>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Import Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <h5>Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.pre-approvals.export') }}" class="btn btn-outline-success">
                        <i class="fas fa-download me-2"></i>Export All Data
                    </a>
                    <a href="{{ route('admin.pre-approvals.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>Add Manually
                    </a>
                    <a href="{{ route('admin.pre-approvals.template') }}" class="btn btn-outline-info">
                        <i class="fas fa-file-download me-2"></i>Download Template
                    </a>
                </div>
            </div>
        </div>
        
        @if(session('import_errors'))
            <div class="alert alert-warning mt-3">
                <h6>Import completed with some errors:</h6>
                <ul class="mb-0">
                    @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <!-- Pre-Approvals Table -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Pre-Approved Users
                    </h5>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="d-flex">
                        <select name="status" class="form-select me-2">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>Used</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                        <input type="text" name="search" class="form-control me-2" placeholder="Search..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($preApprovals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Shop Details</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($preApprovals as $approval)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $approval->first_name }} {{ $approval->last_name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="fas fa-envelope me-1"></i>{{ $approval->email }}<br>
                                            <i class="fas fa-phone me-1"></i>{{ $approval->phone_number }}
                                            @if($approval->secondary_phone)
                                                <br><i class="fas fa-mobile-alt me-1"></i>{{ $approval->secondary_phone }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($approval->shop_name)
                                            <div class="small">
                                                <strong>{{ $approval->shop_name }}</strong><br>
                                                @if($approval->business_address)
                                                    {{ Str::limit($approval->business_address, 30) }}<br>
                                                @endif
                                                @if($approval->state)
                                                    {{ $approval->state }}, {{ $approval->local_government }}
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $approval->user_type)) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $approval->status === 'pending' ? 'warning' : ($approval->status === 'used' ? 'success' : 'danger') }}">
                                            {{ ucfirst($approval->status) }}
                                        </span>
                                        @if($approval->status === 'used' && $approval->usedByUser)
                                            <br><small class="text-muted">Used by: {{ $approval->usedByUser->name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $approval->created_at->format('M d, Y') }}<br>
                                            {{ $approval->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info" onclick="viewDetails({{ $approval->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($approval->status === 'pending')
                                                <button class="btn btn-outline-danger" onclick="deleteApproval({{ $approval->id }})">
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
                    {{ $preApprovals->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-plus text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted">No Pre-Approvals Yet</h4>
                    <p class="text-muted">Upload shop owner details to enable automatic registration approval.</p>
                    <a href="{{ route('admin.pre-approvals.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add First Pre-Approval
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Template Download Modal -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Download the Excel template with the required column headers:</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-2"></i>first_name (Required)</li>
                    <li><i class="fas fa-check text-success me-2"></i>last_name (Required)</li>
                    <li><i class="fas fa-check text-success me-2"></i>email (Required)</li>
                    <li><i class="fas fa-check text-success me-2"></i>phone_number (Required)</li>
                    <li><i class="fas fa-check text-muted me-2"></i>secondary_phone (Optional)</li>
                    <li><i class="fas fa-check text-muted me-2"></i>shop_name (Optional)</li>
                    <li><i class="fas fa-check text-muted me-2"></i>business_address (Optional)</li>
                    <li><i class="fas fa-check text-muted me-2"></i>state (Optional)</li>
                    <li><i class="fas fa-check text-muted me-2"></i>local_government (Optional)</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('admin.pre-approvals.template') }}" class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Download Template
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewDetails(id) {
    // Implement view details functionality
    alert('View details for approval ID: ' + id + '. Full details modal coming soon.');
}

function deleteApproval(id) {
    if (confirm('Are you sure you want to delete this pre-approval?')) {
        fetch(`/admin/pre-approvals/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to delete: ' + data.message);
            }
        });
    }
}
</script>
@endpush
@endsection
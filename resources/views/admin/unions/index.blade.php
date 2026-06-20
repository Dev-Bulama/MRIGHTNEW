@extends('layouts.app')

@section('title', 'Union Management')
@section('page-title', 'Union Management')
@section('page-description', 'Manage union executives and their assigned locations')

@push('styles')
<style>
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #667eea;
    }
    
    .union-card {
        transition: transform 0.2s;
    }
    
    .union-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .role-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        margin: 0.125rem;
        border-radius: 15px;
    }
    
    .state-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
        margin: 0.1rem;
        background: #e3f2fd;
        color: #1976d2;
        border-radius: 10px;
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
                        <h3 class="text-primary mb-1">{{ $unions->total() }}</h3>
                        <div class="text-muted">Total Unions</div>
                    </div>
                    <div class="text-primary opacity-50">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-success mb-1">{{ $unions->where('status', 'active')->count() }}</h3>
                        <div class="text-muted">Active Unions</div>
                    </div>
                    <div class="text-success opacity-50">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-info mb-1">{{ count($states) }}</h3>
                        <div class="text-muted">States Covered</div>
                    </div>
                    <div class="text-info opacity-50">
                        <i class="fas fa-map-marker-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-warning mb-1">{{ $roles->count() }}</h3>
                        <div class="text-muted">Available Roles</div>
                    </div>
                    <div class="text-warning opacity-50">
                        <i class="fas fa-user-tag fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions & Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Union Executives
                    </h5>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.unions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create New Union
                    </a>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="row mt-3">
                <div class="col-md-3">
                    <form method="GET">
                        <select name="state" class="form-select" onchange="this.form.submit()">
                            <option value="">All States</option>
                            @foreach($states as $state)
                                <option value="{{ $state }}" {{ request('state') === $state ? 'selected' : '' }}>
                                    {{ $state }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="role" value="{{ request('role') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    </form>
                </div>
                <div class="col-md-3">
                    <form method="GET">
                        <select name="role" class="form-select" onchange="this.form.submit()">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="state" value="{{ request('state') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="d-flex">
                        <input type="search" name="search" class="form-control me-2" 
                               placeholder="Search by name or email..." 
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <input type="hidden" name="state" value="{{ request('state') }}">
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Unions Grid -->
    @if($unions->count() > 0)
        <div class="row">
            @foreach($unions as $union)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card union-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $union->name }}</h6>
                            <span class="badge bg-{{ $union->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($union->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar bg-primary text-white rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    {{ strtoupper(substr($union->first_name, 0, 1) . substr($union->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $union->name }}</div>
                                    <small class="text-muted">{{ $union->email }}</small>
                                </div>
                            </div>
                            
                            <!-- Contact Info -->
                            <div class="mb-3">
                                <small class="text-muted d-block">
                                    <i class="fas fa-phone me-1"></i>{{ $union->phone_number }}
                                </small>
                                @if($union->secondary_phone)
                                    <small class="text-muted d-block">
                                        <i class="fas fa-phone me-1"></i>{{ $union->secondary_phone }}
                                    </small>
                                @endif
                            </div>
                            
                            <!-- Assigned States -->
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Assigned States:</small>
                                <div class="d-flex flex-wrap">
                                    @foreach($union->assigned_states as $state)
                                        <span class="state-badge">{{ $state }}</span>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Roles -->
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Roles:</small>
                                <div class="d-flex flex-wrap">
                                    @foreach($union->roles as $role)
                                        <span class="role-badge bg-light text-dark">{{ $role->display_name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Stats -->
                            <div class="row text-center">
                                <div class="col">
                                    <small class="text-muted d-block">Shop Owners</small>
                                    <strong>{{ $union->manageableShopOwners()->count() }}</strong>
                                </div>
                                <div class="col">
                                    <small class="text-muted d-block">Created</small>
                                    <strong>{{ $union->created_at->format('M Y') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100" role="group">
                                <a href="{{ route('admin.unions.show', $union) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                <a href="{{ route('admin.unions.edit', $union) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                        onclick="deleteUnion({{ $union->id }}, '{{ $union->name }}')">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $unions->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-users text-muted mb-3" style="font-size: 4rem;"></i>
            <h4 class="text-muted">No Union Executives Found</h4>
            <p class="text-muted">Create union executives to manage shop owners by location.</p>
            <a href="{{ route('admin.unions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create First Union
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function deleteUnion(unionId, unionName) {
    if (confirm(`Are you sure you want to delete union executive "${unionName}"? This action cannot be undone.`)) {
        fetch(`/admin/unions/${unionId}`, {
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
                alert('Failed to delete union executive: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred while deleting the union executive.');
        });
    }
}
</script>
@endpush
@endsection
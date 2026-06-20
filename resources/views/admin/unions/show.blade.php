@extends('layouts.app')

@section('title', 'Union Executive Details')
@section('page-title', $union->name)
@section('page-description', 'View union executive details and managed shop owners')

@push('styles')
<style>
    .profile-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: bold;
        margin: 0 auto 1rem;
    }
    
    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #667eea;
        height: 100%;
    }
    
    .info-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }
    
    .shop-owner-card {
        transition: transform 0.2s;
        border-radius: 8px;
    }
    
    .shop-owner-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .badge-custom {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
    }
    
    .state-badge {
        background: #e3f2fd;
        color: #1976d2;
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
        border-radius: 10px;
        margin: 0.2rem;
    }
    
    .role-badge {
        background: #f3e5f5;
        color: #7b1fa2;
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
        border-radius: 10px;
        margin: 0.2rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Profile Header -->
    <div class="profile-card">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <div class="profile-avatar">
                    {{ strtoupper(substr($union->first_name, 0, 1) . substr($union->last_name, 0, 1)) }}
                </div>
                <span class="badge bg-{{ $union->status === 'active' ? 'success' : 'secondary' }} badge-custom">
                    {{ ucfirst($union->status) }}
                </span>
            </div>
            <div class="col-md-6">
                <h2 class="mb-2">{{ $union->name }}</h2>
                <p class="mb-1"><i class="fas fa-envelope me-2"></i>{{ $union->email }}</p>
                <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $union->phone_number }}</p>
                @if($union->secondary_phone)
                    <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $union->secondary_phone }}</p>
                @endif
                <p class="mb-1"><i class="fas fa-calendar me-2"></i>Joined {{ $union->created_at->format('F Y') }}</p>
                @if($union->assignedBy)
                    <p class="mb-0"><i class="fas fa-user-shield me-2"></i>Assigned by {{ $union->assignedBy->name }}</p>
                @endif
            </div>
            <div class="col-md-3 text-center">
                <div class="mb-3">
                    <h4 class="mb-1">{{ $stats['total_shop_owners'] }}</h4>
                    <small>Shop Owners Managed</small>
                </div>
                <div>
                    <h4 class="mb-1">{{ $stats['total_receipts'] }}</h4>
                    <small>Total Receipts</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-primary mb-1">{{ $stats['total_shop_owners'] }}</h3>
                        <div class="text-muted">Total Shop Owners</div>
                    </div>
                    <div class="text-primary opacity-50">
                        <i class="fas fa-store fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-success mb-1">{{ $stats['active_shops'] }}</h3>
                        <div class="text-muted">Active Shops</div>
                    </div>
                    <div class="text-success opacity-50">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-info mb-1">{{ $stats['total_receipts'] }}</h3>
                        <div class="text-muted">Total Receipts</div>
                    </div>
                    <div class="text-info opacity-50">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="text-warning mb-1">{{ $stats['monthly_receipts'] }}</h3>
                        <div class="text-muted">This Month</div>
                    </div>
                    <div class="text-warning opacity-50">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Union Information -->
        <div class="col-xl-4">
            <!-- Assigned Locations -->
            <div class="info-card">
                <h5><i class="fas fa-map-marker-alt me-2"></i>Assigned Locations</h5>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-2">States ({{ count($union->assigned_states) }})</h6>
                    <div class="d-flex flex-wrap">
                        @foreach($union->assigned_states as $state)
                            <span class="state-badge">{{ $state }}</span>
                        @endforeach
                    </div>
                </div>
                
                @if(!empty($union->assigned_lgas))
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Specific LGAs ({{ count($union->assigned_lgas) }})</h6>
                        <div class="d-flex flex-wrap">
                            @foreach($union->assigned_lgas as $lga)
                                <span class="state-badge">{{ $lga }}</span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Manages all LGAs in assigned states
                    </p>
                @endif
            </div>

            <!-- Assigned Roles -->
            <div class="info-card">
                <h5><i class="fas fa-user-tag me-2"></i>Assigned Roles</h5>
                
                @if($union->roles->count() > 0)
                    @foreach($union->roles as $role)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">{{ $role->display_name }}</h6>
                                <small class="text-muted">
                                    Assigned {{ \Carbon\Carbon::parse($role->pivot->assigned_at)->diffForHumans() }}
                                </small>
                            </div>
                            <p class="text-muted mb-2 small">{{ $role->description }}</p>
                            <div class="d-flex flex-wrap">
                                @foreach($role->formatted_permissions as $permission)
                                    <span class="role-badge">{{ $permission['name'] }}</span>
                                @endforeach
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                @else
                    <p class="text-muted">No roles assigned</p>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="info-card">
                <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.unions.edit', $union) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>Edit Union Details
                    </a>
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#assignShopOwnersModal">
                        <i class="fas fa-users me-2"></i>Assign Shop Owners
                    </button>
                    <a href="{{ route('admin.unions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Unions
                    </a>
                </div>
            </div>
        </div>

        <!-- Managed Shop Owners -->
        <div class="col-xl-8">
            <div class="info-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5><i class="fas fa-store me-2"></i>Managed Shop Owners</h5>
                    <span class="badge bg-primary">{{ $shopOwners->total() }} Total</span>
                </div>
                
                @if($shopOwners->count() > 0)
                    <div class="row">
                        @foreach($shopOwners as $shopOwner)
                            <div class="col-md-6 mb-3">
                                <div class="card shop-owner-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar bg-success text-white rounded-circle me-3" 
                                                 style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                {{ strtoupper(substr($shopOwner->first_name, 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $shopOwner->name }}</h6>
                                                <small class="text-muted">{{ $shopOwner->email }}</small>
                                            </div>
                                            <span class="badge bg-{{ $shopOwner->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($shopOwner->status) }}
                                            </span>
                                        </div>
                                        
                                        @if($shopOwner->shop)
                                            <div class="mb-2">
                                                <strong>{{ $shopOwner->shop->shop_name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $shopOwner->shop->state }}, {{ $shopOwner->shop->local_government }}
                                                </small>
                                            </div>
                                        @endif
                                        
                                        <div class="row text-center">
                                            <div class="col">
                                                <small class="text-muted d-block">Receipts</small>
                                                <strong>{{ $shopOwner->receipts()->count() }}</strong>
                                            </div>
                                            <div class="col">
                                                <small class="text-muted d-block">Joined</small>
                                                <strong>{{ $shopOwner->created_at->format('M Y') }}</strong>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-2">
<a href="{{ route('admin.users.details', $shopOwner) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $shopOwners->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-store text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted">No Shop Owners Managed</h6>
                        <p class="text-muted">This union executive doesn't have any assigned shop owners yet.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignShopOwnersModal">
                            <i class="fas fa-plus me-2"></i>Assign Shop Owners
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Assign Shop Owners Modal -->
<div class="modal fade" id="assignShopOwnersModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Shop Owners</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.unions.assign-shop-owners', $union) }}">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">Select shop owners in the assigned locations to manage under this union executive.</p>
                    
                    <div id="shopOwnersLoading" class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Loading available shop owners...</p>
                    </div>
                    
                    <div id="shopOwnersList" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Assign Selected
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const assignModal = document.getElementById('assignShopOwnersModal');
    
    assignModal.addEventListener('show.bs.modal', function() {
        loadAvailableShopOwners();
    });
    
    function loadAvailableShopOwners() {
        // This would load shop owners that can be assigned to this union
        // Based on location compatibility
        fetch(`/admin/unions/{{ $union->id }}/available-shop-owners`)
            .then(response => response.json())
            .then(data => {
                displayShopOwners(data.shopOwners);
            })
            .catch(error => {
                document.getElementById('shopOwnersLoading').innerHTML = 
                    '<p class="text-danger">Error loading shop owners. Please try again.</p>';
            });
    }
    
    function displayShopOwners(shopOwners) {
        const loading = document.getElementById('shopOwnersLoading');
        const list = document.getElementById('shopOwnersList');
        
        loading.style.display = 'none';
        list.style.display = 'block';
        
        if (shopOwners.length === 0) {
            list.innerHTML = '<p class="text-muted text-center">No available shop owners to assign.</p>';
            return;
        }
        
        let html = '<div class="row">';
        shopOwners.forEach(shopOwner => {
            html += `
                <div class="col-md-6 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" 
                               name="shop_owner_ids[]" value="${shopOwner.id}" 
                               id="shopOwner_${shopOwner.id}">
                        <label class="form-check-label w-100" for="shopOwner_${shopOwner.id}">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <strong>${shopOwner.name}</strong>
                                    <br>
                                    <small class="text-muted">${shopOwner.shop ? shopOwner.shop.shop_name : 'No shop'}</small>
                                    <br>
                                    <small class="text-muted">${shopOwner.location}</small>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        list.innerHTML = html;
    }
});
</script>
@endpush
@endsection
@extends('layouts.union')

@section('breadcrumb')
    <li class="breadcrumb-item active">Shop Owners</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Shop Owner Overview</h1>
            <p class="text-muted">Manage shop owners in your assigned locations</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshData()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
            <a href="{{ route('union.shop-owners.export') }}" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Export
            </a>
        </div>
    </div>
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}
.stats-card {
    border-radius: 12px;
    padding: 1rem;
    color: #fff;
}
.stats-card.primary { background: linear-gradient(135deg, #4dabf7, #1c7ed6); }
.stats-card.success { background: linear-gradient(135deg, #51cf66, #2b8a3e); }
.stats-card.info { background: linear-gradient(135deg, #339af0, #1864ab); }
.stats-card.warning { background: linear-gradient(135deg, #fcc419, #f08c00); }
</style>
    <!-- Statistics Cards -->
    <!--<div class="row mb-4">-->
    <!--    <div class="col-lg-3 col-md-6 mb-3">-->
    <!--        <div class="stats-card primary">-->
    <!--            <div class="d-flex align-items-center">-->
    <!--                <div class="flex-grow-1">-->
    <!--                    <h6 class="mb-1">Total Shop Owners</h6>-->
    <!--                    <h3 class="mb-0">{{ $stats['total_shop_owners'] ?? 0 }}</h3>-->
    <!--                </div>-->
    <!--                <div>-->
    <!--                    <i class="fas fa-store fa-2x opacity-75"></i>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

    <!--    <div class="col-lg-3 col-md-6 mb-3">-->
    <!--        <div class="stats-card success">-->
    <!--            <div class="d-flex align-items-center">-->
    <!--                <div class="flex-grow-1">-->
    <!--                    <h6 class="mb-1">Active Shops</h6>-->
    <!--                    <h3 class="mb-0">{{ $stats['active_shop_owners'] ?? 0 }}</h3>-->
    <!--                </div>-->
    <!--                <div>-->
    <!--                    <i class="fas fa-check-circle fa-2x opacity-75"></i>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

    <!--    <div class="col-lg-3 col-md-6 mb-3">-->
    <!--        <div class="stats-card info">-->
    <!--            <div class="d-flex align-items-center">-->
    <!--                <div class="flex-grow-1">-->
    <!--                    <h6 class="mb-1">New This Month</h6>-->
    <!--                    <h3 class="mb-0">{{ $stats['new_this_month'] ?? 0 }}</h3>-->
    <!--                </div>-->
    <!--                <div>-->
    <!--                    <i class="fas fa-user-plus fa-2x opacity-75"></i>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

    <!--    <div class="col-lg-3 col-md-6 mb-3">-->
    <!--        <div class="stats-card warning">-->
    <!--            <div class="d-flex align-items-center">-->
    <!--                <div class="flex-grow-1">-->
    <!--                    <h6 class="mb-1">High Performers</h6>-->
    <!--                    <h3 class="mb-0">{{ $stats['high_performers'] ?? 0 }}</h3>-->
    <!--                </div>-->
    <!--                <div>-->
    <!--                    <i class="fas fa-trophy fa-2x opacity-75"></i>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
 <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stats-card primary shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-uppercase small text-light">Total Shop Owners</h6>
                        <h3 class="mb-0 fw-bold">{{ $stats['total_shop_owners'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-store fa-2x text-light opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stats-card success shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-uppercase small text-light">Active Shops</h6>
                        <h3 class="mb-0 fw-bold">{{ $stats['active_shop_owners'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-light opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stats-card info shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-uppercase small text-light">New This Month</h6>
                        <h3 class="mb-0 fw-bold">{{ $stats['new_this_month'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-user-plus fa-2x text-light opacity-75"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stats-card warning shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 text-uppercase small text-light">High Performers</h6>
                        <h3 class="mb-0 fw-bold">{{ $stats['high_performers'] ?? 0 }}</h3>
                    </div>
                    <i class="fas fa-trophy fa-2x text-light opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('union.shop-owners.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Name, email, or shop...">
                </div>

                <div class="col-md-2">
                    <label for="state" class="form-label">State</label>
                    <select class="form-select" id="state" name="state">
                        <option value="">All States</option>
                        @if(isset($states))
                            @foreach($states as $state)
                                <option value="{{ $state }}" {{ request('state') == $state ? 'selected' : '' }}>
                                    {{ $state }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="lga" class="form-label">LGA</label>
                    <select class="form-select" id="lga" name="lga">
                        <option value="">All LGAs</option>
                        @if(isset($lgas))
                            @foreach($lgas as $lga)
                                <option value="{{ $lga }}" {{ request('lga') == $lga ? 'selected' : '' }}>
                                    {{ $lga }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="filter" class="form-label">Performance</label>
                    <select class="form-select" id="filter" name="filter">
                        <option value="">All</option>
                        <option value="high_performance" {{ request('filter') == 'high_performance' ? 'selected' : '' }}>High Performers</option>
                        <option value="low_performance" {{ request('filter') == 'low_performance' ? 'selected' : '' }}>Low Performers</option>
                        <option value="inactive" {{ request('filter') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Shop Owners Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Shop Owners List</h5>
        </div>
        <div class="card-body p-0">
            @if(isset($shopOwners) && $shopOwners->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Shop Owner</th>
                                <th>Shop Details</th>
                                <th>Location</th>
                                <th>Performance</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shopOwners as $shopOwner)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $shopOwner->name }}</strong><br>
                                                <small class="text-muted">{{ $shopOwner->email }}</small><br>
                                                <small class="text-muted">{{ $shopOwner->phone_number }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($shopOwner->shop)
                                            <strong>{{ $shopOwner->shop->shop_name }}</strong><br>
                                            <small class="text-muted">{{ Str::limit($shopOwner->shop->business_address, 40) }}</small>
                                        @else
                                            <span class="text-muted">No shop registered</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($shopOwner->shop)
                                            <span class="badge bg-primary">{{ $shopOwner->shop->state }}</span><br>
                                            <small class="text-muted">{{ $shopOwner->shop->local_government }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <small>Monthly:</small>
                                            <strong>{{ $shopOwner->monthly_receipts ?? 0 }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small>Revenue:</small>
                                            <strong>₦{{ number_format($shopOwner->monthly_revenue ?? 0) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small>Total:</small>
                                            <strong>₦{{ number_format($shopOwner->total_revenue ?? 0) }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'active' => 'success',
                                                'inactive' => 'secondary',
                                                'suspended' => 'danger',
                                                'pending' => 'warning'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$shopOwner->status] ?? 'secondary' }}">
                                            {{ ucfirst($shopOwner->status) }}
                                        </span>
                                        @if($shopOwner->last_login_at)
                                            <br><small class="text-muted">
                                                Last: {{ $shopOwner->last_login_at->diffForHumans() }}
                                            </small>
                                        @else
                                            <br><small class="text-muted">Never logged in</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('union.shop-owners.show', $shopOwner) }}" 
                                               class="btn btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-info" 
                                                    onclick="viewStatistics({{ $shopOwner->id }})" title="Statistics">
                                                <i class="fas fa-chart-bar"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer">
                    {{ $shopOwners->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-store text-muted mb-3" style="font-size: 4rem;"></i>
                    <h5 class="text-muted">No Shop Owners Found</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'state', 'status', 'filter']))
                            No shop owners match your current filters. Try adjusting your search criteria.
                        @else
                            No shop owners have been assigned to your locations yet.
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'state', 'status', 'filter']))
                        <a href="{{ route('union.shop-owners.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-times me-2"></i>Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}
</style>

<script>
function refreshData() {
    location.reload();
}

function viewStatistics(shopOwnerId) {
    window.open(`/union/shop-owners/${shopOwnerId}/statistics`, '_blank');
}

// LGA dependency on State
document.getElementById('state').addEventListener('change', function() {
    const state = this.value;
    const lgaSelect = document.getElementById('lga');
    
    // Clear current LGA options
    lgaSelect.innerHTML = '<option value="">All LGAs</option>';
    
    if (state) {
        fetch(`/lgas/${state}`)
            .then(response => response.json())
            .then(lgas => {
                lgas.forEach(lga => {
                    const option = document.createElement('option');
                    option.value = lga;
                    option.textContent = lga;
                    lgaSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading LGAs:', error));
    }
});
</script>
@endsection
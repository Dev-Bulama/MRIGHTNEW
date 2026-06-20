<div class="shop-details-modal">
    <!-- Shop Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                @if($shop->logo)
                    <img src="{{ Storage::url($shop->logo) }}" 
                         class="rounded me-3" 
                         style="width: 80px; height: 80px; object-fit: cover;" 
                         alt="Shop Logo">
                @else
                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-store fa-2x text-muted"></i>
                    </div>
                @endif
                
                <div>
                    <h4 class="mb-1">{{ $shop->shop_name }}</h4>
                    <p class="text-muted mb-1">{{ $shop->business_email }}</p>
                    <p class="text-muted mb-0">{{ $shop->business_phone_1 }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 text-end">
            @if($shop->approved)
                <span class="badge bg-success fs-6 px-3 py-2">
                    <i class="fas fa-check me-2"></i>Approved
                </span>
            @elseif($shop->status === 'rejected')
                <span class="badge bg-danger fs-6 px-3 py-2">
                    <i class="fas fa-times me-2"></i>Rejected
                </span>
            @else
                <span class="badge bg-warning fs-6 px-3 py-2">
                    <i class="fas fa-clock me-2"></i>Pending
                </span>
            @endif
        </div>
    </div>

    <!-- Shop Information Tabs -->
    <ul class="nav nav-tabs mb-3" id="shopDetailsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" 
                    data-bs-target="#basic" type="button" role="tab">
                <i class="fas fa-info-circle me-2"></i>Basic Info
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="owner-tab" data-bs-toggle="tab" 
                    data-bs-target="#owner" type="button" role="tab">
                <i class="fas fa-user me-2"></i>Owner Details
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="location-tab" data-bs-toggle="tab" 
                    data-bs-target="#location" type="button" role="tab">
                <i class="fas fa-map-marker-alt me-2"></i>Location
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="activity-tab" data-bs-toggle="tab" 
                    data-bs-target="#activity" type="button" role="tab">
                <i class="fas fa-chart-line me-2"></i>Activity
            </button>
        </li>
    </ul>

    <div class="tab-content" id="shopDetailsTabContent">
        <!-- Basic Information -->
        <div class="tab-pane fade show active" id="basic" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted">Shop Name:</td>
                            <td>{{ $shop->shop_name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Business Email:</td>
                            <td>{{ $shop->business_email }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Primary Phone:</td>
                            <td>{{ $shop->business_phone_1 }}</td>
                        </tr>
                        @if($shop->business_phone_2)
                        <tr>
                            <td class="fw-bold text-muted">Secondary Phone:</td>
                            <td>{{ $shop->business_phone_2 }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="fw-bold text-muted">Registration Date:</td>
                            <td>{{ $shop->created_at->format('F j, Y \a\t g:i A') }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="fw-bold text-muted mb-2">Business Address:</h6>
                    <address class="mb-3">
                        {{ $shop->business_address }}
                    </address>
                    
                    @if($shop->terms_and_conditions)
                        <h6 class="fw-bold text-muted mb-2">Terms & Conditions:</h6>
                        <div class="bg-light p-3 rounded small">
                            {{ Str::limit($shop->terms_and_conditions, 200) }}
                            @if(strlen($shop->terms_and_conditions) > 200)
                                <a href="#" class="text-primary">Read more...</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Owner Details -->
        <div class="tab-pane fade" id="owner" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-user me-2"></i>Shop Owner Information
                    </h6>
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted">Full Name:</td>
                            <td>{{ $shop->owner_full_name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Account Email:</td>
                            <td>{{ $shop->user->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Phone Number:</td>
                            <td>{{ $shop->user->phone_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">User Type:</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ ucwords(str_replace('_', ' ', $shop->user->user_type ?? 'Shop Owner')) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Account Status:</td>
                            <td>
                                @if($shop->user && $shop->user->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-warning">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Member Since:</td>
                            <td>{{ $shop->user ? $shop->user->created_at->format('F j, Y') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-chart-bar me-2"></i>Owner Activity Summary
                    </h6>
                    
                    @php
                        $userReceipts = $shop->user ? $shop->user->receipts()->count() : 0;
                        $shopReceipts = $shop->receipts()->count();
                        $lastLogin = $shop->user ? $shop->user->last_login_at : null;
                    @endphp
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card bg-light">
                                <div class="card-body text-center p-3">
                                    <h4 class="mb-1">{{ $shopReceipts }}</h4>
                                    <small class="text-muted">Receipts Generated</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-light">
                                <div class="card-body text-center p-3">
                                    <h4 class="mb-1">₦0</h4>
                                    <small class="text-muted">Total Earnings</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($lastLogin)
                        <p class="text-muted mt-3 mb-0">
                            <i class="fas fa-clock me-2"></i>
                            Last seen: {{ $lastLogin->diffForHumans() }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Location Details -->
        <div class="tab-pane fade" id="location" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>Location Information
                    </h6>
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted">Country:</td>
                            <td>{{ $shop->country }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">State:</td>
                            <td>{{ $shop->state }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Local Government:</td>
                            <td>{{ $shop->local_government }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Full Address:</td>
                            <td>{{ $shop->business_address }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-map me-2"></i>Area Statistics
                    </h6>
                    
                    @php
                        $areaShops = \App\Models\Shop::where('state', $shop->state)
                                                   ->where('local_government', $shop->local_government)
                                                   ->count();
                        $stateShops = \App\Models\Shop::where('state', $shop->state)->count();
                    @endphp
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Shops in {{ $shop->local_government }}:</span>
                                        <strong>{{ $areaShops }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Shops in {{ $shop->state }} State:</span>
                                        <strong>{{ $stateShops }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity & Performance -->
        <div class="tab-pane fade" id="activity" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-chart-line me-2"></i>Performance Metrics
                    </h6>
                    
                    @php
                        $totalReceipts = $shop->receipts()->count();
                        $thisMonthReceipts = $shop->receipts()->whereMonth('created_at', now()->month)->count();
                        $lastMonthReceipts = $shop->receipts()->whereMonth('created_at', now()->subMonth()->month)->count();
                        $avgReceiptsPerMonth = $totalReceipts > 0 ? round($totalReceipts / max(1, $shop->created_at->diffInMonths(now()) + 1), 1) : 0;
                    @endphp
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card border-primary">
                                <div class="card-body text-center p-3">
                                    <h3 class="text-primary mb-1">{{ $totalReceipts }}</h3>
                                    <small class="text-muted">Total Receipts</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-success">
                                <div class="card-body text-center p-3">
                                    <h3 class="text-success mb-1">{{ $thisMonthReceipts }}</h3>
                                    <small class="text-muted">This Month</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-info">
                                <div class="card-body text-center p-3">
                                    <h3 class="text-info mb-1">{{ $avgReceiptsPerMonth }}</h3>
                                    <small class="text-muted">Monthly Average</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-warning">
                                <div class="card-body text-center p-3">
                                    <h3 class="text-warning mb-1">
                                        @if($lastMonthReceipts == 0 && $thisMonthReceipts > 0)
                                            +100%
                                        @elseif($lastMonthReceipts > 0)
                                            {{ round((($thisMonthReceipts - $lastMonthReceipts) / $lastMonthReceipts) * 100, 1) }}%
                                        @else
                                            0%
                                        @endif
                                    </h3>
                                    <small class="text-muted">Growth</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-history me-2"></i>Recent Activity
                    </h6>
                    
                    @php
                        $recentReceipts = $shop->receipts()->with('user')->latest()->take(5)->get();
                    @endphp
                    
                    @if($recentReceipts->count() > 0)
                        <div class="timeline">
                            @foreach($recentReceipts as $receipt)
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-receipt text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Receipt Generated</h6>
                                            <p class="mb-1 text-muted small">
                                                Receipt #{{ $receipt->receipt_number }}
                                                @if($receipt->customer_name)
                                                    for {{ $receipt->customer_name }}
                                                @endif
                                            </p>
                                            <small class="text-muted">{{ $receipt->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox text-muted mb-3" style="font-size: 2rem;"></i>
                            <p class="text-muted">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="border-top pt-3 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    Registered {{ $shop->created_at->diffForHumans() }}
                </small>
            </div>
            
            <div class="btn-group">
                @if(!$shop->approved && $shop->status !== 'rejected')
                    <button type="button" class="btn btn-success" 
                            onclick="approveShop({{ $shop->id }})">
                        <i class="fas fa-check me-2"></i>Approve Shop
                    </button>
                    <button type="button" class="btn btn-danger" 
                            onclick="rejectShop({{ $shop->id }})">
                        <i class="fas fa-times me-2"></i>Reject Shop
                    </button>
                @elseif($shop->approved)
                    <button type="button" class="btn btn-warning" 
                            onclick="suspendShop({{ $shop->id }})">
                        <i class="fas fa-pause me-2"></i>Suspend Shop
                    </button>
                @else
                    <button type="button" class="btn btn-info" 
                            onclick="reinstateShop({{ $shop->id }})">
                        <i class="fas fa-undo me-2"></i>Reinstate Shop
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.timeline-item {
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 8px;
    top: 25px;
    bottom: -15px;
    width: 2px;
    background: #e9ecef;
}

.shop-details-modal .nav-tabs {
    border-bottom: 2px solid #dee2e6;
}

.shop-details-modal .nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
}

.shop-details-modal .nav-tabs .nav-link.active {
    color: #495057;
    border-bottom: 2px solid #007bff;
    background: none;
}
</style>
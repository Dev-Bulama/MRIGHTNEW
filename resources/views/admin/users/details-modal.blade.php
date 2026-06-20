@php
    $user->load(['shop', 'receipts']);
    $stats = $stats ?? [];
@endphp
<div class="user-details-modal">
    <!-- User Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <img src="{{ $user->avatar_url }}" 
                     alt="{{ $user->full_name }}" 
                     class="rounded-circle me-3"
                     style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #e9ecef;">
                <div>
                    <h4 class="mb-1">{{ $user->full_name }}</h4>
                    <p class="text-muted mb-1">{{ $user->email }}</p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar me-1"></i>
                        Joined {{ $user->created_at->format('F j, Y') }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 text-end">
            <div class="mb-3">
                <span class="badge bg-{{ $user->user_type === 'admin' ? 'danger' : ($user->user_type === 'shop_owner' ? 'success' : 'info') }} fs-6 px-3 py-2">
                    <i class="fas fa-{{ $user->user_type === 'admin' ? 'shield-alt' : ($user->user_type === 'shop_owner' ? 'store' : 'user') }} me-2"></i>
                    {{ $user->user_type_display }}
                </span>
            </div>
            
            <div class="mb-2">
                <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'suspended' ? 'danger' : 'warning') }} fs-6 px-3 py-2">
                    <i class="fas fa-{{ $user->status === 'active' ? 'check-circle' : ($user->status === 'suspended' ? 'ban' : 'clock') }} me-2"></i>
                    {{ $user->status_display }}
                </span>
            </div>
        </div>
    </div>

    <!-- User Details Tabs -->
    <ul class="nav nav-tabs mb-3" id="userDetailsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" 
                    data-bs-target="#basic" type="button" role="tab">
                <i class="fas fa-info-circle me-2"></i>Basic Info
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="activity-tab" data-bs-toggle="tab" 
                    data-bs-target="#activity" type="button" role="tab">
                <i class="fas fa-chart-line me-2"></i>Activity
            </button>
        </li>
        @if($user->shop)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="shop-tab" data-bs-toggle="tab" 
                    data-bs-target="#shop" type="button" role="tab">
                <i class="fas fa-store me-2"></i>Shop Info
            </button>
        </li>
        @endif
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="receipts-tab" data-bs-toggle="tab" 
                    data-bs-target="#receipts" type="button" role="tab">
                <i class="fas fa-receipt me-2"></i>Receipts
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Basic Information -->
        <div class="tab-pane fade show active" id="basic" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">Personal Information</h6>
                    
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Full Name:</td>
                            <td>{{ $user->full_name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">First Name:</td>
                            <td>{{ $user->first_name ?: 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Last Name:</td>
                            <td>{{ $user->last_name ?: 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Email:</td>
                            <td>
                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                    {{ $user->email }}
                                </a>
                                @if($user->email_verified_at)
                                    <i class="fas fa-check-circle text-success ms-1" title="Verified"></i>
                                @else
                                    <i class="fas fa-exclamation-circle text-warning ms-1" title="Unverified"></i>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Primary Phone:</td>
                            <td>
                                @if($user->phone_number)
                                    <a href="tel:{{ $user->phone_number }}" class="text-decoration-none">
                                        {{ $user->phone_number }}
                                    </a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Secondary Phone:</td>
                            <td>
                                @if($user->secondary_phone)
                                    <a href="tel:{{ $user->secondary_phone }}" class="text-decoration-none">
                                        {{ $user->secondary_phone }}
                                    </a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">Account Information</h6>
                    
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">User ID:</td>
                            <td><code>{{ $user->id }}</code></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">User Type:</td>
                            <td>
                                <span class="badge bg-{{ $user->user_type === 'admin' ? 'danger' : ($user->user_type === 'shop_owner' ? 'success' : 'info') }}">
                                    {{ $user->user_type_display }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status:</td>
                            <td>
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'suspended' ? 'danger' : 'warning') }}">
                                    {{ $user->status_display }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Registration:</td>
                            <td>{{ $user->created_at->format('M j, Y g:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Last Login:</td>
                            <td>
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('M j, Y g:i A') }}
                                    <br><small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Last IP:</td>
                            <td>{{ $user->last_login_ip ?: 'Unknown' }}</td>
                        </tr>
                    </table>
                    
                    @if($user->import_metadata)
                        <div class="mt-3">
                            <h6 class="fw-bold text-warning mb-2">Import Information</h6>
                            <div class="bg-light p-3 rounded">
                                <small class="text-muted">
                                    <strong>Imported:</strong> {{ \Carbon\Carbon::parse($user->import_metadata['imported_at'])->format('M j, Y g:i A') }}<br>
                                    <strong>Source:</strong> {{ ucfirst($user->import_metadata['source'] ?? 'Unknown') }}<br>
                                    @if(isset($user->import_metadata['import_batch']))
                                        <strong>Batch:</strong> {{ $user->import_metadata['import_batch'] }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity Tab -->
        <div class="tab-pane fade" id="activity" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">Account Statistics</h6>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card border-primary">
                                <div class="card-body text-center p-3">
                                    <h4 class="text-primary mb-1">{{ $userStats['account_age'] }}</h4>
                                    <small class="text-muted">Days Active</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-success">
                                <div class="card-body text-center p-3">
                                    <h4 class="text-success mb-1">{{ $userStats['total_receipts'] }}</h4>
                                    <small class="text-muted">Total Receipts</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card border-info">
                                <div class="card-body text-center p-3">
                                    <h4 class="text-info mb-1">₦{{ number_format($userStats['total_spent'], 2) }}</h4>
                                    <small class="text-muted">Total Transaction Value</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">Recent Activity</h6>
                    
                    <div class="activity-timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Account Registered</h6>
                                <p class="text-muted mb-0 small">{{ $user->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                        
                        @if($user->account_activated_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Account Activated</h6>
                                <p class="text-muted mb-0 small">{{ $user->account_activated_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($user->last_login_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Last Login</h6>
                                <p class="text-muted mb-0 small">{{ $user->last_login_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Shop Information -->
        @if($user->shop)
        <div class="tab-pane fade" id="shop" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">Shop Details</h6>
                    
                    <div class="d-flex align-items-center mb-3">
                        @if($user->shop->logo)
                            <img src="{{ Storage::url($user->shop->logo) }}" 
                                 class="rounded me-3" 
                                 style="width: 60px; height: 60px; object-fit: cover;" 
                                 alt="Shop Logo">
                        @else
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-store fa-lg text-muted"></i>
                            </div>
                        @endif
                        
                        <div>
                            <h5 class="mb-1">{{ $user->shop->shop_name }}</h5>
                            <p class="text-muted mb-0">{{ $user->shop->business_email }}</p>
                        </div>
                    </div>
                    
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" width="40%">Owner:</td>
                            <td>{{ $user->shop->owner_full_name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Phone:</td>
                            <td>{{ $user->shop->business_phone_1 }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Location:</td>
                            <td>{{ $user->shop->state }}, {{ $user->shop->country }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status:</td>
                            <td>
                                @if($user->shop->approved)
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Registered:</td>
                            <td>{{ $user->shop->created_at->format('M j, Y') }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3">Shop Performance</h6>
                    
                    @php
                        $shopReceipts = $user->shop->receipts()->count();
                        $shopRevenue = $user->shop->receipts()
                            ->where('payment_gateway_status', 'successful')
                            ->sum('amount') ?? 0;
                    @endphp
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-body text-center p-3">
                                    <h4 class="text-primary mb-1">{{ $shopReceipts }}</h4>
                                    <small class="text-muted">Total Receipts</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-body text-center p-3">
                                    <h4 class="text-success mb-1">₦{{ number_format($shopRevenue, 2) }}</h4>
                                    <small class="text-muted">Total Revenue</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.shops.show', $user->shop) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i>View Shop Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Receipts -->
        <div class="tab-pane fade" id="receipts" role="tabpanel">
            <h6 class="fw-bold text-primary mb-3">Recent Receipts</h6>
            
            @if($user->receipts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Receipt #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->receipts as $receipt)
                                <tr>
                                    <td>
                                        <strong>{{ $receipt->receipt_number }}</strong>
                                    </td>
                                    <td>{{ $receipt->customer_name ?: 'Guest' }}</td>
                                    <td>₦{{ number_format($receipt->amount ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $receipt->payment_gateway_status === 'successful' ? 'success' : ($receipt->payment_gateway_status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($receipt->payment_gateway_status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td>{{ $receipt->created_at->format('M j, Y') }}</td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-sm" 
                                                onclick="window.open('/admin/receipts?search={{ $receipt->receipt_number }}', '_blank')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($userStats['total_receipts'] > 5)
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.receipts.index') }}?search={{ $user->email }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>View All {{ $userStats['total_receipts'] }} Receipts
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="fas fa-receipt text-muted mb-3" style="font-size: 2rem;"></i>
                    <h6 class="text-muted">No Receipts Found</h6>
                    <p class="text-muted mb-0">This user hasn't generated any receipts yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="border-top pt-3 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    User ID: {{ $user->id }} • Last updated {{ $user->updated_at->diffForHumans() }}
                </small>
            </div>
            
            <div class="btn-group">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary" target="_blank">
                    <i class="fas fa-edit me-2"></i>Edit User
                </a>
                
                @if($user->status === 'active')
                    <button type="button" class="btn btn-warning" 
                            onclick="suspendUser({{ $user->id }})">
                        <i class="fas fa-pause me-2"></i>Suspend
                    </button>
                @else
                    <button type="button" class="btn btn-success" 
                            onclick="activateUser({{ $user->id }})">
                        <i class="fas fa-play me-2"></i>Activate
                    </button>
                @endif
                
                <a href="{{ route('admin.users.contact', $user) }}" class="btn btn-outline-info" target="_blank">
                    <i class="fas fa-envelope me-2"></i>Contact
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.activity-timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -1.75rem;
    top: 1.5rem;
    bottom: -1.5rem;
    width: 2px;
    background: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.timeline-content h6 {
    font-size: 0.9rem;
    color: #495057;
}

.user-details-modal .nav-tabs {
    border-bottom: 2px solid #dee2e6;
}

.user-details-modal .nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
}

.user-details-modal .nav-tabs .nav-link.active {
    color: #495057;
    border-bottom: 2px solid #007bff;
    background: none;
}
</style>
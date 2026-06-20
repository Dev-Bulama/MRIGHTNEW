@extends('layouts.union')

@section('title', 'Union Dashboard')
@section('page-title', 'Union Dashboard')
@section('page-description', 'Manage shop owners in your assigned locations')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                Partnering association Dashboard
                @if($selectedState)
                    <small class="text-muted">- {{ $selectedState }}</small>
                @endif
            </h1>
            <!--<p class="text-muted">Manage shop owners in your assigned locations</p>-->
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshStats()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
            <a href="{{ route('union.dashboard.export', request()->all()) }}" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Export
            </a>
        </div>
    </div>

    <!-- Location Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" method="GET" action="{{ route('union.dashboard') }}">
                <div class="row g-3">
                    <!--<div class="col-md-4">-->
                    <!--    <label for="search" class="form-label">Search</label>-->
                    <!--    <input type="text" -->
                    <!--           class="form-control" -->
                    <!--           id="search" -->
                    <!--           name="search" -->
                    <!--           value="{{ $searchTerm ?? '' }}" -->
                    <!--           placeholder="Name, email, or shop...">-->
                    <!--</div>-->
                    
                    <div class="col-md-3">
                        <label for="state" class="form-label">State</label>
                        <select class="form-select" id="state" name="state">
                            <option value="">All Assigned States</option>
                            @foreach($availableStates as $state)
                                <option value="{{ $state }}" {{ $selectedState == $state ? 'selected' : '' }}>
                                    {{ $state }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="lga" class="form-label">Local Government</label>
                        <select class="form-select" id="lga" name="lga" {{ !$selectedState ? 'disabled' : '' }}>
                            <option value="">Select LGA</option>
                            @if($selectedState && $selectedLga)
                                <option value="{{ $selectedLga }}" selected>{{ $selectedLga }}</option>
                            @endif
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('union.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Alerts -->
    @if(isset($alerts) && count($alerts) > 0)
        <div class="row mb-4">
            <div class="col-12">
                @foreach($alerts as $alert)
                    <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                        <strong>{{ $alert['title'] }}:</strong> {{ $alert['message'] }}
                        @if(isset($alert['action_url']))
                            <a href="{{ $alert['action_url'] }}" class="btn btn-sm btn-outline-{{ $alert['type'] }} ms-2">
                                {{ $alert['action_text'] }}
                            </a>
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-6" id="statsContainer">
        <!-- Shop Owner Statistics -->
    <!--    <div class="col-lg-3 col-md-6 mb-3">-->
    <!--        <div class="card border-0 shadow-sm h-100">-->
    <!--            <div class="card-body">-->
    <!--                <div class="d-flex align-items-center">-->
    <!--                    <div class="flex-shrink-0">-->
    <!--                        <div class="bg-primary bg-gradient rounded-3 p-3">-->
    <!--                            <i class="fas fa-store text-white fa-lg"></i>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <div class="flex-grow-1 ms-3">-->
    <!--                        <h6 class="text-muted mb-1">Total Shop Owners</h6>-->
    <!--                        <h3 class="mb-0" id="totalShopOwners">{{ $stats['total_shop_owners'] ?? 0 }}</h3>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

    <!--    <div class="col-lg-3 col-md-6 mb-3">-->
    <!--        <div class="card border-0 shadow-sm h-100">-->
    <!--            <div class="card-body">-->
    <!--                <div class="d-flex align-items-center">-->
    <!--                    <div class="flex-shrink-0">-->
    <!--                        <div class="bg-success bg-gradient rounded-3 p-3">-->
    <!--                            <i class="fas fa-check-circle text-white fa-lg"></i>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <div class="flex-grow-1 ms-3">-->
    <!--                        <h6 class="text-muted mb-1">Active Shops</h6>-->
    <!--                        <h3 class="mb-0" id="activeShops">{{ $stats['active_shop_owners'] ?? 0 }}</h3>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

    <!--    <div class="col-lg-3 col-md-6 mb-3">-->
    <!--        <div class="card border-0 shadow-sm h-100">-->
    <!--            <div class="card-body">-->
    <!--                <div class="d-flex align-items-center">-->
    <!--                    <div class="flex-shrink-0">-->
    <!--                        <div class="bg-info bg-gradient rounded-3 p-3">-->
    <!--                            <i class="fas fa-user-plus text-white fa-lg"></i>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <div class="flex-grow-1 ms-3">-->
    <!--                        <h6 class="text-muted mb-1">New This Month</h6>-->
    <!--                        <h3 class="mb-0" id="newThisMonth">{{ $stats['new_registrations_this_month'] ?? 0 }}</h3>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->

        <div class="col-lg-12 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-gradient rounded-3 p-3">
                                <i class="fas fa-trophy text-white fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">High Performers</h6>
                            <h3 class="mb-0" id="highPerformers">{{ $stats['high_performers'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue & Receipt Statistics -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="card-title mb-0">Revenue & Receipt Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h5 class="text-primary mb-1" id="totalReceipts">{{ $receiptStats['total_receipts'] ?? 0 }}</h5>
                            <small class="text-muted">Total Receipts</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h5 class="text-success mb-1" id="successfulReceipts">{{ $receiptStats['successful_receipts'] ?? 0 }}</h5>
                            <small class="text-muted">Successful Receipts</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h5 class="text-info mb-1" id="totalRevenue">₦{{ number_format($receiptStats['total_revenue'] ?? 0, 2) }}</h5>
                            <small class="text-muted">Total Revenue</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h5 class="text-warning mb-1" id="monthlyRevenue">₦{{ number_format($receiptStats['revenue_this_month'] ?? 0, 2) }}</h5>
                            <small class="text-muted">This Month</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="card-title mb-0">Payout Overview</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Pending Payouts:</span>
                        <strong id="pendingPayouts">{{ $payoutStats['pending_payouts'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Commission:</span>
                        <strong id="totalCommission">₦{{ number_format($payoutStats['total_commission_earned'] ?? 0, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Paid Out:</span>
                        <strong id="totalPaidOut">₦{{ number_format($payoutStats['total_paid_out'] ?? 0, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LGA Breakdown (Only show when specific state is selected) -->
    @if($selectedState && !empty($lgaBreakdown))
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        <h6 class="card-title mb-0">{{ $selectedState }} LGA Breakdown</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Local Government</th>
                                        <th>Shop Count</th>
                                        <th>Total Receipts</th>
                                        <th>Successful Receipts</th>
                                        <th>Total Revenue</th>
                                        <th>This Month</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lgaBreakdown as $lga)
                                        <tr>
                                            <td>
                                                <a href="{{ route('union.dashboard', array_merge(request()->all(), ['lga' => $lga->lga])) }}" 
                                                   class="text-decoration-none">
                                                    {{ $lga->lga }}
                                                </a>
                                            </td>
                                            <td>{{ $lga->shop_count }}</td>
                                            <td>{{ $lga->total_receipts }}</td>
                                            <td>{{ $lga->successful_receipts }}</td>
                                            <td>₦{{ number_format($lga->total_revenue, 2) }}</td>
                                            <td>{{ $lga->receipts_this_month }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- State Performance & Recent Shop Owners -->
    <div class="row">
        <!-- State Performance -->
        @if(!empty($statePerformance) && count($statePerformance) > 1)
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        <h6 class="card-title mb-0">State Performance Breakdown</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>State</th>
                                        <th>Shops</th>
                                        <th>Receipts</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statePerformance as $state)
                                        <tr>
                                            <td>
                                                <a href="{{ route('union.dashboard', ['state' => $state->state]) }}" 
                                                   class="text-decoration-none">
                                                    {{ $state->state }}
                                                </a>
                                            </td>
                                            <td>{{ $state->shop_count }}</td>
                                            <td>{{ $state->total_receipts }}</td>
                                            <td>₦{{ number_format($state->total_revenue, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Shop Owners -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="card-title mb-0">Recent Shop Owners</h6>
                </div>
                <div class="card-body">
                    @if(isset($recentShopOwners) && $recentShopOwners->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Shop</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentShopOwners as $shopOwner)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $shopOwner->name }}</strong><br>
                                                    <small class="text-muted">{{ $shopOwner->email }}</small>
                                                </div>
                                            </td>
                                            <td>{{ $shopOwner->shop ? $shopOwner->shop->shop_name : 'No shop' }}</td>
                                            <td>
                                                <small>
                                                    {{ $shopOwner->shop ? $shopOwner->shop->state : 'N/A' }}<br>
                                                    {{ $shopOwner->shop ? $shopOwner->shop->local_government : 'N/A' }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $shopOwner->status === 'active' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($shopOwner->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('union.shop-owners.show', $shopOwner) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('union.shop-owners.index') }}" class="btn btn-outline-primary">
                                View All Shop Owners
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-store text-muted mb-3" style="font-size: 3rem;"></i>
                            <h6 class="text-muted">No Shop Owners Yet</h6>
                            <p class="text-muted">Shop owners in your assigned locations will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading overlay -->
<div id="loadingOverlay" class="d-none">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

@endsection
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Setting up DYNAMIC state change listener...');
    
    const stateSelect = document.getElementById('state');
    const lgaSelect = document.getElementById('lga');
    const searchInput = document.getElementById('search');
    const filterForm = document.getElementById('filterForm');
    let debounceTimer;
    let searchAbortController;

    if (stateSelect && lgaSelect) {
        // DYNAMIC state change listener - calls backend for assigned LGAs
        stateSelect.addEventListener('change', function() {
            const selectedState = this.value;
            console.log('DYNAMIC STATE CHANGED TO:', selectedState);
            
            if (selectedState) {
                console.log('Loading ASSIGNED LGAs for state:', selectedState);
                // Enable LGA dropdown and load ASSIGNED LGAs from backend
                lgaSelect.disabled = false;
                loadLgas(selectedState);  // Call backend for assigned LGAs
                
                // Do NOT auto-submit form - let user manually submit
            } else {
                console.log('No state selected - disabling LGA dropdown');
                // Disable and clear LGA dropdown
                lgaSelect.disabled = true;
                lgaSelect.innerHTML = '<option value="">Select LGA</option>';
            }
        });

        // Handle LGA change - submit form when LGA is selected
        lgaSelect.addEventListener('change', function() {
            console.log('LGA changed to:', this.value);
            // Submit when user selects an LGA
            if (this.value) {
                debounceSubmit(800); // Longer delay for LGA changes
            }
        });

        console.log('Dynamic state change listener added successfully');
    } else {
        console.log('ERROR: Could not find state or LGA elements');
    }

    // OPTIMIZED: Handle search input with improved debouncing
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchValue = this.value.trim();
            console.log('Search input changed:', searchValue);
            
            // Cancel previous search request if still pending
            if (searchAbortController) {
                searchAbortController.abort();
            }
            
            clearTimeout(debounceTimer);
            
            // Different debounce times based on search length
            let debounceTime;
            if (searchValue.length === 0) {
                debounceTime = 300; // Quick clear
            } else if (searchValue.length < 3) {
                debounceTime = 1000; // Longer wait for short terms
            } else {
                debounceTime = 600; // Moderate wait for longer terms
            }
            
            debounceTimer = setTimeout(() => {
                submitFilterForm();
            }, debounceTime);
        });
    }

    // Manual form submission (when user clicks search button)
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted manually');
            submitFilterForm();
        });
    }

    // OPTIMIZED: Debounced form submission with configurable delay
    function debounceSubmit(delay = 600) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            submitFilterForm();
        }, delay);
    }

    // OPTIMIZED: Submit filter form via AJAX with abort capability
    function submitFilterForm() {
        console.log('Submitting filter form with optimization');
        
        // Cancel previous request if still pending
        if (searchAbortController) {
            searchAbortController.abort();
        }
        
        // Create new abort controller
        searchAbortController = new AbortController();
        
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);

        // Show loading indicator
        showLoading(true);

        // Make AJAX request with abort capability
        fetch(`{{ route('union.dashboard.statistics') }}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            signal: searchAbortController.signal // Add abort capability
        })
        .then(response => {
            console.log('Statistics response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Statistics data received:', data);
            if (data.success) {
                updateStatistics(data.stats);
            } else {
                console.error('Failed to load statistics:', data.message);
                showErrorMessage('Failed to load statistics: ' + data.message);
            }
        })
        .catch(error => {
            if (error.name === 'AbortError') {
                console.log('Search request aborted');
            } else {
                console.error('Statistics fetch error:', error);
                showErrorMessage('Error loading statistics. Please try again.');
            }
        })
        .finally(() => {
            showLoading(false);
            searchAbortController = null;
        });

        // Update URL without reloading page
        const newUrl = `{{ route('union.dashboard') }}?${params.toString()}`;
        window.history.pushState({}, '', newUrl);
    }

    // DYNAMIC: Load LGAs with proper POST request for assigned LGAs only
    function loadLgas(state) {
        console.log('Loading ASSIGNED LGAs for state:', state);
        
        // Clear existing options first
        lgaSelect.innerHTML = '<option value="">Loading assigned LGAs...</option>';
        lgaSelect.disabled = true;
        
        fetch(`{{ route('union.dashboard.get-lgas') }}`, {
            method: 'POST',  // IMPORTANT: This must be POST, not GET
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ state: state })  // Send state as JSON in POST body
        })
        .then(response => {
            console.log('LGA API response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('LGA API data received:', data);
            
            if (data.success) {
                // Clear loading message
                lgaSelect.innerHTML = '<option value="">All LGAs</option>';
                
                if (data.lgas && data.lgas.length > 0) {
                    console.log(`✅ SUCCESS: Loading ${data.lgas.length} ASSIGNED LGAs for ${state}:`);
                    console.log('📍 Your assigned LGAs:', data.lgas);
                    
                    // Add ONLY assigned LGAs to dropdown
                    data.lgas.forEach(lga => {
                        const option = document.createElement('option');
                        option.value = lga;
                        option.textContent = lga;
                        lgaSelect.appendChild(option);
                    });

                    console.log('✅ Assigned LGAs successfully loaded into dropdown');
                    
                    // Re-enable the dropdown
                    lgaSelect.disabled = false;
                    
                    // Show debug info if available
                    if (data.debug_info) {
                        console.log('🔍 Debug info:', data.debug_info);
                    }
                    
                } else {
                    console.log('⚠️ No LGAs assigned for this state');
                    lgaSelect.innerHTML = '<option value="">No LGAs assigned</option>';
                    lgaSelect.disabled = false;
                    
                    // Show info message to user
                    showInfoMessage(`No LGAs assigned for ${state}. Contact admin to assign LGAs.`);
                }

                // Restore selected LGA if exists and is in the assigned list
                const currentLga = '{{ $selectedLga ?? "" }}';
                if (currentLga && data.lgas && data.lgas.includes(currentLga)) {
                    lgaSelect.value = currentLga;
                    console.log('✅ Restored selected LGA:', currentLga);
                } else if (currentLga && data.lgas && !data.lgas.includes(currentLga)) {
                    console.warn('⚠️ Previously selected LGA not in assigned list:', currentLga);
                    showWarningMessage(`LGA "${currentLga}" is not assigned to you.`);
                }
                
            } else {
                console.error('❌ LGA API request failed:', data.message);
                lgaSelect.innerHTML = '<option value="">Error loading LGAs</option>';
                lgaSelect.disabled = false;
                showErrorMessage('Failed to load LGAs: ' + data.message);
            }
        })
        .catch(error => {
            console.error('❌ Error loading LGAs:', error);
            lgaSelect.innerHTML = '<option value="">Error loading LGAs</option>';
            lgaSelect.disabled = false;
            showErrorMessage('Error loading LGAs. Please try again.');
        });
    }

    // Helper functions for showing user messages
    function showErrorMessage(message) {
        showMessage(message, 'danger', 'fas fa-exclamation-triangle');
    }

    function showWarningMessage(message) {
        showMessage(message, 'warning', 'fas fa-exclamation-triangle');
    }

    function showInfoMessage(message) {
        showMessage(message, 'info', 'fas fa-info-circle');
    }

    function showSuccessMessage(message) {
        showMessage(message, 'success', 'fas fa-check-circle');
    }

    // Generic message display function
    function showMessage(message, type, icon) {
        // Remove existing alert if any
        const existingAlert = document.getElementById('dynamic-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        // Create new alert
        const alertDiv = document.createElement('div');
        alertDiv.id = 'dynamic-alert';
        alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-2`;
        alertDiv.innerHTML = `
            <i class="${icon} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert after the filter form
        if (filterForm && filterForm.parentNode) {
            filterForm.parentNode.insertBefore(alertDiv, filterForm.nextSibling);
        } else {
            // Fallback: insert at top of container
            const container = document.querySelector('.container-fluid');
            if (container) {
                container.insertBefore(alertDiv, container.firstChild);
            }
        }

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv && alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Enhanced loading function
    function showLoading(show) {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            if (show) {
                loadingOverlay.classList.remove('d-none');
            } else {
                loadingOverlay.classList.add('d-none');
            }
        }
        
        // Also update search input with loading state
        if (searchInput) {
            searchInput.style.opacity = show ? '0.6' : '1';
            searchInput.disabled = show;
        }
        
        // Update state and LGA selects
        if (stateSelect) stateSelect.disabled = show;
        if (lgaSelect && !show) {
            // Only re-enable LGA if a state is selected
            lgaSelect.disabled = !stateSelect.value;
        }
    }

    // Update statistics in the UI (you may need to implement this function based on your UI)
    function updateStatistics(stats) {
        console.log('Updating statistics with:', stats);
        
        // Update shop owner stats
        if (document.getElementById('totalShopOwners')) {
            document.getElementById('totalShopOwners').textContent = stats.shop_owners?.total || 0;
        }
        if (document.getElementById('activeShops')) {
            document.getElementById('activeShops').textContent = stats.shop_owners?.active || 0;
        }
        // Add more stat updates as needed based on your HTML structure
    }

    // Initialize: Load LGAs for current state if exists
    const currentState = stateSelect ? stateSelect.value : null;
    if (currentState) {
        console.log('🚀 Initializing: Loading LGAs for current state on page load:', currentState);
        loadLgas(currentState);
    }
});
</script>
<!--<script>-->
<!--document.addEventListener('DOMContentLoaded', function() {-->
<!--    console.log('Setting up state change listener...');-->
    
<!--    const stateSelect = document.getElementById('state');-->
<!--    const lgaSelect = document.getElementById('lga');-->
    
<!--    if (stateSelect && lgaSelect) {-->
<!--        stateSelect.addEventListener('change', function() {-->
<!--            console.log('STATE CHANGED TO:', this.value);-->
            
<!--            if (this.value === 'Borno') {-->
<!--                console.log('Borno selected - enabling LGA dropdown');-->
<!--                lgaSelect.disabled = false;-->
<!--                lgaSelect.innerHTML = `-->
<!--                    <option value="">Select LGA</option>-->
<!--                    <option value="Kukawa">Kukawa</option>-->
<!--                    <option value="Maiduguri">Maiduguri</option>-->
<!--                    <option value="Bama">Bama</option>-->
<!--                `;-->
<!--                console.log('LGA options added');-->
<!--            } else if (this.value) {-->
<!--                lgaSelect.disabled = false;-->
<!--                lgaSelect.innerHTML = `-->
<!--                    <option value="">Select LGA</option>-->
<!--                    <option value="Test LGA 1">Test LGA 1</option>-->
<!--                    <option value="Test LGA 2">Test LGA 2</option>-->
<!--                `;-->
<!--            } else {-->
<!--                lgaSelect.disabled = true;-->
<!--                lgaSelect.innerHTML = '<option value="">Select LGA</option>';-->
<!--            }-->
<!--        });-->
<!--        console.log('State change listener added successfully');-->
<!--    } else {-->
<!--        console.log('ERROR: Could not find state or LGA elements');-->
<!--    }-->
<!--});-->
<!--</script>-->
@push('styles')
<style>
    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        z-index: 9999;
    }
    
    .card {
        transition: transform 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .bg-gradient {
        background: linear-gradient(135deg, var(--bs-primary), var(--bs-primary-dark)) !important;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        color: #6c757d;
        border-bottom: 2px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard script loaded');

    const stateSelect = document.getElementById('state');
    const lgaSelect = document.getElementById('lga');
    const searchInput = document.getElementById('search');
    const filterForm = document.getElementById('filterForm');
    let debounceTimer;

    // Handle state change to load LGAs (NO AUTO-SUBMIT)
    if (stateSelect) {
        stateSelect.addEventListener('change', function() {
            const selectedState = this.value;
            console.log('State changed to:', selectedState);
            
            if (selectedState) {
                console.log('Enabling LGA dropdown and loading LGAs');
                // Enable LGA dropdown and load LGAs
                lgaSelect.disabled = false;
                loadLgas(selectedState);
                
                // Do NOT auto-submit form here - let user manually submit
            } else {
                console.log('Disabling LGA dropdown');
                // Disable and clear LGA dropdown
                lgaSelect.disabled = true;
                lgaSelect.innerHTML = '<option value="">Select LGA</option>';
            }
        });
    }

    // Handle LGA change - submit form when LGA is selected
    if (lgaSelect) {
        lgaSelect.addEventListener('change', function() {
            console.log('LGA changed to:', this.value);
            // Only submit when user selects an LGA
            if (this.value) {
                debounceSubmit();
            }
        });
    }

    // Handle search input with debounce
   // OPTIMIZED: Handle search input with improved debouncing
if (searchInput) {
    let searchAbortController;
    
    searchInput.addEventListener('input', function() {
        const searchValue = this.value.trim();
        console.log('Search input changed:', searchValue);
        
        // Cancel previous search request if still pending
        if (searchAbortController) {
            searchAbortController.abort();
        }
        
        clearTimeout(debounceTimer);
        
        // Different debounce times based on search length
        let debounceTime;
        if (searchValue.length === 0) {
            debounceTime = 300; // Quick clear
        } else if (searchValue.length < 3) {
            debounceTime = 1000; // Longer wait for short terms
        } else {
            debounceTime = 600; // Moderate wait for longer terms
        }
        
        debounceTimer = setTimeout(() => {
            submitFilterForm();
        }, debounceTime);
    });
}

    // Manual form submission (when user clicks search button)
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted manually');
            submitFilterForm();
        });
    }

    // Debounced form submission
   // OPTIMIZED: Debounced form submission with configurable delay
function debounceSubmit(delay = 600) {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        submitFilterForm();
    }, delay);
}

    // Submit filter form via AJAX and update statistics
    // function submitFilterForm() {
    //     console.log('Submitting filter form');
    //     const formData = new FormData(filterForm);
    //     const params = new URLSearchParams(formData);

    //     // Show loading indicator
    //     showLoading(true);

    //     // Make AJAX request to get updated statistics
    //     fetch(`{{ route('union.dashboard.statistics') }}?${params.toString()}`, {
    //         method: 'GET',
    //         headers: {
    //             'X-Requested-With': 'XMLHttpRequest',
    //             'Accept': 'application/json',
    //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
    //         }
    //     })
    //     .then(response => {
    //         console.log('Statistics response status:', response.status);
    //         return response.json();
    //     })
    //     .then(data => {
    //         console.log('Statistics data received:', data);
    //         if (data.success) {
    //             updateStatistics(data.stats);
    //         } else {
    //             console.error('Failed to load statistics:', data.message);
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Statistics fetch error:', error);
    //     })
    //     .finally(() => {
    //         showLoading(false);
    //     });

    //     // Update URL without reloading page
    //     const newUrl = `{{ route('union.dashboard') }}?${params.toString()}`;
    //     window.history.pushState({}, '', newUrl);
    // }
    // OPTIMIZED: Submit filter form via AJAX with abort capability
function submitFilterForm() {
    console.log('Submitting filter form with optimization');
    
    // Cancel previous request if still pending
    if (window.searchAbortController) {
        window.searchAbortController.abort();
    }
    
    // Create new abort controller
    window.searchAbortController = new AbortController();
    
    const formData = new FormData(filterForm);
    const params = new URLSearchParams(formData);

    // Show loading indicator
    showLoading(true);

    // Make AJAX request with abort capability
    fetch(`{{ route('union.dashboard.statistics') }}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        signal: window.searchAbortController.signal // Add abort capability
    })
    .then(response => {
        console.log('Statistics response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Statistics data received:', data);
        if (data.success) {
            updateStatistics(data.stats);
        } else {
            console.error('Failed to load statistics:', data.message);
            showErrorMessage('Failed to load statistics: ' + data.message);
        }
    })
    .catch(error => {
        if (error.name === 'AbortError') {
            console.log('Search request aborted');
        } else {
            console.error('Statistics fetch error:', error);
            showErrorMessage('Error loading statistics. Please try again.');
        }
    })
    .finally(() => {
        showLoading(false);
        window.searchAbortController = null;
    });

    // Update URL without reloading page
    const newUrl = `{{ route('union.dashboard') }}?${params.toString()}`;
    window.history.pushState({}, '', newUrl);
}

    // Load LGAs for selected state
    
    // OPTIMIZED: Load LGAs with improved error handling for ASSIGNED LGAs only

// FIXED: Load LGAs with proper POST request for assigned LGAs only
function loadLgas(state) {
    console.log('Loading assigned LGAs for state:', state);
    
    // Clear existing options first
    const lgaSelect = document.getElementById('lga');
    lgaSelect.innerHTML = '<option value="">Loading LGAs...</option>';
    lgaSelect.disabled = true;
    
    fetch(`{{ route('union.dashboard.get-lgas') }}`, {
        method: 'POST',  // IMPORTANT: This must be POST, not GET
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ state: state })  // Send state as JSON in POST body
    })
    .then(response => {
        console.log('LGA response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('LGA data received:', data);
        
        if (data.success) {
            // Clear loading message
            lgaSelect.innerHTML = '<option value="">All LGAs</option>';
            
            if (data.lgas && data.lgas.length > 0) {
                console.log(`Loading ${data.lgas.length} assigned LGAs for ${state}`);
                console.log('Assigned LGAs:', data.lgas);
                
                // Add ONLY assigned LGAs to dropdown
                data.lgas.forEach(lga => {
                    const option = document.createElement('option');
                    option.value = lga;
                    option.textContent = lga;
                    lgaSelect.appendChild(option);
                });

                console.log('Assigned LGAs successfully loaded into dropdown');
                
                // Re-enable the dropdown
                lgaSelect.disabled = false;
                
                // Show debug info if available
                if (data.debug_info) {
                    console.log('Debug info:', data.debug_info);
                }
                
            } else {
                console.log('No LGAs assigned for this state');
                lgaSelect.innerHTML = '<option value="">No LGAs assigned</option>';
                lgaSelect.disabled = false;
                
                // Show info message to user
                showInfoMessage(`No LGAs assigned for ${state}. Contact admin to assign LGAs.`);
            }

            // Restore selected LGA if exists and is in the assigned list
            const currentLga = '{{ $selectedLga ?? "" }}';
            if (currentLga && data.lgas && data.lgas.includes(currentLga)) {
                lgaSelect.value = currentLga;
                console.log('Restored selected LGA:', currentLga);
            } else if (currentLga && data.lgas && !data.lgas.includes(currentLga)) {
                console.warn('Previously selected LGA not in assigned list:', currentLga);
                showWarningMessage(`LGA "${currentLga}" is not assigned to you.`);
            }
            
        } else {
            console.error('LGA request failed:', data.message);
            lgaSelect.innerHTML = '<option value="">Error loading LGAs</option>';
            lgaSelect.disabled = false;
            showErrorMessage('Failed to load LGAs: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error loading LGAs:', error);
        lgaSelect.innerHTML = '<option value="">Error loading LGAs</option>';
        lgaSelect.disabled = false;
        showErrorMessage('Error loading LGAs. Please try again.');
    });
}


    // Fallback: Load LGAs with inline data
    function loadLgasInline(state) {
        console.log('Using fallback inline LGA data for:', state);
        
        const lgaData = {
            'Abia': ['Aba North', 'Aba South', 'Arochukwu', 'Bende', 'Ikwuano', 'Isiala Ngwa North', 'Isiala Ngwa South', 'Isuikwuato', 'Obi Ngwa', 'Ohafia', 'Osisioma', 'Ugwunagbo', 'Ukwa East', 'Ukwa West', 'Umuahia North', 'Umuahia South', 'Umu Nneochi'],
            'Borno': ['Abadam', 'Askira/Uba', 'Bama', 'Bayo', 'Biu', 'Chibok', 'Damboa', 'Dikwa', 'Gubio', 'Guzamala', 'Gwoza', 'Hawul', 'Jere', 'Kaga', 'Kala/Balge', 'Konduga', 'Kukawa', 'Kwaya Kusar', 'Mafa', 'Magumeri', 'Maiduguri', 'Marte', 'Mobbar', 'Monguno', 'Ngala', 'Nganzai', 'Shani'],
            'Cross River': ['Abi', 'Akamkpa', 'Akpabuyo', 'Bakassi', 'Bekwarra', 'Biase', 'Boki', 'Calabar Municipal', 'Calabar South', 'Etung', 'Ikom', 'Obanliku', 'Obubra', 'Obudu', 'Odukpani', 'Ogoja', 'Yakurr', 'Yala'],
            'Benue': ['Ado', 'Agatu', 'Apa', 'Buruku', 'Gboko', 'Guma', 'Gwer East', 'Gwer West', 'Katsina-Ala', 'Konshisha', 'Kwande', 'Logo', 'Makurdi', 'Obi', 'Ogbadibo', 'Ohimini', 'Oju', 'Okpokwu', 'Otukpo', 'Tarka', 'Ukum', 'Ushongo', 'Vandeikya'],
            'Lagos': ['Agege', 'Ajeromi-Ifelodun', 'Alimosho', 'Amuwo-Odofin', 'Apapa', 'Badagry', 'Epe', 'Eti Osa', 'Ibeju-Lekki', 'Ifako-Ijaiye', 'Ikeja', 'Ikorodu', 'Kosofe', 'Lagos Island', 'Lagos Mainland', 'Mushin', 'Ojo', 'Oshodi-Isolo', 'Shomolu', 'Surulere']
        };

        if (lgaData[state]) {
            lgaSelect.innerHTML = '<option value="">All LGAs</option>';
            lgaData[state].forEach(lga => {
                const option = document.createElement('option');
                option.value = lga;
                option.textContent = lga;
                lgaSelect.appendChild(option);
            });
            console.log('Fallback LGAs loaded:', lgaData[state].length, 'items');
        } else {
            console.log('No LGA data available for state:', state);
            lgaSelect.innerHTML = '<option value="">No LGAs available</option>';
        }
    }

    // Update statistics in the UI
    function updateStatistics(stats) {
        console.log('Updating statistics with:', stats);
        
        // Update shop owner stats
        if (document.getElementById('totalShopOwners')) {
            document.getElementById('totalShopOwners').textContent = stats.shop_owners?.total || 0;
        }
        if (document.getElementById('activeShops')) {
            document.getElementById('activeShops').textContent = stats.shop_owners?.active || 0;
        }
        if (document.getElementById('newThisMonth')) {
            document.getElementById('newThisMonth').textContent = stats.shop_owners?.new_this_month || 0;
        }
        if (document.getElementById('highPerformers')) {
            document.getElementById('highPerformers').textContent = stats.shop_owners?.high_performers || 0;
        }

        // Update receipt stats
        if (document.getElementById('totalReceipts')) {
            document.getElementById('totalReceipts').textContent = stats.receipts?.total || 0;
        }
        if (document.getElementById('successfulReceipts')) {
            document.getElementById('successfulReceipts').textContent = stats.receipts?.successful || 0;
        }
        if (document.getElementById('totalRevenue')) {
            document.getElementById('totalRevenue').textContent = '₦' + (stats.receipts?.total_revenue || 0).toLocaleString('en-NG', {minimumFractionDigits: 2});
        }
        if (document.getElementById('monthlyRevenue')) {
            document.getElementById('monthlyRevenue').textContent = '₦' + (stats.receipts?.revenue_this_month || 0).toLocaleString('en-NG', {minimumFractionDigits: 2});
        }

        // Update payout stats
        if (document.getElementById('pendingPayouts')) {
            document.getElementById('pendingPayouts').textContent = stats.payouts?.pending || 0;
        }
        if (document.getElementById('totalCommission')) {
            document.getElementById('totalCommission').textContent = '₦' + (stats.payouts?.total_commission_earned || 0).toLocaleString('en-NG', {minimumFractionDigits: 2});
        }
        if (document.getElementById('totalPaidOut')) {
            document.getElementById('totalPaidOut').textContent = '₦' + (stats.payouts?.total_paid_out || 0).toLocaleString('en-NG', {minimumFractionDigits: 2});
        }
    }

    // Show/hide loading indicator
    function showLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            if (show) {
                overlay.classList.remove('d-none');
            } else {
                overlay.classList.add('d-none');
            }
        }
    }

    // Initialize LGAs if state is already selected
    const currentState = stateSelect ? stateSelect.value : null;
    console.log('Current state on page load:', currentState);
    if (currentState) {
        console.log('Loading LGAs for current state');
        loadLgas(currentState);
    }

    // Make functions globally available for testing
    window.testLoadLgas = loadLgas;
    window.testUpdateStats = updateStatistics;
});

// Refresh statistics function (called by refresh button)
function refreshStats() {
    console.log('Refresh button clicked');
    const filterForm = document.getElementById('filterForm');
    const formData = new FormData(filterForm);
    const params = new URLSearchParams(formData);

    fetch(`{{ route('union.dashboard.statistics') }}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateStatistics(data.stats);
            
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>Statistics refreshed successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.container-fluid').firstChild);
            
            // Auto-dismiss after 3 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Error refreshing stats:', error);
    });
}
// Helper function to show error messages
function showErrorMessage(message) {
    showMessage(message, 'danger', 'fas fa-exclamation-triangle');
}

// Helper function to show warning messages
function showWarningMessage(message) {
    showMessage(message, 'warning', 'fas fa-exclamation-triangle');
}

// Helper function to show info messages
function showInfoMessage(message) {
    showMessage(message, 'info', 'fas fa-info-circle');
}

// Generic message display function
function showMessage(message, type, icon) {
    // Remove existing alert if any
    const existingAlert = document.getElementById('dynamic-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.id = 'dynamic-alert';
    alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-2`;
    alertDiv.innerHTML = `
        <i class="${icon} me-2"></i>
        <span>${message}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert after the filter form
    const filterForm = document.getElementById('filterForm');
    if (filterForm && filterForm.parentNode) {
        filterForm.parentNode.insertBefore(alertDiv, filterForm.nextSibling);
    }

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Enhanced loading function
function showLoading(show) {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        if (show) {
            loadingOverlay.classList.remove('d-none');
        } else {
            loadingOverlay.classList.add('d-none');
        }
    }
    
    // Also update search input with loading state
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.style.opacity = show ? '0.6' : '1';
        searchInput.disabled = show;
    }
    
    // Update state and LGA selects
    const stateSelect = document.getElementById('state');
    const lgaSelect = document.getElementById('lga');
    if (stateSelect) stateSelect.disabled = show;
    if (lgaSelect && !show) {
        // Only re-enable LGA if a state is selected
        lgaSelect.disabled = !stateSelect.value;
    }
}
</script>
@endpush
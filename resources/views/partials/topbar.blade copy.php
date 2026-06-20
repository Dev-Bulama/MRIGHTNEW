<!-- Topbar -->
<nav class="topbar navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
    <div class="container-fluid">
        <!-- Mobile Menu Toggle -->
        <button class="btn btn-link d-lg-none me-3 p-1" type="button" onclick="toggleSidebar()">
            <i class="fas fa-bars text-muted fs-5"></i>
        </button>
        
        <!-- Page Title -->
        <div class="page-title-container flex-grow-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    @if(isset($breadcrumbs) && is_array($breadcrumbs))
                        @foreach($breadcrumbs as $index => $breadcrumb)
                            @if($loop->last)
                                <li class="breadcrumb-item active fw-semibold" aria-current="page">
                                    {{ $breadcrumb['title'] }}
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    @if(isset($breadcrumb['url']))
                                        <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none">
                                            {{ $breadcrumb['title'] }}
                                        </a>
                                    @else
                                        {{ $breadcrumb['title'] }}
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="breadcrumb-item active fw-semibold" aria-current="page">
                            @yield('page-title', 'Dashboard')
                        </li>
                    @endif
                </ol>
            </nav>
            
            @hasSection('page-description')
                <small class="text-muted d-none d-md-block">@yield('page-description')</small>
            @endif
        </div>

        <!-- Dashboard Search (Authenticated Users Only)
        @auth
        <div class="search-container me-4">
            <div class="input-group" style="width: 350px;">
                <input type="text" 
                       class="form-control" 
                       id="dashboardSearch" 
                       placeholder="Search receipts, phone serial, customer..."
                       autocomplete="off">
                <select class="form-select" id="searchType" style="max-width: 120px;">
                    <option value="receipt">Receipts</option>
                    <option value="customer">Customers</option>
                    @if(auth()->user()->isAdmin())
                        <option value="shop">Shops</option>
                    @endif
                </select>
                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            
           Search Results Dropdown
            <div class="search-results position-absolute bg-white border rounded shadow-lg mt-1" 
                 id="searchResults" 
                 style="display: none; z-index: 1050; width: 350px; max-height: 400px; overflow-y: auto;">
                <div class="search-results-content"> -->
                    <!-- Results will be populated here -->
                <!-- </div>
            </div>
        </div>
        @endauth  -->

        <!-- Topbar Actions -->
        <div class="topbar-actions d-flex align-items-center gap-2">
            
            <!-- Quick Search -->
            <div class="search-container d-none d-md-flex">
                <form class="d-flex" role="search" action="{{ route('search.quick') }}" method="GET">
                    <div class="input-group">
                        <input class="form-control form-control-sm border-0 bg-light" 
                               type="search" 
                               name="query"
                               placeholder="Search serial number..." 
                               aria-label="Search"
                               style="min-width: 200px;">
                        <button class="btn btn-light btn-sm" type="submit">
                            <i class="fas fa-search text-muted"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Quick Actions Dropdown -->
            @if(auth()->user()->isShopOwner() && auth()->user()->shop && auth()->user()->shop->isActive())
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus me-1"></i>
                        <span class="d-none d-sm-inline">Quick Action</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('receipt.create') }}">
                                <i class="fas fa-receipt me-2 text-primary"></i>
                                Generate New Receipt
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('receipt.resale') }}">
                                <i class="fas fa-sync-alt me-2 text-info"></i>
                                Resale Phone
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <!-- <li>
                            <a class="dropdown-item" href="{{ route('search.index') }}">
                                <i class="fas fa-search me-2 text-success"></i>
                                Search Phone Receipt
                            </a>
                        </li> -->
                    </ul>
                </div>
            @endif
            
            <!-- Notifications -->
            <div class="dropdown">
                <button class="btn btn-link position-relative p-2" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell text-muted fs-5"></i>
                    @php
                        $notificationCount = 0;
                        if(auth()->user()->isShopOwner() && auth()->user()->shop) {
                            if(!auth()->user()->shop->approved) $notificationCount++;
                            $notificationCount += auth()->user()->receipts()->where('payment_gateway_status', 'pending')->count();
                        }
                        if(auth()->user()->isAdmin()) {
                            $notificationCount += \App\Models\Shop::pendingApproval()->count();
                        }
                    @endphp
                    @if($notificationCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              style="font-size: 0.6rem; transform: translate(-50%, -50%) !important;">
                            {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                        </span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-0" style="width: 320px;">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Notifications</span>
                        @if($notificationCount > 0)
                            <span class="badge bg-primary">{{ $notificationCount }}</span>
                        @endif
                    </div>
                    
                    <div class="notification-list" style="max-height: 300px; overflow-y: auto;">
                        @if($notificationCount > 0)
                            @if(auth()->user()->isShopOwner() && auth()->user()->shop && !auth()->user()->shop->approved)
                                <a href="{{ route('shop.show', auth()->user()->shop) }}" class="dropdown-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <i class="fas fa-store text-warning"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fs-6">Shop Approval Pending</h6>
                                            <p class="mb-1 small text-muted">Your shop is awaiting admin approval</p>
                                            <small class="text-muted">{{ auth()->user()->shop->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </a>
                            @endif
                            
                            @if(auth()->user()->isAdmin())
                                @php $pendingShops = \App\Models\Shop::pendingApproval()->take(3)->get(); @endphp
                                @foreach($pendingShops as $shop)
                                    <a href="{{ route('admin.shops.show', $shop) }}" class="dropdown-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <i class="fas fa-store text-info"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fs-6">New Shop Registration</h6>
                                                <p class="mb-1 small text-muted">{{ $shop->shop_name }} needs approval</p>
                                                <small class="text-muted">{{ $shop->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @endif
                            
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('notifications.index') }}" class="dropdown-item text-center text-primary">
                                <small>View all notifications</small>
                            </a>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-bell-slash text-muted fs-3 mb-3"></i>
                                <p class="text-muted mb-0">No new notifications</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- User Profile Dropdown -->
            <div class="dropdown">
                <button class="btn btn-link d-flex align-items-center p-1" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ auth()->user()->avatar_url }}" 
                         alt="{{ auth()->user()->name }}" 
                         class="rounded-circle me-2"
                         width="32" height="32">
                    <span class="d-none d-md-inline fw-medium">{{ auth()->user()->first_name }}</span>
                    <i class="fas fa-chevron-down ms-2 small text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="dropdown-header">
                        <div class="text-center">
                            <img src="{{ auth()->user()->avatar_url }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="rounded-circle mb-2"
                                 width="48" height="48">
                            <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                            <small class="text-muted">{{ auth()->user()->email }}</small>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="fas fa-user me-2"></i>My Profile
                        </a>
                    </li>
                    
                    @if(auth()->user()->isShopOwner() && auth()->user()->shop)
                        <li>
                            <a class="dropdown-item" href="{{ route('shop.show', auth()->user()->shop) }}">
                                <i class="fas fa-store me-2"></i>My Shop
                            </a>
                        </li>
                    @endif
                    
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-cog me-2"></i>Account Settings
                        </a>
                    </li>
                    
                    <li>
                        <a class="dropdown-item" href="{{ route('help.index') }}">
                            <i class="fas fa-question-circle me-2"></i>Help & Support
                        </a>
                    </li>
                    
                    <li><hr class="dropdown-divider"></li>
                    
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Sign Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<style>
.topbar {
    height: var(--topbar-height);
    z-index: 1020;
    border-bottom: 1px solid #e9ecef;
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px);
}

@media (min-width: 992px) {
    .topbar {
        left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
    }
}

.breadcrumb {
    margin-bottom: 0;
    background: none;
    padding: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
    font-weight: bold;
}

.breadcrumb-item.active {
    color: var(--dark-color);
}

.search-container .form-control {
    transition: all 0.3s ease;
}

.search-container .form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 138, 188, 0.25);
    min-width: 280px;
}

.notification-list .dropdown-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f8f9fa;
}

.notification-list .dropdown-item:hover {
    background-color: #f8f9fa;
}

.notification-list .dropdown-item:last-child {
    border-bottom: none;
}

.topbar-actions .btn-link {
    color: #6c757d;
    text-decoration: none;
}

.topbar-actions .btn-link:hover {
    color: var(--primary-color);
}

.search-results-item {
    padding: 12px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.search-results-item:hover {
    background-color: #f8f9fa;
}

.search-results-item:last-child {
    border-bottom: none;
}

/* Mobile search */
@media (max-width: 767.98px) {
    .search-container {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid #e9ecef;
        padding: 1rem;
        box-shadow: var(--shadow);
        transform: translateY(-100%);
        transition: transform 0.3s ease;
        z-index: -1;
    }
    
    .search-container.show {
        transform: translateY(0);
    }
    
    .mobile-search-toggle {
        display: block !important;
    }
}

/* Dropdown menu animations */
.dropdown-menu {
    border: none;
    box-shadow: var(--shadow-lg);
    border-radius: 12px;
    padding: 0.5rem 0;
    animation: dropdownSlide 0.2s ease-out;
}

@keyframes dropdownSlide {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-header {
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    font-weight: 600;
}

/* Badge positioning fix */
.position-relative .badge {
    font-size: 0.6rem;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script>
// Dashboard search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('dashboardSearch');
    const searchType = document.getElementById('searchType');
    const searchButton = document.getElementById('searchButton');
    const searchResults = document.getElementById('searchResults');
    
    if (!searchInput) return; // Exit if search not available
    
    let searchTimeout;
    
    // Search on input with debouncing
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length >= 3) {
            searchTimeout = setTimeout(() => performDashboardSearch(query), 500);
        } else {
            hideSearchResults();
        }
    });
    
    // Search on button click
    searchButton.addEventListener('click', function() {
        const query = searchInput.value.trim();
        if (query.length >= 3) {
            performDashboardSearch(query);
        }
    });
    
    // Search on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchButton.click();
        }
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-container')) {
            hideSearchResults();
        }
    });
    
    function performDashboardSearch(query) {
        const type = searchType.value;
        
        showSearchLoading();
        
        fetch('{{ route("search.query") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                phone_number: query,
                search_type: type
            })
        })
        .then(response => response.json())
        .then(data => {
            displaySearchResults(data.results || []);
        })
        .catch(error => {
            console.error('Search error:', error);
            showSearchError('Search failed. Please try again.');
        });
    }
    
    function showSearchLoading() {
        searchResults.innerHTML = '<div class="p-3 text-center"><i class="fas fa-spinner fa-spin me-2"></i>Searching...</div>';
        searchResults.style.display = 'block';
    }
    
    function displaySearchResults(results) {
        if (results.length === 0) {
            searchResults.innerHTML = '<div class="p-3 text-muted text-center">No results found</div>';
        } else {
            let html = '';
            results.forEach(result => {
                html += `
                    <div class="search-results-item" onclick="viewSearchResult('${result.receipt_number}')">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${result.receipt_number}</h6>
                                <p class="mb-1 text-muted small">${result.customer_name} | ${result.customer_phone}</p>
                                <p class="mb-0 text-muted small">${result.phone_name} (${result.phone_color || 'N/A'})</p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">₦${parseFloat(result.amount || 0).toLocaleString()}</span>
                                <br><small class="text-muted">${result.date}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            searchResults.innerHTML = html;
        }
        searchResults.style.display = 'block';
    }
    
    function showSearchError(message) {
        searchResults.innerHTML = `<div class="p-3 text-danger text-center">${message}</div>`;
        searchResults.style.display = 'block';
    }
    
    function hideSearchResults() {
        searchResults.style.display = 'none';
    }
    
    // Global function to view search result
    window.viewSearchResult = function(receiptNumber) {
        hideSearchResults();
        searchInput.value = '';
        // Redirect to receipt view
        window.location.href = `/receipt/${receiptNumber}/view`;
    };
});

// Mobile search toggle functionality
function toggleMobileSearch() {
    const searchContainer = document.querySelector('.search-container');
    searchContainer.classList.toggle('show');
}

// Close mobile search when clicking outside
document.addEventListener('click', function(event) {
    const searchContainer = document.querySelector('.search-container');
    const searchToggle = document.querySelector('.mobile-search-toggle');
    
    if (window.innerWidth < 768 && 
        searchContainer.classList.contains('show') && 
        !searchContainer.contains(event.target) && 
        !searchToggle?.contains(event.target)) {
        searchContainer.classList.remove('show');
    }
});

// Auto-focus search input when dropdown opens
document.addEventListener('shown.bs.dropdown', function(event) {
    const searchInput = event.target.querySelector('input[type="search"]');
    if (searchInput) {
        searchInput.focus();
    }
});

// Quick search functionality
document.addEventListener('DOMContentLoaded', function() {
    const quickSearchForm = document.querySelector('.search-container form');
    if (quickSearchForm) {
        quickSearchForm.addEventListener('submit', function(e) {
            const query = this.querySelector('input[name="query"]').value.trim();
            if (!query) {
                e.preventDefault();
                if (typeof showToast === 'function') {
                    showToast('Please enter a serial number to search', 'warning');
                } else {
                    alert('Please enter a serial number to search');
                }
            }
        });
    }
});

// Mark notifications as read when dropdown is opened
document.addEventListener('shown.bs.dropdown', function(event) {
    if (event.target.querySelector('.fa-bell')) {
        // Mark notifications as read via AJAX
        fetch('{{ route("notifications.mark-read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        }).catch(error => console.error('Error marking notifications as read:', error));
    }
});
</script>
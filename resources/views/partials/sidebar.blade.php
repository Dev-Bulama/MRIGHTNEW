<style>
/* Enhanced Mobile Navigation Styles */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: var(--sidebar-width);
    z-index: 1030;
    transform: translateX(-100%);
    transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    overflow-y: auto;
    overflow-x: hidden;
    background: #ffffff;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

/* Desktop sidebar - always visible */
@media (min-width: 992px) {
    .sidebar {
        transform: translateX(0);
        position: fixed;
    }
    
    .main-content {
        margin-left: var(--sidebar-width);
        padding-top: var(--topbar-height);
    }
    
    .topbar {
        left: var(--sidebar-width);
        width: calc(100% - var(--sidebar-width));
    }
}

/* Mobile sidebar - hidden by default, shown when .show class is added */
.sidebar.show {
    transform: translateX(0);
}

/* Enhanced overlay with better visibility */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.6);
    z-index: 1025;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    backdrop-filter: blur(2px);
}

.sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* Enhanced hamburger button styling */
.topbar .btn[onclick="toggleSidebar()"] {
    position: relative;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: transparent;
    border: 2px solid transparent;
}

.topbar .btn[onclick="toggleSidebar()"]:hover {
    background: rgba(13, 138, 188, 0.1);
    border-color: rgba(13, 138, 188, 0.2);
    transform: scale(1.05);
}

.topbar .btn[onclick="toggleSidebar()"].active {
    background: rgba(13, 138, 188, 0.15);
    border-color: rgba(13, 138, 188, 0.3);
}

/* Hamburger icon animation */
.topbar .btn[onclick="toggleSidebar()"] i {
    transition: transform 0.3s ease, color 0.3s ease;
}

.topbar .btn[onclick="toggleSidebar()"].active i {
    transform: rotate(90deg);
    color: var(--primary-color, #0d8abc);
}

/* Sidebar close button enhancement */
.sidebar-header .btn[onclick="toggleSidebar()"] {
    padding: 8px;
    border-radius: 6px;
    transition: all 0.2s ease;
    background: transparent;
    border: 1px solid transparent;
}

.sidebar-header .btn[onclick="toggleSidebar()"]:hover {
    background: rgba(220, 53, 69, 0.1);
    border-color: rgba(220, 53, 69, 0.2);
    color: #dc3545;
}

/* Mobile responsive adjustments */
@media (max-width: 991.98px) {
    .main-content {
        margin-left: 0;
        padding-top: var(--topbar-height);
        transition: filter 0.3s ease;
    }
    
    .topbar {
        left: 0;
        width: 100%;
    }
    
    /* Blur main content when sidebar is open */
    body.sidebar-open .main-content {
        filter: blur(2px);
    }
    
    /* Hide main content scrollbar when sidebar is open */
    body.sidebar-open {
        overflow: hidden;
    }
}

/* Improved Navigation Styles */
.sidebar-nav .nav-link {
    color: #6c757d;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.25rem;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    display: flex;
    align-items: center;
    text-decoration: none;
    position: relative;
    overflow: hidden;
}

.sidebar-nav .nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(13, 138, 188, 0.1), transparent);
    transition: left 0.5s ease;
}

.sidebar-nav .nav-link:hover {
    background-color: #f8f9fa;
    color: var(--primary-color, #0d8abc);
    transform: translateX(8px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.sidebar-nav .nav-link:hover::before {
    left: 100%;
}

.sidebar-nav .nav-link.active {
    background: linear-gradient(135deg, var(--primary-color, #0d8abc) 0%, #0056b3 100%);
    color: white;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(13, 138, 188, 0.3);
}

.sidebar-nav .nav-link.active:hover {
    transform: none;
    box-shadow: 0 6px 16px rgba(13, 138, 188, 0.4);
}

.sidebar-nav .nav-link i {
    width: 20px;
    text-align: center;
    margin-right: 12px;
    transition: transform 0.3s ease;
}

.sidebar-nav .nav-link:hover i {
    transform: scale(1.1);
}

/* Badge animations */
.sidebar-nav .badge {
    transition: all 0.3s ease;
}

.sidebar-nav .nav-link:hover .badge {
    transform: scale(1.1);
}

/* User Profile Enhancement */
.user-profile {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.user-profile:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
}

.user-profile .avatar img {
    object-fit: cover;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.user-profile:hover .avatar img {
    border-color: var(--primary-color, #0d8abc);
    transform: scale(1.05);
}

/* Logo Enhancement */
.logo {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.logo::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
    transform: rotate(45deg);
    transition: transform 0.6s ease;
}

.logo:hover::before {
    transform: rotate(45deg) translateX(100%);
}

.logo:hover {
    transform: scale(1.05);
}

/* Smooth scrolling for sidebar */
.sidebar {
    scrollbar-width: thin;
    scrollbar-color: rgba(13, 138, 188, 0.3) transparent;
}

.sidebar::-webkit-scrollbar {
    width: 4px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(13, 138, 188, 0.3);
    border-radius: 2px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(13, 138, 188, 0.5);
}

/* Loading animation for sidebar */
@keyframes slideInLeft {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.sidebar.show {
    animation: slideInLeft 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

/* Focus states for accessibility */
.sidebar-nav .nav-link:focus,
.topbar .btn:focus {
    outline: 2px solid var(--primary-color, #0d8abc);
    outline-offset: 2px;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .sidebar {
        border-right: 2px solid #000;
    }
    
    .sidebar-nav .nav-link {
        border: 1px solid transparent;
    }
    
    .sidebar-nav .nav-link:hover {
        border-color: #000;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .sidebar,
    .sidebar-overlay,
    .sidebar-nav .nav-link,
    .topbar .btn {
        transition: none;
    }
}
</style>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column bg-white shadow-lg" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header p-4 border-bottom">
        <div class="d-flex align-items-center">
            <div class="logo-container me-3">
                @php
                    $mrightLogo = \App\Models\SystemLogo::getMrightLogo();
                @endphp
                @if($mrightLogo)
                    <img src="{{ $mrightLogo->logo_url }}" 
                         alt="M-right" 
                         style="width: 45px; height: 45px; object-fit: contain; border-radius: 8px;">
                @else
                    <div class="logo bg-gradient-primary rounded-3 d-flex align-items-center justify-content-center" 
                         style="width: 45px; height: 45px;">
                        <i class="fas fa-receipt text-white fs-5"></i>
                    </div>
                @endif
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-0 fw-bold text-dark">M-right</h5>
                <small class="text-muted">Digital Receipt</small>
            </div>
            <button class="btn btn-link p-0 d-lg-none" type="button" onclick="toggleSidebar()">
                <i class="fas fa-times text-muted"></i>
            </button>
        </div>
    </div>
    
    <!-- User Profile Section -->
    @auth
    <div class="user-profile p-4 border-bottom bg-light">
        <div class="d-flex align-items-center">
            <div class="avatar me-3">
                <img src="{{ auth()->user()->avatar_url ? auth()->user()->avatar_url : 'https://via.placeholder.com/40x40/667eea/ffffff?text=U' }}"
                     alt="{{ auth()->user()->name }}" 
                     class="rounded-circle"
                     width="40" height="40">
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-semibold">{{ auth()->user()->first_name }}</h6>
                <small class="text-muted">{{ auth()->user()->user_type_display }}</small>
                
                @if(auth()->user()->assigned_states)
                    <div class="mt-1">
                        <small class="text-info">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ implode(', ', auth()->user()->assigned_states) }}
                        </small>
                    </div>
                @endif
            </div>
            <div class="dropdown">
                <button class="btn btn-link p-0 text-muted" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                        <i class="fas fa-user me-2"></i>Profile
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        
        @if(auth()->user()->shop && auth()->user()->shop->isActive())
            <div class="mt-3 p-2 bg-white rounded-2 border">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">This Month</small>
                    <span class="badge bg-primary">{{ auth()->user()->currentMonthReceipts->count() }} receipts</span>
                </div>
            </div>
        @endif
    </div>
    @endauth
    
    <!-- Navigation Menu -->
    <nav class="sidebar-nav flex-grow-1 p-3">
        @auth
        <ul class="nav nav-pills flex-column">
            <!-- Dashboard -->
            <li class="nav-item mb-1">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   href="{{ route('dashboard') }}">
                    <i class="fas fa-home me-3"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @if(auth()->user()->isShopOwner())
                <!-- Shop Owner Menu -->
                
                @if(!auth()->user()->shop || !auth()->user()->shop->isActive())
                    <!-- Shop Setup (if not approved) -->
                    <li class="nav-item mb-1">
                        <a class="nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}"
                           href="{{ route('shop.create') }}">
                            <i class="fas fa-store me-3"></i>
                            <span>Setup Shop Profile</span>
                            <span class="badge bg-warning ms-auto">Required</span>
                        </a>
                    </li>
                @else
                    <!-- Generate Receipt -->
                    <li class="nav-item mb-1">
                        <a class="nav-link {{ request()->routeIs('receipt.create') ? 'active' : '' }}" 
                           href="{{ route('receipt.create') }}">
                            <i class="fas fa-plus-circle me-3"></i>
                            <span>Generate Receipt</span>
                        </a>
                    </li>
                    
                    <!-- Update as Resale -->
                    <li class="nav-item mb-1">
                        <a class="nav-link {{ request()->routeIs('receipt.resale') ? 'active' : '' }}" 
                           href="{{ route('receipt.resale') }}">
                            <i class="fas fa-sync-alt me-3"></i>
                            <span>Resale phone</span>
                        </a>
                    </li>
                    
                    <!-- My Receipts -->
                    <li class="nav-item mb-1">
                        <a class="nav-link {{ request()->routeIs('receipt.index') ? 'active' : '' }}" 
                           href="{{ route('receipt.index') }}">
                            <i class="fas fa-list me-3"></i>
                            <span>My Receipts</span>
                            @if(auth()->user()->receipts->count() > 0)
                                <span class="badge bg-primary ms-auto">{{ auth()->user()->receipts->count() }}</span>
                            @endif
                        </a>
                    </li>
 
                    <!--@if(auth()->user()->isShopOwner() && auth()->user()->shop && auth()->user()->shop->isActive())-->
                        <!--<li class="nav-item mb-1">-->
                        <!--    <a class="nav-link {{ request()->routeIs('shop.payouts.*') ? 'active' : '' }}" -->
                        <!--       href="{{ route('shop.payouts.index') }}">-->
                        <!--        <i class="fas fa-money-bill-wave me-3"></i>-->
                        <!--        <span>Payout Requests</span>-->
                        <!--    </a>-->
                        <!--</li>-->
                    <!--@endif-->
                    
                    <!-- Shop Management -->
                    <li class="nav-item mb-1">
                        <a class="nav-link {{ request()->routeIs('shop.show', 'shop.edit') ? 'active' : '' }}" 
                           href="{{ route('shop.show', auth()->user()->shop) }}">
                            <i class="fas fa-store me-3"></i>
                            <span>My Shop</span>
                        </a>
                    </li>
                @endif
                
                <!--@if(auth()->user()->isShopOwner() && auth()->user()->shop && auth()->user()->shop->isActive())-->
                    <!-- Shop Payment Overview -->
                    <!--<li class="nav-item mb-1">-->
                    <!--    <a class="nav-link {{ request()->routeIs('shop.payments.*') ? 'active' : '' }}" -->
                    <!--       href="{{ route('shop.payments.index') }}">-->
                    <!--        <i class="fas fa-money-check-alt me-3"></i>-->
                    <!--        <span>Payment Overview</span>-->
                    <!--    </a>-->
                    <!--</li>-->
                <!--@endif-->
                
            @elseif(auth()->user()->isAdmin())
                <!-- Admin Menu -->
                
                <!-- Shop Management -->
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('admin.shops.*') ? 'active' : '' }}" 
                       href="{{ route('admin.shops.index') }}">
                        <i class="fas fa-store me-3"></i>
                        <span>Manage Shops</span>
                        @php $pendingShops = \App\Models\Shop::pendingApproval()->count(); @endphp
                        @if($pendingShops > 0)
                            <span class="badge bg-warning ms-auto">{{ $pendingShops }}</span>
                        @endif
                    </a>
                </li>
                
                <!-- Receipt Analytics -->
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('admin.receipts.*') ? 'active' : '' }}" 
                       href="{{ route('admin.receipts.index') }}">
                        <i class="fas fa-chart-bar me-3"></i>
                        <span>Receipt Analytics</span>
                    </a>
                </li>
                
                <!-- Users Management -->
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                       href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users me-3"></i>
                        <span>Manage Users</span>
                    </a>
                </li>
                
                <!-- System Reports -->
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                       href="{{ route('admin.reports.index') }}">
                        <i class="fas fa-file-alt me-3"></i>
                        <span>System Reports</span>
                    </a>
                </li>

                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('admin.logos.*') ? 'active' : '' }}" 
                       href="{{ route('admin.logos.index') }}">
                        <i class="fas fa-image me-3"></i>
                        <span>Logo Management</span>
                    </a>
                </li>
                
                <!-- Pre-Approval Management -->
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('admin.pre-approvals.*') ? 'active' : '' }}" 
                       href="{{ route('admin.pre-approvals.index') }}">
                        <i class="fas fa-user-check me-3"></i>
                        <span>Pre-Approval Management</span>
                        @php $pendingApprovals = \App\Models\PreApprovedUser::where('status', 'pending')->count(); @endphp
                        @if($pendingApprovals > 0)
                            <span class="badge bg-info ms-auto">{{ $pendingApprovals }}</span>
                        @endif
                    </a>
                </li>
                
                <!-- Union Management -->
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('admin.unions.*') ? 'active' : '' }}" 
                       href="{{ route('admin.unions.index') }}">
                        <i class="fas fa-users me-3"></i>
                        <span>Union Management</span>
                    </a>
                </li>

                <!-- Role Management -->
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" 
                       href="{{ route('admin.roles.index') }}">
                        <i class="fas fa-user-tag me-3"></i>
                        <span>Role Management</span>
                    </a>
                </li>

                <!-- Payout Management -->
                <!--<li class="nav-item mb-1">-->
                <!--    <a class="nav-link {{ request()->routeIs('admin.payouts.*') ? 'active' : '' }}" -->
                <!--       href="{{ route('admin.payouts.index') }}">-->
                <!--        <i class="fas fa-money-check-alt me-3"></i>-->
                <!--        <span>Payout Requests</span>-->
                <!--    </a>-->
                <!--</li>-->
            @endif
            
            <!-- Payment Management - ADMIN ONLY -->
            @if(auth()->user()->isAdmin())
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" 
                       href="{{ route('admin.payments.index') }}">
                        <i class="fas fa-credit-card me-3"></i>
                        <span>Payment Management</span>
                        @php $pendingPayments = \App\Models\Receipt::where('payment_gateway_status', 'pending')->count(); @endphp
                        @if($pendingPayments > 0)
                            <span class="badge bg-warning ms-auto">{{ $pendingPayments }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item mb-1">
    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
       href="{{ route('admin.settings.index') }}">
        <i class="fas fa-cogs me-3"></i>
        <span>System Settings</span>
    </a>
</li>
            @endif
            
            <!-- Performance -->
            @if(auth()->user()->isShopOwner() && auth()->user()->shop && auth()->user()->shop->isActive())
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('performance.*') ? 'active' : '' }}" 
                       href="{{ route('performance.index') }}">
                        <i class="fas fa-chart-line me-3"></i>
                        <span>Performance</span>
                    </a>
                </li>
            @endif
        </ul>
        
        <!-- Divider -->
        <hr class="my-4">
        
        <!-- Secondary Menu -->
        <ul class="nav nav-pills flex-column">
            <!-- Help & Support -->
            <li class="nav-item mb-1">
                <a class="nav-link" href="{{ route('help.index') }}">
                    <i class="fas fa-question-circle me-3"></i>
                    <span>Help & Support</span>
                </a>
            </li>
            
            <!-- M-RIGHT PORTAL -->
            <li class="nav-item mb-1">
                <a class="nav-link" href="{{ route('mright.index') }}" target="_blank">
                    <i class="fas fa-shield me-3"></i>
                    <span>M-RIGHT PORTAL</span>
                    <i class="fas fa-external-link-alt ms-auto small"></i>
                </a>
            </li>
        </ul>
        @else
        <!-- Guest Navigation (for login/register pages) -->
        <ul class="nav nav-pills flex-column">
            <li class="nav-item mb-1">
                <a class="nav-link" href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt me-3"></i>
                    <span>Sign In</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link" href="{{ route('register') }}">
                    <i class="fas fa-user-plus me-3"></i>
                    <span>Register</span>
                </a>
            </li>
        </ul>
        @endauth
    </nav>
    
    <!-- Sidebar Footer -->
    <div class="sidebar-footer p-3 border-top bg-light">
        <div class="text-center">
            <small class="text-muted">
                Version 1.0.0<br>
                &copy; {{ date('Y') }} M-right Portal
            </small>
        </div>
    </div>
</div>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay d-lg-none" id="sidebar-overlay" onclick="toggleSidebar()"></div>

<style>
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: var(--sidebar-width);
    z-index: 1030;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    overflow-y: auto;
}

@media (min-width: 992px) {
    .sidebar {
        transform: translateX(0);
    }
    
    .main-content {
        margin-left: var(--sidebar-width);
        padding-top: var(--topbar-height);
    }
}

.sidebar.show {
    transform: translateX(0);
}

.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1025;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* Navigation Styles */
.sidebar-nav .nav-link {
    color: #6c757d;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.25rem;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    text-decoration: none;
}

.sidebar-nav .nav-link:hover {
    background-color: #f8f9fa;
    color: var(--primary-color);
    transform: translateX(5px);
}

.sidebar-nav .nav-link.active {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
    color: white;
    font-weight: 500;
}

.sidebar-nav .nav-link.active:hover {
    transform: none;
}

.sidebar-nav .nav-link i {
    width: 20px;
    text-align: center;
}

/* User Profile */
.user-profile .avatar img {
    object-fit: cover;
    border: 2px solid #e9ecef;
}

/* Logo */
.logo {
    position: relative;
    overflow: hidden;
}

.logo::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    transition: transform 0.6s ease;
}

.logo:hover::before {
    transform: rotate(45deg) translateX(100%);
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    .main-content {
        margin-left: 0;
        padding-top: var(--topbar-height);
    }
}
</style>

<script>
// Enhanced mobile navigation functionality
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const body = document.body;
    
    if (!sidebar || !overlay) {
        console.error('Sidebar elements not found');
        return;
    }
    
    // Toggle sidebar visibility
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
    
    // Prevent body scroll when sidebar is open on mobile
    if (window.innerWidth < 992) {
        body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
    }
    
    // Add/remove active state to hamburger button
    const hamburgerBtn = document.querySelector('[onclick="toggleSidebar()"]');
    if (hamburgerBtn) {
        hamburgerBtn.classList.toggle('active');
    }
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const hamburgerButtons = document.querySelectorAll('[onclick="toggleSidebar()"]');
    
    if (window.innerWidth < 992 && 
        sidebar && sidebar.classList.contains('show') && 
        !sidebar.contains(event.target)) {
        
        // Check if click was on any hamburger button
        let clickedHamburger = false;
        hamburgerButtons.forEach(btn => {
            if (btn.contains(event.target)) {
                clickedHamburger = true;
            }
        });
        
        // Close sidebar if click was not on hamburger button
        if (!clickedHamburger) {
            toggleSidebar();
        }
    }
});

// Handle window resize - ensure proper state on desktop/mobile switch
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const body = document.body;
    
    if (window.innerWidth >= 992) {
        // Desktop mode - ensure sidebar is visible and overlay is hidden
        if (sidebar) sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
        body.style.overflow = '';
        
        // Remove active state from hamburger buttons
        const hamburgerButtons = document.querySelectorAll('[onclick="toggleSidebar()"]');
        hamburgerButtons.forEach(btn => btn.classList.remove('active'));
    }
});

// Initialize navigation on page load
document.addEventListener('DOMContentLoaded', function() {
    // Ensure proper initial state
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const body = document.body;
    
    if (window.innerWidth >= 992) {
        if (sidebar) sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
        body.style.overflow = '';
    }
    
    // Add enhanced styling for hamburger button
    const hamburgerButtons = document.querySelectorAll('[onclick="toggleSidebar()"]');
    hamburgerButtons.forEach(btn => {
        btn.style.transition = 'all 0.3s ease';
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});

// Add keyboard accessibility
document.addEventListener('keydown', function(event) {
    const sidebar = document.getElementById('sidebar');
    
    // Close sidebar with Escape key
    if (event.key === 'Escape' && sidebar && sidebar.classList.contains('show')) {
        toggleSidebar();
    }
});

// Handle touch events for better mobile experience
let touchStartX = 0;
let touchEndX = 0;

document.addEventListener('touchstart', function(event) {
    touchStartX = event.changedTouches[0].screenX;
}, false);

document.addEventListener('touchend', function(event) {
    touchEndX = event.changedTouches[0].screenX;
    handleSwipe();
}, false);

function handleSwipe() {
    const sidebar = document.getElementById('sidebar');
    const swipeThreshold = 50;
    
    if (window.innerWidth < 992 && sidebar) {
        // Swipe right to open sidebar (from left edge)
        if (touchEndX - touchStartX > swipeThreshold && touchStartX < 50) {
            if (!sidebar.classList.contains('show')) {
                toggleSidebar();
            }
        }
        
        // Swipe left to close sidebar
        if (touchStartX - touchEndX > swipeThreshold) {
            if (sidebar.classList.contains('show')) {
                toggleSidebar();
            }
        }
    }
}
</script>
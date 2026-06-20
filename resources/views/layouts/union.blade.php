<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'M-Right Union') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Scripts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease-in-out;
        }
        
        .main-content {
            margin-left: 250px;
            flex: 1;
            background: #f8f9fa;
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
        }
        
        .content-wrapper {
            padding: 20px;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .nav-section-header {
            padding: 15px 20px 5px 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.7;
            font-weight: 600;
        }
        
        .nav-item {
            margin: 2px 10px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .nav-text {
            flex: 1;
        }
        
        .badge {
            font-size: 0.65rem;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 1001;
        }

        .mobile-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            .content-wrapper {
                padding: 15px;
            }
        }

        @media (max-width: 576px) {
            .content-wrapper {
                padding: 10px;
            }
            
            .sidebar {
                width: 280px;
            }
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('union.dashboard') }}" class="text-decoration-none text-white">
                    @php
                        $mrightLogo = \App\Models\SystemLogo::getMrightLogo();
                    @endphp
                    @if($mrightLogo)
                        <img src="{{ $mrightLogo->logo_url }}" 
                             alt="M-right" 
                             style="width: 30px; height: 30px; object-fit: contain; margin-right: 8px;">
                    @else
                        <i class="fas fa-user-tie me-2"></i>
                    @endif
                    <span style="font-size: 1.2rem; font-weight: bold;">M-right Partner's</span>
                </a>
                <div style="margin-top: 10px; font-size: 0.85rem;">
                    <div>{{ auth()->user()->name }}</div>
                    <small style="opacity: 0.8;">Union Executive</small>
                </div>
            </div>

            <!-- Navigation -->
            <!--<div class="sidebar-nav">-->
<div class="sidebar-nav">
    @php
        $user = auth()->user();

        // Permissions
        $hasShopAccess = $user->hasAnyPermission(['view_shop_owners', 'manage_shop_owners', 'view_shop_statistics']);
        $hasPreApprovalAccess = $user->hasAnyPermission(['view_pre_approvals', 'manage_pre_approvals', 'upload_pre_approvals']);
        $hasReportAccess = $user->hasAnyPermission(['view_reports', 'export_data', 'view_analytics']);
    @endphp

    <!-- Dashboard -->
    <div class="nav-item">
        <a href="{{ route('union.dashboard') }}" 
           class="nav-link {{ request()->routeIs('union.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span class="nav-text">Dashboard</span>
        </a>
    </div>

    <!-- Reports -->
    @if($hasReportAccess)
        <div class="nav-item">
            <a href="{{ route('union.reports.index') }}" 
               class="nav-link {{ request()->routeIs('union.reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span class="nav-text">Reports</span>
            </a>
        </div>
    @endif

    <!-- Shop Owners -->
    @if($hasShopAccess)
        <div class="nav-item">
            <a href="{{ route('union.shop-owners.index') }}" 
               class="nav-link {{ request()->routeIs('union.shop-owners.*') ? 'active' : '' }}">
                <i class="fas fa-store"></i>
                <span class="nav-text">Shop Owners</span>
                @php
                    $pendingShops = \App\Models\Shop::where('approved', false)->count();
                @endphp
                @if($pendingShops > 0)
                    <span class="badge bg-warning ms-auto">{{ $pendingShops }}</span>
                @endif
            </a>
        </div>
    @endif

    <!-- Pre-Approvals -->
    @if($hasPreApprovalAccess)
        <div class="nav-item">
            <a href="{{ route('union.pre-approvals.index') }}" 
               class="nav-link {{ request()->routeIs('union.pre-approvals.*') ? 'active' : '' }}">
                <i class="fas fa-user-check"></i>
                <span class="nav-text">Pre-Approvals</span>
                @php
                    $pendingApprovals = \App\Models\PreApprovedUser::where('status', 'pending')->count();
                @endphp
                @if($pendingApprovals > 0)
                    <span class="badge bg-info ms-auto">{{ $pendingApprovals }}</span>
                @endif
            </a>
        </div>
    @endif

    <!-- Export Data -->
    @if($hasReportAccess)
        <div class="nav-item">
            <a href="{{ route('union.dashboard.export') }}" class="nav-link">
                <i class="fas fa-file-export"></i>
                <span class="nav-text">Export Data</span>
            </a>
        </div>
    @endif

    <!-- Account Section -->
    <div class="nav-section-header">Account</div>

    <!-- Profile -->
    <div class="nav-item">
        <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
            <i class="fas fa-user-cog"></i>
            <span class="nav-text">Profile</span>
        </a>
    </div>

    <!-- Logout -->
    <div class="nav-item">
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-text">Logout</span>
            </button>
        </form>
    </div>
</div>

        </nav>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Mobile Header -->
            <div class="mobile-header">
                <button class="mobile-toggle" id="mobileToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="d-flex align-items-center">
                    @php
                        $mrightLogo = \App\Models\SystemLogo::getMrightLogo();
                    @endphp
                    @if($mrightLogo)
                        <img src="{{ $mrightLogo->logo_url }}" 
                             alt="M-right" 
                             style="width: 25px; height: 25px; object-fit: contain; margin-right: 8px;">
                    @endif
                    <span style="font-weight: bold;">M-right Partner's</span>
                </div>
                <div style="width: 40px;"></div>
            </div>

            <div class="content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Mobile Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileToggle = document.getElementById('mobileToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            function toggleSidebar() {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
                document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
            }
            
            function closeSidebar() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
            
            // Toggle sidebar on mobile menu button click
            mobileToggle.addEventListener('click', toggleSidebar);
            
            // Close sidebar when clicking overlay
            overlay.addEventListener('click', closeSidebar);
            
            // Close sidebar when clicking a nav link on mobile
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeSidebar();
                }
            });
        });
    </script>
</body>
</html>
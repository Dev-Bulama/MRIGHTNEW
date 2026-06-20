@extends('layouts.app')

@section('title', 'Search & Verify')
@section('page-title', 'Search & Verify')
@section('page-description', 'Search for phones, receipts, and verify anti-theft status')

@push('styles')
<style>
.search-hero {
    background: linear-gradient(135deg, #0d8abc 0%, #4dabf7 100%);
    border-radius: 20px;
    padding: 3rem 2rem;
    color: white;
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
    overflow: hidden;
}

.search-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

.search-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -5%;
    width: 150px;
    height: 150px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    animation: float 8s ease-in-out infinite reverse;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.search-form-container {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 10;
    margin-top: -50px;
}

.search-input-group {
    position: relative;
    margin-bottom: 1.5rem;
}

.search-input {
    border: 2px solid #e9ecef;
    border-radius: 15px;
    padding: 1rem 1.5rem;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: #0d8abc;
    box-shadow: 0 0 0 0.2rem rgba(13, 138, 188, 0.25);
}

.search-btn {
    border-radius: 15px;
    padding: 1rem 2rem;
    font-weight: 600;
    background: linear-gradient(135deg, #0d8abc, #4dabf7);
    border: none;
    color: white;
    transition: all 0.3s ease;
}

.search-btn:hover {
    background: linear-gradient(135deg, #0b7aa3, #3d9fe8);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(13, 138, 188, 0.3);
    color: white;
}

.search-type-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.search-type-tab {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 25px;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    flex: 1;
    min-width: 100px;
    text-align: center;
}

.search-type-tab.active {
    background: linear-gradient(135deg, #0d8abc, #4dabf7);
    border-color: #0d8abc;
    color: white;
}

.search-type-tab:hover:not(.active) {
    background: #e9ecef;
    border-color: #0d8abc;
}

.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
    display: none;
}

.suggestion-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.2s ease;
}

.suggestion-item:hover {
    background: #f8f9fa;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.search-stats {
    display: flex;
    justify-content: space-around;
    margin: 2rem 0;
    gap: 1rem;
    flex-wrap: wrap;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    flex: 1;
    min-width: 150px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #0d8abc;
    display: block;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.action-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border: 2px solid transparent;
    cursor: pointer;
}

.action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: #0d8abc;
}

.action-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #0d8abc, #4dabf7);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: white;
}

.recent-searches {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.recent-search-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f8f9fa;
    cursor: pointer;
    transition: all 0.2s ease;
}

.recent-search-item:hover {
    background: #f8f9fa;
    border-radius: 8px;
    padding-left: 1rem;
    padding-right: 1rem;
}

.recent-search-item:last-child {
    border-bottom: none;
}

.search-results {
    margin-top: 2rem;
    display: none;
}

.result-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border-left: 4px solid #0d8abc;
}

.result-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transform: translateX(5px);
}

.result-type-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.badge-receipt {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.badge-shop {
    background: linear-gradient(135deg, #0d8abc, #4dabf7);
    color: white;
}

.loading-spinner {
    display: none;
    text-align: center;
    padding: 2rem;
}

.verification-panel {
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 15px;
    padding: 2rem;
    color: white;
    text-align: center;
    margin: 2rem 0;
}

.verification-input {
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    margin-bottom: 1rem;
}

.verification-input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.verification-input:focus {
    border-color: white;
    background: rgba(255, 255, 255, 0.2);
    box-shadow: none;
    color: white;
}

/* Dashboard Search Styles */
.search-container {
    position: relative;
}

.search-container .input-group {
    border-radius: 15px;
    overflow: hidden;
}

.search-container .form-control {
    border-radius: 15px 0 0 15px;
    border: 2px solid #e9ecef;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}

.search-container .form-control:focus {
    border-color: #0d8abc;
    box-shadow: 0 0 0 0.2rem rgba(13, 138, 188, 0.25);
}

.search-container .form-select {
    border-radius: 0;
    border: 2px solid #e9ecef;
    border-left: none;
    border-right: none;
    max-width: 120px;
    padding: 0.5rem;
}

.search-container .btn {
    border-radius: 0 15px 15px 0;
    border: 2px solid #e9ecef;
    border-left: none;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
}

.search-container .btn:hover {
    background: #f8f9fa;
    color: #0d8abc;
}

.search-results {
    position: absolute;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    z-index: 1050;
    width: 350px;
    max-height: 400px;
    overflow-y: auto;
    display: none;
    margin-top: 0.5rem;
}

.search-results-content {
    padding: 1rem;
}

.search-result-item {
    padding: 0.75rem;
    border-bottom: 1px solid #f8f9fa;
    cursor: pointer;
    transition: all 0.2s ease;
}

.search-result-item:hover {
    background: #f8f9fa;
    border-radius: 8px;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.search-result-meta {
    font-size: 0.8rem;
    color: #6c757d;
}

.search-result-type {
    display: inline-block;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 600;
    margin-right: 0.5rem;
}

.type-receipt {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.type-customer {
    background: rgba(13, 138, 188, 0.1);
    color: #0d8abc;
}

.type-shop {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}

@media (max-width: 768px) {
    .search-hero {
        padding: 2rem 1rem;
        margin-bottom: 2rem;
    }
    
    .search-form-container {
        margin-top: -30px;
        padding: 1.5rem;
    }
    
    .search-type-tabs {
        flex-direction: column;
    }
    
    .search-type-tab {
        flex: none;
        min-width: auto;
    }
    
    .search-stats {
        flex-direction: column;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }

    .search-container {
        width: 100%;
        margin-right: 0;
        margin-bottom: 1rem;
    }

    .search-container .input-group {
        width: 100%;
    }

    .search-results {
        width: 100%;
    }
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.tips-section {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 2rem;
    margin: 2rem 0;
}

.tip-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.tip-icon {
    background: #0d8abc;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
    font-size: 0.8rem;
}
</style>
@endpush

@section('content')
<!-- Search Hero Section -->
<div class="search-hero">
    <h1 class="display-5 fw-bold mb-3">Search & Verify Phones</h1>
    <p class="lead mb-0">Find receipt information, verify authenticity, and check anti-theft status</p>
</div>

<!-- Main Search Form -->
<div class="search-form-container">
    <form id="mainSearchForm">
        @csrf
        
        <!-- Search Type Tabs -->
        <div class="search-type-tabs">
            <div class="search-type-tab active" data-type="all">
                <i class="fas fa-search me-1"></i>All
            </div>
            <div class="search-type-tab" data-type="receipt">
                <i class="fas fa-receipt me-1"></i>Receipts
            </div>
            <div class="search-type-tab" data-type="phone">
                <i class="fas fa-mobile-alt me-1"></i>Phones
            </div>
            <div class="search-type-tab" data-type="shop">
                <i class="fas fa-store me-1"></i>Shops
            </div>
            <div class="search-type-tab" data-type="customer">
                <i class="fas fa-user me-1"></i>Customers
            </div>
        </div>
        
        <!-- Search Input -->
        <div class="search-input-group">
            <div class="input-group">
                <input type="text" 
                       class="form-control search-input" 
                       id="searchQuery" 
                       name="query" 
                       placeholder="Enter receipt number, phone serial, customer name, or any search term..."
                       autocomplete="off">
                <button class="btn search-btn" type="submit" id="searchBtn">
                    <i class="fas fa-search me-2"></i>Search
                    <span class="spinner-border spinner-border-sm ms-2 d-none" id="searchSpinner"></span>
                </button>
            </div>
            
            <!-- Search Suggestions -->
            <div class="search-suggestions" id="searchSuggestions">
                <!-- Suggestions will be populated by JavaScript -->
            </div>
        </div>
        
        <input type="hidden" id="searchType" name="search_type" value="all">
    </form>
</div>

<!-- Search Statistics -->
<div class="search-stats">
    <div class="stat-card">
        <span class="stat-number">{{ number_format($stats['total_receipts']) }}</span>
        <div class="stat-label">Total Receipts</div>
    </div>
    <div class="stat-card">
        <span class="stat-number">{{ number_format($stats['verified_receipts']) }}</span>
        <div class="stat-label">Verified Receipts</div>
    </div>
    <div class="stat-card">
        <span class="stat-number">{{ number_format($stats['total_shops']) }}</span>
        <div class="stat-label">Active Shops</div>
    </div>
    <div class="stat-card">
        <span class="stat-number">{{ $stats['search_success_rate'] }}</span>
        <div class="stat-label">Success Rate</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="action-card" onclick="showQuickVerification()">
                <div class="action-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h5 class="fw-bold mb-2">Quick Verification</h5>
                <p class="text-muted mb-3">Instantly verify a receipt with receipt number</p>
                <small class="text-primary">Most popular</small>
            </div>
            
            <div class="action-card" onclick="window.location.href='{{ route('search.advanced') }}'">
                <div class="action-icon">
                    <i class="fas fa-search-plus"></i>
                </div>
                <h5 class="fw-bold mb-2">Advanced Search</h5>
                <p class="text-muted mb-3">Search with multiple filters and criteria</p>
                <small class="text-info">Power users</small>
            </div>
            
            <div class="action-card" onclick="showAntiTheftCheck()">
                <div class="action-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h5 class="fw-bold mb-2">Anti-Theft Check</h5>
                <p class="text-muted mb-3">Check if a phone is reported stolen</p>
                <small class="text-warning">Security</small>
            </div>
            
            <div class="action-card" onclick="showBulkLookup()">
                <div class="action-icon">
                    <i class="fas fa-list"></i>
                </div>
                <h5 class="fw-bold mb-2">Bulk Lookup</h5>
                <p class="text-muted mb-3">Search multiple items at once</p>
                <small class="text-secondary">Coming soon</small>
            </div>
        </div>
        
        <!-- Quick Verification Panel (Hidden by default) -->
        <div class="verification-panel" id="quickVerificationPanel" style="display: none;">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-shield-alt me-2"></i>Quick Receipt Verification
            </h5>
            <p class="mb-4">Enter a receipt number to instantly verify its authenticity</p>
            
            <form id="quickVerificationForm">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <input type="text" 
                               class="form-control verification-input" 
                               id="verificationReceiptNumber" 
                               placeholder="Enter receipt number (e.g., MR-TEC20240731001)"
                               required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-light btn-block w-100">
                            <i class="fas fa-check me-2"></i>Verify
                        </button>
                    </div>
                </div>
            </form>
            
            <button class="btn btn-outline-light btn-sm" onclick="hideQuickVerification()">
                <i class="fas fa-times me-1"></i>Close
            </button>
        </div>
        
        <!-- Search Results -->
        <div class="search-results" id="searchResults">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold">Search Results</h5>
                <small class="text-muted" id="resultsCount">0 results found</small>
            </div>
            
            <div id="resultsContainer">
                <!-- Results will be populated by JavaScript -->
            </div>
            
            <!-- Loading Spinner -->
            <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Searching...</span>
                </div>
                <p class="mt-2 text-muted">Searching our database...</p>
            </div>
            
            <!-- Empty State -->
            <div class="empty-state" id="emptyState" style="display: none;">
                <i class="fas fa-search"></i>
                <h5>No results found</h5>
                <p>Try adjusting your search terms or search type</p>
            </div>
        </div>
        
        <!-- Tips Section -->
        <div class="tips-section">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-lightbulb me-2 text-warning"></i>Search Tips
            </h6>
            
            <div class="tip-item">
                <div class="tip-icon">1</div>
                <div>
                    <strong>Receipt Numbers:</strong> Format like MR-XXX20240731001. Use the exact format for best results.
                </div>
            </div>
            
            <div class="tip-item">
                <div class="tip-icon">2</div>
                <div>
                    <strong>Phone Serials:</strong> Enter the complete IMEI or serial number found on the device or receipt.
                </div>
            </div>
            
            <div class="tip-item">
                <div class="tip-icon">3</div>
                <div>
                    <strong>Partial Matches:</strong> You can search with partial names or numbers if you don't have complete information.
                </div>
            </div>
            
            <div class="tip-item mb-0">
                <div class="tip-icon">4</div>
                <div>
                    <strong>Search Types:</strong> Use specific search types (Receipts, Phones, etc.) to narrow down results.
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        @auth
        <!-- Dashboard Search (Authenticated Users Only) -->
        <div class="search-container mb-4">
            <div class="input-group" style="width: 350px;">
                <input type="text" 
                       class="form-control" 
                       id="dashboardSearch" 
                       placeholder="Search receipts, phone serial, customer..."
                       autocomplete="off">
                <select class="form-select" id="dashboardSearchType" style="max-width: 120px;">
                    <option value="receipt">Receipts</option>
                    <option value="customer">Customers</option>
                    @if(auth()->user()->isAdmin())
                        <option value="shop">Shops</option>
                    @endif
                </select>
                <button class="btn btn-outline-secondary" type="button" id="dashboardSearchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            
            <!-- Search Results Dropdown -->
            <div class="search-results position-absolute bg-white border rounded shadow-lg mt-1" 
                 id="dashboardSearchResults" 
                 style="display: none; z-index: 1050; width: 350px; max-height: 400px; overflow-y: auto;">
                <div class="search-results-content">
                    <!-- Results will be populated here -->
                </div>
            </div>
        </div>
        
        <!-- Recent Searches -->
        @if(!empty($recentSearches))
        <div class="recent-searches">
            <h6 class="fw-bold mb-3">
                <i class="fas fa-history me-2"></i>Recent Searches
            </h6>
            
            @foreach($recentSearches as $search)
            <div class="recent-search-item" onclick="repeatSearch('{{ $search['query'] }}', '{{ $search['type'] }}')">
                <div>
                    <strong>{{ $search['query'] }}</strong>
                    <br><small class="text-muted">{{ ucfirst($search['type']) }} search</small>
                </div>
                <div>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($search['timestamp'])->diffForHumans() }}</small>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        @endauth
        
        <!-- Help & Support -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-question-circle me-2"></i>Need Help?
                </h6>
            </div>
            <div class="card-body">
                <p class="card-text">
                    Can't find what you're looking for? Our help center has guides and FAQs to assist you.
                </p>
                <div class="d-grid gap-2">
                    <a href="{{ route('help.guides') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-book me-1"></i>Search Guide
                    </a>
                    <a href="{{ route('help.contact') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-envelope me-1"></i>Contact Support
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Search Statistics Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Search Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary mb-0">{{ number_format($stats['total_receipts']) }}</h4>
                        <small class="text-muted">Receipts</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-0">{{ number_format($stats['total_shops']) }}</h4>
                        <small class="text-muted">Shops</small>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        {{ $stats['search_success_rate'] }} search success rate
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    initializeSearch();
    
    // Initialize dashboard search if authenticated
    @auth
    initializeDashboardSearch();
    @endauth
    
    function initializeSearch() {
        // Setup event listeners
        setupEventListeners();
        
        // Initialize search suggestions
        initializeSearchSuggestions();
        
        // Setup search type tabs
        setupSearchTypeTabs();
    }
    
    function initializeDashboardSearch() {
        const searchInput = document.getElementById('dashboardSearch');
        const searchButton = document.getElementById('dashboardSearchButton');
        const searchType = document.getElementById('dashboardSearchType');
        const resultsContainer = document.getElementById('dashboardSearchResults');
        
        // Handle search input
        searchInput.addEventListener('input', debounce(function() {
            const query = searchInput.value.trim();
            if (query.length >= 2) {
                fetchDashboardSearchResults(query, searchType.value);
            } else {
                resultsContainer.style.display = 'none';
            }
        }, 300));
        
        // Handle search button click
        searchButton.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query) {
                fetchDashboardSearchResults(query, searchType.value);
            }
        });
        
        // Handle search type change
        searchType.addEventListener('change', function() {
            const query = searchInput.value.trim();
            if (query) {
                fetchDashboardSearchResults(query, searchType.value);
            }
        });
        
        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                resultsContainer.style.display = 'none';
            }
        });
    }
    
    function fetchDashboardSearchResults(query, type) {
        const resultsContainer = document.getElementById('dashboardSearchResults');
        const contentContainer = resultsContainer.querySelector('.search-results-content');
        
        // Show loading state
        contentContainer.innerHTML = '<div class="text-center p-3"><div class="spinner-border spinner-border-sm text-primary"></div><p class="mt-2">Searching...</p></div>';
        resultsContainer.style.display = 'block';
        
        fetch(`{{ route('api.search.dashboard') }}?query=${encodeURIComponent(query)}&type=${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.results && data.results.length > 0) {
                    let html = '';
                    
                    data.results.forEach(result => {
                        html += `
                            <div class="search-result-item" onclick="window.location.href='${result.url}'">
                                <div class="search-result-title">${result.title}</div>
                                <div class="search-result-meta">
                                    <span class="search-result-type ${'type-' + result.type}">${result.type}</span>
                                    ${result.meta}
                                </div>
                            </div>
                        `;
                    });
                    
                    contentContainer.innerHTML = html;
                } else {
                    contentContainer.innerHTML = '<div class="text-center p-3"><i class="fas fa-search fa-lg mb-2"></i><p>No results found</p></div>';
                }
            })
            .catch(error => {
                console.error('Dashboard search error:', error);
                contentContainer.innerHTML = '<div class="text-center p-3"><i class="fas fa-exclamation-triangle fa-lg mb-2"></i><p>Search failed</p></div>';
            });
    }
    
    function setupEventListeners() {
        // Main search form
        document.getElementById('mainSearchForm').addEventListener('submit', handleSearch);
        
        // Quick verification form
        document.getElementById('quickVerificationForm').addEventListener('submit', handleQuickVerification);
        
        // Search input for real-time suggestions
        document.getElementById('searchQuery').addEventListener('input', debounce(handleSearchInput, 300));
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-input-group')) {
                hideSuggestions();
            }
        });
        
        // Keyboard navigation for suggestions
        document.getElementById('searchQuery').addEventListener('keydown', handleKeyNavigation);
    }
    
    function setupSearchTypeTabs() {
        const tabs = document.querySelectorAll('.search-type-tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Update search type
                const searchType = this.dataset.type;
                document.getElementById('searchType').value = searchType;
                
                // Update placeholder text
                updatePlaceholder(searchType);
                
                // Clear previous results
                clearResults();
            });
        });
    }
    
    function updatePlaceholder(searchType) {
        const input = document.getElementById('searchQuery');
        const placeholders = {
            'all': 'Enter receipt number, phone serial, customer name, or any search term...',
            'receipt': 'Enter receipt number (e.g., MR-TEC20240731001)...',
            'phone': 'Enter phone model, serial number, or IMEI...',
            'shop': 'Enter shop name or business details...',
            'customer': 'Enter customer name or phone number...'
        };
        
        input.placeholder = placeholders[searchType] || placeholders['all'];
    }
    
    function initializeSearchSuggestions() {
        // Search suggestions will be loaded via AJAX
        window.searchSuggestions = [];
        window.currentSuggestionIndex = -1;
    }
    
    function handleSearchInput(e) {
        const query = e.target.value.trim();
        const searchType = document.getElementById('searchType').value;
        
        if (query.length >= 2) {
            fetchSearchSuggestions(query, searchType);
        } else {
            hideSuggestions();
        }
    }
    
    function fetchSearchSuggestions(query, searchType) {
        fetch(`{{ route('api.search.suggestions') }}?query=${encodeURIComponent(query)}&type=${searchType}`)
            .then(response => response.json())
            .then(data => {
                if (data.suggestions && data.suggestions.length > 0) {
                    showSuggestions(data.suggestions);
                } else {
                    hideSuggestions();
                }
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
                hideSuggestions();
            });
    }
    
    function showSuggestions(suggestions) {
        const container = document.getElementById('searchSuggestions');
        container.innerHTML = '';
        
        suggestions.forEach((suggestion, index) => {
            const item = document.createElement('div');
            item.className = 'suggestion-item';
            item.textContent = suggestion;
            item.addEventListener('click', () => selectSuggestion(suggestion));
            container.appendChild(item);
        });
        
        container.style.display = 'block';
        window.searchSuggestions = suggestions;
        window.currentSuggestionIndex = -1;
    }
    
    function hideSuggestions() {
        document.getElementById('searchSuggestions').style.display = 'none';
        window.currentSuggestionIndex = -1;
    }
    
    function selectSuggestion(suggestion) {
        document.getElementById('searchQuery').value = suggestion;
        hideSuggestions();
        // Trigger search
        document.getElementById('mainSearchForm').dispatchEvent(new Event('submit'));
    }
    
    function handleKeyNavigation(e) {
        const suggestions = window.searchSuggestions || [];
        const suggestionItems = document.querySelectorAll('.suggestion-item');
        
        if (suggestions.length === 0) return;
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            window.currentSuggestionIndex = Math.min(window.currentSuggestionIndex + 1, suggestions.length - 1);
            highlightSuggestion();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            window.currentSuggestionIndex = Math.max(window.currentSuggestionIndex - 1, -1);
            highlightSuggestion();
        } else if (e.key === 'Enter' && window.currentSuggestionIndex >= 0) {
            e.preventDefault();
            selectSuggestion(suggestions[window.currentSuggestionIndex]);
        } else if (e.key === 'Escape') {
            hideSuggestions();
        }
    }
    
    function highlightSuggestion() {
        const suggestionItems = document.querySelectorAll('.suggestion-item');
        
        suggestionItems.forEach((item, index) => {
            if (index === window.currentSuggestionIndex) {
                item.style.background = '#f8f9fa';
            } else {
                item.style.background = '';
            }
        });
        
        // Update input value with highlighted suggestion
        if (window.currentSuggestionIndex >= 0) {
            const suggestion = window.searchSuggestions[window.currentSuggestionIndex];
            // Don't update input value during navigation to preserve user's typing
        }
    }
    
    function handleSearch(e) {
        e.preventDefault();
        
        const query = document.getElementById('searchQuery').value.trim();
        const searchType = document.getElementById('searchType').value;
        
        if (!query) {
            alert('Please enter a search term.');
            return;
        }
        
        // Show loading state
        showLoading();
        hideSuggestions();
        
        // Prepare form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('query', query);
        formData.append('search_type', searchType);
        formData.append('limit', 20);
        
        // Perform search
        fetch('{{ route("search.query") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            displayResults(data);
        })
        .catch(error => {
            hideLoading();
            console.error('Search error:', error);
            showError('Search failed. Please try again.');
        });
    }
    
    function handleQuickVerification(e) {
        e.preventDefault();
        
        const receiptNumber = document.getElementById('verificationReceiptNumber').value.trim();
        
        if (!receiptNumber) {
            alert('Please enter a receipt number.');
            return;
        }
        
        // Redirect to public verification page
        window.open(`{{ route('public.verify', '') }}/${receiptNumber}`, '_blank');
    }
    
    function showLoading() {
        document.getElementById('searchResults').style.display = 'block';
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('resultsContainer').innerHTML = '';
        document.getElementById('emptyState').style.display = 'none';
        
        // Disable search button
        const searchBtn = document.getElementById('searchBtn');
        const spinner = document.getElementById('searchSpinner');
        searchBtn.disabled = true;
        spinner.classList.remove('d-none');
    }
    
    function hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        
        // Enable search button
        const searchBtn = document.getElementById('searchBtn');
        const spinner = document.getElementById('searchSpinner');
        searchBtn.disabled = false;
        spinner.classList.add('d-none');
    }
    
    function displayResults(data) {
        const container = document.getElementById('resultsContainer');
        const countElement = document.getElementById('resultsCount');
        
        if (data.success && data.results.total > 0) {
            countElement.textContent = `${data.results.total} result(s) found for "${data.query}"`;
            
            let html = '';
            
            // Display receipts
            if (data.results.receipts && data.results.receipts.length > 0) {
                data.results.receipts.forEach(receipt => {
                    html += createReceiptResultCard(receipt);
                });
            }
            
            // Display shops
            if (data.results.shops && data.results.shops.length > 0) {
                data.results.shops.forEach(shop => {
                    html += createShopResultCard(shop);
                });
            }
            
            container.innerHTML = html;
            document.getElementById('emptyState').style.display = 'none';
        } else {
            container.innerHTML = '';
            countElement.textContent = 'No results found';
            document.getElementById('emptyState').style.display = 'block';
        }
    }
    
    function createReceiptResultCard(receipt) {
        const statusBadge = getStatusBadge(receipt.status);
        const paymentBadge = getPaymentBadge(receipt.payment_status);
        
        return `
            <div class="result-card">
                <span class="result-type-badge badge-receipt">Receipt</span>
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="fw-bold mb-2">${receipt.receipt_number}</h6>
                        <p class="mb-1"><strong>Customer:</strong> ${receipt.customer_name}</p>
                        <p class="mb-1"><strong>Phone:</strong> ${receipt.phone_name} (${receipt.phone_color})</p>
                        <p class="mb-1"><strong>Serial:</strong> ${receipt.phone_serial_number}</p>
                        <p class="mb-1"><strong>Shop:</strong> ${receipt.shop ? receipt.shop.shop_name : 'N/A'}</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <h5 class="text-primary mb-2">₦${parseFloat(receipt.amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})}</h5>
                        <div class="mb-2">
                            ${statusBadge}
                            ${paymentBadge}
                        </div>
                        <small class="text-muted d-block">${formatDate(receipt.created_at)}</small>
                        <div class="mt-2">
                            <a href="{{ route('receipt.show', '') }}/${receipt.id}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    function createShopResultCard(shop) {
        return `
            <div class="result-card">
                <span class="result-type-badge badge-shop">Shop</span>
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="fw-bold mb-2">${shop.shop_name}</h6>
                        <p class="mb-1"><strong>Owner:</strong> ${shop.owner_full_name || 'N/A'}</p>
                        <p class="mb-1"><strong>Address:</strong> ${shop.business_address || 'N/A'}</p>
                        <p class="mb-1"><strong>Phone:</strong> ${shop.business_phone_1 || 'N/A'}</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge bg-success mb-2">Approved</span>
                        <br>
                        <small class="text-muted d-block">Registered: ${formatDate(shop.created_at)}</small>
                        <div class="mt-2">
                            <a href="{{ route('shop.show', '') }}/${shop.id}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-store me-1"></i>View Shop
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    function getStatusBadge(status) {
        const badges = {
            'active': '<span class="badge bg-success">Active</span>',
            'cancelled': '<span class="badge bg-danger">Cancelled</span>',
            'void': '<span class="badge bg-secondary">Void</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
    }
    
    function getPaymentBadge(paymentStatus) {
        const badges = {
            'paid': '<span class="badge bg-success">Paid</span>',
            'pending': '<span class="badge bg-warning">Pending</span>',
            'partial': '<span class="badge bg-info">Partial</span>',
            'failed': '<span class="badge bg-danger">Failed</span>'
        };
        return badges[paymentStatus] || '<span class="badge bg-secondary">Unknown</span>';
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-NG', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }
    
    function showError(message) {
        alert(message); // In production, use a better notification system
    }
    
    function clearResults() {
        document.getElementById('searchResults').style.display = 'none';
        document.getElementById('resultsContainer').innerHTML = '';
    }
    
    // Utility function for debouncing
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Global functions for UI interactions
    window.showQuickVerification = function() {
        document.getElementById('quickVerificationPanel').style.display = 'block';
        document.getElementById('verificationReceiptNumber').focus();
    };
    
    window.hideQuickVerification = function() {
        document.getElementById('quickVerificationPanel').style.display = 'none';
        document.getElementById('verificationReceiptNumber').value = '';
    };
    
    window.showAntiTheftCheck = function() {
        // Set search type to phone and focus on search
        document.querySelector('[data-type="phone"]').click();
        document.getElementById('searchQuery').focus();
        document.getElementById('searchQuery').placeholder = 'Enter phone serial number for anti-theft check...';
    };
    
    window.showBulkLookup = function() {
        alert('Bulk lookup feature coming soon! This will allow you to search multiple items at once.');
    };
    
    window.repeatSearch = function(query, type) {
        document.getElementById('searchQuery').value = query;
        document.querySelector(`[data-type="${type}"]`).click();
        document.getElementById('mainSearchForm').dispatchEvent(new Event('submit'));
    };
});
</script>
@endpush
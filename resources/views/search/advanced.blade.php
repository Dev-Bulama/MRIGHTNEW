@extends('layouts.app')

@section('title', 'Advanced Search')
@section('page-title', 'Advanced Search')
@section('page-description', 'Search with multiple filters and criteria for precise results')

@push('styles')
<style>
.advanced-search-hero {
    background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
    border-radius: 20px;
    padding: 3rem 2rem;
    color: white;
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
    overflow: hidden;
}

.advanced-search-hero::before {
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

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.search-form-container {
    background: white;
    border-radius: 15px;
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 10;
    margin-top: -50px;
}

.filter-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
}

.filter-section h6 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #0d8abc;
    display: inline-block;
}

.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #0d8abc;
    box-shadow: 0 0 0 0.2rem rgba(13, 138, 188, 0.25);
}

.input-group .form-control {
    border-radius: 10px 0 0 10px;
}

.input-group .input-group-text {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-left: none;
    border-radius: 0 10px 10px 0;
    color: #6c757d;
}

.search-btn {
    background: linear-gradient(135deg, #6f42c1, #e83e8c);
    border: none;
    color: white;
    border-radius: 12px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.search-btn:hover {
    background: linear-gradient(135deg, #5a359a, #d91a72);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(111, 66, 193, 0.3);
    color: white;
}

.filter-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1rem;
}

.filter-chip {
    background: #e7f3ff;
    color: #0d8abc;
    border: 1px solid #b3d9ff;
    border-radius: 20px;
    padding: 0.25rem 0.75rem;
    font-size: 0.8rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-chip .remove-filter {
    background: none;
    border: none;
    color: #0d8abc;
    padding: 0;
    cursor: pointer;
    font-size: 0.9rem;
}

.quick-filter-buttons {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.quick-filter-btn {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quick-filter-btn:hover, .quick-filter-btn.active {
    background: #0d8abc;
    border-color: #0d8abc;
    color: white;
}

.search-tips {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 2rem;
}

.range-inputs {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 1rem;
    align-items: center;
}

.range-separator {
    text-align: center;
    color: #6c757d;
    font-weight: 500;
}

@media (max-width: 768px) {
    .advanced-search-hero {
        padding: 2rem 1rem;
        margin-bottom: 2rem;
    }
    
    .search-form-container {
        margin-top: -30px;
        padding: 1.5rem;
    }
    
    .filter-section {
        padding: 1rem;
    }
    
    .quick-filter-buttons {
        flex-direction: column;
    }
    
    .range-inputs {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .range-separator {
        order: -1;
    }
}

.form-group {
    margin-bottom: 1.5rem;
}

.checkbox-group {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.form-check {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-check:hover {
    background: #e9ecef;
    border-color: #0d8abc;
}

.form-check-input:checked ~ .form-check-label {
    color: #0d8abc;
    font-weight: 500;
}
</style>
@endpush

@section('content')
<!-- Advanced Search Hero Section -->
<div class="advanced-search-hero">
    <h1 class="display-5 fw-bold mb-3">Advanced Search</h1>
    <p class="lead mb-0">Use multiple filters to find exactly what you're looking for</p>
</div>

<!-- Advanced Search Form -->
<div class="search-form-container">
    <form id="advancedSearchForm" action="{{ route('search.advanced.process') }}" method="POST">
        @csrf
        
        <!-- Quick Filter Buttons -->
        <div class="quick-filter-buttons">
            <button type="button" class="quick-filter-btn" onclick="applyQuickFilter('today')">
                <i class="fas fa-calendar-day me-1"></i>Today
            </button>
            <button type="button" class="quick-filter-btn" onclick="applyQuickFilter('week')">
                <i class="fas fa-calendar-week me-1"></i>This Week
            </button>
            <button type="button" class="quick-filter-btn" onclick="applyQuickFilter('month')">
                <i class="fas fa-calendar me-1"></i>This Month
            </button>
            <button type="button" class="quick-filter-btn" onclick="applyQuickFilter('paid')">
                <i class="fas fa-check-circle me-1"></i>Paid Only
            </button>
            <button type="button" class="quick-filter-btn" onclick="applyQuickFilter('antitheft')">
                <i class="fas fa-shield-alt me-1"></i>Anti-Theft Enabled
            </button>
            <button type="button" class="quick-filter-btn" onclick="clearAllFilters()">
                <i class="fas fa-times me-1"></i>Clear All
            </button>
        </div>
        
        <!-- Active Filters Display -->
        <div class="filter-chips" id="activeFilters">
            <!-- Active filters will be displayed here -->
        </div>
        
        <!-- Receipt Information -->
        <div class="filter-section">
            <h6><i class="fas fa-receipt me-2"></i>Receipt Information</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="receipt_number" class="form-label">Receipt Number</label>
                    <input type="text" 
                           class="form-control" 
                           id="receipt_number" 
                           name="receipt_number"
                           placeholder="e.g., MR-TEC20250731001"
                           value="{{ old('receipt_number') }}">
                </div>
                <div class="col-md-6">
                    <label for="shop_id" class="form-label">Shop</label>
                    <select class="form-select" id="shop_id" name="shop_id">
                        <option value="">All Shops</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                {{ $shop->shop_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Customer Information -->
        <div class="filter-section">
            <h6><i class="fas fa-user me-2"></i>Customer Information</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="customer_name" class="form-label">Customer Name</label>
                    <input type="text" 
                           class="form-control" 
                           id="customer_name" 
                           name="customer_name"
                           placeholder="Enter customer name"
                           value="{{ old('customer_name') }}">
                </div>
                <div class="col-md-6">
                    <label for="customer_phone" class="form-label">Customer Phone</label>
                    <input type="text" 
                           class="form-control" 
                           id="customer_phone" 
                           name="customer_phone"
                           placeholder="Enter phone number"
                           value="{{ old('customer_phone') }}">
                </div>
                <div class="col-md-12">
                    <label for="customer_email" class="form-label">Customer Email</label>
                    <input type="email" 
                           class="form-control" 
                           id="customer_email" 
                           name="customer_email"
                           placeholder="Enter email address"
                           value="{{ old('customer_email') }}">
                </div>
            </div>
        </div>
        
        <!-- Phone Information -->
        <div class="filter-section">
            <h6><i class="fas fa-mobile-alt me-2"></i>Phone Information</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="phone_name" class="form-label">Phone Model</label>
                    <input type="text" 
                           class="form-control" 
                           id="phone_name" 
                           name="phone_name"
                           placeholder="e.g., iPhone 15 Pro Max"
                           value="{{ old('phone_name') }}">
                </div>
                <div class="col-md-6">
                    <label for="phone_serial_number" class="form-label">Serial Number</label>
                    <input type="text" 
                           class="form-control" 
                           id="phone_serial_number" 
                           name="phone_serial_number"
                           placeholder="Enter IMEI or serial number"
                           value="{{ old('phone_serial_number') }}">
                </div>
            </div>
        </div>
        
        <!-- Amount Range -->
        <div class="filter-section">
            <h6><i class="fas fa-naira-sign me-2"></i>Amount Range</h6>
            <div class="range-inputs">
                <div>
                    <label for="amount_from" class="form-label">From (₦)</label>
                    <input type="number" 
                           class="form-control" 
                           id="amount_from" 
                           name="amount_from"
                           placeholder="Minimum amount"
                           min="0"
                           step="100"
                           value="{{ old('amount_from') }}">
                </div>
                <div class="range-separator">to</div>
                <div>
                    <label for="amount_to" class="form-label">To (₦)</label>
                    <input type="number" 
                           class="form-control" 
                           id="amount_to" 
                           name="amount_to"
                           placeholder="Maximum amount"
                           min="0"
                           step="100"
                           value="{{ old('amount_to') }}">
                </div>
            </div>
        </div>
        
        <!-- Date Range -->
        <div class="filter-section">
            <h6><i class="fas fa-calendar me-2"></i>Date Range</h6>
            <div class="range-inputs">
                <div>
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" 
                           class="form-control" 
                           id="date_from" 
                           name="date_from"
                           value="{{ old('date_from') }}">
                </div>
                <div class="range-separator">to</div>
                <div>
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" 
                           class="form-control" 
                           id="date_to" 
                           name="date_to"
                           value="{{ old('date_to') }}">
                </div>
            </div>
        </div>
        
        <!-- Status Filters -->
        <div class="filter-section">
            <h6><i class="fas fa-tags me-2"></i>Status Filters</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Receipt Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="void" {{ old('status') == 'void' ? 'selected' : '' }}>Void</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select class="form-select" id="payment_status" name="payment_status">
                        <option value="">All Payments</option>
                        <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="failed" {{ old('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="receipt_type" class="form-label">Receipt Type</label>
                    <select class="form-select" id="receipt_type" name="receipt_type">
                        <option value="">All Types</option>
                        <option value="sale" {{ old('receipt_type') == 'sale' ? 'selected' : '' }}>Original Sale</option>
                        <option value="resale" {{ old('receipt_type') == 'resale' ? 'selected' : '' }}>Resale</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Additional Options -->
        <div class="filter-section">
            <h6><i class="fas fa-cog me-2"></i>Additional Options</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="checkbox-group">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="enable_antitheft" 
                                   name="enable_antitheft" 
                                   value="1"
                                   {{ old('enable_antitheft') ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_antitheft">
                                <i class="fas fa-shield-alt me-1"></i>
                                Anti-Theft Enabled Only
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="per_page" class="form-label">Results Per Page</label>
                    <select class="form-select" id="per_page" name="per_page">
                        <option value="20" {{ old('per_page', 20) == 20 ? 'selected' : '' }}>20 results</option>
                        <option value="50" {{ old('per_page') == 50 ? 'selected' : '' }}>50 results</option>
                        <option value="100" {{ old('per_page') == 100 ? 'selected' : '' }}>100 results</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Search Actions -->
        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
            <button type="submit" class="btn search-btn btn-lg">
                <i class="fas fa-search me-2"></i>Search Receipts
                <span class="spinner-border spinner-border-sm ms-2 d-none" id="searchSpinner"></span>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-lg" onclick="resetForm()">
                <i class="fas fa-undo me-2"></i>Reset Form
            </button>
            <a href="{{ route('search.index') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Simple Search
            </a>
        </div>
    </form>
</div>

<!-- Search Tips -->
<div class="search-tips">
    <h6 class="fw-bold mb-3">
        <i class="fas fa-lightbulb me-2 text-warning"></i>Advanced Search Tips
    </h6>
    
    <div class="row g-3">
        <div class="col-md-6">
            <div class="tip-item">
                <strong>Partial Matches:</strong> You don't need to fill all fields. The system will search based on the information provided.
            </div>
        </div>
        <div class="col-md-6">
            <div class="tip-item">
                <strong>Date Ranges:</strong> Use date filters to narrow down results to specific time periods.
            </div>
        </div>
        <div class="col-md-6">
            <div class="tip-item">
                <strong>Amount Filters:</strong> Set minimum and maximum amounts to find receipts within specific price ranges.
            </div>
        </div>
        <div class="col-md-6">
            <div class="tip-item">
                <strong>Quick Filters:</strong> Use the quick filter buttons above for common search scenarios.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeAdvancedSearch();
});

function initializeAdvancedSearch() {
    // Setup form validation
    setupFormValidation();
    
    // Setup filter tracking
    setupFilterTracking();
    
    // Setup amount validation
    setupAmountValidation();
    
    // Setup date validation
    setupDateValidation();
    
    // Initialize active filters display
    updateActiveFilters();
}

function setupFormValidation() {
    const form = document.getElementById('advancedSearchForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check if at least one field is filled
        const formData = new FormData(this);
        let hasValue = false;
        
        for (let [key, value] of formData.entries()) {
            if (key !== '_token' && value.trim() !== '') {
                hasValue = true;
                break;
            }
        }
        
        if (!hasValue) {
            alert('Please fill at least one search field.');
            return;
        }
        
        // Validate amount range
        const amountFrom = parseFloat(document.getElementById('amount_from').value || 0);
        const amountTo = parseFloat(document.getElementById('amount_to').value || 0);
        
        if (amountFrom > 0 && amountTo > 0 && amountFrom > amountTo) {
            alert('Minimum amount cannot be greater than maximum amount.');
            document.getElementById('amount_from').focus();
            return;
        }
        
        // Validate date range
        const dateFrom = document.getElementById('date_from').value;
        const dateTo = document.getElementById('date_to').value;
        
        if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
            alert('Start date cannot be later than end date.');
            document.getElementById('date_from').focus();
            return;
        }
        
        // Show loading state
        showLoading();
        
        // Submit form
        this.submit();
    });
}

function setupFilterTracking() {
    // Track when filters are changed to update active filters display
    const inputs = document.querySelectorAll('#advancedSearchForm input, #advancedSearchForm select');
    
    inputs.forEach(input => {
        input.addEventListener('change', updateActiveFilters);
        if (input.type === 'text' || input.type === 'email') {
            input.addEventListener('input', debounce(updateActiveFilters, 500));
        }
    });
}

function setupAmountValidation() {
    const amountFrom = document.getElementById('amount_from');
    const amountTo = document.getElementById('amount_to');
    
    amountFrom.addEventListener('change', function() {
        const fromValue = parseFloat(this.value || 0);
        const toValue = parseFloat(amountTo.value || 0);
        
        if (fromValue > 0 && toValue > 0 && fromValue > toValue) {
            amountTo.value = this.value;
        }
    });
}

function setupDateValidation() {
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    
    dateFrom.addEventListener('change', function() {
        if (this.value && dateTo.value && new Date(this.value) > new Date(dateTo.value)) {
            dateTo.value = this.value;
        }
        
        // Set minimum date for "to" field
        dateTo.min = this.value;
    });
    
    dateTo.addEventListener('change', function() {
        if (this.value && dateFrom.value && new Date(dateFrom.value) > new Date(this.value)) {
            dateFrom.value = this.value;
        }
        
        // Set maximum date for "from" field
        dateFrom.max = this.value;
    });
}

function updateActiveFilters() {
    const container = document.getElementById('activeFilters');
    const formData = new FormData(document.getElementById('advancedSearchForm'));
    
    container.innerHTML = '';
    
    const filterLabels = {
        'receipt_number': 'Receipt #',
        'customer_name': 'Customer',
        'customer_phone': 'Phone',
        'customer_email': 'Email',
        'phone_name': 'Phone Model',
        'phone_serial_number': 'Serial #',
        'shop_id': 'Shop',
        'amount_from': 'Min Amount',
        'amount_to': 'Max Amount',
        'date_from': 'From Date',
        'date_to': 'To Date',
        'status': 'Status',
        'payment_status': 'Payment',
        'receipt_type': 'Type',
        'enable_antitheft': 'Anti-Theft'
    };
    
    for (let [key, value] of formData.entries()) {
        if (key !== '_token' && value.trim() !== '') {
            let displayValue = value;
            
            // Special handling for select options
            if (key === 'shop_id') {
                const shopSelect = document.getElementById('shop_id');
                displayValue = shopSelect.options[shopSelect.selectedIndex].text;
            }
            
            if (key === 'enable_antitheft') {
                displayValue = 'Enabled';
            }
            
            // Truncate long values
            if (displayValue.length > 20) {
                displayValue = displayValue.substring(0, 20) + '...';
            }
            
            const chip = document.createElement('div');
            chip.className = 'filter-chip';
            chip.innerHTML = `
                <span>${filterLabels[key] || key}: ${displayValue}</span>
                <button type="button" class="remove-filter" onclick="removeFilter('${key}')">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(chip);
        }
    }
}

function removeFilter(fieldName) {
    const field = document.getElementById(fieldName) || document.querySelector(`[name="${fieldName}"]`);
    
    if (field) {
        if (field.type === 'checkbox') {
            field.checked = false;
        } else {
            field.value = '';
        }
        
        updateActiveFilters();
    }
}

function applyQuickFilter(filterType) {
    // Remove active class from all quick filter buttons
    document.querySelectorAll('.quick-filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active class to clicked button
    event.target.classList.add('active');
    
    const today = new Date();
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    
    switch (filterType) {
        case 'today':
            const todayStr = today.toISOString().split('T')[0];
            dateFrom.value = todayStr;
            dateTo.value = todayStr;
            break;
            
        case 'week':
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay());
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekStart.getDate() + 6);
            
            dateFrom.value = weekStart.toISOString().split('T')[0];
            dateTo.value = weekEnd.toISOString().split('T')[0];
            break;
            
        case 'month':
            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            
            dateFrom.value = monthStart.toISOString().split('T')[0];
            dateTo.value = monthEnd.toISOString().split('T')[0];
            break;
            
        case 'paid':
            document.getElementById('payment_status').value = 'paid';
            break;
            
        case 'antitheft':
            document.getElementById('enable_antitheft').checked = true;
            break;
    }
    
    updateActiveFilters();
}

function clearAllFilters() {
    // Remove active class from all quick filter buttons
    document.querySelectorAll('.quick-filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    resetForm();
}

function resetForm() {
    document.getElementById('advancedSearchForm').reset();
    updateActiveFilters();
    
    // Remove any validation states
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    
    document.querySelectorAll('.invalid-feedback').forEach(el => {
        el.remove();
    });
}

function showLoading() {
    const submitBtn = document.querySelector('#advancedSearchForm button[type="submit"]');
    const spinner = document.getElementById('searchSpinner');
    
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
}

function hideLoading() {
    const submitBtn = document.querySelector('#advancedSearchForm button[type="submit"]');
    const spinner = document.getElementById('searchSpinner');
    
    submitBtn.disabled = false;
    spinner.classList.add('d-none');
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

// Auto-complete functionality for phone models
document.getElementById('phone_name').addEventListener('input', function() {
    // You can add auto-complete functionality here
    // For now, we'll just format the input
    this.value = this.value.replace(/[^a-zA-Z0-9\s\-\+]/g, '');
});

// Format serial number input
document.getElementById('phone_serial_number').addEventListener('input', function() {
    this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
});

// Format phone number input
document.getElementById('customer_phone').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9\+\-\s]/g, '');
});
</script>
@endpush
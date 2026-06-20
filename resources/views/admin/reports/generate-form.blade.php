<form id="reportGenerationForm" onsubmit="processReportGeneration(event)">
    @csrf
    <input type="hidden" name="report_type" value="{{ $reportType }}">
    
    <!-- Report Configuration -->
    <div class="row mb-4">
        <div class="col-md-6">
            <label class="form-label">Report Name</label>
            <input type="text" class="form-control" name="report_name" 
                   value="{{ $reportConfig['default_name'] }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Report Format</label>
            <select class="form-select" name="format" required>
                <option value="pdf">PDF Report</option>
                <option value="excel">Excel Spreadsheet</option>
                <option value="csv">CSV Data</option>
                <option value="html">HTML View</option>
            </select>
        </div>
    </div>

    <!-- Date Range -->
    <div class="row mb-4">
        <div class="col-md-4">
            <label class="form-label">Date Range</label>
            <select class="form-select" name="date_range" onchange="toggleCustomDates(this.value)">
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="this_week">This Week</option>
                <option value="last_week">Last Week</option>
                <option value="this_month" selected>This Month</option>
                <option value="last_month">Last Month</option>
                <option value="this_quarter">This Quarter</option>
                <option value="this_year">This Year</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>
        <div class="col-md-4" id="customDateFrom" style="display: none;">
            <label class="form-label">From Date</label>
            <input type="date" class="form-control" name="date_from">
        </div>
        <div class="col-md-4" id="customDateTo" style="display: none;">
            <label class="form-label">To Date</label>
            <input type="date" class="form-control" name="date_to">
        </div>
    </div>

    <!-- Report-Specific Options -->
    @if($reportType === 'revenue-analysis')
        <div class="mb-4">
            <h6 class="text-primary mb-3">Revenue Analysis Options</h6>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Group By</label>
                    <select class="form-select" name="group_by">
                        <option value="day">Daily</option>
                        <option value="week">Weekly</option>
                        <option value="month" selected>Monthly</option>
                        <option value="shop">By Shop</option>
                        <option value="payment_method">By Payment Method</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Include</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="include_charts" value="1" checked>
                        <label class="form-check-label">Charts & Graphs</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="include_trends" value="1" checked>
                        <label class="form-check-label">Trend Analysis</label>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($reportType === 'shop-performance')
        <div class="mb-4">
            <h6 class="text-success mb-3">Shop Performance Options</h6>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Filter by Shop</label>
                    <select class="form-select" name="shop_filter">
                        <option value="all">All Shops</option>
                        <option value="top_10">Top 10 Performers</option>
                        <option value="bottom_10">Bottom 10 Performers</option>
                        <option value="approved_only">Approved Only</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Metrics to Include</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="include_receipts" value="1" checked>
                        <label class="form-check-label">Receipt Count</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="include_revenue" value="1" checked>
                        <label class="form-check-label">Revenue Generated</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="include_success_rate" value="1" checked>
                        <label class="form-check-label">Success Rate</label>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($reportType === 'customer-insights')
        <div class="mb-4">
            <h6 class="text-info mb-3">Customer Insights Options</h6>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Analysis Type</label>
                    <select class="form-select" name="analysis_type">
                        <option value="behavior">Customer Behavior</option>
                        <option value="retention">Retention Analysis</option>
                        <option value="segmentation">Customer Segmentation</option>
                        <option value="lifetime_value">Lifetime Value</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Customer Filter</label>
                    <select class="form-select" name="customer_filter">
                        <option value="all">All Customers</option>
                        <option value="registered">Registered Only</option>
                        <option value="guest">Guest Customers</option>
                        <option value="high_value">High Value Customers</option>
                    </select>
                </div>
            </div>
        </div>
    @endif

    @if($reportType === 'geographic-analysis')
        <div class="mb-4">
            <h6 class="text-warning mb-3">Geographic Analysis Options</h6>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Geographic Level</label>
                    <select class="form-select" name="geo_level">
                        <option value="state">By State</option>
                        <option value="lga">By Local Government</option>
                        <option value="city">By City</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Include Maps</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="include_maps" value="1" checked>
                        <label class="form-check-label">Geographic Maps</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="include_heatmap" value="1">
                        <label class="form-check-label">Activity Heatmap</label>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Advanced Options -->
    <div class="mb-4">
        <h6 class="text-dark mb-3">Advanced Options</h6>
        <div class="row">
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="include_summary" value="1" checked>
                    <label class="form-check-label">Executive Summary</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="include_recommendations" value="1">
                    <label class="form-check-label">Recommendations</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="email_report" value="1">
                    <label class="form-check-label">Email when ready</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="schedule_recurring" value="1">
                    <label class="form-check-label">Schedule as recurring</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Options (conditional) -->
    <div id="emailOptions" style="display: none;">
        <div class="mb-3">
            <label class="form-label">Email Recipients</label>
            <input type="email" class="form-control" name="email_recipients" 
                   placeholder="admin@example.com, manager@example.com">
            <small class="text-muted">Separate multiple emails with commas</small>
        </div>
    </div>

    <!-- Recurring Options (conditional) -->
    <div id="recurringOptions" style="display: none;">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Frequency</label>
                <select class="form-select" name="recurring_frequency">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Next Run Date</label>
                <input type="datetime-local" class="form-control" name="next_run_date">
            </div>
        </div>
    </div>

    <!-- Generation Options -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Report generation may take a few minutes for large datasets
            </small>
        </div>
        <div>
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-play me-2"></i>Generate Report
            </button>
        </div>
    </div>
</form>

<script>
function toggleCustomDates(value) {
    const fromDiv = document.getElementById('customDateFrom');
    const toDiv = document.getElementById('customDateTo');
    
    if (value === 'custom') {
        fromDiv.style.display = 'block';
        toDiv.style.display = 'block';
    } else {
        fromDiv.style.display = 'none';
        toDiv.style.display = 'none';
    }
}

// Toggle email and recurring options
document.addEventListener('change', function(e) {
    if (e.target.name === 'email_report') {
        const emailOptions = document.getElementById('emailOptions');
        emailOptions.style.display = e.target.checked ? 'block' : 'none';
    }
    
    if (e.target.name === 'schedule_recurring') {
        const recurringOptions = document.getElementById('recurringOptions');
        recurringOptions.style.display = e.target.checked ? 'block' : 'none';
    }
});

function processReportGeneration(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());
    
    // Collect checkbox values
    const checkboxes = event.target.querySelectorAll('input[type="checkbox"]:checked');
    checkboxes.forEach(checkbox => {
        data[checkbox.name] = checkbox.value;
    });
    
    showLoading('Generating report...');
    
    fetch('/admin/reports/process', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('success', 'Report generation started! You will be notified when it\'s ready.');
            bootstrap.Modal.getInstance(document.getElementById('reportModal')).hide();
            
            // Refresh the page after a short delay
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert('error', data.message || 'Failed to generate report');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('error', 'Failed to generate report. Please try again.');
    });
}
</script>
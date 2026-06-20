<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" data-auto-dismiss="true">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" data-auto-dismiss="true">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert" data-auto-dismiss="true">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Warning!</strong> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert" data-auto-dismiss="true">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Info!</strong> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Validation Errors -->
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Invalid Email Or Password</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- System Alerts -->
@auth
    @if(auth()->user()->isShopOwner())
        <!-- Shop Not Approved Alert -->
        @if(!auth()->user()->shop)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-store me-3 mt-1"></i>
                    <div class="flex-grow-1">
                        <strong>Setup Required!</strong> 
                        You need to set up your shop profile before you can generate receipts.
                        <div class="mt-2">
                            <a href="{{ route('shop.create') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-plus me-1"></i>Setup Shop Profile
                            </a>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @elseif(!auth()->user()->shop->approved)
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-clock me-3 mt-1"></i>
                    <div class="flex-grow-1">
                        <strong>Approval Pending!</strong> 
                        Your shop profile is awaiting admin approval. You'll be able to generate receipts once approved.
                        <div class="mt-2">
                            <a href="{{ route('shop.show', auth()->user()->shop) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye me-1"></i>View Shop Profile
                            </a>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <!-- Pending Payments Alert
        @php
            $pendingPayments = auth()->user()->receipts()->where('payment_gateway_status', 'pending')->count();
        @endphp
        @if($pendingPayments > 0)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-credit-card me-3 mt-1"></i>
                    <div class="flex-grow-1">
                        <strong>Pending Payments!</strong> 
                        You have {{ $pendingPayments }} receipt{{ $pendingPayments > 1 ? 's' : '' }} with pending payments.
                        <div class="mt-2">
                            <a href="{{ route('receipt.index', ['status' => 'pending']) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-list me-1"></i>View Pending Receipts
                            </a>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endif -->
    
    @if(auth()->user()->isAdmin())
        <!-- Pending Shop Approvals -->
        @php
            $pendingShops = \App\Models\Shop::pendingApproval()->count();
        @endphp
        @if($pendingShops > 0)
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-store me-3 mt-1"></i>
                    <div class="flex-grow-1">
                        <strong>Action Required!</strong> 
                        {{ $pendingShops }} shop{{ $pendingShops > 1 ? 's' : '' }} awaiting approval.
                        <div class="mt-2">
                            <a href="{{ route('admin.shops.index', ['status' => 'pending']) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-tasks me-1"></i>Review Shops
                            </a>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endif
    
    <!-- Account Verification Alert -->
    @if(!auth()->user()->isVerified() && auth()->user()->status === 'pending')
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-envelope me-3 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>Verify Your Account!</strong> 
                    Please check your email and verify your account to access all features.
                    <div class="mt-2">
                        <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fas fa-paper-plane me-1"></i>Resend Verification Email
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endauth

<!-- Maintenance Mode Alert (if applicable) -->
@if(app()->isDownForMaintenance())
    <div class="alert alert-danger" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-tools me-3"></i>
            <div>
                <strong>Maintenance Mode!</strong> 
                The system is currently under maintenance. Some features may be temporarily unavailable.
            </div>
        </div>
    </div>
@endif

<style>
/* Custom Alert Styles */
.alert {
    border: none;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid;
    box-shadow: var(--shadow-sm);
    animation: slideInDown 0.4s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-success {
    background: linear-gradient(135deg, #d1e7dd 0%, #badbcc 100%);
    border-left-color: var(--success-color);
    color: #0a3622;
}

.alert-success .btn {
    background-color: var(--success-color);
    border-color: var(--success-color);
    color: white;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
    border-left-color: var(--danger-color);
    color: #58151c;
}

.alert-danger .btn {
    background-color: var(--danger-color);
    border-color: var(--danger-color);
    color: white;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left-color: var(--warning-color);
    color: #664d03;
}

.alert-warning .btn {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: #212529;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #b8daff 100%);
    border-left-color: var(--info-color);
    color: #055160;
}

.alert-info .btn {
    background-color: var(--info-color);
    border-color: var(--info-color);
    color: white;
}

/* Alert Icons */
.alert i.fas {
    font-size: 1.1rem;
    opacity: 0.8;
}

/* Alert Buttons */
.alert .btn {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.alert .btn:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow);
}

/* Close Button */
.alert .btn-close {
    font-size: 0.875rem;
    opacity: 0.6;
}

.alert .btn-close:hover {
    opacity: 1;
}

/* List in alerts */
.alert ul {
    padding-left: 1.25rem;
}

.alert ul li {
    margin-bottom: 0.25rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .alert {
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .alert .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .alert .d-flex i {
        margin-bottom: 0.5rem;
        margin-right: 0 !important;
    }
    
    .alert .btn {
        margin-top: 0.5rem;
    }
}

/* Alert animation on dismiss */
.alert.fade.show {
    animation: slideInDown 0.4s ease-out;
}

.alert.fade:not(.show) {
    animation: slideOutUp 0.3s ease-in;
}

@keyframes slideOutUp {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(-20px);
    }
}

/* System status indicator */
.alert .status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 0.5rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.7;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.alert .status-indicator.warning {
    background-color: var(--warning-color);
}

.alert .status-indicator.danger {
    background-color: var(--danger-color);
}

.alert .status-indicator.info {
    background-color: var(--info-color);
}

/* Special alert for critical actions */
.alert-critical {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border-left-color: #a71e2a;
}

.alert-critical .btn-close {
    filter: invert(1);
}

/* Alert with progress bar */
.alert-with-progress {
    position: relative;
    overflow: hidden;
}

.alert-with-progress::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: rgba(255, 255, 255, 0.3);
    animation: progressBar 5s linear;
}

@keyframes progressBar {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    const autoDismissAlerts = document.querySelectorAll('.alert[data-auto-dismiss="true"]');
    
    autoDismissAlerts.forEach(function(alert) {
        // Add progress bar for auto-dismiss
        alert.classList.add('alert-with-progress');
        
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Handle verification email resend
    const verificationForms = document.querySelectorAll('form[action*="verification.send"]');
    verificationForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            setLoading(button, true);
            
            // Re-enable button after 30 seconds to allow retry
            setTimeout(function() {
                setLoading(button, false);
            }, 30000);
        });
    });
    
    // Track alert interactions for analytics
    document.querySelectorAll('.alert .btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const alertType = this.closest('.alert').className.match(/alert-(\w+)/);
            if (alertType) {
                // You can send analytics data here
                console.log('Alert action clicked:', alertType[1], this.textContent.trim());
            }
        });
    });
    
    // Add smooth animations to dynamically added alerts
    function addAlert(type, message, autoClose = true) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert" ${autoClose ? 'data-auto-dismiss="true"' : ''}>
                <i class="fas fa-${getAlertIcon(type)} me-2"></i>
                <strong>${getAlertTitle(type)}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        const container = document.querySelector('.container-fluid');
        if (container) {
            container.insertAdjacentHTML('afterbegin', alertHtml);
        }
    }
    
    function getAlertIcon(type) {
        const icons = {
            'success': 'check-circle',
            'danger': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
    
    function getAlertTitle(type) {
        const titles = {
            'success': 'Success!',
            'danger': 'Error!',
            'warning': 'Warning!',
            'info': 'Info!'
        };
        return titles[type] || 'Notice!';
    }
    
    // Make addAlert function globally available
    window.addAlert = addAlert;
});
</script>
@extends('layouts.app')

@section('title', 'Verification Results')
@section('page-title', 'Receipt Verification Results')
@section('page-description', 'Detailed verification information and receipt analysis')

@push('styles')
<style>
.result-hero {
    background: linear-gradient(135deg, #0d8abc 0%, #4dabf7 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.verification-result-container {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.verification-header {
    text-align: center;
    padding: 2rem 1rem;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 2rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.status-badge.verified {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.status-badge.invalid {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.status-badge.pending {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: #212529;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.info-card {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.info-card:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.info-card h6 {
    color: #0d8abc;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #0d8abc;
    display: inline-block;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 500;
    color: #6c757d;
    flex: 1;
}

.info-value {
    font-weight: 600;
    color: #343a40;
    text-align: right;
    flex: 1;
    word-break: break-word;
}

.security-panel {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border: 2px solid #2196f3;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.security-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(33, 150, 243, 0.2);
}

.security-item:last-child {
    border-bottom: none;
}

.security-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 0.9rem;
}

.security-icon.verified {
    background: #4caf50;
    color: white;
}

.security-icon.warning {
    background: #ff9800;
    color: white;
}

.security-icon.error {
    background: #f44336;
    color: white;
}

.history-timeline {
    position: relative;
    padding-left: 2rem;
}

.history-timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.history-item {
    position: relative;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    margin-left: 1rem;
}

.history-item::before {
    content: '';
    position: absolute;
    left: -1.75rem;
    top: 1.5rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: white;
    border: 3px solid;
    z-index: 1;
}

.history-item.sale::before {
    border-color: #28a745;
}

.history-item.resale::before {
    border-color: #17a2b8;
}

.history-item.current::before {
    border-color: #0d8abc;
    background: #0d8abc;
}

.anti-theft-status {
    background: linear-gradient(135deg, #ff5722, #ff9800);
    color: white;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    margin-bottom: 2rem;
}

.anti-theft-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.anti-theft-feature {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}

.verification-actions {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
}

.action-group {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .info-value {
        text-align: left;
    }
    
    .action-group {
        flex-direction: column;
        align-items: stretch;
    }
    
    .anti-theft-features {
        grid-template-columns: 1fr;
    }
    
    .history-timeline {
        padding-left: 1rem;
    }
    
    .history-item {
        margin-left: 0.5rem;
    }
    
    .history-item::before {
        left: -1.25rem;
    }
}

.receipt-preview {
    background: white;
    border: 2px solid #0d8abc;
    border-radius: 12px;
    padding: 2rem;
    margin: 2rem 0;
    position: relative;
}

.receipt-header {
    text-align: center;
    border-bottom: 2px solid #0d8abc;
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;
}

.receipt-logo {
    width: 60px;
    height: 60px;
    background: #0d8abc;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}
</style>
@endpush

@section('content')
<!-- Result Hero Section -->
<div class="result-hero">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold mb-2">Verification Complete</h1>
            <p class="lead mb-0">Detailed analysis of receipt {{ $receipt->receipt_number }}</p>
        </div>
        <div class="col-md-4 text-center">
            <i class="fas fa-shield-check" style="font-size: 4rem; opacity: 0.3;"></i>
        </div>
    </div>
</div>

<div class="verification-result-container">
    <!-- Verification Status Header -->
    <div class="verification-header">
        @if($verification_status['is_valid'])
            <div class="status-badge verified">
                <i class="fas fa-check-circle"></i>
                <span>Receipt Verified & Valid</span>
            </div>
            <h4 class="fw-bold mb-2">{{ $receipt->receipt_number }}</h4>
            <p class="text-muted mb-0">This receipt has been successfully verified in our system</p>
        @else
            <div class="status-badge invalid">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Verification Issues Detected</span>
            </div>
            <h4 class="fw-bold mb-2">{{ $receipt->receipt_number }}</h4>
            <p class="text-muted mb-0">Please review the details below for more information</p>
        @endif
    </div>

    <!-- Receipt Information Grid -->
    <div class="info-grid">
        <!-- Basic Receipt Information -->
        <div class="info-card">
            <h6><i class="fas fa-receipt me-2"></i>Receipt Details</h6>
            
            <div class="info-item">
                <span class="info-label">Receipt Number</span>
                <span class="info-value text-primary">{{ $receipt->receipt_number }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Receipt Type</span>
                <span class="info-value">
                    @if($receipt->receipt_type === 'sale')
                        <span class="badge bg-success">Original Sale</span>
                    @else
                        <span class="badge bg-info">Resale</span>
                    @endif
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Amount</span>
                <span class="info-value text-success">₦{{ number_format($receipt->amount, 2) }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Status</span>
                <span class="info-value">
                    @if($receipt->status === 'active')
                        <span class="badge bg-success">Active</span>
                    @elseif($receipt->status === 'cancelled')
                        <span class="badge bg-danger">Cancelled</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($receipt->status) }}</span>
                    @endif
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Payment Status</span>
                <span class="info-value">
                    @if($receipt->payment_status === 'paid')
                        <span class="badge bg-success">Paid</span>
                    @elseif($receipt->payment_status === 'pending')
                        <span class="badge bg-warning">Pending</span>
                    @elseif($receipt->payment_status === 'partial')
                        <span class="badge bg-info">Partial</span>
                    @else
                        <span class="badge bg-danger">{{ ucfirst($receipt->payment_status) }}</span>
                    @endif
                </span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Date Generated</span>
                <span class="info-value">{{ $receipt->created_at->format('M j, Y \a\t g:i A') }}</span>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="info-card">
            <h6><i class="fas fa-user me-2"></i>Customer Details</h6>
            
            <div class="info-item">
                <span class="info-label">Full Name</span>
                <span class="info-value">{{ $receipt->customer_name }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Phone Number</span>
                <span class="info-value">{{ $receipt->customer_phone }}</span>
            </div>
            
            @if($receipt->customer_email)
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $receipt->customer_email }}</span>
                </div>
            @endif
            
            @if($receipt->customer_address)
                <div class="info-item">
                    <span class="info-label">Address</span>
                    <span class="info-value">{{ $receipt->customer_address }}</span>
                </div>
            @endif
        </div>

        <!-- Phone Information -->
        <div class="info-card">
            <h6><i class="fas fa-mobile-alt me-2"></i>Phone Details</h6>
            
            <div class="info-item">
                <span class="info-label">Phone Model</span>
                <span class="info-value">{{ $receipt->phone_name }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Color</span>
                <span class="info-value">{{ $receipt->phone_color }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Serial Number</span>
                <span class="info-value">{{ $receipt->phone_serial_number }}</span>
            </div>
            
            @if($receipt->phone_imei)
                <div class="info-item">
                    <span class="info-label">IMEI</span>
                    <span class="info-value">{{ $receipt->phone_imei }}</span>
                </div>
            @endif
            
            @if($receipt->phone_storage)
                <div class="info-item">
                    <span class="info-label">Storage</span>
                    <span class="info-value">{{ $receipt->phone_storage }}</span>
                </div>
            @endif
        </div>

        <!-- Shop Information -->
        <div class="info-card">
            <h6><i class="fas fa-store me-2"></i>Shop Details</h6>
            
            <div class="info-item">
                <span class="info-label">Shop Name</span>
                <span class="info-value">{{ $receipt->shop->shop_name }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Owner</span>
                <span class="info-value">{{ $receipt->shop->owner_full_name }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Location</span>
                <span class="info-value">{{ $receipt->shop->state }}, {{ $receipt->shop->local_government }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Contact</span>
                <span class="info-value">{{ $receipt->shop->business_phone_1 }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Shop Status</span>
                <span class="info-value">
                    @if($receipt->shop->approved)
                        <span class="badge bg-success">Verified Shop</span>
                    @else
                        <span class="badge bg-warning">Pending Approval</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Security Features -->
    <div class="security-panel">
        <h5 class="fw-bold mb-3 text-primary">
            <i class="fas fa-shield-alt me-2"></i>Security Features Analysis
        </h5>
        
        @if(isset($security_features))
            @foreach($security_features as $feature => $status)
                <div class="security-item">
                    <div class="security-icon {{ $status === 'Verified' || $status === 'Valid' || $status === 'Approved Shop' ? 'verified' : ($status === 'Pending' ? 'warning' : 'error') }}">
                        <i class="fas {{ $status === 'Verified' || $status === 'Valid' || $status === 'Approved Shop' ? 'fa-check' : ($status === 'Pending' ? 'fa-clock' : 'fa-times') }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <strong>{{ ucwords(str_replace('_', ' ', $feature)) }}:</strong>
                        <span class="ms-2">{{ $status }}</span>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    @if(isset($anti_theft_status) && $anti_theft_status['enabled'])
        <!-- Anti-Theft Status -->
        <div class="anti-theft-status">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-shield-alt me-2"></i>Anti-Theft Protection Status
            </h5>
            
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h6 class="mb-2">{{ $anti_theft_status['status'] }}</h6>
                    <p class="mb-3 opacity-75">
                        This device is protected by M-right's anti-theft system. 
                        If reported stolen, alerts will be triggered and tracking will be activated.
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="anti-theft-features">
                        <div class="anti-theft-feature">
                            <i class="fas fa-eye fs-3 mb-2"></i>
                            <div>Trackable</div>
                        </div>
                        <div class="anti-theft-feature">
                            <i class="fas fa-bell fs-3 mb-2"></i>
                            <div>Alerts</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <small>
                    <i class="fas fa-clock me-1"></i>
                    Last Check: {{ $anti_theft_status['last_check']->format('M j, Y \a\t g:i A') }}
                </small>
                <span class="mx-2">•</span>
                <small>
                    Alert Level: <strong>{{ $anti_theft_status['alert_level'] }}</strong>
                </small>
            </div>
        </div>
    @endif

    @if(isset($resale_history) && count($resale_history) > 0)
        <!-- Ownership History -->
        <div class="mb-4">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-history me-2 text-info"></i>Ownership History
            </h5>
            
            <div class="history-timeline">
                @foreach($resale_history as $index => $history)
                    <div class="history-item {{ $history['type'] }} {{ $loop->last ? 'current' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-2">
                                    @if($history['type'] === 'original_sale')
                                        <i class="fas fa-shopping-cart me-2 text-success"></i>Original Sale
                                    @else
                                        <i class="fas fa-sync-alt me-2 text-info"></i>Resale Transaction
                                    @endif
                                    
                                    @if($loop->last)
                                        <span class="badge bg-primary ms-2">Current Owner</span>
                                    @endif
                                </h6>
                                
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <small class="text-muted d-block">Receipt Number</small>
                                        <span class="fw-medium">{{ $history['receipt_number'] }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <small class="text-muted d-block">Shop</small>
                                        <span class="fw-medium">{{ $history['shop'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Transaction Date</small>
                                <span class="fw-medium">{{ \Carbon\Carbon::parse($history['date'])->format('M j, Y') }}</span>
                                <br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($history['date'])->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Verification Summary -->
    <div class="receipt-preview">
        <div class="receipt-header">
            <div class="receipt-logo">
                <i class="fas fa-receipt"></i>
            </div>
            <h5 class="fw-bold mb-2">Phone Anti-Theft Digital Receipt</h5>
            <p class="text-muted mb-0">Verification Summary</p>
        </div>
        
        <div class="row g-3">
            <div class="col-md-6">
                <strong>Receipt:</strong> {{ $receipt->receipt_number }}<br>
                <strong>Phone:</strong> {{ $receipt->phone_name }} ({{ $receipt->phone_color }})<br>
                <strong>Serial:</strong> {{ $receipt->phone_serial_number }}<br>
                <strong>Customer:</strong> {{ $receipt->customer_name }}
            </div>
            <div class="col-md-6">
                <strong>Shop:</strong> {{ $receipt->shop->shop_name }}<br>
                <strong>Amount:</strong> ₦{{ number_format($receipt->amount, 2) }}<br>
                <strong>Date:</strong> {{ $receipt->created_at->format('M j, Y') }}<br>
                <strong>Status:</strong> 
                @if($verification_status['is_valid'])
                    <span class="text-success">✓ Verified</span>
                @else
                    <span class="text-danger">✗ Invalid</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="verification-actions">
        <h6 class="fw-bold mb-3">Available Actions</h6>
        
        <div class="action-group">
            <a href="{{ route('receipt.view', $receipt) }}" class="btn btn-primary btn-lg">
                <i class="fas fa-eye me-2"></i>View Full Receipt
            </a>
            
            <a href="{{ route('receipt.download', $receipt) }}" class="btn btn-success btn-lg">
                <i class="fas fa-download me-2"></i>Download PDF
            </a>
            
            <button class="btn btn-info btn-lg" onclick="printVerification()">
                <i class="fas fa-print me-2"></i>Print Results
            </button>
            
            <a href="{{ route('search.index') }}" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-search me-2"></i>New Search
            </a>
        </div>
        
        @if($receipt->enable_antitheft)
            <div class="mt-3">
                <a href="{{ route('customer.report-issue', ['receipt_id' => $receipt->id]) }}" class="btn btn-outline-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>Report Issue with This Phone
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Additional Information -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Verification Details
                </h6>
            </div>
            <div class="card-body">
                <p class="card-text">
                    This verification was performed on {{ now()->format('M j, Y \a\t g:i A') }} 
                    and confirms the authenticity of the digital receipt in our secure database.
                </p>
                
                <ul class="list-unstyled mb-0">
                    <li class="mb-1">
                        <i class="fas fa-check text-success me-2"></i>
                        Receipt digitally signed
                    </li>
                    <li class="mb-1">
                        <i class="fas fa-check text-success me-2"></i>
                        Shop verification confirmed
                    </li>
                    <li class="mb-1">
                        <i class="fas fa-check text-success me-2"></i>
                        Timestamp validation passed
                    </li>
                    @if($receipt->enable_antitheft)
                        <li class="mb-1">
                            <i class="fas fa-shield-alt text-success me-2"></i>
                            Anti-theft protection active
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-share-alt me-2 text-info"></i>
                    Share Verification
                </h6>
            </div>
            <div class="card-body">
                <p class="card-text">
                    Share this verification result with others to prove ownership or authenticity.
                </p>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="shareVerification()">
                        <i class="fas fa-link me-1"></i>Copy Verification Link
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="emailVerification()">
                        <i class="fas fa-envelope me-1"></i>Email Verification
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth animations to cards
    const cards = document.querySelectorAll('.info-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in');
    });
});

function printVerification() {
    // Create a print-friendly version
    const printContent = document.querySelector('.verification-result-container').cloneNode(true);
    
    // Remove action buttons from print version
    const actions = printContent.querySelector('.verification-actions');
    if (actions) actions.remove();
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Verification Results - {{ $receipt->receipt_number }}</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                <style>
                    body { font-family: Arial, sans-serif; font-size: 12px; }
                    .info-card { page-break-inside: avoid; margin-bottom: 1rem; }
                    .security-panel { page-break-inside: avoid; }
                    @media print {
                        .btn { display: none !important; }
                        .no-print { display: none !important; }
                    }
                </style>
            </head>
            <body>
                <div class="container-fluid p-4">
                    <div class="text-center mb-4">
                        <h3>Phone Anti-Theft Digital Receipt Verification</h3>
                        <p>Generated on: ${new Date().toLocaleString()}</p>
                    </div>
                    ${printContent.innerHTML}
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function shareVerification() {
    const url = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: 'M-right Receipt Verification - {{ $receipt->receipt_number }}',
            text: 'Receipt verification results for {{ $receipt->phone_name }}',
            url: url
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            showToast('Verification link copied to clipboard!', 'success');
        }).catch(() => {
            // Manual copy fallback
            const textArea = document.createElement('textarea');
            textArea.value = url;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showToast('Verification link copied to clipboard!', 'success');
        });
    }
}

function emailVerification() {
    const subject = encodeURIComponent('M-right Receipt Verification - {{ $receipt->receipt_number }}');
    const body = encodeURIComponent(`
Receipt Verification Results

Receipt Number: {{ $receipt->receipt_number }}
Phone Model: {{ $receipt->phone_name }}
Serial Number: {{ $receipt->phone_serial_number }}
Customer: {{ $receipt->customer_name }}
Shop: {{ $receipt->shop->shop_name }}
Verification Status: {{ $verification_status['is_valid'] ? 'Verified' : 'Invalid' }}

View full verification: ${window.location.href}

This verification was generated by the Phone Anti-Theft Digital Receipt System.
    `);
    
    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}

// Add copy functionality for serial numbers and important data
document.querySelectorAll('.info-value').forEach(element => {
    if (element.textContent.length > 10) {
        element.style.cursor = 'pointer';
        element.title = 'Click to copy';
        
        element.addEventListener('click', function() {
            const text = this.textContent.trim();
            navigator.clipboard.writeText(text).then(() => {
                showToast(`Copied: ${text}`, 'info');
            });
        });
    }
});
</script>
@endpush
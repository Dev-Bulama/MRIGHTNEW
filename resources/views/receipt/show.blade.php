@extends('layouts.app')

@section('title', 'Receipt Details - ' . $receipt->receipt_number)
@section('page-title', 'Receipt Details')
@section('page-description', 'View and manage receipt information')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-2">
                <i class="fas fa-receipt text-primary me-2"></i>
                Receipt Details
            </h1>
            <p class="text-muted mb-0">
                Receipt Number: <strong>{{ $receipt->receipt_number }}</strong>
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="btn-group" role="group">
                <a href="{{ route('receipt.print', $receipt) }}" class="btn btn-outline-primary">
                    <i class="fas fa-print me-1"></i>Print
                </a>
                <a href="{{ route('receipt.download', $receipt) }}" class="btn btn-outline-success">
                    <i class="fas fa-download me-1"></i>Download PDF
                </a>
                <a href="{{ route('receipt.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Receipts
                </a>
            </div>
        </div>
    </div>

    <!-- Receipt Status Alert -->
    <div class="row mb-4">
        <div class="col-12">
            @php
                $statusColor = match($receipt->payment_gateway_status) {
                    'successful' => 'success',
                    'pending' => 'warning',
                    'failed' => 'danger',
                    default => 'info'
                };
                $statusIcon = match($receipt->payment_gateway_status) {
                    'successful' => 'fa-check-circle',
                    'pending' => 'fa-clock',
                    'failed' => 'fa-times-circle',
                    default => 'fa-info-circle'
                };
            @endphp
            <div class="alert alert-{{ $statusColor }} alert-dismissible fade show" role="alert">
                <i class="fas {{ $statusIcon }} me-2"></i>
                <strong>Payment Status:</strong> {{ ucfirst($receipt->payment_gateway_status ?? 'Unknown') }}
                @if($receipt->payment_gateway_status === 'pending')
                    - Customer payment is being processed
                @elseif($receipt->payment_gateway_status === 'successful')
                    - Payment completed successfully
                @elseif($receipt->payment_gateway_status === 'failed')
                    - Payment failed or was cancelled
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>

    <!-- Receipt Information -->
    <div class="row">
        <!-- Customer Information -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user text-primary me-2"></i>
                        Customer Information
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Full Name:</dt>
                        <dd class="col-sm-8">{{ $receipt->customer_name }}</dd>
                        
                        <dt class="col-sm-4">Phone:</dt>
                        <dd class="col-sm-8">
                            <a href="tel:{{ $receipt->customer_phone }}" class="text-decoration-none">
                                {{ $receipt->customer_phone }}
                            </a>
                        </dd>
                        
                        @if($receipt->customer_email)
                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8">
                                <a href="mailto:{{ $receipt->customer_email }}" class="text-decoration-none">
                                    {{ $receipt->customer_email }}
                                </a>
                            </dd>
                        @endif
                        
                        @if($receipt->customer_address)
                            <dt class="col-sm-4">Address:</dt>
                            <dd class="col-sm-8">{{ $receipt->customer_address }}</dd>
                        @endif
                        
                        @if($receipt->customer_sex)
                            <dt class="col-sm-4">Gender:</dt>
                            <dd class="col-sm-8">{{ ucfirst($receipt->customer_sex) }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Phone Information -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-mobile-alt text-success me-2"></i>
                        Phone Information
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Phone Model:</dt>
                        <dd class="col-sm-8">{{ $receipt->phone_name }}</dd>
                        
                        <dt class="col-sm-4">Color:</dt>
                        <dd class="col-sm-8">
                            <span class="badge" style="background-color: {{ strtolower($receipt->phone_color) }}; color: white;">
                                {{ $receipt->phone_color }}
                            </span>
                        </dd>
                        
                        <dt class="col-sm-4">Serial Number:</dt>
                        <dd class="col-sm-8">
                            <code class="bg-light p-1 rounded">{{ $receipt->phone_serial_number }}</code>
                        </dd>
                        
                        @if($receipt->resale_code)
                            <dt class="col-sm-4">Resale Code:</dt>
                            <dd class="col-sm-8">
                                <!--<code> hidden</code>-->
<!--<code class="bg-warning p-1 rounded" style="color: transparent; text-shadow: 0 0 0 #fcf8e3;">-->
<!--    {{ $receipt->resale_code }}-->
<!--</code>-->

                            </dd>
                        @endif
                        
                        <dt class="col-sm-4">Anti-Theft:</dt>
                        <dd class="col-sm-8">
                            @if($receipt->enable_antitheft)
                                <span class="badge bg-success">
                                    <i class="fas fa-shield-alt me-1"></i>Enabled
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-shield-alt me-1"></i>Disabled
                                </span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment & Shop Information -->
    <div class="row">
        <!-- Payment Details -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card text-warning me-2"></i>
                        Payment Details
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Amount:</dt>
                        <dd class="col-sm-8">
                            <h5 class="text-success mb-0">₦{{ number_format($receipt->amount, 2) }}</h5>
                            <small class="text-muted">{{ $receipt->amount_in_words }}</small>
                        </dd>
                        
                        <dt class="col-sm-4">Service Fee:</dt>
                        <dd class="col-sm-8">₦{{ number_format($receipt->service_fee ?? 0, 2) }}</dd>
                        
                        <dt class="col-sm-4">Payment Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $statusColor }}">
                                {{ ucfirst($receipt->payment_status) }}
                            </span>
                        </dd>
                        
                        <dt class="col-sm-4">Receipt Type:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $receipt->receipt_type === 'resale' ? 'info' : 'primary' }}">
                                {{ ucfirst($receipt->receipt_type) }}
                            </span>
                        </dd>
                        
                        @if($receipt->payment_reference)
                            <dt class="col-sm-4">Payment Ref:</dt>
                            <dd class="col-sm-8">
                                <code class="bg-light p-1 rounded">{{ $receipt->payment_reference }}</code>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Shop Information -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-store text-info me-2"></i>
                        Shop Information
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Shop Name:</dt>
                        <dd class="col-sm-8">{{ $receipt->shop->shop_name }}</dd>
                        
                        <dt class="col-sm-4">Owner:</dt>
                        <dd class="col-sm-8">{{ $receipt->shop->owner_full_name }}</dd>
                        
                        <dt class="col-sm-4">Address:</dt>
                        <dd class="col-sm-8">{{ $receipt->shop->business_address }}</dd>
                        
                        <dt class="col-sm-4">Phone:</dt>
                        <dd class="col-sm-8">
                            <a href="tel:{{ $receipt->shop->business_phone_1 }}" class="text-decoration-none">
                                {{ $receipt->shop->business_phone_1 }}
                            </a>
                        </dd>
                        
                        <dt class="col-sm-4">Generated:</dt>
                        <dd class="col-sm-8">
                            {{ $receipt->generated_at ? $receipt->generated_at->format('M j, Y g:i A') : $receipt->created_at->format('M j, Y g:i A') }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    @if($receipt->notes || $receipt->parentReceipt || $receipt->childReceipts->count() > 0)
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle text-secondary me-2"></i>
                            Additional Information
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($receipt->notes)
                            <div class="mb-3">
                                <h6 class="fw-bold">Notes:</h6>
                                <p class="text-muted">{{ $receipt->notes }}</p>
                            </div>
                        @endif

                        @if($receipt->parentReceipt)
                            <div class="mb-3">
                                <h6 class="fw-bold">Original Receipt:</h6>
                                <p class="mb-0">
                                    This is a resale of receipt 
                                    <a href="{{ route('receipt.show', $receipt->parentReceipt) }}" class="text-decoration-none">
                                        {{ $receipt->parentReceipt->receipt_number }}
                                    </a>
                                </p>
                            </div>
                        @endif

                        @if($receipt->childReceipts->count() > 0)
                            <div class="mb-3">
                                <h6 class="fw-bold">Resale History:</h6>
                                @foreach($receipt->childReceipts as $childReceipt)
                                    <p class="mb-1">
                                        <a href="{{ route('receipt.show', $childReceipt) }}" class="text-decoration-none">
                                            {{ $childReceipt->receipt_number }}
                                        </a>
                                        - Sold to {{ $childReceipt->customer_name }} on {{ $childReceipt->created_at->format('M j, Y') }}
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Receipt Actions</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('receipt.view', $receipt) }}" class="btn btn-primary">
                            <i class="fas fa-eye me-1"></i>View Receipt
                        </a>
                        <a href="{{ route('receipt.print', $receipt) }}" class="btn btn-outline-primary">
                            <i class="fas fa-print me-1"></i>Print Receipt
                        </a>
                        <a href="{{ route('receipt.download', $receipt) }}" class="btn btn-outline-success">
                            <i class="fas fa-download me-1"></i>Download PDF
                        </a>
                        @if($receipt->canBeResold())
                            <a href="{{ route('receipt.resale') }}" class="btn btn-outline-info">
                                <i class="fas fa-sync-alt me-1"></i>Mark as Resale
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 10px 10px 0 0 !important;
}

.btn-group .btn {
    border-radius: 5px !important;
    margin: 0 2px;
}

dl.row dt {
    font-weight: 600;
    color: #495057;
}

dl.row dd {
    color: #6c757d;
}

@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-group .btn {
        width: 100%;
        margin: 0;
    }
}
</style>
@endsection
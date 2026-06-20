@extends('layouts.app')

@section('title', 'Receipt View')

@push('styles')
<style>
    .receipt-container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
        page-break-inside: avoid;
    }
    
    .receipt-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        text-align: center;
    }
    
    .receipt-body {
        padding: 1rem;
        font-size: 0.85rem;
        line-height: 1.3;
    }
    
    .info-section {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        background: #f8f9fa;
        page-break-inside: avoid;
    }
    
    .info-section h5 {
        color: #667eea;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.3rem;
        margin-bottom: 0.6rem;
        font-size: 0.95rem;
        font-weight: 600;
    }
    
    .receipt-number {
        font-size: 1.4rem;
        font-weight: bold;
        letter-spacing: 1px;
        margin: 0.3rem 0;
    }
    
    .amount-display {
        background: #28a745;
        color: white;
        padding: 0.75rem;
        border-radius: 6px;
        text-align: center;
        font-size: 1.1rem;
        font-weight: bold;
        margin: 0.75rem 0;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 15px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.65rem;
        margin: 0.1rem;
    }
    
    .status-active { background: #d4edda; color: #155724; }
    .status-paid { background: #28a745; color: white; }
    .status-pending { background: #ffc107; color: #856404; }
    .status-partial { background: #fd7e14; color: white; }
    .status-resale { background: #17a2b8; color: white; }
    
    .resale-info {
        background: #e7f3ff;
        border: 1px solid #b3d7ff;
        border-radius: 6px;
        padding: 0.75rem;
        margin: 0.75rem 0;
    }
    
    .resale-info h6 {
        color: #0056b3;
        margin-bottom: 0.4rem;
        font-size: 0.9rem;
    }
    
    .masked-phone {
        font-family: 'Courier New', monospace;
        background: #f0f0f0;
        padding: 0.15rem 0.4rem;
        border-radius: 3px;
        font-weight: bold;
        font-size: 0.85rem;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 0.75rem;
    }
    
    .info-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }
    
    .info-item {
        margin-bottom: 0.5rem;
    }
    
    .info-label {
        font-weight: 600;
        color: #495057;
        display: block;
        margin-bottom: 0.15rem;
        font-size: 0.8rem;
    }
    
    .info-value {
        color: #212529;
        font-size: 0.85rem;
    }
    
    .horizontal-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .security-footer {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 0.5rem;
        text-align: center;
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 0.75rem;
    }
    
    @media print {
        .no-print { display: none !important; }
        .receipt-container { 
            box-shadow: none; 
            margin: 0;
            max-width: 100%;
            font-size: 11px;
        }
        .receipt-body {
            padding: 0.5rem;
            font-size: 11px;
        }
        .info-section {
            border: 1px solid #ccc;
            background: white !important;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .info-section h5 {
            font-size: 12px;
            margin-bottom: 0.4rem;
        }
        .receipt-header {
            background: #667eea !important;
            color: white !important;
            padding: 0.5rem;
        }
        .receipt-number {
            font-size: 16px;
        }
        .amount-display {
            padding: 0.5rem;
            font-size: 14px;
        }
        .info-grid {
            gap: 0.5rem;
        }
        .info-label {
            font-size: 10px;
        }
        .info-value {
            font-size: 11px;
        }
        body { 
            font-size: 11px;
            -webkit-print-color-adjust: exact;
        }
        .security-footer {
            font-size: 9px;
            padding: 0.3rem;
        }
    }
    
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr 1fr;
        }
        .horizontal-layout {
            grid-template-columns: 1fr;
        }
        .receipt-container {
            margin: 0 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Action Buttons -->
    <div class="row mb-4 no-print">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('receipt.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Receipts
                </a>
                <div>
                    <button onclick="window.print()" class="btn btn-primary me-2">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </button>
                    <a href="{{ route('receipt.download', $receipt) }}" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Display -->
    <div class="receipt-container">
        <!-- Receipt Header -->
        <div class="receipt-header">
            <h1>📧 M-right Digital Receipt</h1>
            <p class="mb-0">PHONE E-RECEIPT ACCESSIBLE ONLINE</p>
            <div class="receipt-number">{{ $receipt->receipt_number }}</div>
            <div class="mt-3">
                <span class="status-badge status-{{ strtolower($receipt->status) }}">
                    {{ ucfirst($receipt->status) }}
                </span>
                <span class="status-badge status-{{ strtolower($receipt->payment_status) }}">
                    {{ ucfirst($receipt->payment_status) }}
                </span>
                @if($receipt->receipt_type === 'resale')
                    <span class="status-badge status-resale">
                        <i class="fas fa-sync-alt me-1"></i>Resale
                    </span>
                @endif
            </div>
        </div>

        <div class="receipt-body">
            <!-- Resale Information (Only for resale receipts) -->
            @if($receipt->receipt_type === 'resale' && $receipt->parentReceipt)
            <div class="resale-info">
                <h6><i class="fas fa-info-circle me-1"></i>Resale Information</h6>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Previous Owner:</span>
                        <span class="info-value">{{ $receipt->parentReceipt->customer_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Previous Phone:</span>
                        <span class="info-value">{{ $receipt->parentReceipt->customer_phone }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Original Sale:</span>
                        <span class="info-value">{{ $receipt->parentReceipt->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Information Layout - 2 Columns -->
            <div class="horizontal-layout">
                <!-- Left Column -->
                <div>
                    <!-- Shop Information -->
                    <div class="info-section">
                        <h5><i class="fas fa-store me-1"></i>Shop Information</h5>
                        <div class="info-item">
                            <span class="info-label">Shop Name:</span>
                            <span class="info-value">{{ $receipt->shop->shop_name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Owner:</span>
                            <span class="info-value">{{ $receipt->shop->owner_full_name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $receipt->shop->business_phone_1 ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Location:</span>
                            <span class="info-value">{{ $receipt->shop->state ?? 'N/A' }}, {{ $receipt->shop->local_government ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="info-section">
                        <h5><i class="fas fa-user me-1"></i>Customer Information</h5>
                        <div class="info-item">
                            <span class="info-label">Name:</span>
                            <span class="info-value">{{ $receipt->customer_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Phone:</span>
                            <span class="info-value masked-phone">{{ $receipt->masked_phone }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $receipt->customer_email ?? 'Not provided' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <!-- Phone Details -->
                    <div class="info-section">
                        <h5><i class="fas fa-mobile-alt me-1"></i>Phone Details</h5>
                        <div class="info-item">
                            <span class="info-label">Model:</span>
                            <span class="info-value">{{ $receipt->phone_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Color:</span>
                            <span class="info-value">{{ $receipt->phone_color }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Serial Number:</span>
                            <span class="info-value">{{ $receipt->phone_serial_number }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Anti-theft:</span>
                            <span class="info-value">
                                @if($receipt->enable_antitheft)
                                    <span class="badge bg-success">Enabled</span>
                                @else
                                    <span class="badge bg-secondary">Actiate at www.mright.com.ng</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Transaction Details -->
                    <div class="info-section">
                        <h5><i class="fas fa-receipt me-1"></i>Transaction Details</h5>
                        <div class="info-item">
                            <span class="info-label">Type:</span>
                            <span class="info-value">{{ $receipt->receipt_type === 'resale' ? 'Resale' : 'New Sale' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date:</span>
                            <span class="info-value">{{ $receipt->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Generated By:</span>
                            <span class="info-value">{{ $receipt->user->name ?? 'System' }}</span>
                        </div>
                        @if($receipt->notes)
                        <div class="info-item">
                            <span class="info-label">Notes:</span>
                            <span class="info-value">{{ $receipt->notes }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Amount Information -->
            <div class="amount-display">
                <div>Sale Amount: ₦{{ number_format($receipt->amount, 2) }}</div>
                <small style="opacity: 0.9;">{{ $receipt->amount_in_words }}</small>
            </div>

            <!-- Security Footer -->
            <div class="security-footer">
                <strong>Digital Receipt Verification:</strong> Receipt #{{ $receipt->receipt_number }} • 
                Generated by M-right Digital System • 
                @if($receipt->enable_antitheft)Anti-theft Protected • @endif
                Verify online at support center
            </div>
        </div>
    </div>
</div>
@endsection
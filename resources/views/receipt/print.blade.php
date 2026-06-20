<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Receipt - {{ $receipt->receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            @page {
                margin: 0.5in;
                size: A4 portrait;
            }
            
            .no-print {
                display: none !important;
            }
            
            body {
                font-size: 11px !important;
                line-height: 1.3 !important;
            }
            
            .receipt-container {
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                page-break-inside: avoid !important;
            }
            
            .receipt-header {
                background: #667eea !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
            }
            
            .amount-display {
                background: #28a745 !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.3;
            background: white;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 0;
        }
        
        .receipt-header {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .receipt-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .receipt-number {
            font-size: 1.3rem;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 0.5rem 0;
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
        .status-resale { background: #17a2b8; color: white; }
        
        .resale-info {
            background: #e7f3ff;
            border: 1px solid #b3d7ff;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .resale-info h6 {
            color: #0056b3;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .info-section {
            background: #f8f9fa;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 0.75rem;
            border: 1px solid #dee2e6;
        }
        
        .info-section h5 {
            color: #667eea;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 0.25rem;
        }
        
        .horizontal-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.5rem;
        }
        
        .info-item {
            margin-bottom: 0.5rem;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            display: block;
            margin-bottom: 0.1rem;
            font-size: 0.8rem;
        }
        
        .info-value {
            color: #212529;
            font-size: 0.85rem;
        }
        
        .masked-phone {
            font-family: 'Courier New', monospace;
            background: #f0f0f0;
            padding: 0.15rem 0.4rem;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .amount-display {
            background: #28a745;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 1rem 0;
        }
        
        .security-footer {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 0.75rem;
            text-align: center;
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 1rem;
        }
        
        .print-actions {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-decoration: none;
            background: white;
            color: #333;
            cursor: pointer;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .btn-outline-success {
            border-color: #28a745;
            color: #28a745;
        }
        
        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <!-- Print Actions (Hidden in Print) -->
    <div class="no-print print-actions">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Print Receipt
        </button>
        <a href="{{ route('receipt.download', $receipt) }}" class="btn btn-outline-success">
            📄 Download PDF
        </a>
        <a href="{{ route('receipt.index') }}" class="btn btn-outline-secondary">
            ← Back to Receipts
        </a>
    </div>

    <!-- RECEIPT CONTENT ONLY -->
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="receipt-title">📧 M-RIGHT DIGITAL RECEIPT</div>
            <div>PHONE E-RECEIPT ACCESSIBLE ONLINE</div>
            <div class="receipt-number">{{ $receipt->receipt_number }}</div>
            <div style="margin-top: 0.5rem;">
                <span class="status-badge status-{{ strtolower($receipt->status) }}">
                    {{ ucfirst($receipt->status) }}
                </span>
                <span class="status-badge status-{{ strtolower($receipt->payment_status) }}">
                    {{ ucfirst($receipt->payment_status) }}
                </span>
                @if($receipt->receipt_type === 'resale')
                    <span class="status-badge status-resale">🔄 Resale</span>
                @endif
            </div>
        </div>

        <!-- Resale Information (Only for resale receipts) -->
        @if($receipt->receipt_type === 'resale' && $receipt->parentReceipt)
        <div class="resale-info">
            <h6>📋 Resale Information</h6>
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

        <!-- Main Information Layout -->
        <div class="horizontal-layout">
            <!-- Left Column -->
            <div>
                <!-- Shop Information -->
                <div class="info-section">
                    <h5>🏪 Shop Information</h5>
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
                    <h5>👤 Customer Information</h5>
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
                    <h5>📱 Phone Details</h5>
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
                                ✅ Enabled
                            @else
                                ❌ Disabled
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="info-section">
                    <h5>📋 Transaction Details</h5>
                    <div class="info-item">
                        <span class="info-label">Type:</span>
                        <span class="info-value">{{ $receipt->receipt_type === 'resale' ? 'Phone Resale' : 'New Phone Sale' }}</span>
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
            <div style="font-size: 0.9rem; opacity: 0.9; margin-top: 0.5rem;">{{ $receipt->amount_in_words }}</div>
        </div>

        <!-- Security Footer -->
        <div class="security-footer">
            <strong>Digital Receipt Verification:</strong> Receipt #{{ $receipt->receipt_number }} • 
            Generated by M-right Digital System • 
            @if($receipt->enable_antitheft)Anti-theft Protected • @endif
            Verify online at support center
        </div>
    </div>
</body>
</html>
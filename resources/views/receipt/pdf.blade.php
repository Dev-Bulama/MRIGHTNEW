<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 15px;
        }
        
        /* Logo Section - Updated positioning */
        .logo-header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            padding-bottom: 8px;
            position: absolute;
            top: 15px; /* Adjust this value to match red box position */
            left: 15px;
            right: 15px;
            border-bottom: none; /* Removed border as it might not be needed */
        }
        
        .logo-item {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: middle;
            padding: 5px;
        }
        
        .logo-image {
            max-height: 30px;
            max-width: 60px;
            object-fit: contain;
            margin-bottom: 3px;
        }
        
        .logo-fallback {
            background: #4a90e2;
            color: white;
            padding: 8px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
            display: inline-block;
            margin-bottom: 3px;
        }
        
        .logo-text {
            font-size: 9px;
            font-weight: bold;
            color: #4a90e2;
            margin-bottom: 1px;
        }
        
        .logo-subtitle {
            font-size: 7px;
            color: #666;
            text-transform: uppercase;
        }
        
        .receipt-header {
            text-align: center;
            background: #4a90e2;
            color: white;
            padding: 15px;
            margin-top: 50px; /* Added space for the logos above */
            margin-bottom: 15px;
            border-radius: 5px;
            position: relative;
        }
        
        .receipt-number {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 8px 0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            margin: 0 3px;
        }
        
        .status-active { background: #d4edda; color: #155724; }
        .status-paid { background: #e74c3c; color: white; }
        .status-pending { background: #ffc107; color: #856404; }
        .status-resale { background: #17a2b8; color: white; }
        
        .resale-info {
            background: #e7f3ff;
            border: 1px solid #b3d7ff;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        
        .resale-info h4 {
            color: #0056b3;
            font-size: 12px;
            margin: 0 0 8px 0;
        }
        
        .info-section {
            border: 1px solid #ddd;
            margin-bottom: 10px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        
        .info-section h3 {
            color: #4a90e2;
            font-size: 12px;
            margin: 0 0 8px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #ddd;
        }
        
        .horizontal-layout {
            display: table;
            width: 100%;
        }
        
        .col-50 {
            display: table-cell;
            width: 50%;
            padding-right: 10px;
            vertical-align: top;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-grid-3 {
            display: table;
            width: 100%;
        }
        
        .info-item {
            display: table-cell;
            width: 33.33%;
            padding-right: 8px;
            margin-bottom: 6px;
        }
        
        .info-item-2 {
            display: table-cell;
            width: 50%;
            padding-right: 8px;
            margin-bottom: 6px;
        }
        
        .info-label {
            font-weight: bold;
            color: #495057;
            font-size: 9px;
            display: block;
            margin-bottom: 2px;
        }
        
        .info-value {
            color: #212529;
            font-size: 10px;
        }
        
        .masked-phone {
            font-family: 'Courier New', monospace;
            background: #f0f0f0;
            padding: 2px 4px;
            border-radius: 2px;
            font-weight: bold;
        }
        
        .amount-display {
            background: #e74c3c;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            margin: 15px 0;
        }
        
        .amount-large {
            font-size: 18px;
            margin: 8px 0;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
        }
        
        p { margin: 6px 0; }
        strong { font-weight: bold; }
        
        /* Print optimization */
        @page { size: A4; margin: 10mm; }
        @media print {
            body { font-size: 10px; padding: 5px; }
            .receipt-header { padding: 10px; margin-bottom: 10px; }
            .info-section { margin-bottom: 8px; padding: 8px; }
            .amount-display { padding: 10px; margin: 10px 0; }
        }
        
        /* Removed the l1 class as it's not needed with new positioning */
    </style>
</head>
<body>
    <!-- Logo Header Section - Moved to top of body -->
    <div class="logo-header">
        <!-- MRight System (Left) -->
        <div class="logo-item">
            @php
                $mrightLogoBase64 = \App\Models\SystemLogo::getMrightLogoBase64();
            @endphp
            @if($mrightLogoBase64)
                <img src="{{ $mrightLogoBase64 }}" alt="MRight Digital Receipt" class="logo-image">
            @else
                <div class="logo-fallback">MRIGHT</div>
            @endif
        </div>
        
        <!-- AMPAT Authority (Right) -->
        <div class="logo-item">
            @php
                $ampatLogoBase64 = \App\Models\SystemLogo::getAmpatLogoBase64();
            @endphp
            @if($ampatLogoBase64)
                <img src="{{ $ampatLogoBase64 }}" alt="AMPAT Authority" class="logo-image">
            @else
                <div class="logo-fallback">AMPAT</div>
            @endif
        </div>
    </div>

    <!-- Receipt Header -->
    <div class="receipt-header">
        
        <h1 style="margin: 0; font-size: 16px;">📧 M-RIGHT DIGITAL RECEIPT</h1>
        <p style="margin: 3px 0;">PHONE E-RECEIPT ACCESSIBLE ONLINE</p>
        <div class="receipt-number">{{ $receipt->receipt_number }}</div>
        <div>
            <span class="status-badge status-{{ strtolower($receipt->status) }}">
                {{ ucfirst($receipt->status) }}
            </span>
            <span class="status-badge status-{{ strtolower($receipt->payment_status) }}">
                {{ ucfirst($receipt->payment_status) }}
            </span>
            @if($receipt->receipt_type === 'resale')
                <span class="status-badge status-resale">Resale</span>
            @endif
        </div>
    </div>

    <!-- Resale Information (Only for resale receipts) -->
    @if($receipt->receipt_type === 'resale' && $receipt->parentReceipt)
    <div class="resale-info">
        <h4>📋 RESALE INFORMATION</h4>
        <div class="info-grid-3">
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

    <!-- Main Content - Two Columns -->
    <div class="horizontal-layout">
        <div class="col-50">
            <!-- Shop Information -->
            <div class="info-section">
                <h3>🏪 SHOP INFORMATION</h3>
                <p><strong>Shop Name:</strong><br>{{ $receipt->shop->shop_name ?? 'N/A' }}</p>
                <p><strong>Owner:</strong><br>{{ $receipt->shop->owner_full_name ?? 'N/A' }}</p>
                <p><strong>Phone:</strong><br>{{ $receipt->shop->business_phone_1 ?? 'N/A' }}</p>
                <p><strong>Location:</strong><br>{{ $receipt->shop->state ?? 'N/A' }}, {{ $receipt->shop->local_government ?? 'N/A' }}</p>
            </div>

            <!-- Customer Information -->
            <div class="info-section">
                <h3>👤 CUSTOMER INFORMATION</h3>
                <p><strong>Name:</strong><br>{{ $receipt->customer_name }}</p>
                <p><strong>Phone:</strong><br>
                    <span class="masked-phone">{{ substr($receipt->customer_phone, 0, -3) . '***' }}</span>
                </p>
                <p><strong>Email:</strong><br>{{ $receipt->customer_email ?? 'Not provided' }}</p>
            </div>
        </div>

        <div class="col-50">
            <!-- Phone Details -->
            <div class="info-section">
                <h3>📱 PHONE DETAILS</h3>
                <p><strong>Model:</strong><br>{{ $receipt->phone_name }}</p>
                <p><strong>Color:</strong><br>{{ $receipt->phone_color }}</p>
                <p><strong>Serial Number:</strong><br>{{ $receipt->phone_serial_number }}</p>
                <p><strong>Anti-theft:</strong><br>
                    @if($receipt->enable_antitheft)
                        <span style="background: #e74c3c; color: white; padding: 2px 6px; border-radius: 3px; font-size: 9px;">ENABLED</span>
                    @else
                        <span style="background: #6c757d; color: white; padding: 2px 6px; border-radius: 3px; font-size: 9px;">DISABLED</span>
                    @endif
                </p>
            </div>

            <!-- Transaction Details -->
            <div class="info-section">
                <h3>📋 TRANSACTION DETAILS</h3>
                <p><strong>Type:</strong><br>{{ $receipt->receipt_type === 'resale' ? 'Phone Resale' : 'New Phone Sale' }}</p>
                <p><strong>Date:</strong><br>{{ $receipt->created_at->format('M d, Y g:i A') }}</p>
                <p><strong>Generated By:</strong><br>{{ $receipt->user->name ?? 'System' }}</p>
                @if($receipt->notes)
                <p><strong>Notes:</strong><br>{{ $receipt->notes }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Amount Display -->
    <div class="amount-display">
        <div class="amount-large">₦{{ number_format($receipt->amount ?? 0, 2) }}</div>
        <div style="font-size: 11px; opacity: 0.9;">{{ $receipt->amount_in_words ?? 'Zero Naira Only' }}</div>
    </div>

    <!-- Receipt Portal Login Section -->
    @if($receipt->resale_pin)
    <div style="background:#F8FAFC;border:1px solid #E8ECEF;border-radius:6px;padding:10px;margin-bottom:10px;font-size:10px;">
        <strong style="color:#C0392B;">📱 Receipt Portal Login:</strong>
        Access your Phone Anti-Theft Digital Receipt at any time using:<br>
        Phone Number: <strong>{{ $receipt->customer_phone }}</strong> &nbsp;|&nbsp;
        Resale PIN: <strong>{{ $receipt->resale_pin }}</strong><br>
        <span style="color:#7F8C8D;">Portal URL: {{ url('/receipt-portal/login') }}</span>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Phone Anti-Theft Digital Receipt Verification:</strong> Receipt #{{ $receipt->receipt_number }}</p>
        <p>This is an official Phone Anti-Theft Digital Receipt issued by M-Right Anti-Theft System.</p>
        <p>Generated by M-Right Anti-Theft System •
        @if($receipt->enable_antitheft)Anti-Theft Protected • @endif
        Verify online at {{ url('/') }}</p>
        <p style="font-size: 8px; margin-top: 5px;">
            <strong>Verification URL:</strong> {{ route('public.receipt.verify', $receipt->receipt_number) }}
        </p>
    </div>
</body>
</html>
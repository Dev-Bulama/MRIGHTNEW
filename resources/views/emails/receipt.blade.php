<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Receipt - {{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .receipt-number {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 10px 0;
        }
        .content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .amount-display {
            background: #28a745;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }
        .resale-info {
            background: #e7f3ff;
            border: 1px solid #b3d7ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📧 M-right Digital Receipt</h1>
        <p>PHONE E-RECEIPT ACCESSIBLE ONLINE</p>
        <div class="receipt-number">{{ $receipt->receipt_number }}</div>
    </div>

    <div class="content">
        <h2>Hello {{ $receipt->customer_name }},</h2>
        <!--<p>Thank you for your {{ $receipt->receipt_type === 'resale' ? 'phone resale' : 'phone purchase' }}! Your digital receipt has been generated and is attached to this email.</p>-->
        <!--<p>Thank you for using M-right Digital Receipt! Your digital receipt has been generated and is attached to this email.</p>-->
        <p>Thank you for using M-right Digital Receipt! Your {{ $receipt->receipt_type === 'resale' ? 'phone resale' : 'phone purchase' }}! Your digital receipt has been generated and is attached to this email.</p>


        @if($receipt->receipt_type === 'resale' && $receipt->parentReceipt)
        <div class="resale-info">
            <h3>📋 Resale Information</h3>
            <p><strong>Previous Owner:</strong> {{ $receipt->parentReceipt->customer_name }}</p>
            <p><strong>Original Sale Date:</strong> {{ $receipt->parentReceipt->created_at->format('M d, Y') }}</p>
            <p>This phone was previously owned and has been properly transferred to you.</p>
        </div>
        @endif

        <h3>📱 Transaction Details</h3>
        <div class="info-row">
            <span class="info-label">Phone Model:</span>
            <span>{{ $receipt->phone_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Color:</span>
            <span>{{ $receipt->phone_color }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Serial Number:</span>
            <span>{{ $receipt->phone_serial_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Transaction Date:</span>
            <span>{{ $receipt->created_at->format('M d, Y g:i A') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Shop:</span>
            <span>{{ $receipt->shop->shop_name ?? 'N/A' }}</span>
        </div>

        <div class="amount-display">
            Sale Amount: ₦{{ number_format($receipt->amount, 2) }}
        </div>

        @if($receipt->enable_antitheft)
        <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin: 15px 0;">
            <h4>🛡️ Anti-theft Protection Enabled</h4>
            <p>Your device is protected by M-right anti-theft system. Keep your receipt safe for future reference.</p>
        </div>
        @endif

        <h3>🏪 Shop Information</h3>
        <div class="info-row">
            <span class="info-label">Shop Name:</span>
            <span>{{ $receipt->shop->shop_name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Owner:</span>
            <span>{{ $receipt->shop->owner_full_name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Contact:</span>
            <span>{{ $receipt->shop->business_phone_1 ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Location:</span>
            <span>{{ $receipt->shop->state ?? 'N/A' }}, {{ $receipt->shop->local_government ?? 'N/A' }}</span>
        </div>
    </div>

    <!--<div style="text-align: center; margin: 20px 0;">-->
    <!--    <a href="{{ route('receipt.view', $receipt) }}" class="btn">View Receipt Online</a>-->
    <!--</div>-->

    <div class="footer">
        <p><strong>Important:</strong> Keep this receipt safe and your resale code remembered. The resale code is used to add the phone to M-right phone antitheft (M-right com.ng) or to resale.</p>
        <p>This is an automated email from M-right Digital Receipt System.</p>
        <p>For support, contact your shop or visit our help center.</p>
    </div>
</body>
</html>
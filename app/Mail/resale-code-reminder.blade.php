<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resale Code Reminder - M-right Digital</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .email-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #0d8abc, #4dabf7);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .header .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .content {
            padding: 2rem;
        }
        
        .greeting {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            color: #495057;
        }
        
        .security-alert {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: center;
        }
        
        .security-alert .icon {
            color: #856404;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .phone-details {
            background: #e8f4f8;
            border: 1px solid #0d8abc;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .phone-details h3 {
            color: #0d8abc;
            margin-top: 0;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(13, 138, 188, 0.1);
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #0d8abc;
        }
        
        .detail-value {
            color: #495057;
            font-weight: 500;
        }
        
        .masked-code {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-align: center;
            margin: 1.5rem 0;
            font-family: 'Courier New', monospace;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 2px;
        }
        
        .instructions {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .instructions h4 {
            color: #0c5460;
            margin-top: 0;
            margin-bottom: 1rem;
        }
        
        .instructions ol {
            color: #0c5460;
            margin: 0;
            padding-left: 1.5rem;
        }
        
        .instructions li {
            margin-bottom: 0.5rem;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 2rem;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        
        .footer p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .footer .logo {
            font-weight: bold;
            color: #0d8abc;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .button {
            display: inline-block;
            background: #0d8abc;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin: 1rem 0;
            transition: background-color 0.3s ease;
        }
        
        .button:hover {
            background: #0a6b94;
            color: white;
        }
        
        .warning-text {
            color: #856404;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .content {
                padding: 1.5rem;
            }
            
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .detail-label {
                margin-bottom: 0.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">🔐</div>
            <h1>Resale Code Reminder</h1>
            <p style="margin: 0; opacity: 0.9;">M-right Digital Receipt System</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello <strong>{{ $customer_name }}</strong>,
            </div>
            
            <p>
                Someone has requested a resale code reminder for a phone originally purchased by you. 
                For your security, we're providing a partial hint of your resale code.
            </p>
            
            <!-- Security Alert -->
            <div class="security-alert">
                <div class="icon">🛡️</div>
                <div class="warning-text">Security Notice</div>
                <p style="margin: 0.5rem 0 0 0; color: #856404;">
                    If you didn't request this reminder, please contact us immediately.
                </p>
            </div>
            
            <!-- Phone Details -->
            <div class="phone-details">
                <h3>📱 Phone Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Phone Model:</span>
                    <span class="detail-value">{{ $phone_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Serial Number:</span>
                    <span class="detail-value">{{ $phone_serial }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Receipt Number:</span>
                    <span class="detail-value">{{ $receipt_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Original Shop:</span>
                    <span class="detail-value">{{ $shop_name }}</span>
                </div>
            </div>
            
            <!-- Masked Resale Code -->
            <div class="masked-code">
                Your Resale Code Hint: {{ $masked_code }}
            </div>
            
            <!-- Instructions -->
            <div class="instructions">
                <h4>🔍 How to Use This Hint</h4>
                <ol>
                    <li>Look at the hint above - it shows the first and last characters of your resale code</li>
                    <li>The asterisks (*) represent the hidden middle characters</li>
                    <li>Try to remember what you used for the middle part of your code</li>
                    <li>Return to the resale form and enter your complete resale code</li>
                    <li>If you still can't remember, check your original receipt copy</li>
                </ol>
            </div>
            
            <p>
                <strong>Important:</strong> Keep your resale code secure and only share it with authorized dealers 
                when you're ready to sell your phone. This code is required to transfer ownership and maintain 
                anti-theft protection.
            </p>
            
            <div style="text-align: center; margin: 2rem 0;">
                <a href="{{ config('app.url') }}/receipt/resale" class="button">
                    Continue with Resale Process
                </a>
            </div>
            
            <p style="color: #6c757d; font-size: 0.9rem; margin-top: 2rem;">
                <strong>Need Help?</strong> If you're having trouble with the resale process or have questions 
                about your resale code, please contact our support team.
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="logo">M-right Digital Receipt System</div>
            <p>
                This email was sent to {{ $customer_name }} at the request of a verified dealer.<br>
                For security questions, contact support immediately.
            </p>
            <p style="margin-top: 1rem; color: #adb5bd; font-size: 0.8rem;">
                This is an automated security email. Please do not reply directly to this message.
            </p>
        </div>
    </div>
</body>
</html>
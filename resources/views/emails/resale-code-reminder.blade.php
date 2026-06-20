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
        
        .content {
            padding: 2rem;
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
        
        .phone-details {
            background: #e8f4f8;
            border: 1px solid #0d8abc;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 2rem;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔐 Resale Code Reminder</h1>
            <p style="margin: 0; opacity: 0.9;">M-right Digital Receipt System</p>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $customer_name }}</strong>,</p>
            
            <p>
                Someone has requested a resale code reminder for a phone originally purchased by you. 
                For your security, we're providing a partial hint of your resale code.
            </p>
            
            <div class="phone-details">
                <h3>📱 Phone Details</h3>
                <p><strong>Phone Model:</strong> {{ $phone_name }}</p>
                <p><strong>Serial Number:</strong> {{ $phone_serial }}</p>
                <p><strong>Receipt Number:</strong> {{ $receipt_number }}</p>
                <p><strong>Original Shop:</strong> {{ $shop_name }}</p>
            </div>
            
            <div class="masked-code">
                Your Resale Code Hint: {{ $masked_code }}
            </div>
            
            <p>
                <strong>How to use this hint:</strong>
            </p>
            <ul>
                <li>The hint shows the first and last characters of your resale code</li>
                <li>The asterisks (*) represent the hidden middle characters</li>
                <li>Try to remember what you used for the middle part</li>
                <li>Enter your complete resale code in the resale form</li>
            </ul>
            
            <p>
                <strong>Important:</strong> Keep your resale code secure and only share it with authorized dealers.
            </p>
        </div>
        
        <div class="footer">
            <p><strong>M-right Digital Receipt System</strong></p>
            <p>This is an automated security email. Please do not reply.</p>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resale Code Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="margin: 0; font-size: 24px;">🔐 Resale Code Reminder</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">M-right Digital Receipt System</p>
    </div>
    
    <div style="background: white; padding: 30px; border: 1px solid #ddd; border-top: none;">
        <p>Dear {{ $customer_name }},</p>
        
        <p>You requested a reminder for your resale code. Here are the details:</p>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #495057;">📱 Phone Details</h3>
            <p><strong>Model:</strong> {{ $phone_name }}</p>
            <p><strong>Serial Number:</strong> {{ $phone_serial }}</p>
            <p><strong>Receipt Number:</strong> {{ $receipt_number }}</p>
        </div>
        
        <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #856404;">🔐 Resale Code Hint</h3>
            <p>Your resale code starts with: <strong style="font-size: 18px; color: #495057;">{{ $masked_code }}</strong></p>
            <p><small>For security, we only show partial information. The complete code was provided when you received your original receipt.</small></p>
        </div>
        
        <div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0;"><strong>💡 Need the complete code?</strong> Check your original receipt or contact our support team.</p>
        </div>
        
        <p>If you didn't request this reminder, please ignore this email.</p>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
        
        <div style="text-align: center; color: #6c757d; font-size: 14px;">
            <p>{{ $shop_name }}<br>
            Secure Digital Receipt System</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
@extends('emails.layout')

@section('title', 'Resale Code Search Alert')

@section('content')
<h2>🔍 Someone trying to resale your phone</h2>

<p>Hello {{ $customer_name }},</p>

<div class="alert alert-warning">
    <h3>⚠️ Resale Code Search Alert</h3>
<p>We discover that your resale code has been accessed. If it's you reselling the phone, ignore the message you will receive about that shortly 

If it's NOT you, check the details in the resale receipt that you would get shortly to call the attention of the shop Owner quickly. The resale receipt also contains the detail of the person who bought the phone for further action.</p>
    <!--<p>Someone is attempting to resale your phone using your resale code. This email is sent for your security and awareness.</p>-->
</div>

<h3>Your Phone Details:</h3>
<div class="alert alert-info">
    <strong>Phone:</strong> {{ $phone_name }}<br>
    <strong>Serial Number:</strong> {{ $phone_serial }}<br>
    <strong>Original Receipt:</strong> {{ $receipt_number }}<br>
    <strong>Purchase Date:</strong> {{ $original_date }}
</div>

<h3>Shop Searching Your Code:</h3>
<div class="alert alert-success">
    <strong>Shop Name:</strong> {{ $searching_shop_name }}<br>
    <strong>Address:</strong> {{ $searching_shop_address }}<br>
    <strong>Phone:</strong> {{ $searching_shop_phone }}<br>
    <strong>Search Time:</strong> {{ $search_time }}<br>
    <strong>Location:</strong> {{ $search_location }}
</div>

<h3>What This Means:</h3>
<ul>
    <li>🔄 <strong>If you're selling your phone:</strong> This is normal - the buyer's shop is verifying your phone for resale</li>
    <li>🚨 <strong>If you're NOT selling:</strong> Someone may have your resale code without permission</li>
    <li>📞 <strong>Unsure?</strong> Contact the shop above to verify the transaction</li>
</ul>

<h3>Important Security Notice:</h3>
<div class="alert alert-warning">
    <strong>Keep Your Resale Code Safe!</strong><br>
    Only share your resale code when you're genuinely selling your phone. This code allows ownership transfer in our system.
</div>

<h3>Next Steps:</h3>
<ol>
    <li><strong>If authorized:</strong> No action needed - your phone ownership will transfer once the resale is completed</li>
    <li><strong>If unauthorized:</strong> Contact the shop immediately using the details above</li>
    <li><strong>For support:</strong> Reach out to us using the contact information below</li>
</ol>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ config('app.url') }}/search" class="btn">Track Your Receipt Status</a>
</div>

<h3>Need Help?</h3>
<p>If you have questions or concerns about this resale search:</p>
<ul>
    <li>📧 Email: <a href="mailto:info@skillychat.com.ng">info@skillychat.com.ng</a></li>
    <li>🌐 Visit: <a href="{{ config('app.url') }}">{{ config('app.url') }}</a></li>
    <li>📞 Contact your market chairman or AMPAT Executive</li>
    <li>🏪 Contact the searching shop directly: {{ $searching_shop_phone }}</li>
</ul>

<p><strong>This is an automated security notification to keep you informed about your phone's resale activity.</strong></p>
@endsection
@extends('emails.layout')

@section('title', 'Shop ' . ucfirst($status))

@section('content')
<h2>Shop Registration Update</h2>

<p>Hello {{ $shop->owner_full_name }},</p>

@if($status === 'approved')
    <div class="alert alert-success">
        <h3>🎉 Congratulations! Your shop has been approved!</h3>
        <p><strong>Shop Name:</strong> {{ $shop->shop_name }}</p>
        <p><strong>Business Address:</strong> {{ $shop->business_address }}</p>
    </div>
    
    <p>Great news! Your shop registration has been approved and you can now start generating digital receipts for your customers.</p>
    
    <h3>What You Can Do Now:</h3>
    <ul>
        <li>✅ Generate digital receipts for customers</li>
        <li>✅ Manage your shop profile and settings</li>
        <li>✅ View payment analytics and reports</li>
        <li>✅ Export customer data and receipts</li>
    </ul>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $dashboardUrl }}" class="btn">Access Your Dashboard</a>
    </div>
    
@else
    <div class="alert alert-warning">
        <h3>Shop Registration Requires Attention</h3>
        <p><strong>Shop Name:</strong> {{ $shop->shop_name }}</p>
    </div>
    
    <p>We've reviewed your shop registration and need some additional information or corrections before approval.</p>
    
    @if($message)
        <div class="alert alert-info">
            <strong>Admin Message:</strong><br>
            {{ $message }}
        </div>
    @endif
    
    <h3>Next Steps:</h3>
    <ol>
        <li>Review your shop information</li>
        <li>Make any necessary corrections</li>
        <li>Resubmit for approval</li>
    </ol>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $shopUrl }}" class="btn">Edit Shop Details</a>
    </div>
@endif

<h3>Support Information:</h3>
<p>If you have questions about your shop registration:</p>
<ul>
    <li>📧 Email: <a href="mailto:info@skillychat.com.ng">info@skillychat.com.ng</a></li>
    <li>📞 Contact your market chairman</li>
    <li>🌐 Visit: <a href="{{ config('app.url') }}">M-right Portal</a></li>
</ul>
@endsection
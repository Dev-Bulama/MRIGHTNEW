@extends('emails.layout')

@section('title', 'Welcome to M-right')

@section('content')
<h2>Welcome, {{ $user->first_name }}! 🎉</h2>

<p>Thank you for registering with M-right Digital Receipt System. Your account has been successfully created!</p>

<div class="alert alert-success">
    <strong>Account Details:</strong><br>
    <strong>Name:</strong> {{ $user->name }}<br>
    <strong>Email:</strong> {{ $user->email }}<br>
    <strong>Account Type:</strong> {{ ucfirst(str_replace('_', ' ', $user->user_type)) }}<br>
    <strong>Status:</strong> {{ ucfirst($user->status) }}
</div>

@if($user->isShopOwner())
    <h3>Next Steps for Shop Owners:</h3>
    <ol>
        <li><strong>Login to your account</strong> using the button below</li>
        <li><strong>Complete your shop registration</strong> with business details</li>
        <li><strong>Wait for admin approval</strong> (usually within 24 hours)</li>
        <li><strong>Start generating digital receipts</strong> for your customers</li>
    </ol>
    
    <div class="alert alert-info">
        <strong>Important:</strong> Your shop needs to be approved by our admin team before you can generate receipts. You'll receive another email once approved.
    </div>
@else
    <h3>What's Next:</h3>
    <p>You can now access your customer dashboard to view receipts and manage your profile.</p>
@endif

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ $loginUrl }}" class="btn">Login to Your Account</a>
</div>

<h3>Need Help?</h3>
<p>If you have any questions or need assistance:</p>
<ul>
    <li>📧 Email: <a href="mailto:info@skillychat.com.ng">info@skillychat.com.ng</a></li>
    <li>🌐 Visit: <a href="{{ config('app.url') }}">{{ config('app.url') }}</a></li>
    <li>📞 Contact your market chairman or AMPAT Executive</li>
</ul>

<p><strong>Welcome to the future of digital receipts!</strong></p>
@endsection
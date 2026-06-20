@extends('layouts.app')

@section('title', 'Contact User')
@section('page-title', 'Contact: ' . $user->full_name)
@section('page-description', 'Send message to user via email or SMS')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>Send Message to {{ $user->full_name }}
                    </h5>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6>Contact Information:</h6>
                        <ul class="mb-0">
                            <li><strong>Email:</strong> {{ $user->email }}</li>
                            @if($user->phone_number)
                                <li><strong>Primary Phone:</strong> {{ $user->phone_number }}</li>
                            @endif
                            @if($user->secondary_phone)
                                <li><strong>Secondary Phone:</strong> {{ $user->secondary_phone }}</li>
                            @endif
                        </ul>
                    </div>
                    
                    <form method="POST" action="{{ route('admin.users.send-message', $user) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   name="subject" value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      name="message" rows="6" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Send Options</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="send_email" 
                                       id="sendEmail" value="1" checked>
                                <label class="form-check-label" for="sendEmail">
                                    Send via Email
                                </label>
                            </div>
                            @if($user->phone_number)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="send_sms" 
                                           id="sendSms" value="1">
                                    <label class="form-check-label" for="sendSms">
                                        Send via SMS
                                    </label>
                                </div>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Users
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
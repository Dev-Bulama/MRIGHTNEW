@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-lg border-0" style="border-radius: 15px;">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="logo bg-gradient-primary rounded-3 d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-key text-white fs-4"></i>
                            </div>
                            <h2 class="fw-bold text-dark mb-2">Reset Password</h2>
                            <p class="text-muted">Enter your email to receive a password reset link</p>
                        </div>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autofocus
                                           placeholder="Enter your email address">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">We'll send a password reset link to this email address.</small>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Send Reset Link
                                </button>
                            </div>

                            <!-- Back to Login -->
                            <div class="text-center">
                                <p class="text-muted mb-0">
                                    Remember your password? 
                                    <a href="{{ route('login') }}" class="text-decoration-none fw-medium">
                                        <i class="fas fa-arrow-left me-1"></i>Back to Login
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Help Text -->
                <div class="text-center mt-4">
                    <small class="text-white opacity-75">
                        If you don't receive an email, check your spam folder<br>
                        or contact support for assistance.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
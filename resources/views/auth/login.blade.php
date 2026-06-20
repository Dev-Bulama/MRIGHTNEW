@extends('layouts.app')

@section('title', 'Login')
@section('description', 'Sign in to your Phone Anti-Theft Digital Receipt account')

@section('content')
<!-- Session Expiration Message -->
@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Session Expired:</strong> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
<!-- Add this in the <head> section of login.blade.php -->
<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
<style></style>
<div class="auth-container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="auth-wrapper">
        <!-- Background Pattern -->
        <div class="auth-background"></div>
        
        <div class="container">
            <div class="row justify-content-center min-vh-100 align-items-center">
                <div class="col-xl-5 col-lg-6 col-md-7 col-sm-9">
                    <div class="auth-card card shadow-lg border-0">
                        <!-- Card Header -->
                        <div class="card-header text-center border-0 bg-transparent pb-0">
                          <div class="auth-logo mb-4">
    <div class="logo-container d-inline-flex align-items-center justify-content-center rounded-4 mb-3"
         style="width: 100px; height: 80px; background: #f8f9fa; border: 2px solid #e9ecef;">
        @php
            $mrightLogo = \App\Models\SystemLogo::getMrightLogo();
            $ampatLogo = \App\Models\SystemLogo::getAmpatLogo();
        @endphp
        
        @if($mrightLogo)
            <img src="{{ $mrightLogo->logo_url }}" 
                 alt="Phone Anti-Theft Digital Receipt" 
                 style="max-width: 90px; max-height: 70px; object-fit: contain;">
        @else
            <div class="bg-gradient-primary rounded-3 d-flex align-items-center justify-content-center text-white"
                 style="width: 70px; height: 60px;">
                <i class="fas fa-receipt" style="font-size: 2rem;"></i>
            </div>
        @endif
    </div>
    
    <!-- @if($ampatLogo)
    <div class="text-center mb-2">
        <img src="{{ $ampatLogo->logo_url }}" 
             alt="AMPAT Authority" 
             style="max-width: 60px; max-height: 30px; object-fit: contain; opacity: 0.8;">
    </div>
    @endif -->
    
    <h2 class="fw-bold text-dark mb-1">Welcome Back</h2>
    <p class="text-muted mb-0">Sign in to your Phone Anti-Theft Digital Receipt account</p>
</div>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="card-body px-4 pb-4">
                            <!-- Session Status -->
                            @include('partials.alerts')
                            
                            <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
                                @csrf
                                
                                <!-- Email Address -->
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label fw-medium">
                                        <i class="fas fa-envelope me-2 text-muted"></i>Email Address
                                    </label>
                                    <input id="email" 
                                           type="email" 
                                           name="email" 
                                           class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autocomplete="email" 
                                           autofocus
                                           placeholder="Enter your email address">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <!-- Password -->
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label fw-medium">
                                        <i class="fas fa-lock me-2 text-muted"></i>Password
                                    </label>
                                    <div class="input-group">
                                        <input id="password" 
                                               type="password" 
                                               name="password" 
                                               class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                               required 
                                               autocomplete="current-password"
                                               placeholder="Enter your password">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Remember Me & Forgot Password -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label text-muted" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                    
                                    @if (Route::has('password.request'))
                                        <a class="text-decoration-none text-primary" href="{{ route('password.request') }}">
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Sign In
                                    </button>
                                </div>
                                
                                <!-- Register Link -->
                                <div class="text-center">
                                    <p class="text-muted mb-0">
                                        Don't have an account? 
                                        <a href="{{ route('register') }}" class="text-decoration-none text-primary fw-medium">
                                            Create one here
                                        </a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Features Section -->
                    <div class="features-section mt-4">
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="feature-item text-center">
                                    <div class="feature-icon bg-success text-white rounded-3 d-inline-flex align-items-center justify-content-center mb-2"
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <h6 class="small fw-medium text-dark">Secure</h6>
                                    <p class="small text-muted mb-0">Bank-level security</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="feature-item text-center">
                                    <div class="feature-icon bg-info text-white rounded-3 d-inline-flex align-items-center justify-content-center mb-2"
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <h6 class="small fw-medium text-dark">Mobile Ready</h6>
                                    <p class="small text-muted mb-0">Works on any device</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="feature-item text-center">
                                    <div class="feature-icon bg-warning text-white rounded-3 d-inline-flex align-items-center justify-content-center mb-2"
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <h6 class="small fw-medium text-dark">24/7 Access</h6>
                                    <p class="small text-muted mb-0">Always available</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.auth-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
}

.auth-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(120, 119, 198, 0.2) 0%, transparent 50%);
    animation: floating 20s ease-in-out infinite;
}

@keyframes floating {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-10px) rotate(1deg); }
    66% { transform: translateY(5px) rotate(-1deg); }
}

.auth-wrapper {
    position: relative;
    z-index: 1;
    padding: 2rem 0;
}

.auth-card {
    border-radius: 20px;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
    animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.logo-container {
    position: relative;
    overflow: hidden;
}

.logo-container::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    transition: transform 0.6s ease;
}

.logo-container:hover::before {
    transform: rotate(45deg) translateX(100%);
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 0.875rem 1.25rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.9);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(13, 138, 188, 0.15);
    background: white;
    transform: translateY(-1px);
}

.form-control-lg {
    font-size: 1.1rem;
    padding: 1rem 1.25rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
    border: none;
    border-radius: 12px;
    padding: 0.875rem 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(13, 138, 188, 0.3);
}

.btn-outline-secondary {
    border-color: #e9ecef;
    color: #6c757d;
    border-radius: 0 12px 12px 0;
    border-left: none;
}

.btn-outline-secondary:hover {
    background-color: #f8f9fa;
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.features-section {
    animation: slideInUp 0.8s ease-out;
}

.feature-item {
    transition: transform 0.3s ease;
}

.feature-item:hover {
    transform: translateY(-5px);
}

.feature-icon {
    transition: all 0.3s ease;
}

.feature-item:hover .feature-icon {
    transform: scale(1.1);
}

/* Input group styling */
.input-group .form-control {
    border-radius: 12px 0 0 12px;
}

.input-group .btn {
    border-radius: 0 12px 12px 0;
}

/* Loading state */
.btn-loading {
    position: relative;
    color: transparent;
}

.btn-loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin-left: -10px;
    margin-top: -10px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .auth-wrapper {
        padding: 1rem 0;
    }
    
    .auth-card {
        margin: 1rem;
        border-radius: 16px;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    .logo-container {
        width: 60px !important;
        height: 60px !important;
    }
    
    .logo-container i {
        font-size: 1.5rem !important;
    }
    
    .features-section {
        margin-top: 2rem !important;
    }
    
    .feature-icon {
        width: 40px !important;
        height: 40px !important;
    }
}

/* Form validation styling */
.was-validated .form-control:invalid,
.form-control.is-invalid {
    border-color: var(--danger-color);
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.was-validated .form-control:valid,
.form-control.is-valid {
    border-color: var(--success-color);
}

/* Enhanced focus styles */
.form-control:focus {
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
}

/* Smooth animations */
.fade-in {
    animation: fadeIn 0.6s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePasswordIcon');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            if (type === 'password') {
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        });
    }
    
    // Form submission handling
    const loginForm = document.querySelector('.auth-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            const email = this.querySelector('#email').value.trim();
            const password = this.querySelector('#password').value;
            
            // Basic validation
            if (!email || !password) {
                e.preventDefault();
                showToast('Please fill in all required fields', 'warning');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                showToast('Please enter a valid email address', 'warning');
                return;
            }
            
            // Show loading state
            setLoading(submitButton, true);
            
            // Prevent double submission
            submitButton.disabled = true;
            
            // Re-enable button if form submission fails (network error, etc.)
            setTimeout(() => {
                setLoading(submitButton, false);
                submitButton.disabled = false;
            }, 10000);
        });
    }
    
    // Auto-focus first empty field
    const firstEmptyField = loginForm.querySelector('input:not([value]):not([readonly]):not([disabled])');
    if (firstEmptyField) {
        firstEmptyField.focus();
    }
    
    // Add floating label effect
    const formInputs = document.querySelectorAll('.form-control');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Check if input has value on page load
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });
    
    // Remember me persistence
    const rememberCheckbox = document.getElementById('remember');
    const emailInput = document.getElementById('email');
    
    // Load remembered email
    if (localStorage.getItem('rememberedEmail')) {
        emailInput.value = localStorage.getItem('rememberedEmail');
        rememberCheckbox.checked = true;
    }
    
    // Save/remove email based on remember me
    if (rememberCheckbox) {
        rememberCheckbox.addEventListener('change', function() {
            if (this.checked && emailInput.value) {
                localStorage.setItem('rememberedEmail', emailInput.value);
            } else {
                localStorage.removeItem('rememberedEmail');
            }
        });
    }
    
    // Update stored email when user types
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            if (rememberCheckbox.checked) {
                localStorage.setItem('rememberedEmail', this.value);
            }
        });
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Enter to submit form
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        const form = document.querySelector('.auth-form');
        if (form) {
            form.submit();
        }
    }
});
</script>
@endsection
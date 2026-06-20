@extends('layouts.app')

@section('title', 'Create Account')

@section('content')

<div class="min-vh-100 d-flex align-items-center justify-content-center py-4" 
     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-lg border-0" style="border-radius: 15px;">
                    <div class="card-body p-5">
                        <!-- Header -->
                      <div class="text-center mb-4">
    <div class="logo-container d-inline-flex align-items-center justify-content-center rounded-3 mb-3"
         style="width: 80px; height: 60px; background: #f8f9fa; border: 1px solid #e9ecef;">
        @php
            $mrightLogo = \App\Models\SystemLogo::getMrightLogo();
        @endphp
        
        @if($mrightLogo)
            <img src="{{ $mrightLogo->logo_url }}" 
                 alt="M-right" 
                 style="max-width: 75px; max-height: 55px; object-fit: contain;">
        @else
            <div class="bg-gradient-primary rounded-3 d-flex align-items-center justify-content-center text-white"
                 style="width: 50px; height: 50px;">
                <i class="fas fa-receipt fs-4"></i>
            </div>
        @endif
    </div>
    <h2 class="fw-bold text-dark mb-2">Create Account</h2>
    <p class="text-muted">Join M-right Digital Receipt System</p>
</div>

                        <!-- Registration Form -->
                        <form method="POST" action="{{ route('register') }}" id="registrationForm">
                            @csrf
                            
                            <!-- Name Fields -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="first_name" class="form-label">Full Name</label>
                                    <input type="text"
       class="form-control @error('full_name') is-invalid @enderror" 
       id="full_name" 
       name="full_name" 
       value="{{ old('full_name') }}" 
       required 
       autocomplete="name" placeholder="Enter your name">
       
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" 
                                           class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ old('last_name') }}" 
                                           required 
                                           autocomplete="family-name"
                                           placeholder="Doe">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> -->
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
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
       autocomplete="email" placeholder="Enter your Email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                       <!-- Phone Numbers -->
<div class="row mb-3">
    <div class="col-md-6">
        <label for="phone_number" class="form-label">
            Phone Number<span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-phone text-muted"></i>
            </span>
            <input type="tel" 
   class="form-control @error('phone_number') is-invalid @enderror" 
   id="phone_number" 
   name="phone_number" 
   value="{{ old('phone_number') }}" 
   required 
   autocomplete="tel"
   maxlength="11"
   placeholder="08012345678">
            @error('phone_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted">Your primary contact number</small>
    </div>
    <div class="col-md-6">
        <label for="phone_number_confirmation" class="form-label">
            Confirm Phone Number<span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-phone-square text-muted"></i>
            </span>
            <input type="tel" 
   class="form-control @error('phone_number_confirmation') is-invalid @enderror" 
   id="phone_number_confirmation" 
   name="phone_number_confirmation" 
   value="{{ old('phone_number_confirmation') }}" 
   required 
   maxlength="11"
   placeholder="Re-enter phone number">
            @error('phone_number_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="invalid-feedback" id="phone-mismatch-error" style="display: none;">
                Phone numbers do not match
            </div>
        </div>
        <small class="text-muted">Re-enter your phone number to confirm</small>
    </div>
</div>

                            <!-- User Type -->
                            <div class="mb-3">
                                <label for="user_type" class="form-label">Account Type</label>
                                <select class="form-select @error('user_type') is-invalid @enderror" 
                                        id="user_type" 
                                        name="user_type" 
                                        required>
                                    <option value="">Select Account Type</option>
                                    <!-- <option value="customer" {{ old('user_type') === 'customer' ? 'selected' : '' }}>
                                        Customer - Access receipts and purchase history
                                    </option> -->
                                    <option value="shop_owner" {{ old('user_type') === 'shop_owner' ? 'selected' : '' }}>
                                        Shop Owner - Generate receipts for customers
                                    </option>
                                </select>
                                @error('user_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           autocomplete="new-password"
                                           placeholder="Enter strong password">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePasswordVisibility('password')">
                                        <i class="fas fa-eye" id="password-toggle-icon"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Minimum 8 characters with letters, numbers and symbols</small>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required 
                                           autocomplete="new-password"
                                           placeholder="Confirm your password">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePasswordVisibility('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation-toggle-icon"></i>
                                    </button>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Terms and Privacy -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" 
                                           type="checkbox" 
                                           id="terms" 
                                           name="terms" 
                                           value="1" 
                                           required
                                           {{ old('terms') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="terms">
                                        I agree to the 
                                        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#termsModal">
                                            Terms of Service & Privacy Policy
                                        </a> 
                                        
                                        <!--<a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#privacyModal">-->
                                        <!--    Privacy Policy-->
                                        <!--</a>-->
                                    </label>
                                    @error('terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner"></span>
                                    <i class="fas fa-user-plus me-2"></i>
                                    Create Account
                                </button>
                            </div>

                            <!-- Login Link -->
                            <div class="text-center">
                                <p class="text-muted mb-0">
                                    Already have an account? 
                                    <a href="{{ route('login') }}" class="text-decoration-none fw-medium">
                                        Sign In
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="text-center mt-4">
                    <small class="text-white opacity-75">
                        Secure • Verified • Trusted<br>
                        Digital Receipt System for Modern Businesses
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<!--<div class="modal fade" id="termsModal" tabindex="-1">-->
<!--    <div class="modal-dialog modal-lg">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header">-->
<!--                <h5 class="modal-title">Terms of Service</h5>-->
<!--                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>-->
<!--            </div>-->
<!--            <div class="modal-body">-->
<!--                <p>By creating an account with M-right Digital Receipt System, you agree to:</p>-->
<!--                <p>Terms of service and privacy policy: -->

<!-- Terms of Service: -->
<!--M-right explained the purpose of this site in detail in the frequency ask questions (FAQ) under 'help and support' page that is accessible in the right hand side drop down menu of every user account. Users or Shop Owners are advised to go through all the questions and answers there in detail to accept using the side or decline their accounts. M-right would never be liable in any way for any contrary usage or claims. -->

<!--It is also responsibility of the user to keep  account login detail safe and to be logging out everytime as M-right would never be liable for any insecurity issues caused by user negligence.-->

<!-- Privacy Policy -->
<!--M-right is committed to protect the privacy of user data it collects and use it solely for the intended purposes of system operations in order to serve you better. -->

<!--The company has started  and passed the preliminary stage of data privacy training with the intermediary of the authority concerned and planning to start the full training of it's staff and certification shortly. Therefore users are assured data privacy policy compliance.-->

<!-- To comply with internal and external policies, M-right reserves the following rights:-->

<!--1.   M-right reserve right to deactivate user account for unethical usage-->

<!--2. User data may be   released to authorities to resolving stolen phone disputes. User data may also be retrieve as report or statistical record to address internal and national problems. -->

<!--3. This terms and condition with the privacy policy are subject to review over time and the company reserves the right to discontinue rendering thid service at anytime for any reason that may not be anticipated and explain now. -->

<!--4. In case of system or infrastructure failure that beyond the control of the company. M-right would not be liable to provide the affected data or record in the system.-->

<!-- Meaning definition of terms:-->
 
<!-- M-right is the name of the company rendering this services. It is called *company*in some of the clauses in this terms and conditions of services.-->

<!-- User is any individual or an entity that creates account in this system -->

<!-- Customer is a third party to whom the shop Owners generate receipt for. -->

<!-- System, site,  portal, application: Are all the all referring to the web based software.-->

<!--User data, record or user information stands for raw or organized  facts of the user.-->

<!--Therefore, By clicking 'I agree' in the registration form, the user agreed with this terms, conditions and the privacy policy.</p>-->
                <!--<ul>-->
                <!--    <li>Provide accurate and truthful information</li>-->
                <!--    <li>Use the service for legitimate business purposes only</li>-->
                <!--    <li>Maintain the security of your account credentials</li>-->
                <!--    <li>Comply with all applicable laws and regulations</li>-->
                <!--    <li>Respect the intellectual property rights of the platform</li>-->
                <!--</ul>-->
<!--                <p>We reserve the right to suspend or terminate accounts that violate these terms.</p>-->
<!--            </div>-->
<!--            <div class="modal-footer">-->
<!--                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="termsModalLabel">Terms of Service & Privacy Policy</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <p class="mb-3">By creating an account with <strong>M-right Digital Receipt System</strong>, you agree to the following terms:</p>

        <h6 class="fw-bold mt-3">📌 Terms of Service</h6>
        <p>
          M-right explains the purpose of this site in detail in the <em>Frequently Asked Questions (FAQ)</em> section, available under the 
          <strong>Help & Support</strong> page in the right-hand dropdown menu of every user account. 
        </p>
        <p>
          Users and Shop Owners are advised to carefully review the questions and answers before deciding to continue using the system. 
          M-right shall not be held liable for any contrary usage or claims. 
        </p>
        <p>
          It is the responsibility of the user to safeguard login details and always log out after use. M-right is not liable for any security issues caused by user negligence.
        </p>

        <h6 class="fw-bold mt-4">🔒 Privacy Policy</h6>
        <p>
          M-right is committed to protecting the privacy of user data. Information collected is used solely for system operations to serve users better. 
        </p>
        <p>
          The company has completed preliminary data privacy training under the guidance of the relevant authority and is preparing to commence full training and certification for staff. Users are therefore assured of compliance with privacy standards.
        </p>

        <h6 class="fw-bold mt-4">⚖️ Rights & Limitations</h6>
        <ol>
          <li>M-right reserves the right to deactivate any user account for unethical usage.</li>
          <li>User data may be shared with authorities in the case of stolen phone disputes. Data may also be retrieved as reports or statistical records for internal or national purposes.</li>
          <li>These Terms & Conditions and the Privacy Policy are subject to periodic review. The company reserves the right to discontinue services at any time without prior notice.</li>
          <li>In the event of system or infrastructure failure beyond the company’s control, M-right shall not be liable for unavailable or lost records.</li>
        </ol>

        <h6 class="fw-bold mt-4">📖 Definitions</h6>
        <ul>
          <li><strong>M-right</strong>: The company rendering these services (sometimes referred to as "company").</li>
          <li><strong>User</strong>: Any individual or entity that creates an account in this system.</li>
          <li><strong>Customer</strong>: A third party to whom shop owners generate receipts.</li>
          <li><strong>System / Site / Portal / Application</strong>: Refers to the web-based software.</li>
          <li><strong>User Data / Record / User Information</strong>: Raw or organized facts about the user.</li>
        </ul>

        <p class="mt-3">
          By clicking <strong>“I Agree”</strong> during registration, you confirm your acceptance of these Terms of Service and Privacy Policy.
        </p>

        <p class="text-danger fw-bold mt-3">
          ⚠️ We reserve the right to suspend or terminate accounts that violate these terms.
        </p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">I Agree</button>
      </div>
    </div>
  </div>
</div>


<!-- Privacy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Privacy Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Your privacy is important to us. This policy explains how we collect and use your information:</p>
                <h6>Information We Collect:</h6>
                <ul>
                    <li>Personal details (name, email, phone numbers)</li>
                    <li>Business information (for shop owners)</li>
                    <li>Transaction data and receipt information</li>
                    <li>Usage analytics and system logs</li>
                </ul>
                <h6>How We Use Your Information:</h6>
                <ul>
                    <li>Provide and improve our services</li>
                    <li>Process transactions and generate receipts</li>
                    <li>Send important account notifications</li>
                    <li>Ensure system security and prevent fraud</li>
                </ul>
                <p>We never sell your personal information to third parties.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-toggle-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Form submission handling
document.getElementById('registrationForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('submitSpinner');
    
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
});

// Real-time password confirmation validation
// Real-time password confirmation validation
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmation = this.value;
    
    if (confirmation && password !== confirmation) {
        this.classList.add('is-invalid');
        if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = 'Passwords do not match';
            this.parentNode.appendChild(feedback);
        }
    } else {
        this.classList.remove('is-invalid');
        const feedback = this.parentNode.querySelector('.invalid-feedback');
        if (feedback && feedback.textContent === 'Passwords do not match') {
            feedback.remove();
        }
    }
});

// Real-time phone number confirmation validation
document.getElementById('phone_number_confirmation').addEventListener('input', function() {
    const phoneNumber = document.getElementById('phone_number').value;
    const confirmation = this.value;
    const errorDiv = document.getElementById('phone-mismatch-error');
    
    if (confirmation && phoneNumber !== confirmation) {
        this.classList.add('is-invalid');
        errorDiv.style.display = 'block';
    } else {
        this.classList.remove('is-invalid');
        errorDiv.style.display = 'none';
    }
});

// Also validate when main phone number changes
document.getElementById('phone_number').addEventListener('input', function() {
    const confirmation = document.getElementById('phone_number_confirmation').value;
    const confirmField = document.getElementById('phone_number_confirmation');
    const errorDiv = document.getElementById('phone-mismatch-error');
    
    if (confirmation && this.value !== confirmation) {
        confirmField.classList.add('is-invalid');
        errorDiv.style.display = 'block';
    } else {
        confirmField.classList.remove('is-invalid');
        errorDiv.style.display = 'none';
    }
});
// Phone number formatting
// STRICT FORM VALIDATION FUNCTIONS
function validateFullName(input) {
    const value = input.value.trim();
    const nameRegex = /^[a-zA-Z\s]+$/;
    const words = value.split(' ').filter(word => word.length > 0);
    
    if (!value) {
        showError(input, 'Full name is required');
        return false;
    }
    
    if (words.length < 2) {
        showError(input, 'Please enter first and last name');
        return false;
    }
    
    if (!nameRegex.test(value)) {
        showError(input, 'Name can only contain letters and spaces');
        return false;
    }
    
    if (value.length < 3 || value.length > 50) {
        showError(input, 'Name must be between 3 and 50 characters');
        return false;
    }
    
    clearError(input);
    return true;
}

function validatePhoneNumber(input) {
    let value = input.value.replace(/\D/g, ''); // Remove all non-digits
    
    // Only allow exactly 11 digits for Nigeria
    if (value.length !== 11) {
        showError(input, 'Phone number must be exactly 11 digits');
        input.value = value; // Update field to show only digits
        return false;
    }
    
    // Must start with 0 for Nigeria format
    if (!value.startsWith('0')) {
        showError(input, 'Phone number must start with 0 (e.g., 08012345678)');
        input.value = value;
        return false;
    }
    
    // Set the cleaned value
    input.value = value;
    clearError(input);
    return true;
}
function validatePhoneNumberConfirmation(input) {
    const originalPhone = document.getElementById('phone_number').value;
    const confirmPhone = input.value;
    
    if (!confirmPhone) {
        showError(input, 'Please confirm your phone number');
        return false;
    }
    
    if (originalPhone !== confirmPhone) {
        showError(input, 'Phone numbers do not match');
        return false;
    }
    
    clearError(input);
    return true;
}
function validateEmail(input) {
    const value = input.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (value && !emailRegex.test(value)) {
        showError(input, 'Please enter a valid email address');
        return false;
    }
    
    clearError(input);
    return true;
}

function validateBusinessName(input) {
    const value = input.value.trim();
    
    if (!value) {
        showError(input, 'Business name is required');
        return false;
    }
    
    if (value.length < 3 || value.length > 100) {
        showError(input, 'Business name must be between 3 and 100 characters');
        return false;
    }
    
    clearError(input);
    return true;
}

function validateAddress(input) {
    const value = input.value.trim();
    
    if (!value) {
        showError(input, 'Address is required');
        return false;
    }
    
    if (value.length < 15 || value.length > 200) {
        showError(input, 'Address must be between 15 and 200 characters');
        return false;
    }
    
    clearError(input);
    return true;
}

function showError(input, message) {
    input.classList.add('is-invalid');
    let feedback = input.parentNode.querySelector('.invalid-feedback');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        input.parentNode.appendChild(feedback);
    }
    feedback.textContent = message;
}

function clearError(input) {
    input.classList.remove('is-invalid');
    const feedback = input.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = '';
    }
}

// PREVENT NON-NUMERIC INPUT FOR PHONE FIELDS
function allowOnlyNumbers(event) {
    const char = String.fromCharCode(event.which);
    if (!/[0-9]/.test(char)) {
        event.preventDefault();
    }
}

// PREVENT NUMBERS AND SPECIAL CHARS FOR NAME FIELDS
function allowOnlyLetters(event) {
    const char = String.fromCharCode(event.which);
    if (!/[a-zA-Z\s]/.test(char)) {
        event.preventDefault();
    }
}

// SET UP ALL VALIDATION EVENT LISTENERS
document.addEventListener('DOMContentLoaded', function() {
    // Full Name validation
    const fullNameField = document.getElementById('full_name');
    if (fullNameField) {
        fullNameField.addEventListener('keypress', allowOnlyLetters);
        fullNameField.addEventListener('blur', function() {
            validateFullName(this);
        });
        fullNameField.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateFullName(this);
            }
        });
    }
    
    // Phone number validation
    const phoneField = document.getElementById('phone_number');
    if (phoneField) {
        phoneField.addEventListener('keypress', allowOnlyNumbers);
        phoneField.addEventListener('blur', function() {
            validatePhoneNumber(this);
        });
        phoneField.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validatePhoneNumber(this);
            }
        });
    }
    
    // Secondary phone validation
    const secondaryPhoneField = document.getElementById('secondary_phone');
    if (secondaryPhoneField) {
        secondaryPhoneField.addEventListener('keypress', allowOnlyNumbers);
        secondaryPhoneField.addEventListener('blur', function() {
            if (this.value) {
                validatePhoneNumber(this);
            }
        });
    }
    
    // Email validation
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.addEventListener('blur', function() {
            validateEmail(this);
        });
        emailField.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateEmail(this);
            }
        });
    }
    
    // Business name validation (for shop owners)
    const businessNameField = document.getElementById('business_name');
    if (businessNameField) {
        businessNameField.addEventListener('blur', function() {
            validateBusinessName(this);
        });
    }
    
    // Address validation (for shop owners)
    const addressField = document.getElementById('business_address');
    if (addressField) {
        addressField.addEventListener('blur', function() {
            validateAddress(this);
        });
    }
    
    // Form submission validation
    const form = document.getElementById('registrationForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validate all required fields
           // Validate all required fields
if (fullNameField && !validateFullName(fullNameField)) isValid = false;
if (phoneField && !validatePhoneNumber(phoneField)) isValid = false;
if (emailField && !validateEmail(emailField)) isValid = false;

// Validate phone number confirmation
const phoneConfirmField = document.getElementById('phone_number_confirmation');
if (phoneConfirmField && !validatePhoneNumberConfirmation(phoneConfirmField)) isValid = false;
            
            // Shop owner specific validations
            const userType = document.getElementById('user_type');
            if (userType && userType.value === 'shop_owner') {
                if (businessNameField && !validateBusinessName(businessNameField)) isValid = false;
                if (addressField && !validateAddress(addressField)) isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please correct all validation errors before submitting.');
                return false;
            }
        });
    }
});
</script>
@endpush
@push('scripts')
<script>
// Enhanced registration form with pre-approval validation
let preApprovalChecked = false;
let isPreApproved = false;

// Check pre-approval when key fields change
document.addEventListener('DOMContentLoaded', function() {
    const emailField = document.getElementById('email');
    const phoneField = document.getElementById('phone_number');
    const userTypeField = document.getElementById('user_type');
    
    // Add event listeners for validation
    [emailField, phoneField, userTypeField].forEach(field => {
        if (field) {
            field.addEventListener('blur', checkPreApprovalStatus);
        }
    });
    
    // Form submission validation
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        if (!preApprovalChecked && userTypeField.value === 'shop_owner') {
            e.preventDefault();
            showPreApprovalModal('Please verify your approval status before proceeding.');
            return false;
        }
        
        if (!isPreApproved && userTypeField.value === 'shop_owner') {
            e.preventDefault();
            showNotApprovedModal();
            return false;
        }
        
        return true;
    });
});

function checkPreApprovalStatus() {
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone_number').value;
    const userType = document.getElementById('user_type').value;
    
    if (!email || !phone || !userType) return;
    
    // Only check for shop owners
    if (userType !== 'shop_owner') {
        preApprovalChecked = true;
        isPreApproved = true;
        return;
    }
    
    // Show loading indicator
    showLoadingModal();
    
    fetch('/auth/check-pre-approval', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            email: email,
            phone_number: phone,
            user_type: userType
        })
    })
    .then(response => response.json())
    .then(data => {
        preApprovalChecked = true;
        isPreApproved = data.approved;
        
        hideLoadingModal();
        
        if (data.approved) {
            showApprovedModal(data.message, data.data);
        } else {
            showNotApprovedModal(data.message, data.contact_info);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        hideLoadingModal();
        showErrorModal('Unable to verify approval status. Please try again.');
    });
}

function showLoadingModal() {
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
}

function hideLoadingModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
    if (modal) modal.hide();
}

function showApprovedModal(message, data) {
    document.getElementById('approvedMessage').textContent = message;
    
    // Pre-fill data if available
    if (data.shop_name) {
        document.getElementById('preApprovedShopName').textContent = data.shop_name;
        document.getElementById('preApprovedData').style.display = 'block';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('approvedModal'));
    modal.show();
}

function showNotApprovedModal(message, contactInfo) {
    document.getElementById('notApprovedMessage').textContent = message || 'Your details are not yet approved for registration.';
    document.getElementById('contactInfo').textContent = contactInfo || 'Contact your market chairman for approval.';
    
    const modal = new bootstrap.Modal(document.getElementById('notApprovedModal'));
    modal.show();
}

function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('errorModal'));
    modal.show();
}

function continueRegistration() {
    // Close modal and allow registration to proceed
    const modal = bootstrap.Modal.getInstance(document.getElementById('approvedModal'));
    modal.hide();
}

function cancelRegistration() {
    // Redirect to home or login
    window.location.href = '/';
}

// Existing form functions...
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-toggle-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<!-- Pre-Approval Modals -->
<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <h6>Verifying Approval Status...</h6>
                <p class="text-muted small mb-0">Please wait while we check your details.</p>
            </div>
        </div>
    </div>
</div>

<!-- Approved Modal -->
<div class="modal fade" id="approvedModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Approval Confirmed
                </h5>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                </div>
                <p id="approvedMessage" class="text-center mb-3"></p>
                
                <div id="preApprovedData" style="display: none;">
                    <div class="bg-light p-3 rounded">
                        <h6>Pre-approved Shop Details:</h6>
                        <p class="mb-1"><strong>Shop Name:</strong> <span id="preApprovedShopName"></span></p>
                        <small class="text-muted">These details will be pre-filled in your shop registration.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="continueRegistration()">
                    <i class="fas fa-arrow-right me-1"></i>Continue Registration
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Not Approved Modal -->
<div class="modal fade" id="notApprovedModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Approval Required
                </h5>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                </div>
                <p id="notApprovedMessage" class="text-center mb-3"></p>
                
                <div class="bg-light p-3 rounded">
                    <h6><i class="fas fa-info-circle me-1"></i>What to do next:</h6>
                    <ol class="mb-2">
                        <li>Contact your market chairman or AMPAT Executive</li>
                        <li>Request them to upload your details to the system</li>
                        <li>Return here after your details are uploaded</li>
                    </ol>
                    <p class="text-muted small mb-0" id="contactInfo"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cancelRegistration()">
                    <i class="fas fa-arrow-left me-1"></i>Go Back
                </button>
                <button type="button" class="btn btn-primary" onclick="location.reload()">
                    <i class="fas fa-redo me-1"></i>Try Again
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-circle me-2"></i>Error
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="errorMessage" class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endpush
@endsection
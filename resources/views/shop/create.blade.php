@extends('layouts.app')

@section('title', 'Setup Shop Profile')
@section('page-title', 'Setup Shop Profile')
@section('page-description', 'Create your business profile to start generating digital receipts')

@push('styles')
<style>
.form-section {
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-section:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

.section-title i {
    background: linear-gradient(135deg, #0d8abc, #4dabf7);
    color: white;
    padding: 8px;
    border-radius: 8px;
    margin-right: 12px;
    font-size: 0.9rem;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #0d8abc;
    box-shadow: 0 0 0 0.2rem rgba(13, 138, 188, 0.25);
}

.logo-upload-area {
    border: 2px dashed #ddd;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background: #fafafa;
}

.logo-upload-area:hover {
    border-color: #0d8abc;
    background: #f0f8ff;
}

.logo-upload-area.dragover {
    border-color: #0d8abc;
    background: #e3f2fd;
    transform: scale(1.02);
}

.preview-image {
    max-width: 200px;
    max-height: 200px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
}

.progress-steps::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
}

.step-item {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
}

.step-item.active {
    background: #0d8abc;
    border-color: #0d8abc;
    color: white;
}

.step-item.completed {
    background: #28a745;
    border-color: #28a745;
    color: white;
}

@media (max-width: 768px) {
    .form-section {
        padding: 1.5rem;
    }
    
    .section-title {
        font-size: 1rem;
    }
    
    .progress-steps {
        display: none;
    }
}
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="step-item active">1</div>
            <div class="step-item">2</div>
            <div class="step-item">3</div>
            <div class="step-item">4</div>
        </div>

        <form method="POST" action="{{ route('shop.store') }}" enctype="multipart/form-data" id="shopRegistrationForm">
            @csrf
            
            <!-- Business Information Section -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fas fa-store"></i>
                    Business Information
                </h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="shop_name" class="form-label fw-medium">
                            Shop/Business Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('shop_name') is-invalid @enderror" 
                               id="shop_name" 
                               name="shop_name" 
                               value="{{ old('shop_name') }}" 
                               placeholder="e.g., TechHub Mobile Store"
                               required>
                        @error('shop_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="owner_full_name" class="form-label fw-medium">
                            Owner Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('owner_full_name') is-invalid @enderror" 
                               id="owner_full_name" 
                               name="owner_full_name" 
                               value="{{ old('owner_full_name') }}" 
                               placeholder="e.g., John Doe"
                               required>
                        @error('owner_full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="business_address" class="form-label fw-medium">
                            Business Address <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('business_address') is-invalid @enderror" 
                                  id="business_address" 
                                  name="business_address" 
                                  rows="3" 
                                  placeholder="Enter your complete business address"
                                  required>{{ old('business_address') }}</textarea>
                        @error('business_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fas fa-phone"></i>
                    Contact Information
                </h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="business_phone_1" class="form-label fw-medium">
                            Primary Phone Number <span class="text-danger">*</span>
                        </label>
                        <input type="tel" 
                               class="form-control @error('business_phone_1') is-invalid @enderror" 
                               id="business_phone_1" 
                               name="business_phone_1" 
                               value="{{ old('business_phone_1') }}" 
                               placeholder="e.g., +234 803 123 4567"
                               required>
                        @error('business_phone_1')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="business_phone_2" class="form-label fw-medium">
                            Secondary Phone Number <small class="text-muted">(Optional)</small>
                        </label>
                        <input type="tel" 
                               class="form-control @error('business_phone_2') is-invalid @enderror" 
                               id="business_phone_2" 
                               name="business_phone_2" 
                               value="{{ old('business_phone_2') }}" 
                               placeholder="e.g., +234 805 123 4567">
                        @error('business_phone_2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="business_email" class="form-label fw-medium">
                            Business Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               class="form-control @error('business_email') is-invalid @enderror" 
                               id="business_email" 
                               name="business_email" 
                               value="{{ old('business_email') }}" 
                               placeholder="e.g., info@techhubmobile.com"
                               required>
                        @error('business_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Location Information Section -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    Location Information
                </h5>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="country" class="form-label fw-medium">
                            Country <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('country') is-invalid @enderror" 
                                id="country" 
                                name="country" 
                                required>
                            <option value="">Select Country</option>
                            <option value="Nigeria" {{ old('country') == 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                            <option value="Ghana" {{ old('country') == 'Ghana' ? 'selected' : '' }}>Ghana</option>
                            <option value="Kenya" {{ old('country') == 'Kenya' ? 'selected' : '' }}>Kenya</option>
                            <option value="South Africa" {{ old('country') == 'South Africa' ? 'selected' : '' }}>South Africa</option>
                        </select>
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="state" class="form-label fw-medium">
                            State/Province <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('state') is-invalid @enderror" 
                               id="state" 
                               name="state" 
                               value="{{ old('state') }}" 
                               placeholder="e.g., Lagos"
                               required>
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="local_government" class="form-label fw-medium">
                            Local Government/City <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('local_government') is-invalid @enderror" 
                               id="local_government" 
                               name="local_government" 
                               value="{{ old('local_government') }}" 
                               placeholder="e.g., Ikeja"
                               required>
                        @error('local_government')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Branding Section -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fas fa-image"></i>
                    Branding & Customization
                </h5>
                
                <div class="row">
                    <div class="col-12 mb-4">
                        <label for="logo" class="form-label fw-medium">
                            Shop Logo <small class="text-muted">(Optional - JPG, PNG, GIF, Max: 2MB)</small>
                        </label>
                        <div class="logo-upload-area" onclick="document.getElementById('logo').click()">
                            <div class="upload-content">
                                <i class="fas fa-cloud-upload-alt text-muted mb-3" style="font-size: 3rem;"></i>
                                <h6 class="text-muted mb-2">Click to upload or drag and drop</h6>
                                <p class="text-muted small mb-0">Supports: JPG, PNG, GIF (Max 2MB)</p>
                            </div>
                            <div class="preview-content" style="display: none;">
                                <img class="preview-image" alt="Logo Preview">
                                <p class="text-success mt-2 mb-0">
                                    <i class="fas fa-check-circle me-1"></i>Logo uploaded successfully
                                </p>
                            </div>
                        </div>
                        <input type="file" 
                               class="form-control d-none @error('logo') is-invalid @enderror" 
                               id="logo" 
                               name="logo" 
                               accept="image/*">
                        @error('logo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="terms_and_conditions" class="form-label fw-medium">
                            Custom Terms & Conditions <small class="text-muted">(Optional)</small>
                        </label>
                        <textarea class="form-control @error('terms_and_conditions') is-invalid @enderror" 
                                  id="terms_and_conditions" 
                                  name="terms_and_conditions" 
                                  rows="4" 
                                  placeholder="Enter any specific terms and conditions for your receipts (optional)">{{ old('terms_and_conditions') }}</textarea>
                        @error('terms_and_conditions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            This will appear on your digital receipts. Leave blank to use default terms.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="form-section text-center">
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Your shop registration will be reviewed by our admin team. 
                            You'll receive an email notification once approved (usually within 24-48 hours).
                        </div>
                        
                        <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-store me-2"></i>Register Shop
                                <span class="spinner-border spinner-border-sm ms-2 d-none" id="loadingSpinner"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logo upload functionality
    const logoInput = document.getElementById('logo');
    const uploadArea = document.querySelector('.logo-upload-area');
    const uploadContent = document.querySelector('.upload-content');
    const previewContent = document.querySelector('.preview-content');
    const previewImage = document.querySelector('.preview-image');

    // Handle file selection
    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            handleFileUpload(file);
        }
    });

    // Handle drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('image/')) {
                logoInput.files = files;
                handleFileUpload(file);
            }
        }
    });

    function handleFileUpload(file) {
        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            return;
        }

        // Validate file type
        if (!['image/jpeg', 'image/jpg', 'image/png', 'image/gif'].includes(file.type)) {
            alert('Please select a valid image file (JPG, PNG, GIF)');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            uploadContent.style.display = 'none';
            previewContent.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    // Form validation and submission
    const form = document.getElementById('shopRegistrationForm');
    const submitBtn = document.getElementById('submitBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');

    form.addEventListener('submit', function(e) {
        // Show loading state
        submitBtn.disabled = true;
        loadingSpinner.classList.remove('d-none');
        
        // Basic validation
        const requiredFields = ['shop_name', 'owner_full_name', 'business_address', 
                               'business_phone_1', 'business_email', 'country', 'state', 'local_government'];
        
        let isValid = true;
        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            submitBtn.disabled = false;
            loadingSpinner.classList.add('d-none');
            alert('Please fill in all required fields');
            return;
        }
    });

    // Phone number formatting
    const phoneInputs = ['business_phone_1', 'business_phone_2'];
    phoneInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function(e) {
                // Remove any non-digit characters except +, -, (, ), and spaces
                let value = e.target.value.replace(/[^\d+\-\(\)\s]/g, '');
                e.target.value = value;
            });
        }
    });

    // Real-time validation feedback
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>
@endpush
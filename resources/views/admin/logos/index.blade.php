@extends('layouts.app')

@section('title', 'Logo Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Logo Management</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Logo Management</li>
                    </ol>
                </nav>
            </div>

            <div class="row">
                <!-- AMPAT Logo Management -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-shield-alt me-2"></i>AMPAT Logo
                            </h5>
                            <small>Main Authority Logo</small>
                        </div>
                        <div class="card-body text-center">
                            <div class="logo-preview mb-3" style="min-height: 120px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px;">
                                @if($logos['ampat'])
                                    <img src="{{ $logos['ampat']->logo_url }}" 
                                         alt="AMPAT Logo" 
                                         class="img-fluid"
                                         style="max-height: 100px; max-width: 200px;">
                                @else
                                    <div class="text-muted">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p>No AMPAT logo uploaded</p>
                                    </div>
                                @endif
                            </div>
                            
                            <form id="ampatLogoForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="logo_type" value="ampat">
                                <div class="mb-3">
                                    <input type="file" 
                                           class="form-control" 
                                           name="logo_file" 
                                           accept="image/*"
                                           required>
                                    <small class="text-muted">Max 2MB. Formats: JPG, PNG, GIF</small>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload me-1"></i>Upload AMPAT Logo
                                    </button>
                                    @if($logos['ampat'])
                                        <button type="button" class="btn btn-outline-danger" onclick="deleteSystemLogo('ampat')">
                                            <i class="fas fa-trash me-1"></i>Delete Current Logo
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                        @if($logos['ampat'])
                        <div class="card-footer text-muted small">
                            Uploaded: {{ $logos['ampat']->created_at->format('M d, Y h:i A') }}<br>
                            By: {{ $logos['ampat']->uploader->name ?? 'Unknown' }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- MRight Logo Management -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-cog me-2"></i>MRight Logo
                            </h5>
                            <small>System Provider Logo</small>
                        </div>
                        <div class="card-body text-center">
                            <div class="logo-preview mb-3" style="min-height: 120px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px;">
                                @if($logos['mright'])
                                    <img src="{{ $logos['mright']->logo_url }}" 
                                         alt="MRight Logo" 
                                         class="img-fluid"
                                         style="max-height: 100px; max-width: 200px;">
                                @else
                                    <div class="text-muted">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p>No MRight logo uploaded</p>
                                    </div>
                                @endif
                            </div>
                            
                            <form id="mrightLogoForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="logo_type" value="mright">
                                <div class="mb-3">
                                    <input type="file" 
                                           class="form-control" 
                                           name="logo_file" 
                                           accept="image/*"
                                           required>
                                    <small class="text-muted">Max 2MB. Formats: JPG, PNG, GIF</small>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-upload me-1"></i>Upload MRight Logo
                                    </button>
                                    @if($logos['mright'])
                                        <button type="button" class="btn btn-outline-danger" onclick="deleteSystemLogo('mright')">
                                            <i class="fas fa-trash me-1"></i>Delete Current Logo
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                        @if($logos['mright'])
                        <div class="card-footer text-muted small">
                            Uploaded: {{ $logos['mright']->created_at->format('M d, Y h:i A') }}<br>
                            By: {{ $logos['mright']->uploader->name ?? 'Unknown' }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Logo Guidelines
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-primary">AMPAT Logo</h6>
                                    <ul class="small">
                                        <li>Official authority logo</li>
                                        <li>Appears on all receipts</li>
                                        <li>Positioned top-left on receipts</li>
                                        <li>Represents regulatory oversight</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-success">MRight Logo</h6>
                                    <ul class="small">
                                        <li>System provider branding</li>
                                        <li>Appears on all receipts</li>
                                        <li>Positioned top-center on receipts</li>
                                        <li>Company identification</li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="text-info">Shop Owner Logos</h6>
                                    <ul class="small">
                                        <li>Individual shop branding</li>
                                        <li>Uploaded by shop owners</li>
                                        <li>Positioned top-right on receipts</li>
                                        <li>Managed in shop settings</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Handle AMPAT logo upload
document.getElementById('ampatLogoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    uploadSystemLogo(this, 'AMPAT');
});

// Handle MRight logo upload  
document.getElementById('mrightLogoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    uploadSystemLogo(this, 'MRight');
});

function uploadSystemLogo(form, logoName) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Show loading state
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Uploading...';
    submitBtn.disabled = true;
    
    fetch('{{ route("admin.logos.upload-system") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Upload failed. Please try again.');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function deleteSystemLogo(logoType) {
    if (!confirm(`Are you sure you want to delete the ${logoType.toUpperCase()} logo?`)) {
        return;
    }
    
    fetch('{{ route("admin.logos.delete-system") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            logo_type: logoType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Delete failed. Please try again.');
    });
}

function showAlert(type, message) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alert);
    
    setTimeout(() => {
        alert.remove();
    }, 5000);
}
</script>
@endpush
@endsection
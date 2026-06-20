<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Credentials - Phone Anti-Theft Digital Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .credential-card {
            transition: transform 0.3s ease;
            border-left: 4px solid;
        }
        .credential-card:hover {
            transform: translateY(-5px);
        }
        .admin-card { border-left-color: #dc3545; }
        .shop-owner-card { border-left-color: #28a745; }
        .pending-card { border-left-color: #ffc107; }
        .customer-card { border-left-color: #17a2b8; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold text-primary">
                        <i class="fas fa-key me-3"></i>Test Credentials
                    </h1>
                    <p class="lead text-muted">Use these accounts to test different user roles and functionality</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Development Only:</strong> These credentials are for testing purposes only.
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Admin User -->
                    <div class="col-lg-6">
                        <div class="card credential-card admin-card h-100 shadow-sm">
                            <div class="card-header bg-danger text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-shield-alt me-2"></i>Administrator
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <strong>Email:</strong>
                                    </div>
                                    <div class="col-8">
                                        <code>admin@mright.com</code>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <strong>Password:</strong>
                                    </div>
                                    <div class="col-8">
                                        <code>password123</code>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="fw-bold">Access:</h6>
                                <ul class="small mb-3">
                                    <li>Full admin dashboard</li>
                                    <li>Shop approval management</li>
                                    <li>User management</li>
                                    <li>System reports & analytics</li>
                                    <li>System health monitoring</li>
                                </ul>
                                <a href="{{ route('login') }}" class="btn btn-danger w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login as Admin
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Shop Owner (Approved) -->
                    <div class="col-lg-6">
                        <div class="card credential-card shop-owner-card h-100 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-store me-2"></i>Shop Owner (Approved)
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <strong>Email:</strong>
                                    </div>
                                    <div class="col-8">
                                        <code>shop@mright.com</code>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <strong>Password:</strong>
                                    </div>
                                    <div class="col-8">
                                        <code>password123</code>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="fw-bold">Access:</h6>
                                <ul class="small mb-3">
                                    <li>Generate digital receipts</li>
                                    <li>Manage shop profile</li>
                                    <li>View business analytics</li>
                                    <li>Update resale records</li>
                                    <li>Search functionality</li>
                                </ul>
                                <a href="{{ route('login') }}" class="btn btn-success w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login as Shop Owner
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Shop Owner (Pending) -->
                    <div class="col-lg-6">
                        <div class="card credential-card pending-card h-100 shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-clock me-2"></i>Shop Owner (Pending)
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <strong>Email:</strong>
                                    </div>
                                    <div class="col-8">
                                        <code>pending@mright.com</code>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <strong>Password:</strong>
                                    </div>
                                    <div class="col-8">
                                        <code>password123</code>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="fw-bold">Status:</h6>
                                <ul class="small mb-3">
                                    <li>Awaiting admin approval</li>
                                    <li>Limited dashboard access</li>
                                    <li>Cannot generate receipts yet</li>
                                    <li>Shop: Mobile World (Pending)</li>
                                </ul>
                                <a href="{{ route('login') }}" class="btn btn-warning w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login as Pending Shop
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Customer -->
                    <div class="col-lg-6">
                        <div class="card credential-card customer-card h-100 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user me-2"></i>Customer
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <strong>Email:</strong>
                                    </div>
                                    <div class="col-8">
                                        <code>customer@mright.com</code>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <strong>Password:</strong>
                                    </div>
                                    <div class="col-8">
                                        <code>password123</code>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="fw-bold">Access:</h6>
                                <ul class="small mb-3">
                                    <li>View personal receipts</li>
                                    <li>Search by serial number</li>
                                    <li>Verify phone ownership</li>
                                    <li>Report issues</li>
                                    <li>Access help resources</li>
                                </ul>
                                <a href="{{ route('login') }}" class="btn btn-info w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login as Customer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <div class="alert alert-info">
                        <h6 class="fw-bold">Quick Test Guide:</h6>
                        <ol class="text-start mb-0">
                            <li>Test admin approval workflow with pending shop owner</li>
                            <li>Test receipt generation with approved shop owner</li>
                            <li>Test customer search and receipt viewing</li>
                            <li>Test responsive design on mobile devices</li>
                        </ol>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary me-3">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-success">
                            <i class="fas fa-user-plus me-2"></i>Create New Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Copy credentials to clipboard
        document.querySelectorAll('code').forEach(code => {
            code.style.cursor = 'pointer';
            code.title = 'Click to copy';
            code.addEventListener('click', function() {
                navigator.clipboard.writeText(this.textContent);
                
                // Visual feedback
                const original = this.textContent;
                this.textContent = 'Copied!';
                this.style.color = '#28a745';
                
                setTimeout(() => {
                    this.textContent = original;
                    this.style.color = '';
                }, 1000);
            });
        });
    </script>
</body>
</html>
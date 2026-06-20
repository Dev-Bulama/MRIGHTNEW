<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Verification - {{ $receiptNumber }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        
        .verification-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 1rem;
        }
        
        .verification-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
        }
        
        .back-btn {
            position: absolute;
            top: 2rem;
            left: 2rem;
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            backdrop-filter: blur(10px);
        }
        
        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }
    </style>
</head>
<body>
    <a href="{{ route('home') }}" class="back-btn">
        <i class="fas fa-arrow-left me-2"></i>Back to Home
    </a>
    
    <div class="verification-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="verification-card mx-auto">
                        @if($found)
                            <!-- Success - Receipt Found -->
                            <div class="text-center mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                <h2 class="text-success mt-3">Receipt Verified!</h2>
                                <p class="text-muted">This receipt is valid and verified in our system.</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Receipt Information</h5>
                                    <p><strong>Receipt #:</strong> {{ $receipt->receipt_number }}</p>
                                    <p><strong>Date Issued:</strong> {{ $receipt->created_at->format('F j, Y g:i A') }}</p>
                                    <p><strong>Status:</strong> <span class="badge bg-success">Verified</span></p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Phone Details</h5>
                                    <p><strong>Device:</strong> {{ $receipt->phone_name }}</p>
                                    <p><strong>Color:</strong> {{ $receipt->phone_color }}</p>
                                    <p><strong>Serial #:</strong> {{ $receipt->phone_serial_number }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Customer Information</h5>
                                    <p><strong>Name:</strong> {{ $receipt->customer_name }}</p>
                                    <p><strong>Phone:</strong> {{ $receipt->customer_phone }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Shop Information</h5>
                                    <p><strong>Shop:</strong> {{ $receipt->shop->shop_name }}</p>
                                    <p><strong>Location:</strong> {{ $receipt->shop->state }}, {{ $receipt->shop->local_government }}</p>
                                </div>
                            </div>
                            
                            <div class="alert alert-success mt-4">
                                <i class="fas fa-shield-alt me-2"></i>
                                <strong>Security Verified:</strong> This receipt is legitimate and has been verified through our secure system.
                            </div>
                        @else
                            <!-- Error - Receipt Not Found -->
                            <div class="text-center mb-4">
                                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                                <h2 class="text-danger mt-3">Verification Failed</h2>
                                <p class="text-muted">{{ $message }}</p>
                            </div>
                            
                            <div class="alert alert-danger">
                                <i class="fas fa-warning me-2"></i>
                                <strong>Receipt Number:</strong> {{ $receiptNumber }}
                            </div>
                            
                            <div class="text-center">
                                <p class="mb-3">Possible reasons:</p>
                                <ul class="list-unstyled text-muted">
                                    <li>• Receipt number is incorrect</li>
                                    <li>• Receipt has been cancelled or voided</li>
                                    <li>• This is not a valid M-right receipt</li>
                                </ul>
                            </div>
                        @endif
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('home') }}" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-home me-2"></i>Back to Home
                            </a>
                            @guest
                                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
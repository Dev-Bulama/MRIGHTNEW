<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>M-right Digital Receipt System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%);
            --accent-color: #E8F4FD;
            --text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            background: var(--primary-gradient);
            min-height: 100vh;
        }
        
        .main-container {
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
            position: relative;
        }
        
        .main-title {
            font-size: 4.5rem;
            font-weight: 800;
            color: var(--accent-color);
            text-align: center;
            text-shadow: var(--text-shadow);
            margin-bottom: 1rem;
            letter-spacing: -2px;
            line-height: 1.1;
        }
        
        .subtitle {
            font-size: 1.8rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            text-align: center;
            text-shadow: var(--text-shadow);
            margin-bottom: 4rem;
            letter-spacing: 1px;
        }
        
        .action-buttons {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 3rem;
        }
        
        .action-btn {
            background: var(--accent-color);
            color: #2C3E50;
            border: none;
            border-radius: 50px;
            padding: 1.2rem 3rem;
            font-size: 1.2rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            min-width: 280px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            color: #1A252F;
            background: #FFFFFF;
        }
        
        .search-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 600px;
            width: 100%;
        }
        
        .search-title {
            color: white;
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
            text-shadow: var(--text-shadow);
        }
        
        .search-form {
            display: flex;
            gap: 1rem;
            flex-direction: column;
        }
        
        .search-input {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 15px;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .search-input:focus {
            outline: none;
            background: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        .search-btn {
            background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .search-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        
        .support-text {
            color: rgba(255, 255, 255, 0.9);
            text-align: center;
            font-size: 1rem;
            font-weight: 500;
            text-shadow: var(--text-shadow);
            margin-top: 3rem;
            line-height: 1.6;
        }
        
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            overflow: hidden;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 80px;
            height: 80px;
            bottom: 30%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .stats-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 800px;
            width: 100%;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            text-align: center;
        }
        
        .stat-item {
            color: white;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            display: block;
            margin-bottom: 0.5rem;
            text-shadow: var(--text-shadow);
        }
        
        .stat-label {
            font-size: 1rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: var(--text-shadow);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-title {
                font-size: 2.8rem;
                letter-spacing: -1px;
            }
            
            .subtitle {
                font-size: 1.3rem;
                margin-bottom: 3rem;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 1.5rem;
                width: 100%;
                max-width: 350px;
            }
            
            .action-btn {
                min-width: 100%;
                padding: 1rem 2rem;
                font-size: 1.1rem;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 480px) {
            .main-title {
                font-size: 2.2rem;
            }
            
            .subtitle {
                font-size: 1.1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Loading animation */
        .loading {
            display: none;
            color: white;
            text-align: center;
            margin-top: 1rem;
        }
        
        .result-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            display: none;
        }
        
        .result-success {
            border-left: 5px solid #27AE60;
        }
        
        .result-error {
            border-left: 5px solid #E74C3C;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Floating Background Shapes -->
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        
        <!-- Main Content -->
        <div class="text-center">
            <!-- Main Title -->
            <h1 class="main-title">
                M-right Digital Receipt
            </h1>
            
            <!-- Subtitle -->
            <p class="subtitle">
                PHONE E-RECEIPT ACCESSIBLE ONLINE
            </p>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('register') }}" class="action-btn">
                    CREATE SHOP ACCOUNT
                </a>
                <a href="{{ route('login') }}" class="action-btn">
                    LOGIN
                </a>
                <a href="{{ route('password.request') }}" class="action-btn">
                    FORGET PASSWORD
                </a>
            </div>
            
            <!-- Phone Search/Verification Section -->
    <!--        <div class="search-section">-->
    <!--            <h3 class="search-title">-->
    <!--                <i class="fas fa-shield-check me-2"></i>-->
    <!--                Verify Phone Receipt-->
    <!--            </h3>-->
    <!--            <form class="search-form" id="verificationForm">-->
    <!--                <input type="text" -->
    <!--                       class="search-input" -->
    <!--                       id="searchInput"-->
    <!--                       placeholder="Enter Receipt Number (e.g., MR-TEC202507310001)" -->
    <!--                       required>-->
    <!--                <button type="submit" class="search-btn">-->
    <!--                    <i class="fas fa-search me-2"></i>-->
    <!--                    SEARCH RECEIPT-->
    <!--                </button>-->
    <!--            </form>-->
                
    <!--            <div class="loading" id="searchLoading">-->
    <!--                <i class="fas fa-spinner fa-spin me-2"></i>-->
    <!--                Searching...-->
    <!--            </div>-->
                
                <!-- Search Result -->
    <!--            <div class="result-section" id="searchResult">-->
                    <!-- Results will be populated here -->
    <!--            </div>-->
    <!--        </div>-->
            
            <!-- Statistics -->
    <!--        <div class="stats-section">-->
    <!--            <div class="stats-grid">-->
    <!--                <div class="stat-item">-->
    <!--                    <span class="stat-number" data-target="1000">0</span>-->
    <!--                    <span class="stat-label">Active Shops</span>-->
    <!--                </div>-->
    <!--                <div class="stat-item">-->
    <!--                    <span class="stat-number" data-target="50000">0</span>-->
    <!--                    <span class="stat-label">Receipts Generated</span>-->
    <!--                </div>-->
    <!--                <div class="stat-item">-->
    <!--                    <span class="stat-number" data-target="25000">0</span>-->
    <!--                    <span class="stat-label">Protected Phones</span>-->
    <!--                </div>-->
    <!--                <div class="stat-item">-->
    <!--                    <span class="stat-number" data-target="98">0</span>-->
    <!--                    <span class="stat-label">Success Rate %</span>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
            
            <!-- Support Text -->
    <!--        <p class="support-text">-->
    <!--            For issues or suggestions contact your market chairman or any AMPAT Exco to contact M-right state Coordinator-->
    <!--        </p>-->
    <!--    </div>-->
    <!--</div>-->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Counter Animation
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.dataset.target);
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;
                
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.floor(current).toLocaleString();
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target.toLocaleString();
                        if (target === 98) counter.textContent += '%';
                    }
                };
                
                updateCounter();
            });
        }
        
        // Search/Verification Form
        document.getElementById('verificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const searchInput = document.getElementById('searchInput');
            const loading = document.getElementById('searchLoading');
            const resultDiv = document.getElementById('searchResult');
            const receiptNumber = searchInput.value.trim();
            
            if (!receiptNumber) {
                showError('Please enter a receipt number');
                return;
            }
            
            // Show loading
            loading.style.display = 'block';
            resultDiv.style.display = 'none';
            
            // Simulate search (replace with actual API call)
            setTimeout(() => {
                loading.style.display = 'none';
                
                // For demo purposes - replace with actual verification
                if (receiptNumber.startsWith('MR-')) {
                    showSuccess(receiptNumber);
                } else {
                    showError('Invalid receipt number format');
                }
            }, 1500);
        });
        
        function showSuccess(receiptNumber) {
            const resultDiv = document.getElementById('searchResult');
            resultDiv.className = 'result-section result-success';
            resultDiv.innerHTML = `
                <h5 class="text-success mb-3">
                    <i class="fas fa-check-circle me-2"></i>
                    Receipt Verified Successfully!
                </h5>
                <p class="mb-3"><strong>Receipt Number:</strong> ${receiptNumber}</p>
                <p class="mb-3"><strong>Status:</strong> <span class="badge bg-success">Valid</span></p>
                <div class="text-center">
                    <a href="/verify/${receiptNumber}" class="btn btn-primary me-2">
                        <i class="fas fa-eye me-1"></i>View Full Details
                    </a>
                    <button onclick="hideResult()" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>
            `;
            resultDiv.style.display = 'block';
        }
        
        function showError(message) {
            const resultDiv = document.getElementById('searchResult');
            resultDiv.className = 'result-section result-error';
            resultDiv.innerHTML = `
                <h5 class="text-danger mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Verification Failed
                </h5>
                <p class="mb-3">${message}</p>
                <div class="text-center">
                    <button onclick="hideResult()" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>
            `;
            resultDiv.style.display = 'block';
        }
        
        function hideResult() {
            document.getElementById('searchResult').style.display = 'none';
            document.getElementById('searchInput').value = '';
        }
        
        function showForgotPassword() {
            alert('Password reset functionality coming soon! Please contact your administrator.');
        }
        
        // Initialize animations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            animateCounters();
        });
        
        // Enhanced search with actual API
        async function performActualSearch(receiptNumber) {
            try {
                const response = await fetch('/public/search', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ receipt_number: receiptNumber })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showActualSuccess(data.receipt);
                } else {
                    showError(data.message || 'Receipt not found');
                }
            } catch (error) {
                showError('Search failed. Please try again.');
            }
        }
        
        function showActualSuccess(receipt) {
            const resultDiv = document.getElementById('searchResult');
            resultDiv.className = 'result-section result-success';
            resultDiv.innerHTML = `
                <h5 class="text-success mb-3">
                    <i class="fas fa-check-circle me-2"></i>
                    Receipt Found & Verified!
                </h5>
                <div class="row text-start">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Receipt #:</strong> ${receipt.receipt_number}</p>
                        <p class="mb-2"><strong>Customer:</strong> ${receipt.customer_name}</p>
                        <p class="mb-2"><strong>Phone:</strong> ${receipt.phone_name}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>Shop:</strong> ${receipt.shop_name}</p>
                        <p class="mb-2"><strong>Date:</strong> ${receipt.created_date}</p>
                        <p class="mb-2"><strong>Status:</strong> <span class="badge bg-success">Verified</span></p>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="/verify/${receipt.receipt_number}" class="btn btn-primary me-2">
                        <i class="fas fa-eye me-1"></i>View Full Receipt
                    </a>
                    <button onclick="hideResult()" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>
            `;
            resultDiv.style.display = 'block';
        }
    </script>
</body>
</html>
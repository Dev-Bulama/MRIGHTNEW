<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>M-Right Anti-Theft | Nigeria's #1 Phone Anti-Theft & Ownership Verification Platform</title>
    <meta name="description" content="Verify phone ownership, generate Phone Anti-Theft Digital Receipts, report missing phones, and protect yourself from stolen device fraud in Nigeria.">
    <meta name="keywords" content="phone anti-theft, IMEI verification, phone receipt Nigeria, stolen phone, device verification, M-Right">
    <meta property="og:title" content="M-Right Anti-Theft - Phone Ownership Verification Platform">
    <meta property="og:description" content="Nigeria's premier phone anti-theft and ownership verification platform. Register devices, verify ownership, report missing phones.">
    <meta property="og:type" content="website">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #C0392B;
            --primary-dark: #96281B;
            --primary-light: #E74C3C;
            --accent: #2C3E50;
            --accent2: #3498DB;
            --success: #27AE60;
            --warning: #F39C12;
            --danger: #E74C3C;
            --light-bg: #F8FAFC;
            --text-dark: #1A252F;
            --text-muted: #7F8C8D;
            --border: #E8ECEF;
            --shadow: 0 4px 24px rgba(0,0,0,0.08);
            --shadow-lg: 0 12px 40px rgba(0,0,0,0.15);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: var(--text-dark); overflow-x: hidden; background: #fff; }

        /* ===== NAVBAR ===== */
        .navbar-custom {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
        }
        .navbar-brand-text { font-size: 1.4rem; font-weight: 800; color: var(--primary); }
        .navbar-brand-sub { font-size: 0.65rem; color: var(--text-muted); display: block; font-weight: 500; letter-spacing: 1px; text-transform: uppercase; }
        .nav-link-custom { color: var(--text-dark) !important; font-weight: 500; font-size: 0.9rem; padding: 0.5rem 1rem !important; border-radius: 8px; transition: all 0.2s; }
        .nav-link-custom:hover { background: #F0F4F8; color: var(--primary) !important; }
        .btn-nav-primary { background: var(--primary); color: white !important; border-radius: 50px; padding: 0.5rem 1.5rem !important; font-weight: 600; font-size: 0.85rem; }
        .btn-nav-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }

        /* ===== HERO ===== */
        .hero-section {
            background: linear-gradient(135deg, #C0392B 0%, #96281B 40%, #1A252F 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 6rem 0 4rem;
        }
        .hero-bg-pattern {
            position: absolute; inset: 0; pointer-events: none;
            background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.04) 0%, transparent 50%),
                              radial-gradient(circle at 80% 20%, rgba(255,255,255,0.06) 0%, transparent 50%);
        }
        .hero-badge { background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.95); border: 1px solid rgba(255,255,255,0.25); border-radius: 50px; padding: 0.4rem 1.2rem; font-size: 0.8rem; font-weight: 600; display: inline-block; margin-bottom: 1.5rem; letter-spacing: 0.5px; }
        .hero-title { font-size: clamp(2.2rem, 5vw, 3.8rem); font-weight: 900; color: white; line-height: 1.1; margin-bottom: 1.5rem; }
        .hero-title span { color: #F8C471; }
        .hero-subtitle { font-size: 1.1rem; color: rgba(255,255,255,0.85); line-height: 1.7; margin-bottom: 2.5rem; max-width: 520px; }
        .hero-cta-group { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 3rem; }
        .btn-hero-primary { background: white; color: var(--primary); border: none; border-radius: 50px; padding: 0.9rem 2rem; font-size: 0.95rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
        .btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.3); color: var(--primary); }
        .btn-hero-outline { background: transparent; color: white; border: 2px solid rgba(255,255,255,0.5); border-radius: 50px; padding: 0.9rem 2rem; font-size: 0.95rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s; }
        .btn-hero-outline:hover { background: rgba(255,255,255,0.15); border-color: white; color: white; transform: translateY(-2px); }
        .hero-stats { display: flex; gap: 2rem; flex-wrap: wrap; }
        .hero-stat-item { text-align: center; }
        .hero-stat-num { font-size: 1.8rem; font-weight: 800; color: white; line-height: 1; }
        .hero-stat-label { font-size: 0.75rem; color: rgba(255,255,255,0.7); margin-top: 0.2rem; text-transform: uppercase; letter-spacing: 0.5px; }

        /* ===== SEARCH PHONE SECTION ===== */
        .search-phone-section {
            background: var(--light-bg);
            padding: 5rem 0;
            position: relative;
        }
        .search-phone-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent2), var(--success));
        }
        .search-card {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            max-width: 680px;
            margin: 0 auto;
        }
        .search-card-title { font-size: 1.8rem; font-weight: 800; color: var(--text-dark); margin-bottom: 0.5rem; }
        .search-card-subtitle { color: var(--text-muted); margin-bottom: 2rem; font-size: 0.95rem; }
        .search-label { font-weight: 600; font-size: 0.85rem; color: var(--accent); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .search-input-custom { border: 2px solid var(--border); border-radius: 12px; padding: 0.85rem 1.2rem; font-size: 1rem; width: 100%; transition: all 0.2s; outline: none; }
        .search-input-custom:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(192,57,43,0.1); }
        .btn-search-verify { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; border: none; border-radius: 12px; padding: 1rem 2rem; font-size: 1rem; font-weight: 700; width: 100%; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top: 1rem; }
        .btn-search-verify:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(192,57,43,0.4); }
        .btn-search-verify:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .otp-section { display: none; }
        .otp-input-group { display: flex; gap: 0.5rem; justify-content: center; margin: 1.5rem 0; }
        .otp-digit { width: 48px; height: 56px; border: 2px solid var(--border); border-radius: 10px; text-align: center; font-size: 1.4rem; font-weight: 700; outline: none; transition: all 0.2s; }
        .otp-digit:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(192,57,43,0.15); }
        .search-step { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: #F0F4F8; border-radius: 10px; margin-bottom: 0.5rem; }
        .step-num { width: 28px; height: 28px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; }
        .step-text { font-size: 0.875rem; color: var(--accent); font-weight: 500; }

        /* ===== RESULT CARDS ===== */
        .result-card { display: none; border-radius: 20px; padding: 2rem; margin-top: 1.5rem; }
        .result-verified { background: linear-gradient(135deg, #EAFAF1, #D5F5E3); border: 2px solid #27AE60; }
        .result-missing { background: linear-gradient(135deg, #FEF9E7, #FDEBD0); border: 2px solid #E74C3C; }
        .result-unknown { background: linear-gradient(135deg, #F4F6F7, #EAF2FF); border: 2px solid #3498DB; }
        .result-badge { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.2rem; border-radius: 50px; font-size: 0.85rem; font-weight: 700; margin-bottom: 1rem; }
        .badge-verified { background: #27AE60; color: white; }
        .badge-missing { background: #E74C3C; color: white; }
        .badge-unknown { background: #3498DB; color: white; }
        .result-title { font-size: 1.4rem; font-weight: 800; margin-bottom: 0.75rem; }
        .result-info-row { display: flex; justify-content: space-between; padding: 0.6rem 0; border-bottom: 1px solid rgba(0,0,0,0.06); font-size: 0.9rem; }
        .result-info-label { font-weight: 600; color: var(--text-muted); }
        .result-info-value { font-weight: 600; color: var(--text-dark); }
        .reward-box { background: rgba(243,156,18,0.12); border: 1px solid #F39C12; border-radius: 12px; padding: 1rem; margin-top: 1rem; font-size: 0.9rem; }
        .reward-box strong { color: #D68910; }

        /* ===== FEATURES ===== */
        .features-section { padding: 5rem 0; background: white; }
        .section-label { font-size: 0.8rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 0.75rem; }
        .section-title { font-size: clamp(1.6rem, 3vw, 2.4rem); font-weight: 800; color: var(--text-dark); margin-bottom: 1rem; line-height: 1.2; }
        .section-subtitle { color: var(--text-muted); font-size: 1rem; max-width: 540px; }
        .feature-card { background: var(--light-bg); border-radius: 20px; padding: 2rem; height: 100%; border: 1px solid var(--border); transition: all 0.3s; }
        .feature-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: transparent; }
        .feature-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1.25rem; }
        .feature-title { font-size: 1.05rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.5rem; }
        .feature-desc { font-size: 0.875rem; color: var(--text-muted); line-height: 1.6; }

        /* ===== HOW IT WORKS ===== */
        .how-section { padding: 5rem 0; background: var(--light-bg); }
        .how-step { text-align: center; padding: 1.5rem; }
        .how-step-num { width: 64px; height: 64px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; margin: 0 auto 1.25rem; box-shadow: 0 8px 20px rgba(192,57,43,0.3); }
        .how-step-title { font-size: 1rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.5rem; }
        .how-step-desc { font-size: 0.875rem; color: var(--text-muted); line-height: 1.6; }
        .how-divider { display: flex; align-items: center; justify-content: center; padding-top: 2rem; color: var(--text-muted); font-size: 1.5rem; }

        /* ===== DIGITAL RECEIPT CTA ===== */
        .receipt-cta-section { padding: 5rem 0; background: linear-gradient(135deg, #1A252F 0%, #2C3E50 100%); }
        .receipt-cta-section .section-title { color: white; }
        .receipt-cta-section .section-subtitle { color: rgba(255,255,255,0.7); }
        .receipt-feature-item { display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 1rem; }
        .receipt-feature-icon { color: #F8C471; margin-top: 2px; flex-shrink: 0; }
        .receipt-feature-text { color: rgba(255,255,255,0.85); font-size: 0.9rem; }

        /* ===== MISSING PHONE PROCESS ===== */
        .missing-section { padding: 5rem 0; background: white; }
        .missing-step-card { background: var(--light-bg); border-radius: 16px; padding: 1.5rem; border-left: 4px solid var(--primary); margin-bottom: 1rem; }
        .missing-step-num { font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-bottom: 0.25rem; }
        .missing-step-title { font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.25rem; }
        .missing-step-desc { font-size: 0.825rem; color: var(--text-muted); }

        /* ===== STATISTICS ===== */
        .stats-section { padding: 4rem 0; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }
        .stat-card { text-align: center; padding: 1.5rem; }
        .stat-number { font-size: 2.8rem; font-weight: 900; color: white; line-height: 1; }
        .stat-label { color: rgba(255,255,255,0.8); font-size: 0.85rem; margin-top: 0.3rem; font-weight: 500; }

        /* ===== FAQ ===== */
        .faq-section { padding: 5rem 0; background: var(--light-bg); }
        .faq-item { background: white; border-radius: 12px; margin-bottom: 0.75rem; border: 1px solid var(--border); overflow: hidden; }
        .faq-question { padding: 1.25rem 1.5rem; font-weight: 600; font-size: 0.95rem; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
        .faq-answer { padding: 0 1.5rem 1.25rem; font-size: 0.875rem; color: var(--text-muted); line-height: 1.7; display: none; }
        .faq-answer.show { display: block; }

        /* ===== SHARE SECTION ===== */
        .share-section { padding: 4rem 0; background: white; }
        .share-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; border-radius: 50px; font-size: 0.85rem; font-weight: 600; text-decoration: none; color: white; transition: all 0.2s; }
        .share-btn:hover { transform: translateY(-2px); color: white; box-shadow: 0 4px 16px rgba(0,0,0,0.2); }
        .share-wa { background: #25D366; }
        .share-fb { background: #1877F2; }
        .share-tw { background: #000; }
        .share-li { background: #0A66C2; }
        .share-tg { background: #2CA5E0; }
        .share-em { background: #EA4335; }
        .share-cp { background: var(--accent); }

        /* ===== FOOTER ===== */
        .footer-section { background: var(--text-dark); color: rgba(255,255,255,0.7); padding: 3rem 0 1.5rem; }
        .footer-brand { font-size: 1.3rem; font-weight: 800; color: white; margin-bottom: 0.5rem; }
        .footer-desc { font-size: 0.85rem; line-height: 1.7; }
        .footer-link { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.85rem; display: block; margin-bottom: 0.5rem; transition: color 0.2s; }
        .footer-link:hover { color: white; }
        .footer-heading { color: white; font-weight: 600; font-size: 0.9rem; margin-bottom: 1rem; }
        .footer-divider { border-color: rgba(255,255,255,0.1); margin: 2rem 0 1.5rem; }
        .footer-bottom { font-size: 0.8rem; text-align: center; }

        /* ===== MISC ===== */
        .bg-primary-custom { background: var(--primary); }
        .text-primary-custom { color: var(--primary); }
        .alert-message { display: none; padding: 0.75rem 1rem; border-radius: 10px; margin-top: 1rem; font-size: 0.9rem; font-weight: 500; }
        .alert-success-custom { background: #EAFAF1; color: #1E8449; border: 1px solid #A9DFBF; }
        .alert-error-custom { background: #FDEDEC; color: #922B21; border: 1px solid #F1948A; }
        .spinner-border-sm { width: 1rem; height: 1rem; border-width: 2px; }

        @media (max-width: 768px) {
            .hero-stats { gap: 1.5rem; justify-content: center; }
            .search-card { padding: 1.75rem; }
            .otp-digit { width: 40px; height: 48px; font-size: 1.2rem; }
            .how-divider { display: none; }
            .share-btn { padding: 0.5rem 0.9rem; font-size: 0.8rem; }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar-custom">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <div class="navbar-brand-text">M-Right Anti-Theft</div>
                    <span class="navbar-brand-sub">Phone Anti-Theft & Ownership Verification</span>
                </a>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="#search-phone" class="nav-link-custom d-none d-md-block">Verify Phone</a>
                    <a href="#how-it-works" class="nav-link-custom d-none d-md-block">How It Works</a>
                    <a href="#faq" class="nav-link-custom d-none d-md-block">FAQ</a>
                    <a href="{{ route('receipt.portal.login') }}" class="nav-link-custom">Receipt Portal</a>
                    <a href="{{ route('login') }}" class="nav-link-custom btn-nav-primary">Shop Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="hero-bg-pattern"></div>
        <div class="container position-relative">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="hero-badge">
                        <i class="fas fa-shield-halved me-2"></i>Nigeria's #1 Anti-Theft Platform
                    </div>
                    <h1 class="hero-title">
                        Protect Your Phone.<br>
                        <span>Verify Before You Buy.</span>
                    </h1>
                    <p class="hero-subtitle">
                        Generate Phone Anti-Theft Digital Receipts, verify used phones before purchase, report missing devices, and help law enforcement track stolen phones across Nigeria.
                    </p>
                    <div class="hero-cta-group">
                        <a href="#search-phone" class="btn-hero-primary">
                            <i class="fas fa-search"></i> Verify a Phone Now
                        </a>
                        <a href="{{ route('register') }}" class="btn-hero-outline">
                            <i class="fas fa-store"></i> Register Your Shop
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat-item">
                            <div class="hero-stat-num">{{ number_format(\App\Models\Receipt::count()) }}+</div>
                            <div class="hero-stat-label">Receipts Generated</div>
                        </div>
                        <div class="hero-stat-item">
                            <div class="hero-stat-num">{{ number_format(\App\Models\Shop::where('approved', true)->count()) }}+</div>
                            <div class="hero-stat-label">Verified Shops</div>
                        </div>
                        <div class="hero-stat-item">
                            <div class="hero-stat-num">36+</div>
                            <div class="hero-stat-label">States Covered</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <div style="background:rgba(255,255,255,0.08);border-radius:24px;padding:2rem;border:1px solid rgba(255,255,255,0.15);">
                        <div class="text-center text-white mb-3">
                            <i class="fas fa-mobile-screen-button" style="font-size:3rem;color:#F8C471;"></i>
                        </div>
                        @foreach([['shield-check','Device Verified','Green badge of trust'],['triangle-exclamation','Missing Alert','Instant owner notification'],['file-invoice','Digital Receipt','Tamper-proof ownership proof'],['map-pin','GPS Intelligence','Location tracking for law enforcement']] as $f)
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div style="width:40px;height:40px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-{{ $f[0] }}" style="color:#F8C471;font-size:1rem;"></i>
                            </div>
                            <div>
                                <div style="color:white;font-weight:600;font-size:0.9rem;">{{ $f[1] }}</div>
                                <div style="color:rgba(255,255,255,0.65);font-size:0.8rem;">{{ $f[2] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SEARCH PHONE SECTION -->
    <section class="search-phone-section" id="search-phone">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center mb-4">
                    <div class="section-label">Phone Verification</div>
                    <h2 class="section-title">Search Phone</h2>
                    <p class="section-subtitle mx-auto">Enter the phone's serial number and seller's WhatsApp. We'll send the seller a verification code to confirm they actually own the phone.</p>
                </div>
            </div>
            <div class="search-card" id="search-form-card">
                <!-- Step Indicators -->
                <div class="mb-3">
                    <div class="search-step">
                        <div class="step-num">1</div>
                        <div class="step-text">Enter the phone IMEI/Serial number and seller's WhatsApp</div>
                    </div>
                    <div class="search-step">
                        <div class="step-num">2</div>
                        <div class="step-text">Seller receives a verification code on their WhatsApp</div>
                    </div>
                    <div class="search-step">
                        <div class="step-num">3</div>
                        <div class="step-text">Ask the seller for the code and enter it below</div>
                    </div>
                </div>

                <!-- Step 1: Input Form -->
                <div id="step1">
                    <div class="mb-3">
                        <label class="search-label">Phone Serial Number (IMEI / Serial)</label>
                        <input type="text" id="serial_number" class="search-input-custom" placeholder="Enter Phone Serial Number" maxlength="20">
                    </div>
                    <div class="mb-3">
                        <label class="search-label">Seller WhatsApp Number</label>
                        <input type="tel" id="seller_whatsapp" class="search-input-custom" placeholder="Enter Seller WhatsApp Number">
                    </div>
                    <button class="btn-search-verify" id="btn-initiate" onclick="initiateSearch()">
                        <i class="fas fa-search-plus"></i>
                        <span id="initiate-text">Verify Phone</span>
                        <span class="spinner-border spinner-border-sm d-none" id="initiate-spinner"></span>
                    </button>
                    <div class="alert-message" id="step1-alert"></div>
                </div>

                <!-- Step 2: OTP Verification -->
                <div id="step2" class="otp-section">
                    <div class="text-center mb-3">
                        <i class="fas fa-message" style="font-size:2.5rem;color:var(--primary);"></i>
                        <h5 class="mt-2 fw-700">Enter Verification Code</h5>
                        <p class="text-muted small" id="otp-desc">A 6-digit code was sent to the seller's WhatsApp. Ask the seller for the code.</p>
                    </div>
                    <div class="otp-input-group">
                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        <input type="text" class="otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]">
                    </div>
                    <button class="btn-search-verify" id="btn-verify" onclick="verifyOtp()">
                        <i class="fas fa-check-circle"></i>
                        <span id="verify-text">Verify & Search Phone</span>
                        <span class="spinner-border spinner-border-sm d-none" id="verify-spinner"></span>
                    </button>
                    <div class="text-center mt-2">
                        <button class="btn btn-link text-muted small p-0" onclick="goBack()">← Go back and try again</button>
                    </div>
                    <div class="alert-message" id="step2-alert"></div>
                </div>

                <!-- Result Cards -->
                <!-- Case 1: Found & Not Missing -->
                <div class="result-card result-verified" id="result-case1">
                    <div class="result-badge badge-verified">
                        <i class="fas fa-shield-check"></i> Device Verified
                    </div>
                    <h4 class="result-title text-success" id="r1-title">Device Verified</h4>
                    <p class="text-muted mb-3" id="r1-desc"></p>
                    <div class="border-top border-bottom py-2 mb-2">
                        <div class="result-info-row"><span class="result-info-label">Owner Name</span><span class="result-info-value" id="r1-owner"></span></div>
                        <div class="result-info-row"><span class="result-info-label">Verified Phone</span><span class="result-info-value" id="r1-phone"></span></div>
                        <div class="result-info-row"><span class="result-info-label">Date Registered</span><span class="result-info-value" id="r1-date"></span></div>
                        <div class="result-info-row"><span class="result-info-label">Device</span><span class="result-info-value" id="r1-device"></span></div>
                    </div>
                    <p class="small text-muted mt-2">For instant return of lost-but-found phones, please contact the owner using the details above.</p>
                    <div class="reward-box mt-2">
                        <strong><i class="fas fa-gift me-1"></i> Reward Notice:</strong>
                        Call <strong id="r1-reward-phone">08013131313</strong> to receive a cash reward equivalent to the value of a recovered phone.
                    </div>
                </div>

                <!-- Case 2: Found & Missing -->
                <div class="result-card result-missing" id="result-case2">
                    <div class="result-badge badge-missing">
                        <i class="fas fa-triangle-exclamation"></i> Missing Device Alert
                    </div>
                    <h4 class="result-title text-danger">Missing Device Alert</h4>
                    <p id="r2-desc" class="text-muted mb-3"></p>
                    <div class="border-top border-bottom py-2 mb-2">
                        <div class="result-info-row"><span class="result-info-label">Owner</span><span class="result-info-value" id="r2-owner"></span></div>
                        <div class="result-info-row"><span class="result-info-label">Phone</span><span class="result-info-value" id="r2-phone"></span></div>
                        <div class="result-info-row"><span class="result-info-label">Date Reported</span><span class="result-info-value" id="r2-date"></span></div>
                    </div>
                    <div class="alert alert-danger border-0 mt-2">
                        <strong>⚠ This device has been reported missing.</strong> If you found this phone, kindly return it to its rightful owner immediately.
                    </div>
                    <div class="reward-box">
                        <strong><i class="fas fa-gift me-1"></i> Reward Notice:</strong>
                        Call <strong id="r2-reward-phone">08013131313</strong> to receive a reward for returning this device.
                    </div>
                </div>

                <!-- Case 3: Not Found -->
                <div class="result-card result-unknown" id="result-case3">
                    <div class="result-badge badge-unknown">
                        <i class="fas fa-question-circle"></i> Device Not Found
                    </div>
                    <h4 class="result-title" style="color:var(--accent2)">Device Not Found</h4>
                    <p class="text-muted mb-3">This phone is not registered in our Anti-Theft database.</p>
                    <div class="mb-3">
                        <strong class="small">Possible reasons:</strong>
                        <ul class="small text-muted mt-1">
                            <li>Phone has never been registered</li>
                            <li>No Phone Anti-Theft Digital Receipt exists</li>
                            <li>Serial Number may have been entered incorrectly</li>
                        </ul>
                    </div>
                    <div class="alert alert-info border-0">
                        <strong>Before purchasing this phone:</strong>
                        <ul class="mb-0 mt-1 small">
                            <li>Verify ownership with original documentation</li>
                            <li>Request the original packaging and receipt</li>
                            <li>Ensure you trust the seller completely</li>
                        </ul>
                    </div>
                    <div class="reward-box">
                        <strong><i class="fas fa-phone me-1"></i> Need Help?</strong>
                        Call <strong id="r3-reward-phone">08013131313</strong> if this phone belongs to someone you know.
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button class="btn btn-outline-secondary btn-sm rounded-pill px-4 d-none" id="btn-search-again" onclick="resetSearch()">
                        <i class="fas fa-redo me-1"></i> Search Another Phone
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-6">
                    <div class="section-label">Why M-Right</div>
                    <h2 class="section-title">Complete Phone Protection</h2>
                    <p class="section-subtitle mx-auto">Everything you need to buy, sell, and protect phones with confidence.</p>
                </div>
            </div>
            <div class="row g-4">
                @foreach([
                    ['shield-halved','#FDEDEC','#E74C3C','Anti-Theft Protection','Register your phone and get instant alerts when someone searches your device serial number.'],
                    ['file-invoice','#EAF2FF','#3498DB','Phone Anti-Theft Digital Receipts','Tamper-proof digital receipts that prove ownership and survive device transfers.'],
                    ['magnifying-glass','#EAFAF1','#27AE60','Instant Verification','Search any phone serial number in seconds to know its ownership status before buying.'],
                    ['bell','#FEF9E7','#F39C12','Missing Phone Alerts','Report phones as missing — any future search immediately alerts you and notifies law enforcement.'],
                    ['arrows-rotate','#F4F0FF','#8E44AD','Ownership Transfer','Transfer ownership cleanly when reselling — full history maintained forever.'],
                    ['building-columns','#E8F8F5','#1ABC9C','Law Enforcement Ready','All intelligence is securely stored and available to authorized law enforcement agencies.'],
                ] as $f)
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon" style="background:{{ $f[1] }};color:{{ $f[2] }};">
                            <i class="fas fa-{{ $f[0] }}"></i>
                        </div>
                        <div class="feature-title">{{ $f[3] }}</div>
                        <div class="feature-desc">{{ $f[4] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="how-section" id="how-it-works">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-6">
                    <div class="section-label">The Process</div>
                    <h2 class="section-title">How It Works</h2>
                </div>
            </div>
            <div class="row g-0 align-items-start">
                @foreach([
                    ['1','store','Register Shop','Phone shops register and get approved on the M-Right platform.'],
                    ['2','mobile-screen-button','Generate Receipt','Create a Phone Anti-Theft Digital Receipt for every phone sold, capturing owner details and serial.'],
                    ['3','search','Buyer Verifies','Before buying used phones, buyers search the serial to verify ownership and missing status.'],
                    ['4','triangle-exclamation','Report Missing','If a phone is stolen, owners report it missing instantly from their Receipt Portal.'],
                    ['5','location-dot','Intelligence Gathered','When missing phones are searched, we capture searcher identity and location data for law enforcement.'],
                ] as $i => $s)
                <div class="col">
                    <div class="how-step">
                        <div class="how-step-num">{{ $s[0] }}</div>
                        <div><i class="fas fa-{{ $s[1] }} mb-2" style="font-size:1.5rem;color:var(--primary);"></i></div>
                        <div class="how-step-title">{{ $s[2] }}</div>
                        <div class="how-step-desc">{{ $s[3] }}</div>
                    </div>
                </div>
                @if($i < 4)
                <div class="col-auto how-divider"><i class="fas fa-arrow-right text-muted" style="font-size:1rem;"></i></div>
                @endif
                @endforeach
            </div>
        </div>
    </section>

    <!-- DIGITAL RECEIPT CTA -->
    <section class="receipt-cta-section" id="digital-receipt">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="section-label" style="color:#F8C471;">Digital Receipt</div>
                    <h2 class="section-title">Phone Anti-Theft Digital Receipt</h2>
                    <p class="section-subtitle">Your phone's digital ownership certificate. Generated by verified shops, stored forever, accessible anywhere.</p>
                    <div class="mt-4">
                        @foreach(['Proof of ownership recognized by law enforcement','Transfer ownership securely when reselling','Report phone missing with one click','Download and print anytime from your portal','Full ownership history maintained forever'] as $item)
                        <div class="receipt-feature-item">
                            <i class="fas fa-check-circle receipt-feature-icon"></i>
                            <span class="receipt-feature-text">{{ $item }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 d-flex gap-3 flex-wrap">
                        <a href="{{ route('receipt.portal.login') }}" class="btn-hero-primary">
                            <i class="fas fa-sign-in-alt"></i> Access Receipt Portal
                        </a>
                        <a href="{{ route('register') }}" class="btn-hero-outline">
                            <i class="fas fa-store"></i> Generate for Your Shop
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div style="background:rgba(255,255,255,0.06);border-radius:20px;padding:2rem;border:1px solid rgba(255,255,255,0.12);">
                        <div class="text-center mb-3" style="color:rgba(255,255,255,0.5);font-size:0.8rem;text-transform:uppercase;letter-spacing:1px;">Sample Receipt</div>
                        <div style="background:white;border-radius:16px;padding:1.5rem;">
                            <div class="text-center mb-3">
                                <div style="font-size:0.75rem;color:var(--text-muted);font-weight:600;letter-spacing:1px;text-transform:uppercase;">Phone Anti-Theft Digital Receipt</div>
                                <div style="font-size:1.1rem;font-weight:800;color:var(--primary);">MR-TEC202506-001234</div>
                            </div>
                            @foreach([['Phone Model','Samsung Galaxy A54'],['Color','Midnight Black'],['IMEI/Serial','35XXXXXXX12345'],['Owner','John A. Doe'],['Date','June 20, 2026'],['Status','✓ Active & Verified']] as $row)
                            <div style="display:flex;justify-content:space-between;padding:0.4rem 0;border-bottom:1px solid #F0F0F0;font-size:0.82rem;">
                                <span style="color:var(--text-muted);font-weight:600;">{{ $row[0] }}</span>
                                <span style="color:var(--text-dark);font-weight:600;">{{ $row[1] }}</span>
                            </div>
                            @endforeach
                            <div class="text-center mt-3">
                                <span style="background:#EAFAF1;color:#27AE60;padding:0.3rem 1rem;border-radius:50px;font-size:0.75rem;font-weight:700;">🛡 Anti-Theft Protected</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MISSING PHONE PROCESS -->
    <section class="missing-section" id="missing-process">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-6">
                    <div class="section-label" style="color:#E74C3C;">Missing Phone</div>
                    <h2 class="section-title">What Happens When Your Phone is Stolen?</h2>
                    <p class="section-subtitle mx-auto">Our system creates a digital dragnet that makes selling your stolen phone nearly impossible.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    @foreach([
                        ['01','Report It Missing','Log in to your Receipt Portal and declare your phone missing in one click.'],
                        ['02','Alert Goes Live','Instantly, any search of your phone serial shows a red missing alert to all searchers.'],
                        ['03','Intelligence Collected','We silently capture the IP, location, browser fingerprint and even camera image of anyone searching your stolen phone.'],
                        ['04','Law Enforcement','All intelligence is packaged and ready for authorized law enforcement to retrieve and act on.'],
                        ['05','Claim Your Reward','When the phone is returned, call our reward line to claim your cash reward.'],
                    ] as $step)
                    <div class="missing-step-card">
                        <div class="missing-step-num">{{ $step[0] }}</div>
                        <div class="missing-step-title">{{ $step[1] }}</div>
                        <div class="missing-step-desc">{{ $step[2] }}</div>
                    </div>
                    @endforeach
                </div>
                <div class="col-lg-6">
                    <div style="background:linear-gradient(135deg,#FDEDEC,#FFF5F5);border-radius:20px;padding:2rem;border:2px dashed #E74C3C;">
                        <div class="text-center mb-3">
                            <i class="fas fa-triangle-exclamation" style="font-size:3rem;color:#E74C3C;"></i>
                            <h5 class="mt-2 fw-bold text-danger">What a Thief Sees</h5>
                        </div>
                        <div style="background:white;border-radius:12px;padding:1.5rem;border:2px solid #E74C3C;">
                            <div style="background:#E74C3C;color:white;padding:0.5rem 1rem;border-radius:8px;text-align:center;font-weight:700;margin-bottom:1rem;">⚠ MISSING DEVICE ALERT</div>
                            <div style="font-size:0.85rem;color:var(--text-dark);">
                                <p><strong>This Samsung Galaxy A54 was officially declared missing.</strong></p>
                                <p class="text-muted">Owner: John A. Doe | Date Reported: June 20, 2026</p>
                                <div class="alert alert-danger p-2 small">This device has been reported missing. If you found this phone, kindly return it immediately.</div>
                                <div style="background:#FEF9E7;border:1px solid #F39C12;border-radius:8px;padding:0.75rem;font-size:0.8rem;">
                                    <strong>Reward:</strong> Call 08013131313 to receive a reward.
                                </div>
                            </div>
                        </div>
                        <p class="text-center text-muted small mt-3">Simultaneously, we capture this searcher's location, identity and photo.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATISTICS -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                @foreach([
                    [number_format(\App\Models\Receipt::count()), 'Digital Receipts Generated'],
                    [number_format(\App\Models\Shop::where('approved',true)->count()), 'Verified Phone Shops'],
                    ['36', 'States Covered'],
                    ['24/7', 'Monitoring Active'],
                ] as $stat)
                <div class="col-6 col-lg-3">
                    <div class="stat-card">
                        <div class="stat-number">{{ $stat[0] }}</div>
                        <div class="stat-label">{{ $stat[1] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq-section" id="faq">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-6">
                    <div class="section-label">FAQ</div>
                    <h2 class="section-title">Frequently Asked Questions</h2>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    @foreach([
                        ['What is a Phone Anti-Theft Digital Receipt?','A Phone Anti-Theft Digital Receipt is a tamper-proof digital certificate of phone ownership generated by verified M-Right shops. It contains the phone\'s details, IMEI/serial number, and owner information. It serves as legal proof of ownership and links the phone to the anti-theft system.'],
                        ['How do I verify a phone before buying?','Scroll up to the "Search Phone" section. Enter the phone\'s IMEI or serial number and the seller\'s WhatsApp number. We\'ll send the seller a verification code. Enter the code, and we\'ll instantly show you the phone\'s ownership and missing status.'],
                        ['What happens if I report my phone missing?','Once you report your phone missing via your Receipt Portal, any future search of that phone\'s serial number will immediately show a red "Missing Device Alert" to the searcher. We also silently capture intelligence about the searcher including their IP address, location, and browser fingerprint.'],
                        ['How do I log in to my Receipt Portal?','You need your registered phone number and your 6-digit Resale PIN, both found on your Phone Anti-Theft Digital Receipt. Click "Receipt Portal" in the top navigation.'],
                        ['Can I transfer my phone\'s registration when I sell it?','Yes. When you sell your phone, you (or the buyer\'s verified shop) can generate a new receipt that transfers ownership. The full ownership history is maintained.'],
                        ['Is this service available nationwide?','Yes. M-Right operates across all 36 states and the FCT. Any verified phone shop in Nigeria can generate Phone Anti-Theft Digital Receipts.'],
                    ] as $i => $faq)
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            {{ $faq[0] }}
                            <i class="fas fa-chevron-down text-muted faq-icon"></i>
                        </div>
                        <div class="faq-answer {{ $i === 0 ? 'show' : '' }}">{{ $faq[1] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- SHARE SECTION -->
    <section class="share-section" id="share">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-7">
                    <div class="section-label">Spread the Word</div>
                    <h2 class="section-title mb-2">Share Our Platform</h2>
                    <p class="text-muted mb-4">Help protect more Nigerians from phone theft. Share M-Right with friends, family, and your community.</p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center mb-4">
                        <a href="https://wa.me/?text=Verify%20phones%20before%20buying%20%26%20register%20your%20phone%20for%20anti-theft%20protection%20in%20Nigeria%20%F0%9F%9B%A1%EF%B8%8F%20{{ urlencode(config('app.url')) }}" target="_blank" class="share-btn share-wa"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(config('app.url')) }}" target="_blank" class="share-btn share-fb"><i class="fab fa-facebook-f"></i> Facebook</a>
                        <a href="https://twitter.com/intent/tweet?text=Protect%20your%20phone%20with%20M-Right%20Anti-Theft%20%F0%9F%9B%A1&url={{ urlencode(config('app.url')) }}" target="_blank" class="share-btn share-tw"><i class="fab fa-x-twitter"></i> X (Twitter)</a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(config('app.url')) }}" target="_blank" class="share-btn share-li"><i class="fab fa-linkedin-in"></i> LinkedIn</a>
                        <a href="https://t.me/share/url?url={{ urlencode(config('app.url')) }}&text=Verify%20phones%20before%20buying" target="_blank" class="share-btn share-tg"><i class="fab fa-telegram-plane"></i> Telegram</a>
                        <a href="mailto:?subject=Check%20out%20M-Right%20Anti-Theft&body=Protect%20your%20phone%20with%20M-Right%3A%20{{ config('app.url') }}" class="share-btn share-em"><i class="fas fa-envelope"></i> Email</a>
                        <button class="share-btn share-cp" onclick="copyLink()"><i class="fas fa-link"></i> Copy Link</button>
                    </div>
                    <div id="copy-alert" class="alert alert-success py-2 d-none" style="border-radius:50px;">Link copied to clipboard!</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand">M-Right Anti-Theft</div>
                    <div class="footer-desc mt-2">Nigeria's premier phone anti-theft and ownership verification platform. Protecting buyers, empowering owners, assisting law enforcement.</div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="footer-heading">Platform</div>
                    <a href="#search-phone" class="footer-link">Verify Phone</a>
                    <a href="{{ route('receipt.portal.login') }}" class="footer-link">Receipt Portal</a>
                    <a href="{{ route('register') }}" class="footer-link">Register Shop</a>
                    <a href="{{ route('login') }}" class="footer-link">Shop Login</a>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="footer-heading">Features</div>
                    <a href="#digital-receipt" class="footer-link">Digital Receipt</a>
                    <a href="#missing-process" class="footer-link">Report Missing</a>
                    <a href="#features" class="footer-link">How It Works</a>
                    <a href="#faq" class="footer-link">FAQ</a>
                </div>
                <div class="col-lg-4">
                    <div class="footer-heading">Contact</div>
                    <div class="footer-desc">Reward & Recovery Line: <strong style="color:white;">{{ \App\Models\SystemSetting::get('reward_phone', '08013131313') }}</strong></div>
                    <div class="footer-desc mt-1">For law enforcement and official inquiries, use the admin contact portal.</div>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="footer-bottom">
                © {{ date('Y') }} M-Right Digital Services. All rights reserved. | Nigeria's #1 Phone Anti-Theft & Verification Platform
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let sessionToken = null;
    let currentSerial = null;

    function showAlert(id, msg, type='error') {
        const el = document.getElementById(id);
        el.className = 'alert-message ' + (type === 'success' ? 'alert-success-custom' : 'alert-error-custom');
        el.textContent = msg;
        el.style.display = 'block';
    }
    function hideAlert(id) { document.getElementById(id).style.display = 'none'; }

    async function initiateSearch() {
        const serial = document.getElementById('serial_number').value.trim();
        const whatsapp = document.getElementById('seller_whatsapp').value.trim();
        hideAlert('step1-alert');

        if (!serial || serial.length < 6) { showAlert('step1-alert', 'Please enter a valid IMEI or Serial Number (minimum 6 characters).'); return; }
        if (!whatsapp || whatsapp.length < 7) { showAlert('step1-alert', 'Please enter the seller\'s WhatsApp number.'); return; }

        const btn = document.getElementById('btn-initiate');
        btn.disabled = true;
        document.getElementById('initiate-text').textContent = 'Sending Code...';
        document.getElementById('initiate-spinner').classList.remove('d-none');

        try {
            const resp = await fetch('{{ route("phone.search.initiate") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ serial_number: serial, seller_whatsapp: whatsapp })
            });
            const data = await resp.json();

            if (data.success) {
                sessionToken = data.session_token;
                currentSerial = serial;
                document.getElementById('otp-desc').textContent = data.message;
                document.getElementById('step1').style.display = 'none';
                document.getElementById('step2').style.display = 'block';
                document.querySelectorAll('.otp-digit')[0].focus();
                captureIntelligenceSilently(serial);
            } else {
                showAlert('step1-alert', data.message || 'Failed to send code. Please try again.');
            }
        } catch(e) {
            showAlert('step1-alert', 'Network error. Please check your connection and try again.');
        } finally {
            btn.disabled = false;
            document.getElementById('initiate-text').textContent = 'Verify Phone';
            document.getElementById('initiate-spinner').classList.add('d-none');
        }
    }

    async function verifyOtp() {
        const digits = document.querySelectorAll('.otp-digit');
        const otp = Array.from(digits).map(d => d.value).join('');
        hideAlert('step2-alert');

        if (otp.length !== 6 || !/^\d{6}$/.test(otp)) { showAlert('step2-alert', 'Please enter all 6 digits of the verification code.'); return; }
        if (!sessionToken) { showAlert('step2-alert', 'Session expired. Please start over.'); return; }

        const btn = document.getElementById('btn-verify');
        btn.disabled = true;
        document.getElementById('verify-text').textContent = 'Verifying...';
        document.getElementById('verify-spinner').classList.remove('d-none');

        try {
            const resp = await fetch('{{ route("phone.search.verify") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ session_token: sessionToken, otp: otp })
            });
            const data = await resp.json();

            if (data.success) {
                showSearchResult(data.result);
                // Attempt camera capture for missing phone
                if (data.result.case === 2) {
                    requestCameraCapture(data.result.serial_number);
                }
            } else {
                showAlert('step2-alert', data.message || 'Verification failed. Please try again.');
            }
        } catch(e) {
            showAlert('step2-alert', 'Network error. Please try again.');
        } finally {
            btn.disabled = false;
            document.getElementById('verify-text').textContent = 'Verify & Search Phone';
            document.getElementById('verify-spinner').classList.add('d-none');
        }
    }

    function showSearchResult(result) {
        ['result-case1','result-case2','result-case3'].forEach(id => document.getElementById(id).style.display='none');
        const rewardPhone = result.reward_phone || '08013131313';

        if (result.case === 1) {
            document.getElementById('r1-title').textContent = 'Device Verified';
            document.getElementById('r1-desc').textContent = `This ${result.phone_model} is currently registered and actively used by its verified owner.`;
            document.getElementById('r1-owner').textContent = result.owner_name || '—';
            document.getElementById('r1-phone').textContent = result.owner_phone || '—';
            document.getElementById('r1-date').textContent = result.date_registered || '—';
            document.getElementById('r1-device').textContent = (result.phone_color ? result.phone_color + ' ' : '') + result.phone_model;
            document.getElementById('r1-reward-phone').textContent = rewardPhone;
            document.getElementById('result-case1').style.display = 'block';
        } else if (result.case === 2) {
            document.getElementById('r2-desc').textContent = `This ${result.phone_model} was officially declared missing.`;
            document.getElementById('r2-owner').textContent = result.owner_name || '—';
            document.getElementById('r2-phone').textContent = result.owner_phone || '—';
            document.getElementById('r2-date').textContent = result.date_reported || '—';
            document.getElementById('r2-reward-phone').textContent = rewardPhone;
            document.getElementById('result-case2').style.display = 'block';
        } else {
            document.getElementById('r3-reward-phone').textContent = rewardPhone;
            document.getElementById('result-case3').style.display = 'block';
        }
        document.getElementById('btn-search-again').classList.remove('d-none');
        document.getElementById('step2').style.display = 'none';
    }

    function resetSearch() {
        document.getElementById('serial_number').value = '';
        document.getElementById('seller_whatsapp').value = '';
        document.querySelectorAll('.otp-digit').forEach(d => d.value = '');
        document.getElementById('step1').style.display = 'block';
        document.getElementById('step2').style.display = 'none';
        ['result-case1','result-case2','result-case3'].forEach(id => document.getElementById(id).style.display='none');
        document.getElementById('btn-search-again').classList.add('d-none');
        sessionToken = null; currentSerial = null;
        hideAlert('step1-alert'); hideAlert('step2-alert');
    }

    function goBack() {
        document.getElementById('step2').style.display = 'none';
        document.getElementById('step1').style.display = 'block';
        document.querySelectorAll('.otp-digit').forEach(d => d.value = '');
        sessionToken = null;
    }

    // OTP digit auto-advance
    document.addEventListener('DOMContentLoaded', function() {
        const digits = document.querySelectorAll('.otp-digit');
        digits.forEach((digit, i) => {
            digit.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g,'').slice(-1);
                if (this.value && i < digits.length - 1) digits[i+1].focus();
            });
            digit.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && i > 0) digits[i-1].focus();
                if (e.key === 'Enter') verifyOtp();
            });
        });
        document.getElementById('serial_number').addEventListener('keydown', e => { if (e.key === 'Enter') initiateSearch(); });
        document.getElementById('seller_whatsapp').addEventListener('keydown', e => { if (e.key === 'Enter') initiateSearch(); });
    });

    // FAQ toggle
    function toggleFaq(el) {
        const answer = el.nextElementSibling;
        const icon = el.querySelector('.faq-icon');
        const isOpen = answer.classList.contains('show');
        document.querySelectorAll('.faq-answer').forEach(a => a.classList.remove('show'));
        document.querySelectorAll('.faq-icon').forEach(i => i.style.transform = '');
        if (!isOpen) { answer.classList.add('show'); icon.style.transform = 'rotate(180deg)'; }
    }

    // Share copy link
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            const alert = document.getElementById('copy-alert');
            alert.classList.remove('d-none');
            setTimeout(() => alert.classList.add('d-none'), 3000);
        });
    }

    // Silent intelligence capture
    function captureIntelligenceSilently(serial) {
        const data = {
            serial_number: serial,
            browser: navigator.userAgent.match(/(Chrome|Safari|Firefox|Edge|Opera)[\/\s][\d\.]+/)?.[0] || 'Unknown',
            os: navigator.platform || 'Unknown',
            device_type: /Mobi|Android/i.test(navigator.userAgent) ? 'Mobile' : 'Desktop',
            screen_resolution: screen.width + 'x' + screen.height,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            network_type: navigator.connection ? navigator.connection.effectiveType : null,
            fingerprint: {
                languages: navigator.languages,
                platform: navigator.platform,
                vendor: navigator.vendor,
                cookieEnabled: navigator.cookieEnabled,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                screen: { w: screen.width, h: screen.height, depth: screen.colorDepth }
            }
        };

        // Request GPS
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                data.latitude = pos.coords.latitude;
                data.longitude = pos.coords.longitude;
                sendIntelligence(data);
            }, () => sendIntelligence(data), { timeout: 8000 });
        } else {
            sendIntelligence(data);
        }
    }

    function sendIntelligence(data) {
        fetch('{{ route("phone.search.intelligence") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(data)
        }).catch(() => {});
    }

    // Camera capture for missing phone searches
    function requestCameraCapture(serial) {
        if (!navigator.mediaDevices || !window.isSecureContext) return;
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
            .then(stream => {
                const video = document.createElement('video');
                video.srcObject = stream;
                video.play();
                setTimeout(() => {
                    const canvas = document.createElement('canvas');
                    canvas.width = 320; canvas.height = 240;
                    canvas.getContext('2d').drawImage(video, 0, 0, 320, 240);
                    const imageData = canvas.toDataURL('image/jpeg', 0.7);
                    stream.getTracks().forEach(t => t.stop());

                    fetch('{{ route("phone.search.capture") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body: JSON.stringify({ serial_number: serial, image: imageData })
                    }).catch(() => {});
                }, 1500);
            }).catch(() => {});
    }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>M-Right Anti-Theft | Nigeria's #1 Phone Anti-Theft & Ownership Verification Platform</title>
    <meta name="description" content="Verify phone ownership before buying. Generate Phone Anti-Theft Digital Receipts. Report missing phones instantly.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary:#C0392B; --primary-dark:#96281B; --primary-light:#E74C3C;
            --accent:#2C3E50; --success:#27AE60; --warning:#F39C12;
            --light-bg:#F8FAFC; --text-dark:#1A252F; --text-muted:#7F8C8D;
            --border:#E8ECEF; --shadow:0 4px 24px rgba(0,0,0,0.08);
        }
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',sans-serif;color:var(--text-dark);overflow-x:hidden;background:#fff;}

        /* NAVBAR */
        .navbar-custom{background:rgba(255,255,255,0.97);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,0.2);padding:0.9rem 0;position:fixed;top:0;left:0;right:0;z-index:1000;box-shadow:0 2px 20px rgba(0,0,0,0.15);}
        .navbar-brand-text{font-size:1.2rem;font-weight:800;color:var(--primary);}
        .navbar-brand-sub{font-size:0.6rem;color:var(--text-muted);display:block;font-weight:500;letter-spacing:1px;text-transform:uppercase;}
        .nav-link-custom{color:var(--text-dark)!important;font-weight:500;font-size:0.875rem;padding:0.45rem 0.9rem!important;border-radius:8px;transition:all 0.2s;text-decoration:none;}
        .nav-link-custom:hover{background:#F0F4F8;color:var(--primary)!important;}
        .btn-nav-primary{background:var(--primary);color:white!important;border-radius:50px;padding:0.45rem 1.3rem!important;font-weight:600;font-size:0.85rem;transition:all 0.2s;}
        .btn-nav-primary:hover{background:var(--primary-dark);}

        /* HERO */
        .hero-section{
            background:linear-gradient(160deg, #96281B 0%, #C0392B 35%, #2C3E50 100%);
            min-height:100vh;
            display:flex;align-items:center;justify-content:center;
            padding:7rem 1rem 4rem;
            text-align:center;
            position:relative;overflow:hidden;
        }
        .hero-bg-shapes{position:absolute;inset:0;pointer-events:none;overflow:hidden;}
        .hero-shape{position:absolute;border-radius:50%;background:rgba(255,255,255,0.04);}
        .hero-shape-1{width:500px;height:500px;top:-100px;right:-100px;}
        .hero-shape-2{width:300px;height:300px;bottom:-50px;left:-80px;}
        .hero-shape-3{width:200px;height:200px;top:40%;right:10%;}

        .hero-badge{display:inline-flex;align-items:center;gap:0.5rem;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);color:rgba(255,255,255,0.9);border-radius:50px;padding:0.4rem 1.1rem;font-size:0.78rem;font-weight:600;letter-spacing:0.5px;margin-bottom:1.5rem;}
        .hero-title{font-size:clamp(2rem,5.5vw,3.8rem);font-weight:900;color:white;line-height:1.1;margin-bottom:1rem;}
        .hero-title span{color:#F8C471;}
        .hero-subtitle{font-size:1rem;color:rgba(255,255,255,0.75);line-height:1.7;margin-bottom:2.5rem;max-width:540px;margin-left:auto;margin-right:auto;}

        /* ===== GOOGLE-STYLE SEARCH BOX ===== */
        .search-hero-wrap{max-width:660px;margin:0 auto;position:relative;z-index:10;}
        .search-hero-box{
            background:white;
            border-radius:50px;
            box-shadow:0 8px 40px rgba(0,0,0,0.35),0 0 0 1px rgba(255,255,255,0.1);
            display:flex;align-items:center;
            padding:0.45rem 0.45rem 0.45rem 1.6rem;
            gap:0.5rem;
            transition:box-shadow 0.2s;
        }
        .search-hero-box:focus-within{box-shadow:0 12px 50px rgba(0,0,0,0.4),0 0 0 3px rgba(248,196,113,0.5);}
        .search-hero-icon{color:#7F8C8D;font-size:1.1rem;flex-shrink:0;}
        .search-hero-input{
            flex:1;border:none;outline:none;font-family:'Inter',sans-serif;
            font-size:1rem;font-weight:500;color:var(--text-dark);
            background:transparent;padding:0.5rem 0.25rem;min-width:0;
        }
        .search-hero-input::placeholder{color:#B0BAC3;}
        .search-hero-btn{
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            color:white;border:none;border-radius:50px;
            padding:0.7rem 1.6rem;font-size:0.9rem;font-weight:700;
            cursor:pointer;transition:all 0.25s;white-space:nowrap;flex-shrink:0;
            display:flex;align-items:center;gap:0.4rem;
        }
        .search-hero-btn:hover{background:linear-gradient(135deg,#E74C3C,var(--primary));box-shadow:0 4px 20px rgba(192,57,43,0.5);}
        .search-hero-btn:disabled{opacity:0.7;cursor:not-allowed;}
        .search-suggestions-text{margin-top:1rem;font-size:0.8rem;color:rgba(255,255,255,0.6);}
        .search-suggestions-text a{color:rgba(255,255,255,0.85);text-decoration:none;background:rgba(255,255,255,0.1);padding:0.25rem 0.7rem;border-radius:50px;border:1px solid rgba(255,255,255,0.15);margin:0.2rem;display:inline-block;transition:all 0.2s;font-size:0.77rem;}
        .search-suggestions-text a:hover{background:rgba(255,255,255,0.2);color:white;}

        /* RESULT AREA (below search) */
        .hero-result-area{margin-top:1.5rem;text-align:left;}
        .result-card{border-radius:20px;padding:1.75rem;display:none;animation:fadeInUp 0.3s ease;}
        @keyframes fadeInUp{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}
        .result-verified{background:white;border:2px solid #27AE60;box-shadow:0 8px 40px rgba(39,174,96,0.2);}
        .result-missing{background:white;border:2px solid #E74C3C;box-shadow:0 8px 40px rgba(231,76,60,0.2);}
        .result-unknown{background:white;border:2px solid #3498DB;box-shadow:0 8px 40px rgba(52,152,219,0.15);}
        .result-error{background:white;border:2px solid #F39C12;box-shadow:0 8px 40px rgba(243,156,18,0.15);}
        .result-badge{display:inline-flex;align-items:center;gap:0.5rem;padding:0.35rem 1rem;border-radius:50px;font-size:0.8rem;font-weight:700;margin-bottom:1rem;}
        .badge-ok{background:#EAFAF1;color:#1E8449;}
        .badge-missing{background:#FDEDEC;color:#922B21;}
        .badge-unknown{background:#EAF2FF;color:#1A5276;}
        .result-title{font-size:1.25rem;font-weight:800;color:var(--text-dark);margin-bottom:0.4rem;}
        .result-desc{font-size:0.875rem;color:var(--text-muted);margin-bottom:1rem;}
        .result-grid{display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-bottom:1rem;}
        .result-field{background:#F8FAFC;border-radius:10px;padding:0.6rem 0.9rem;}
        .result-field-label{font-size:0.72rem;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;}
        .result-field-value{font-size:0.9rem;font-weight:700;color:var(--text-dark);margin-top:0.15rem;}
        .reward-notice{background:#FEF9E7;border:1px solid #F9E79F;border-radius:10px;padding:0.75rem 1rem;font-size:0.82rem;}
        .reward-notice strong{color:#B7950B;}
        .not-found-tips{background:#F4F6F7;border-radius:12px;padding:1rem;margin-top:0.75rem;}
        .not-found-tips ul{margin:0;padding-left:1.25rem;}
        .not-found-tips li{font-size:0.82rem;color:var(--text-muted);margin-bottom:0.25rem;}
        .btn-search-again{background:transparent;border:2px solid var(--border);border-radius:50px;padding:0.45rem 1.2rem;font-size:0.82rem;font-weight:600;color:var(--text-muted);cursor:pointer;transition:all 0.2s;margin-top:1rem;}
        .btn-search-again:hover{border-color:var(--primary);color:var(--primary);}

        /* HERO STATS */
        .hero-stats-row{display:flex;gap:2rem;justify-content:center;flex-wrap:wrap;margin-top:2.5rem;}
        .hero-stat{text-align:center;}
        .hero-stat-num{font-size:1.5rem;font-weight:800;color:white;}
        .hero-stat-label{font-size:0.72rem;color:rgba(255,255,255,0.65);text-transform:uppercase;letter-spacing:0.5px;}

        /* SECTIONS */
        .section{padding:5rem 0;}
        .section-alt{background:var(--light-bg);}
        .section-label{font-size:0.75rem;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:2px;margin-bottom:0.6rem;}
        .section-title{font-size:clamp(1.5rem,3vw,2.3rem);font-weight:800;color:var(--text-dark);margin-bottom:0.75rem;line-height:1.2;}
        .section-sub{color:var(--text-muted);font-size:0.95rem;max-width:520px;}

        /* FEATURES */
        .feature-card{background:white;border-radius:20px;padding:1.75rem;border:1px solid var(--border);transition:all 0.25s;height:100%;}
        .feature-card:hover{transform:translateY(-3px);box-shadow:0 12px 40px rgba(0,0,0,0.1);border-color:transparent;}
        .feature-icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:1.1rem;}
        .feature-title{font-size:1rem;font-weight:700;color:var(--text-dark);margin-bottom:0.4rem;}
        .feature-desc{font-size:0.85rem;color:var(--text-muted);line-height:1.65;}

        /* HOW IT WORKS */
        .how-step-num{width:56px;height:56px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.3rem;font-weight:800;margin:0 auto 1rem;box-shadow:0 8px 20px rgba(192,57,43,0.3);}
        .how-step-title{font-size:0.95rem;font-weight:700;color:var(--text-dark);margin-bottom:0.4rem;}
        .how-step-desc{font-size:0.83rem;color:var(--text-muted);line-height:1.6;}

        /* DARK CTA */
        .dark-cta{background:linear-gradient(135deg,#1A252F,#2C3E50);padding:5rem 0;}
        .dark-cta .section-title{color:white;}
        .dark-cta .section-sub{color:rgba(255,255,255,0.65);}
        .receipt-feature{display:flex;align-items:flex-start;gap:0.65rem;margin-bottom:0.9rem;}
        .receipt-feature i{color:#F8C471;margin-top:2px;flex-shrink:0;}
        .receipt-feature span{color:rgba(255,255,255,0.8);font-size:0.875rem;}
        .btn-white{background:white;color:var(--primary);border:none;border-radius:50px;padding:0.8rem 1.8rem;font-size:0.9rem;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:0.4rem;transition:all 0.25s;box-shadow:0 4px 20px rgba(0,0,0,0.2);}
        .btn-white:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(0,0,0,0.3);color:var(--primary);}
        .btn-outline-white{background:transparent;color:white;border:2px solid rgba(255,255,255,0.4);border-radius:50px;padding:0.8rem 1.8rem;font-size:0.9rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:0.4rem;transition:all 0.25s;}
        .btn-outline-white:hover{border-color:white;background:rgba(255,255,255,0.1);color:white;}

        /* STATS BANNER */
        .stats-banner{background:linear-gradient(135deg,var(--primary),var(--primary-dark));padding:3.5rem 0;}
        .stat-num{font-size:2.5rem;font-weight:900;color:white;}
        .stat-label{font-size:0.82rem;color:rgba(255,255,255,0.75);font-weight:500;margin-top:0.2rem;}

        /* MISSING SECTION */
        .missing-step{background:white;border-radius:14px;padding:1.25rem;border-left:4px solid var(--primary);margin-bottom:0.75rem;box-shadow:0 2px 12px rgba(0,0,0,0.04);}
        .missing-num{font-size:1.3rem;font-weight:800;color:var(--primary);}
        .missing-title{font-size:0.9rem;font-weight:700;color:var(--text-dark);}
        .missing-desc{font-size:0.8rem;color:var(--text-muted);margin-top:0.15rem;}

        /* FAQ */
        .faq-item{background:white;border-radius:12px;border:1px solid var(--border);overflow:hidden;margin-bottom:0.65rem;}
        .faq-q{padding:1.1rem 1.4rem;font-weight:600;font-size:0.92rem;cursor:pointer;display:flex;justify-content:space-between;align-items:center;user-select:none;}
        .faq-a{padding:0 1.4rem 1.1rem;font-size:0.85rem;color:var(--text-muted);line-height:1.7;display:none;}
        .faq-a.show{display:block;}
        .faq-icon{transition:transform 0.2s;color:var(--text-muted);font-size:0.8rem;}

        /* DEMO BOX */
        .demo-box{background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:14px;padding:1rem 1.25rem;margin-top:1rem;text-align:left;}
        .demo-box-title{font-size:0.75rem;font-weight:700;color:rgba(255,255,255,0.65);text-transform:uppercase;letter-spacing:1px;margin-bottom:0.55rem;}
        .demo-pills-row{display:flex;flex-wrap:wrap;gap:0.35rem;}
        .demo-pill{display:inline-flex;align-items:center;gap:0.35rem;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);color:rgba(255,255,255,0.9);border-radius:50px;padding:0.28rem 0.75rem;font-size:0.78rem;cursor:pointer;transition:all 0.2s;font-family:'Inter',monospace;white-space:nowrap;-webkit-tap-highlight-color:transparent;}
        .demo-pill:hover,.demo-pill:active{background:rgba(255,255,255,0.22);color:white;}
        .demo-pill .dot{width:7px;height:7px;border-radius:50%;flex-shrink:0;}
        .dot-green{background:#27AE60;}
        .dot-red{background:#E74C3C;}
        .dot-blue{background:#3498DB;}

        /* SHARE */
        .share-btn{display:inline-flex;align-items:center;gap:0.4rem;padding:0.55rem 1.1rem;border-radius:50px;font-size:0.82rem;font-weight:600;text-decoration:none;color:white;transition:all 0.2s;}
        .share-btn:hover{transform:translateY(-2px);color:white;box-shadow:0 4px 16px rgba(0,0,0,0.2);}

        /* FOOTER */
        .footer{background:#1A252F;color:rgba(255,255,255,0.65);padding:3rem 0 1.5rem;}
        .footer-brand{font-size:1.2rem;font-weight:800;color:white;}
        .footer-link{color:rgba(255,255,255,0.55);text-decoration:none;font-size:0.84rem;display:block;margin-bottom:0.45rem;transition:color 0.2s;}
        .footer-link:hover{color:white;}
        .footer-heading{color:white;font-weight:600;font-size:0.88rem;margin-bottom:0.9rem;}

        /* LOADING SPINNER */
        .spin{animation:spin 0.8s linear infinite;display:inline-block;}
        @keyframes spin{to{transform:rotate(360deg);}}

        /* ===== MOBILE RESPONSIVE ===== */
        @media(max-width:991px){
            .navbar-custom{ padding:0.7rem 0; }
            .navbar-brand-text{ font-size:1rem; }
            /* Hide "Receipt Portal" text, show icon-only on small screens */
        }

        @media(max-width:575px){
            /* Navbar — keep it tight */
            .navbar-brand-sub{ display:none; }
            .nav-link-custom.portal-link{ font-size:0.8rem; padding:0.4rem 0.6rem!important; }
            .btn-nav-primary{ padding:0.4rem 0.9rem!important; font-size:0.8rem; }

            /* Hero */
            .hero-section{ padding:5.5rem 0.75rem 2.5rem; }
            .hero-badge{ font-size:0.72rem; padding:0.32rem 0.9rem; }
            .hero-title{ font-size:1.85rem; margin-bottom:0.75rem; }
            .hero-subtitle{ font-size:0.88rem; margin-bottom:2rem; }

            /* Search box — stacked on small screens */
            .search-hero-box{
                border-radius:18px;
                flex-direction:column;
                align-items:stretch;
                padding:0.75rem;
                gap:0.6rem;
            }
            .search-hero-icon{ display:none; } /* hide magnifier when stacked */
            .search-hero-input{
                font-size:0.95rem;
                padding:0.6rem 0.75rem;
                border-bottom:1px solid #E8ECEF;
                border-radius:10px;
                background:#F8FAFC;
            }
            .search-hero-btn{
                border-radius:12px;
                width:100%;
                justify-content:center;
                padding:0.8rem 1rem;
                font-size:0.9rem;
            }

            /* Demo pills — wrap nicely */
            .demo-box{ padding:0.85rem 1rem; }
            .demo-box-title{ font-size:0.72rem; }
            .demo-pill{ font-size:0.73rem; padding:0.25rem 0.65rem; }

            /* Results */
            .result-grid{ grid-template-columns:1fr; }
            .result-card{ padding:1.25rem; border-radius:16px; }
            .result-title{ font-size:1.1rem; }
            .result-badge{ font-size:0.75rem; }

            /* Hero stats */
            .hero-stats-row{ gap:1rem; margin-top:2rem; }
            .hero-stat-num{ font-size:1.3rem; }
            .hero-stat-label{ font-size:0.65rem; }

            /* Sections */
            .section{ padding:3rem 0; }
            .section-title{ font-size:1.5rem; }
            .how-step-num{ width:48px; height:48px; font-size:1rem; }

            /* Stats banner */
            .stat-num{ font-size:2rem; }

            /* Dark CTA */
            .dark-cta{ padding:3rem 0; }
        }

        @media(min-width:576px) and (max-width:767px){
            /* Tablet-ish: keep box horizontal but make button smaller */
            .search-hero-box{ border-radius:50px; }
            .search-hero-btn{ padding:0.65rem 1.2rem; font-size:0.85rem; }
            .hero-title{ font-size:2.4rem; }
            .result-grid{ grid-template-columns:1fr 1fr; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar-custom">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Brand -->
            <a href="{{ route('home') }}" class="text-decoration-none" style="flex-shrink:0;">
                <div class="navbar-brand-text">M-Right Anti-Theft</div>
                <span class="navbar-brand-sub">Phone Anti-Theft & Ownership Verification</span>
            </a>

            <!-- Desktop nav -->
            <div class="d-none d-md-flex align-items-center gap-1">
                <a href="#features" class="nav-link-custom">Features</a>
                <a href="#how-it-works" class="nav-link-custom">How It Works</a>
                <a href="#faq" class="nav-link-custom">FAQ</a>
                <a href="{{ route('receipt.portal.login') }}" class="nav-link-custom">Receipt Portal</a>
                <a href="{{ route('login') }}" class="nav-link-custom btn-nav-primary ms-1">Shop Login</a>
            </div>

            <!-- Mobile nav — only 2 key buttons -->
            <div class="d-flex d-md-none align-items-center gap-2">
                <a href="{{ route('receipt.portal.login') }}" class="nav-link-custom portal-link" style="font-size:0.8rem;padding:0.4rem 0.65rem!important;">
                    <i class="fas fa-shield-halved me-1"></i>Portal
                </a>
                <a href="{{ route('login') }}" class="nav-link-custom btn-nav-primary" style="font-size:0.8rem;padding:0.4rem 0.9rem!important;">
                    Login
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero-section" id="home">
    <div class="hero-bg-shapes">
        <div class="hero-shape hero-shape-1"></div>
        <div class="hero-shape hero-shape-2"></div>
        <div class="hero-shape hero-shape-3"></div>
    </div>
    <div class="container position-relative" style="z-index:5;">
        <!-- Badge -->
        <!-- Title -->
        <h1 class="hero-title" style="font-size:clamp(1.6rem,4.5vw,2.8rem);margin-bottom:0.6rem;">
            Phone Anti-Theft<br>
            <span>Digital Receipt</span>
        </h1>

        <p style="font-size:clamp(0.95rem,2.2vw,1.1rem);color:rgba(255,255,255,0.9);font-weight:600;line-height:1.65;max-width:560px;margin:0 auto 0.75rem;text-shadow:0 1px 3px rgba(0,0,0,0.25);">
            Verify any phone before you buy. This is to avoid buying stolen, robbery or kidnapping implicated fairly used phones that can land you in bigger trouble.
        </p>
        <p style="font-size:clamp(0.85rem,2vw,0.97rem);color:rgba(255,255,255,0.75);line-height:1.7;max-width:520px;margin:0 auto 2rem;">
            Dial <strong style="color:#F8C471;">*#06#</strong> to see the Serial Number of any phone. Use the last digits along with the seller's WhatsApp number to search the phone.
        </p>

        <!-- SEARCH BOX -->
        <div class="search-hero-wrap">
            <div class="search-hero-box" id="search-box">
                <i class="fas fa-mobile-screen-button search-hero-icon" id="search-icon"></i>
                <input
                    type="text"
                    class="search-hero-input"
                    id="serial-input"
                    placeholder="Enter Phone S/no (IMEI or Serial Number)"
                    autocomplete="off"
                    maxlength="30"
                    onkeydown="if(event.key==='Enter') doSearch()"
                >
                <button class="search-hero-btn" id="search-btn" onclick="doSearch()">
                    <span id="btn-text">Search Phone</span>
                    <i class="fas fa-arrow-right" id="btn-icon"></i>
                </button>
            </div>
            <div style="color:rgba(255,255,255,0.65);font-size:0.8rem;text-align:center;margin-top:0.5rem;">
                <i class="fas fa-whatsapp me-1" style="color:#25D366;"></i>
                Seller's WhatsApp no; (or other WhatsApp no related to the seller)
            </div>

            <!-- Demo Quick-Fill Pills -->
            <div class="demo-box">
                <div class="demo-box-title">Try a demo — tap any serial below:</div>
                <div class="demo-pills-row">
                    <span class="demo-pill" onclick="fillSearch('DEMO-IPHONE13-001')">
                        <span class="dot dot-green"></span>DEMO-IPHONE13-001
                    </span>
                    <span class="demo-pill" onclick="fillSearch('DEMO-SAMSUNG-002')">
                        <span class="dot dot-red"></span>DEMO-SAMSUNG-002
                    </span>
                    <span class="demo-pill" onclick="fillSearch('DEMO-TECNO-003')">
                        <span class="dot dot-green"></span>DEMO-TECNO-003
                    </span>
                    <span class="demo-pill" onclick="fillSearch('UNKNOWN-RANDOM-999')">
                        <span class="dot dot-blue"></span>UNKNOWN-RANDOM-999
                    </span>
                </div>
            </div>

            <!-- RESULT AREA -->
            <div class="hero-result-area" id="result-area">

                <!-- Case 1: Verified -->
                <div class="result-card result-verified" id="r-verified">
                    <div class="result-badge badge-ok"><i class="fas fa-shield-check"></i> Device Verified</div>
                    <div class="result-title" id="r1-title">Device Verified</div>
                    <div class="result-desc" id="r1-desc"></div>
                    <div class="result-grid">
                        <div class="result-field"><div class="result-field-label">Owner Name</div><div class="result-field-value" id="r1-name"></div></div>
                        <div class="result-field"><div class="result-field-label">Phone Number</div><div class="result-field-value" id="r1-phone"></div></div>
                        <div class="result-field"><div class="result-field-label">Date Registered</div><div class="result-field-value" id="r1-date"></div></div>
                        <div class="result-field"><div class="result-field-label">Device</div><div class="result-field-value" id="r1-device"></div></div>
                    </div>
                    <p class="small text-muted mb-2">For return of lost-but-found phones, please contact the owner above.</p>
                    <div class="reward-notice"><strong><i class="fas fa-gift me-1"></i> Reward:</strong> Call <strong id="r1-reward">08013131313</strong> to receive a cash reward for returning a recovered phone.</div>
                    <button class="btn-search-again" onclick="resetSearch()"><i class="fas fa-redo me-1"></i> Search Another</button>
                </div>

                <!-- Case 2: Missing -->
                <div class="result-card result-missing" id="r-missing">
                    <div class="result-badge badge-missing"><i class="fas fa-triangle-exclamation"></i> Missing Device Alert</div>
                    <div class="result-title text-danger">Missing Device Alert</div>
                    <div class="result-desc" id="r2-desc"></div>
                    <div class="result-grid">
                        <div class="result-field"><div class="result-field-label">Owner</div><div class="result-field-value" id="r2-name"></div></div>
                        <div class="result-field"><div class="result-field-label">Phone</div><div class="result-field-value" id="r2-phone"></div></div>
                        <div class="result-field" style="grid-column:span 2"><div class="result-field-label">Date Reported</div><div class="result-field-value" id="r2-date"></div></div>
                    </div>
                    <div class="alert alert-danger border-0 rounded-3 p-2 small">
                        <strong>⚠ This device has been reported missing.</strong> If you found this phone, return it to the rightful owner immediately.
                    </div>
                    <div class="reward-notice mt-2"><strong><i class="fas fa-gift me-1"></i> Reward:</strong> Call <strong id="r2-reward">08013131313</strong> to receive a reward for returning this device.</div>
                    <button class="btn-search-again" onclick="resetSearch()"><i class="fas fa-redo me-1"></i> Search Another</button>
                </div>

                <!-- Case 3: Not Found -->
                <div class="result-card result-unknown" id="r-notfound">
                    <div class="result-badge badge-unknown"><i class="fas fa-question-circle"></i> Device Not Found</div>
                    <div class="result-title" style="color:#1A5276;">Device Not Found</div>
                    <div class="result-desc">This phone is <strong>not registered</strong> in our Anti-Theft database.</div>
                    <div class="not-found-tips">
                        <div class="fw-600 small mb-1">Possible reasons:</div>
                        <ul>
                            <li>Phone has never been registered in M-Right</li>
                            <li>No Phone Anti-Theft Digital Receipt exists for this device</li>
                            <li>Serial / IMEI number may have been entered incorrectly</li>
                        </ul>
                    </div>
                    <div class="alert alert-info border-0 rounded-3 p-2 small mt-2">
                        <strong>Before purchasing:</strong> Verify ownership, request original packaging and receipt, and ensure you trust the seller.
                    </div>
                    <div class="reward-notice mt-2"><strong><i class="fas fa-phone me-1"></i> Need help?</strong> Call <strong id="r3-reward">08013131313</strong> if this phone belongs to someone you know.</div>
                    <button class="btn-search-again" onclick="resetSearch()"><i class="fas fa-redo me-1"></i> Search Another</button>
                </div>

                <!-- Error State -->
                <div class="result-card result-error" id="r-error">
                    <div class="result-badge" style="background:#FEF9E7;color:#B7950B;"><i class="fas fa-exclamation-circle"></i> Search Error</div>
                    <div class="result-title">Unable to Complete Search</div>
                    <div class="result-desc" id="r-error-msg">An error occurred. Please try again.</div>
                    <button class="btn-search-again" onclick="resetSearch()"><i class="fas fa-redo me-1"></i> Try Again</button>
                </div>

            </div>
        </div>

        <!-- Hero Stats -->
        <div class="hero-stats-row">
            <div class="hero-stat">
                <div class="hero-stat-num">{{ number_format(\App\Models\Receipt::count()) }}+</div>
                <div class="hero-stat-label">Receipts Generated</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-num">{{ number_format(\App\Models\Shop::where('approved', true)->count()) }}+</div>
                <div class="hero-stat-label">Verified Shops</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-num">36+</div>
                <div class="hero-stat-label">States Covered</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-num">24/7</div>
                <div class="hero-stat-label">Active Monitoring</div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="section" id="features">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">Why M-Right</div>
            <h2 class="section-title">Complete Phone Protection</h2>
            <p class="section-sub mx-auto">Everything you need to buy, sell, and protect phones with total confidence.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['shield-halved','#FDEDEC','#E74C3C','Anti-Theft Protection','Register your phone and get instant alerts when someone searches your device serial number.'],
                ['file-invoice','#EAF2FF','#3498DB','Phone Anti-Theft Digital Receipts','Tamper-proof digital receipts that prove ownership and survive device transfers.'],
                ['magnifying-glass','#EAFAF1','#27AE60','Instant Verification','Search any phone serial number in seconds before buying a used device.'],
                ['bell','#FEF9E7','#F39C12','Missing Phone Alerts','Report phones missing — any future search immediately triggers a red alert.'],
                ['arrows-rotate','#F4F0FF','#8E44AD','Ownership Transfer','Transfer ownership cleanly when reselling with full history preserved.'],
                ['building-columns','#E8F8F5','#1ABC9C','Law Enforcement Ready','Intelligence data securely stored and available to authorized agencies.'],
            ] as $f)
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon" style="background:{{ $f[1] }};color:{{ $f[2] }};"><i class="fas fa-{{ $f[0] }}"></i></div>
                    <div class="feature-title">{{ $f[3] }}</div>
                    <div class="feature-desc">{{ $f[4] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section section-alt" id="how-it-works">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">The Process</div>
            <h2 class="section-title">How It Works</h2>
        </div>
        <div class="row g-4 text-center">
            @foreach([
                ['store','Register Shop','Phone shops register and get approved on M-Right.'],
                ['mobile-screen-button','Generate Receipt','Create a Phone Anti-Theft Digital Receipt for every phone sold.'],
                ['search','Buyer Verifies','Buyers search the serial before purchasing any used phone.'],
                ['triangle-exclamation','Report Missing','Owners report stolen phones instantly from their Receipt Portal.'],
                ['location-dot','Intelligence Gathered','Searchers of missing phones are tracked and logged for law enforcement.'],
            ] as $s)
            <div class="col-6 col-lg">
                <div class="how-step-num"><i class="fas fa-{{ $s[0] }}" style="font-size:1.2rem;"></i></div>
                <div class="how-step-title">{{ $s[1] }}</div>
                <div class="how-step-desc">{{ $s[2] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- DIGITAL RECEIPT CTA -->
<section class="dark-cta" id="digital-receipt">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="section-label" style="color:#F8C471;">Receipt Portal</div>
                <h2 class="section-title">Phone Anti-Theft Digital Receipt</h2>
                <p class="section-sub mb-4">Your phone's official digital ownership certificate. Accessible anywhere, forever.</p>
                @foreach(['Proof of ownership recognized by law enforcement','Report phone missing in one click','Full ownership history on transfer','Download & print anytime','Secure Resale PIN login'] as $f)
                <div class="receipt-feature"><i class="fas fa-check-circle"></i><span>{{ $f }}</span></div>
                @endforeach
                <div class="d-flex gap-3 flex-wrap mt-4">
                    <a href="{{ route('receipt.portal.login') }}" class="btn-white"><i class="fas fa-sign-in-alt"></i> Access Portal</a>
                    <a href="{{ route('register') }}" class="btn-outline-white"><i class="fas fa-store"></i> Register Shop</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div style="background:rgba(255,255,255,0.06);border-radius:20px;padding:2rem;border:1px solid rgba(255,255,255,0.1);">
                    <div class="text-center mb-3" style="font-size:0.72rem;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1px;">Sample Receipt</div>
                    <div style="background:white;border-radius:14px;padding:1.5rem;">
                        <div class="text-center mb-3">
                            <div style="font-size:0.7rem;color:#7F8C8D;font-weight:600;text-transform:uppercase;letter-spacing:1px;">Phone Anti-Theft Digital Receipt</div>
                            <div style="font-size:1.05rem;font-weight:800;color:var(--primary);">MR-TEC202506-001234</div>
                        </div>
                        @foreach([['Device','Apple iPhone 13'],['Color','Midnight Black'],['IMEI','351XXXXXXX12345'],['Owner','Chukwuemeka Obi'],['Issued','June 20, 2026'],['Status','✓ Active & Verified']] as $r)
                        <div style="display:flex;justify-content:space-between;padding:0.35rem 0;border-bottom:1px solid #F0F0F0;font-size:0.8rem;">
                            <span style="color:#7F8C8D;font-weight:600;">{{ $r[0] }}</span>
                            <span style="color:#1A252F;font-weight:600;">{{ $r[1] }}</span>
                        </div>
                        @endforeach
                        <div class="text-center mt-3"><span style="background:#EAFAF1;color:#27AE60;padding:0.3rem 1rem;border-radius:50px;font-size:0.72rem;font-weight:700;">🛡 Anti-Theft Protected</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS BANNER -->
<section class="stats-banner">
    <div class="container">
        <div class="row text-center">
            @foreach([
                [number_format(\App\Models\Receipt::count()), 'Digital Receipts Generated'],
                [number_format(\App\Models\Shop::where('approved',true)->count()), 'Verified Phone Shops'],
                ['36+','States Covered'],
                ['24/7','Monitoring Active'],
            ] as $s)
            <div class="col-6 col-lg-3 mb-3 mb-lg-0">
                <div class="stat-num">{{ $s[0] }}</div>
                <div class="stat-label">{{ $s[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- MISSING PHONE PROCESS -->
<section class="section" id="missing-process">
    <div class="container">
        <div class="row justify-content-center text-center mb-5">
            <div class="col-lg-6">
                <div class="section-label" style="color:#E74C3C;">Missing Phone</div>
                <h2 class="section-title">When Your Phone is Stolen</h2>
                <p class="section-sub mx-auto">Our system makes selling your stolen phone nearly impossible.</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                @foreach([
                    ['01','Report It Missing','Log in to your Receipt Portal and click "Declare Missing" — takes 30 seconds.'],
                    ['02','Alert Goes Live','Immediately, any search of your phone serial shows a red Missing Alert to the searcher.'],
                    ['03','Intelligence Collected','We silently capture the IP address, GPS location, device fingerprint, and photo of anyone searching your stolen phone.'],
                    ['04','Law Enforcement','All intelligence is packaged and available to authorized law enforcement agencies.'],
                    ['05','Claim Your Reward','When recovered, call our reward line to claim your cash reward.'],
                ] as $step)
                <div class="missing-step">
                    <div class="missing-num">{{ $step[0] }}</div>
                    <div class="missing-title">{{ $step[1] }}</div>
                    <div class="missing-desc">{{ $step[2] }}</div>
                </div>
                @endforeach
            </div>
            <div class="col-lg-6 d-flex align-items-center">
                <div style="background:linear-gradient(135deg,#FDEDEC,#FFF5F5);border-radius:20px;padding:2rem;border:2px dashed #E74C3C;width:100%;">
                    <div class="text-center mb-3">
                        <i class="fas fa-triangle-exclamation" style="font-size:2.5rem;color:#E74C3C;"></i>
                        <h5 class="mt-2 fw-bold text-danger">What a Thief Sees</h5>
                    </div>
                    <div style="background:white;border-radius:12px;padding:1.5rem;border:2px solid #E74C3C;">
                        <div style="background:#E74C3C;color:white;padding:0.4rem 1rem;border-radius:8px;text-align:center;font-weight:700;margin-bottom:1rem;font-size:0.85rem;">⚠ MISSING DEVICE ALERT</div>
                        <div style="font-size:0.82rem;color:#1A252F;">
                            <p class="mb-1"><strong>This Samsung Galaxy S23 was officially declared missing.</strong></p>
                            <p class="text-muted mb-2" style="font-size:0.78rem;">Owner: Aisha Bello &nbsp;|&nbsp; Date Reported: Jun 17, 2026</p>
                            <div class="alert alert-danger p-2 small border-0">This device has been reported missing. If you found this phone, kindly return it immediately.</div>
                            <div style="background:#FEF9E7;border:1px solid #F39C12;border-radius:8px;padding:0.65rem;font-size:0.78rem;"><strong>Reward:</strong> Call 08013131313 to receive a reward.</div>
                        </div>
                    </div>
                    <p class="text-center small mt-2" style="color:#7F8C8D;">Simultaneously, we capture this searcher's location & identity.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section section-alt" id="faq">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-label">FAQ</div>
            <h2 class="section-title">Frequently Asked Questions</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @foreach([
                    ['What is a Phone Anti-Theft Digital Receipt?','A tamper-proof digital certificate of phone ownership generated by verified M-Right shops. It contains the phone\'s IMEI/serial, owner details, and purchase information. It serves as legal proof of ownership and links the phone to the anti-theft system.'],
                    ['How do I search a phone before buying?','Just type the phone\'s IMEI or serial number into the search bar at the top of this page and click "Verify Phone." You\'ll instantly see whether the phone is registered, its owner details, and whether it has been reported missing.'],
                    ['What happens if I report my phone missing?','Once you report it missing via your Receipt Portal, any future search of that phone\'s serial shows a red Missing Device Alert. We also silently capture the searcher\'s IP, location, browser fingerprint, and photo for law enforcement.'],
                    ['How do I log in to my Receipt Portal?','You need your registered phone number and your 6-digit Resale PIN from your Phone Anti-Theft Digital Receipt. Click "Receipt Portal" in the top navigation.'],
                    ['Can I use this service nationwide?','Yes. M-Right operates across all 36 states and the FCT. Any verified phone shop in Nigeria can generate Phone Anti-Theft Digital Receipts.'],
                ] as $i => $faq)
                <div class="faq-item">
                    <div class="faq-q" onclick="toggleFaq(this)">
                        {{ $faq[0] }}
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-a {{ $i===0?'show':'' }}">{{ $faq[1] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- SHARE -->
<section class="section">
    <div class="container text-center">
        <div class="section-label">Spread The Word</div>
        <h2 class="section-title mb-2">Share M-Right</h2>
        <p class="text-muted mb-4">Help protect more Nigerians from phone theft.</p>
        <div class="d-flex flex-wrap gap-2 justify-content-center mb-4">
            <a href="https://wa.me/?text=Verify%20phones%20before%20buying%20%F0%9F%9B%A1%EF%B8%8F%20{{ urlencode(config('app.url')) }}" target="_blank" class="share-btn" style="background:#25D366;"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(config('app.url')) }}" target="_blank" class="share-btn" style="background:#1877F2;"><i class="fab fa-facebook-f"></i> Facebook</a>
            <a href="https://twitter.com/intent/tweet?text=Protect%20your%20phone%20with%20M-Right%20Anti-Theft&url={{ urlencode(config('app.url')) }}" target="_blank" class="share-btn" style="background:#000;"><i class="fab fa-x-twitter"></i> X</a>
            <a href="https://t.me/share/url?url={{ urlencode(config('app.url')) }}" target="_blank" class="share-btn" style="background:#2CA5E0;"><i class="fab fa-telegram-plane"></i> Telegram</a>
            <a href="mailto:?subject=Check%20M-Right%20Anti-Theft&body={{ config('app.url') }}" class="share-btn" style="background:#EA4335;"><i class="fas fa-envelope"></i> Email</a>
            <button class="share-btn" style="background:#2C3E50;" onclick="copyLink()"><i class="fas fa-link"></i> Copy Link</button>
        </div>
        <div id="copy-alert" class="alert alert-success py-2 d-none d-inline-block rounded-pill px-4">Link copied!</div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand mb-2">M-Right Anti-Theft</div>
                <div style="font-size:0.85rem;line-height:1.7;">Nigeria's premier phone anti-theft and ownership verification platform. Protecting buyers, empowering owners, assisting law enforcement.</div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="footer-heading">Platform</div>
                <a href="#home" class="footer-link">Verify Phone</a>
                <a href="{{ route('receipt.portal.login') }}" class="footer-link">Receipt Portal</a>
                <a href="{{ route('register') }}" class="footer-link">Register Shop</a>
                <a href="{{ route('login') }}" class="footer-link">Shop Login</a>
            </div>
            <div class="col-lg-2 col-6">
                <div class="footer-heading">Info</div>
                <a href="#digital-receipt" class="footer-link">Digital Receipt</a>
                <a href="#missing-process" class="footer-link">Report Missing</a>
                <a href="#features" class="footer-link">Features</a>
                <a href="#faq" class="footer-link">FAQ</a>
            </div>
            <div class="col-lg-4">
                <div class="footer-heading">Recovery Line</div>
                <div style="font-size:0.88rem;line-height:1.7;">
                    Found a phone? Call: <strong style="color:white;">{{ \App\Models\SystemSetting::get('reward_phone', '08013131313') }}</strong><br>
                    Receive a cash reward for returning recovered phones to their rightful owners.
                </div>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,0.1);margin:2rem 0 1.25rem;">
        <div class="text-center" style="font-size:0.78rem;">
            © {{ date('Y') }} M-Right Anti-Theft System. All rights reserved. | Nigeria's #1 Phone Anti-Theft & Verification Platform
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ---- Fill demo serial ----
function fillSearch(serial) {
    document.getElementById('serial-input').value = serial;
    document.getElementById('serial-input').focus();
    // Auto-search after a short delay
    setTimeout(doSearch, 150);
}

// ---- Main search ----
async function doSearch() {
    const serial = document.getElementById('serial-input').value.trim();
    if (!serial) {
        document.getElementById('serial-input').focus();
        return;
    }

    // UI loading state
    const btn = document.getElementById('search-btn');
    btn.disabled = true;
    document.getElementById('btn-text').textContent = 'Searching…';
    document.getElementById('btn-icon').className = 'fas fa-circle-notch spin';
    document.getElementById('search-icon').className = 'fas fa-circle-notch spin search-hero-icon';
    hideAllResults();

    try {
        const resp = await fetch('{{ route("phone.search.direct") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ serial_number: serial })
        });

        if (!resp.ok) {
            const errText = await resp.text();
            throw new Error(`Server error ${resp.status}: ${errText.substring(0,200)}`);
        }

        const data = await resp.json();
        showResult(data);

        // Silent intelligence for missing phone
        if (data.case === 2) {
            captureIntelligence(serial);
        }

    } catch(e) {
        console.error('Search error:', e);
        document.getElementById('r-error-msg').textContent = e.message.includes('Server error')
            ? 'Server error. Please try again in a moment.'
            : 'Network error. Check your connection and try again.';
        show('r-error');
    } finally {
        btn.disabled = false;
        document.getElementById('btn-text').textContent = 'Verify Phone';
        document.getElementById('btn-icon').className = 'fas fa-arrow-right';
        document.getElementById('search-icon').className = 'fas fa-search search-hero-icon';
    }
}

function showResult(data) {
    const reward = data.reward_phone || '08013131313';
    hideAllResults();

    if (data.case === 1) {
        document.getElementById('r1-title').textContent = 'Device Verified';
        document.getElementById('r1-desc').textContent =
            `This ${data.phone_model} is currently registered and actively used by its verified owner.`;
        document.getElementById('r1-name').textContent = data.owner_name || '—';
        document.getElementById('r1-phone').textContent = data.owner_phone || '—';
        document.getElementById('r1-date').textContent = data.date_registered || '—';
        document.getElementById('r1-device').textContent = (data.phone_color ? data.phone_color + ' ' : '') + data.phone_model;
        document.getElementById('r1-reward').textContent = reward;
        show('r-verified');

    } else if (data.case === 2) {
        document.getElementById('r2-desc').textContent =
            `This ${data.phone_model} was officially declared missing.`;
        document.getElementById('r2-name').textContent = data.owner_name || '—';
        document.getElementById('r2-phone').textContent = data.owner_phone || '—';
        document.getElementById('r2-date').textContent = data.date_reported || '—';
        document.getElementById('r2-reward').textContent = reward;
        show('r-missing');

    } else {
        document.getElementById('r3-reward').textContent = reward;
        show('r-notfound');
    }

    // Scroll result into view on mobile
    document.getElementById('result-area').scrollIntoView({behavior:'smooth', block:'nearest'});
}

function hideAllResults() {
    ['r-verified','r-missing','r-notfound','r-error'].forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
}

function show(id) {
    document.getElementById(id).style.display = 'block';
}

function resetSearch() {
    document.getElementById('serial-input').value = '';
    hideAllResults();
    document.getElementById('serial-input').focus();
}

// ---- FAQ ----
function toggleFaq(el) {
    const a = el.nextElementSibling;
    const ic = el.querySelector('.faq-icon');
    const open = a.classList.contains('show');
    document.querySelectorAll('.faq-a').forEach(x => x.classList.remove('show'));
    document.querySelectorAll('.faq-icon').forEach(x => x.style.transform = '');
    if (!open) { a.classList.add('show'); ic.style.transform = 'rotate(180deg)'; }
}

// ---- Copy link ----
function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        const el = document.getElementById('copy-alert');
        el.classList.remove('d-none');
        setTimeout(() => el.classList.add('d-none'), 2500);
    });
}

// ---- Silent intelligence ----
function captureIntelligence(serial) {
    const data = {
        serial_number: serial,
        browser: navigator.userAgent,
        os: navigator.platform,
        device_type: /Mobi|Android/i.test(navigator.userAgent) ? 'Mobile' : 'Desktop',
        screen_resolution: screen.width + 'x' + screen.height,
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        fingerprint: { languages: navigator.languages, vendor: navigator.vendor }
    };
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            p => { data.latitude = p.coords.latitude; data.longitude = p.coords.longitude; sendIntel(data); },
            () => sendIntel(data),
            { timeout: 6000 }
        );
    } else { sendIntel(data); }
}
function sendIntel(data) {
    fetch('{{ route("phone.search.intelligence") }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body:JSON.stringify(data)
    }).catch(()=>{});
}
</script>
</body>
</html>

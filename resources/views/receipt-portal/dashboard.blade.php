<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt Portal – {{ $receipt->customer_name }} – M-Right</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --primary: #C0392B; --primary-dark: #96281B; }
        body { font-family: 'Inter', sans-serif; background: #F8FAFC; }
        .portal-header { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; padding: 1.25rem 0; }
        .portal-brand { font-weight: 800; font-size: 1.2rem; }
        .portal-brand small { font-size: 0.7rem; font-weight: 400; opacity: 0.8; display: block; }
        .page-header { background: white; border-bottom: 1px solid #E8ECEF; padding: 1.5rem 0; }
        .page-title { font-size: 1.5rem; font-weight: 800; color: #1A252F; margin-bottom: 0.25rem; }
        .page-sub { color: #7F8C8D; font-size: 0.875rem; }
        .badge-active { background: #EAFAF1; color: #1E8449; border: 1px solid #A9DFBF; padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.8rem; font-weight: 700; }
        .badge-missing { background: #FDEDEC; color: #922B21; border: 1px solid #F1948A; padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.8rem; font-weight: 700; }
        .action-card { background: white; border-radius: 16px; border: 1px solid #E8ECEF; padding: 1.75rem; height: 100%; transition: all 0.2s; }
        .action-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .action-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 1rem; }
        .action-title { font-weight: 700; font-size: 1rem; color: #1A252F; margin-bottom: 0.4rem; }
        .action-desc { font-size: 0.825rem; color: #7F8C8D; line-height: 1.5; }
        .btn-action { border-radius: 10px; font-weight: 600; font-size: 0.875rem; padding: 0.6rem 1.25rem; margin-top: 1rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; }
        .info-card { background: white; border-radius: 16px; border: 1px solid #E8ECEF; padding: 1.5rem; }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0; border-bottom: 1px solid #F0F4F8; font-size: 0.875rem; }
        .info-label { color: #7F8C8D; font-weight: 600; }
        .info-value { color: #1A252F; font-weight: 600; text-align: right; }
        .timeline-item { display: flex; gap: 1rem; margin-bottom: 1rem; }
        .timeline-dot { width: 32px; height: 32px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: white; font-size: 0.7rem; font-weight: 700; }
        .timeline-content { flex: 1; background: #F8FAFC; border-radius: 10px; padding: 0.75rem 1rem; }
        .timeline-title { font-weight: 600; font-size: 0.875rem; color: #1A252F; }
        .timeline-meta { font-size: 0.8rem; color: #7F8C8D; margin-top: 0.2rem; }
        .missing-warning { background: #FDEDEC; border: 2px solid #E74C3C; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; }
        .download-item { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #F8FAFC; border-radius: 10px; margin-bottom: 0.75rem; }
        .download-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="portal-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="portal-brand">
                <i class="fas fa-shield-halved me-2"></i> M-Right Anti-Theft
                <small>Phone Anti-Theft Digital Receipt Portal</small>
            </div>
            <form method="POST" action="{{ route('receipt.portal.logout') }}" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="page-title">Welcome, {{ $receipt->customer_name }}</div>
                <div class="page-sub">
                    Receipt #{{ $receipt->receipt_number }} &nbsp;|&nbsp;
                    @if($receipt->is_missing)
                        <span class="badge-missing"><i class="fas fa-triangle-exclamation me-1"></i>Phone Reported Missing</span>
                    @else
                        <span class="badge-active"><i class="fas fa-shield-check me-1"></i>Phone Active & Protected</span>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('receipt.portal.download-pdf') }}" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                    <i class="fas fa-download me-1"></i> Download Receipt
                </a>
            </div>
        </div>
    </div>

    <div class="container py-4">
        @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3 mb-4">{{ session('error') }}</div>
        @endif

        @if($receipt->is_missing)
        <div class="missing-warning">
            <h5 class="text-danger fw-bold mb-1"><i class="fas fa-triangle-exclamation me-2"></i>Your Phone is Currently Reported Missing</h5>
            <p class="text-muted mb-2 small">Any search for your phone's serial number will immediately show a Missing Alert to the searcher. If you've recovered your phone, click below to reverse the missing status.</p>
            <form method="POST" action="{{ route('receipt.portal.reverse-missing') }}">
                @csrf
                <button type="submit" class="btn btn-success btn-sm rounded-pill px-4" onclick="return confirm('Confirm your phone has been recovered?')">
                    <i class="fas fa-check me-1"></i> My Phone Has Been Recovered
                </button>
            </form>
        </div>
        @endif

        <div class="row g-4">
            <!-- Action Cards Row 1 -->
            @if(!$receipt->is_missing)
            <div class="col-md-6 col-lg-4">
                <div class="action-card">
                    <div class="action-icon" style="background:#FDEDEC;color:#E74C3C;">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div class="action-title">Declare Missing</div>
                    <div class="action-desc">Report your phone as missing or stolen. Future searchers will immediately see a Missing Alert.</div>
                    <button class="btn btn-danger btn-action" data-bs-toggle="modal" data-bs-target="#missingModal">
                        <i class="fas fa-bell"></i> Report Missing
                    </button>
                </div>
            </div>
            @endif

            <div class="col-md-6 col-lg-4">
                <div class="action-card">
                    <div class="action-icon" style="background:#EAF2FF;color:#3498DB;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="action-title">View Digital Receipt</div>
                    <div class="action-desc">Download, print, or share your Phone Anti-Theft Digital Receipt PDF.</div>
                    <a href="{{ route('receipt.portal.download-pdf') }}" class="btn btn-primary btn-action" style="background:var(--primary);border-color:var(--primary);">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="action-card">
                    <div class="action-icon" style="background:#EAFAF1;color:#27AE60;">
                        <i class="fas fa-mobile-screen-button"></i>
                    </div>
                    <div class="action-title">Download Apps</div>
                    <div class="action-desc">Download companion apps for enhanced anti-theft protection and device monitoring.</div>
                    <button class="btn btn-success btn-action" data-bs-toggle="modal" data-bs-target="#appsModal">
                        <i class="fas fa-download"></i> View Apps
                    </button>
                </div>
            </div>

            <!-- Device Information -->
            <div class="col-lg-6">
                <div class="info-card">
                    <h6 class="fw-bold mb-3"><i class="fas fa-mobile-screen-button text-primary me-2"></i>Device Information</h6>
                    @foreach([
                        ['Phone Model',$receipt->phone_name],
                        ['Color',$receipt->phone_color ?? 'N/A'],
                        ['IMEI / Serial',$receipt->phone_serial_number],
                        ['Purchase Amount','₦'.number_format($receipt->amount ?? 0, 2)],
                        ['Date of Purchase',$receipt->created_at->format('M d, Y')],
                        ['Shop',$receipt->shop->shop_name ?? 'N/A'],
                        ['Receipt Number',$receipt->receipt_number],
                    ] as $row)
                    <div class="info-row">
                        <span class="info-label">{{ $row[0] }}</span>
                        <span class="info-value">{{ $row[1] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Ownership History -->
            <div class="col-lg-6">
                <div class="info-card">
                    <h6 class="fw-bold mb-3"><i class="fas fa-clock-rotate-left text-primary me-2"></i>Ownership History</h6>
                    @forelse($ownershipHistory as $i => $item)
                    <div class="timeline-item">
                        <div class="timeline-dot">{{ $i + 1 }}</div>
                        <div class="timeline-content">
                            <div class="timeline-title">{{ $item->customer_name }}</div>
                            <div class="timeline-meta">
                                {{ $item->created_at->format('M d, Y') }} &nbsp;·&nbsp;
                                {{ $item->shop->shop_name ?? 'Unknown Shop' }}
                                @if($item->id === $receipt->id)
                                <span class="badge bg-success ms-1 rounded-pill" style="font-size:0.7rem;">Current Owner</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted small">No history available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Declare Missing Modal -->
    <div class="modal fade" id="missingModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger"><i class="fas fa-triangle-exclamation me-2"></i>Report Phone Missing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('receipt.portal.declare-missing') }}">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted small mb-3">Once reported, any search for your phone's serial <strong>{{ $receipt->phone_serial_number }}</strong> will immediately show a red Missing Alert with your contact information.</p>
                        <div class="mb-3">
                            <label class="form-label fw-600 small">Additional Notes (optional)</label>
                            <textarea name="missing_notes" class="form-control" rows="3" placeholder="Describe when/where the phone was lost or stolen..."></textarea>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="confirm_missing" id="confirmMissing" value="1" required>
                            <label class="form-check-label small" for="confirmMissing">
                                I confirm that my phone <strong>{{ $receipt->phone_name }}</strong> has been lost or stolen and I want to report it as missing.
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">
                            <i class="fas fa-bell me-1"></i> Report as Missing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Apps Modal -->
    <div class="modal fade" id="appsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-mobile-screen-button me-2 text-success"></i>Download Apps</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @forelse($downloads as $app)
                    <div class="download-item">
                        <div class="download-icon" style="background:{{ match($app->platform) { 'android'=>'#EAFAF1', 'ios'=>'#EAF2FF', 'windows'=>'#F4F0FF', default=>'#F8FAFC' } }};color:{{ match($app->platform) { 'android'=>'#27AE60', 'ios'=>'#3498DB', 'windows'=>'#8E44AD', default=>'#7F8C8D' } }};">
                            <i class="fab fa-{{ match($app->platform) { 'android'=>'android', 'ios'=>'apple', 'windows'=>'windows', default=>'download' } }}"></i>
                        </div>
                        <div class="flex-1">
                            <div class="fw-600 small">{{ $app->name }}</div>
                            <div class="text-muted" style="font-size:0.78rem;">{{ $app->description }}</div>
                            @if($app->version)<div class="text-muted" style="font-size:0.75rem;">Version {{ $app->version }}</div>@endif
                        </div>
                        @if($app->external_url)
                        <a href="{{ $app->external_url }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">Download</a>
                        @elseif($app->file_path)
                        <a href="{{ asset('storage/'.$app->file_path) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Download</a>
                        @endif
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-mobile-screen-button fa-2x mb-2"></i>
                        <p class="small">No apps available at this time. Check back soon.</p>
                    </div>
                    @endforelse
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">System Settings</h4>
            <p class="text-muted small mb-0">Configure platform behaviour, reward numbers, search messages, and downloads.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success rounded-3 border-0">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <!-- Main Settings -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0"><i class="fas fa-cog me-2 text-primary"></i>Platform Settings</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.system-settings.update') }}">
                        @csrf

                        <h6 class="fw-bold text-muted small text-uppercase mb-3 mt-2">Reward & Recovery</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-600 small">Reward Phone Number</label>
                                <input type="text" name="reward_phone" class="form-control"
                                    value="{{ \App\Models\SystemSetting::get('reward_phone', '08013131313') }}"
                                    placeholder="e.g. 08013131313">
                                <div class="form-text">Displayed on all search results as the reward contact.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600 small">Reward Message</label>
                                <input type="text" name="reward_message" class="form-control"
                                    value="{{ \App\Models\SystemSetting::get('reward_message', 'Call to receive a cash reward equivalent to the value of the recovered phone.') }}"
                                    placeholder="Reward message">
                            </div>
                        </div>

                        <h6 class="fw-bold text-muted small text-uppercase mb-3">Search Result Messages</h6>
                        <div class="mb-3">
                            <label class="form-label fw-600 small">Case 1 Message (Phone Found, Not Missing)</label>
                            <textarea name="search_result_case1_message" class="form-control" rows="2"
                                placeholder="e.g. This phone is registered and actively used by its verified owner.">{{ \App\Models\SystemSetting::get('search_result_case1_message') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-600 small">Case 2 Message (Phone Found, Missing)</label>
                            <textarea name="search_result_case2_message" class="form-control" rows="2"
                                placeholder="e.g. This phone has been officially declared missing.">{{ \App\Models\SystemSetting::get('search_result_case2_message') }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-600 small">Case 3 Message (Phone Not Found)</label>
                            <textarea name="search_result_case3_message" class="form-control" rows="2"
                                placeholder="e.g. This phone is not registered in our Anti-Theft database.">{{ \App\Models\SystemSetting::get('search_result_case3_message') }}</textarea>
                        </div>

                        <h6 class="fw-bold text-muted small text-uppercase mb-3">Intelligence & Verification</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enable_intelligence_collection"
                                        value="1" id="intelToggle"
                                        {{ \App\Models\SystemSetting::get('enable_intelligence_collection', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-600 small" for="intelToggle">Enable Intelligence Collection</label>
                                    <div class="form-text">Capture IP, browser fingerprint, GPS for missing phone searches.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enable_camera_capture"
                                        value="1" id="cameraToggle"
                                        {{ \App\Models\SystemSetting::get('enable_camera_capture', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-600 small" for="cameraToggle">Enable Camera Capture</label>
                                    <div class="form-text">Attempt to capture photo of missing phone searchers (with browser permission).</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enable_whatsapp_otp"
                                        value="1" id="otpToggle"
                                        {{ \App\Models\SystemSetting::get('enable_whatsapp_otp', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-600 small" for="otpToggle">Require WhatsApp OTP Verification</label>
                                    <div class="form-text">Require seller OTP verification before showing phone status.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600 small">OTP Expiry (minutes)</label>
                                <input type="number" name="otp_expiry_minutes" class="form-control"
                                    value="{{ \App\Models\SystemSetting::get('otp_expiry_minutes', '10') }}"
                                    min="5" max="60">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-save me-2"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- App Downloads -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="fas fa-download me-2 text-success"></i>App Downloads</h6>
                    <button class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addDownloadModal">
                        <i class="fas fa-plus me-1"></i> Add
                    </button>
                </div>
                <div class="card-body p-4">
                    @forelse($downloads as $app)
                    <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                        <div class="rounded-3 p-2 bg-light">
                            <i class="fab fa-{{ match($app->platform) { 'android'=>'android', 'ios'=>'apple', 'windows'=>'windows', default=>'download' } }} text-secondary fa-lg"></i>
                        </div>
                        <div class="flex-1">
                            <div class="fw-600 small">{{ $app->name }}</div>
                            <div class="text-muted" style="font-size:0.78rem;">{{ ucfirst($app->platform) }} · {{ $app->version ?? 'No version' }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $app->download_count }} downloads</div>
                        </div>
                        <form method="POST" action="{{ route('admin.system-settings.downloads.destroy', $app) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width:28px;height:28px;" onclick="return confirm('Remove?')">
                                <i class="fas fa-times" style="font-size:0.7rem;"></i>
                            </button>
                        </form>
                    </div>
                    @empty
                    <p class="text-muted small text-center py-3">No downloads added yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Download Modal -->
<div class="modal fade" id="addDownloadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Add App Download</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.system-settings.downloads.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-600 small">App Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. M-Right Anti-Theft App">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600 small">Platform</label>
                            <select name="platform" class="form-select">
                                <option value="android">Android</option>
                                <option value="ios">iOS</option>
                                <option value="windows">Windows</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 small">Version</label>
                            <input type="text" name="version" class="form-control" placeholder="e.g. 1.0.0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">External URL (optional)</label>
                        <input type="url" name="external_url" class="form-control" placeholder="https://play.google.com/...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Or Upload File</label>
                        <input type="file" name="file" class="form-control" accept=".apk,.ipa,.exe,.zip">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Add Download</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

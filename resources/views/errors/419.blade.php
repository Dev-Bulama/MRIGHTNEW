@extends('layouts.app')

@section('title', 'Session Expired')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-clock text-warning mb-3" style="font-size: 4rem;"></i>
                    <h3 class="mb-3">Session Expired</h3>
                    <p class="text-muted mb-4">Your session has expired for security reasons. Please log in again to continue.</p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Again
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Go Home
                        </a>
                    </div>
                    
                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            This helps keep your account secure
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Prevent back button access to dashboard after session expiry
window.history.pushState(null, "", window.location.href);
window.onpopstate = function() {
    window.history.pushState(null, "", window.location.href);
    alert('Please log in again to continue.');
    window.location.href = "{{ route('login') }}";
};

// Clear any cached data
if (typeof(Storage) !== "undefined") {
    localStorage.clear();
    sessionStorage.clear();
}
</script>
@endsection
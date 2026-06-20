@extends(auth()->user()->isUnion() ? 'layouts.union' : 'layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('page-description', 'View and manage your account information')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>Profile Information
                </h5>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Edit Profile
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <img src="{{ $user->avatar_url }}" 
                             alt="{{ $user->name }}" 
                             class="rounded-circle img-fluid mb-3"
                             width="150" height="150">
                        <h5 class="fw-bold">{{ $user->name }}</h5>
                        <span class="badge bg-{{ $user->status_badge_color }}">
                            {{ $user->status_display }}
                        </span>
                    </div>
                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong>Email:</strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $user->email }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong>Phone:</strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $user->phone_number }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong>Account Type:</strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $user->user_type_display }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <strong>Member Since:</strong>
                            </div>
                            <div class="col-sm-8">
                                {{ $user->created_at->format('F j, Y') }}
                            </div>
                        </div>
                        @if($user->last_login_at)
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong>Last Login:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $user->last_login_at->format('F j, Y g:i A') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
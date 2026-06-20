@extends('layouts.app')

@section('title', 'System Settings')
@section('page-title', 'System Settings')
@section('page-description', 'Configure system settings and preferences')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>System Settings
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-cogs text-muted mb-4" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">System Settings Coming Soon</h4>
                    <p class="text-muted mb-4">
                        Configure payment settings, email templates, and system preferences.
                    </p>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
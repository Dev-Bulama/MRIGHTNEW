@extends('layouts.app')

@section('title', 'System Health')
@section('page-title', 'System Health')
@section('page-description', 'Monitor system performance and health metrics')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-heartbeat me-2"></i>System Health Monitor
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-heartbeat text-muted mb-4" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">System Health Monitoring Coming Soon</h4>
                    <p class="text-muted mb-4">
                        Advanced system monitoring, performance metrics, and health checks.
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
@extends('layouts.app')

@section('title', 'System Reports')
@section('page-title', 'System Reports')
@section('page-description', 'Generate and view system reports')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-alt me-2"></i>System Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-file-chart-line text-muted mb-4" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">System Reports Coming Soon</h4>
                    <p class="text-muted mb-4">
                        Generate comprehensive reports for business intelligence and compliance.
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
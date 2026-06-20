@extends('layouts.app')

@section('title', 'Location Reports')
@section('page-title', 'Location Reports')
@section('page-description', 'Geographic distribution of shops and users')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-map me-2"></i>Location Analytics
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-alt text-muted mb-4" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">Location Analytics Coming Soon</h4>
                    <p class="text-muted mb-4">
                        Interactive maps and geographic analytics of shop distribution.
                    </p>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
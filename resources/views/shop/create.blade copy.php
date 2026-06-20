@extends('layouts.app')

@section('title', 'Setup Shop Profile')
@section('page-title', 'Setup Shop Profile')
@section('page-description', 'Create your business profile to start generating digital receipts')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-store me-2"></i>Shop Profile Setup
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <i class="fas fa-tools text-muted mb-4" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">Shop Setup Coming Soon</h4>
                    <p class="text-muted mb-4">
                        The shop profile setup feature is currently under development. 
                        You'll be able to create and customize your shop profile here soon.
                    </p>
                    <div class="alert alert-info">
                        <strong>What you'll be able to do:</strong>
                        <ul class="list-unstyled mt-3 mb-0">
                            <li><i class="fas fa-check text-success me-2"></i>Add your business information</li>
                            <li><i class="fas fa-check text-success me-2"></i>Upload your shop logo</li>
                            <li><i class="fas fa-check text-success me-2"></i>Customize receipt headers</li>
                            <li><i class="fas fa-check text-success me-2"></i>Set business contact details</li>
                        </ul>
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
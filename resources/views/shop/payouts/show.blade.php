@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Payout Request Details</h1>
            <p class="text-muted">Request #{{ $payoutRequest->request_number }}</p>
        </div>
        <a href="{{ route('shop.payouts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Payouts
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Request Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Request Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Request Number:</th>
                                    <td><strong>{{ $payoutRequest->request_number }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'paid' => 'primary',
                                                'cancelled' => 'secondary'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$payoutRequest->status] ?? 'secondary' }} fs-6">
                                            {{ $payoutRequest->status_display }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Amount Requested:</th>
                                    <td><strong>₦{{ number_format($payoutRequest->amount_requested, 2) }}</strong></td>
                                </tr>
                                @if($payoutRequest->amount_approved)
                                    <tr>
                                        <th>Amount Approved:</th>
                                        <td><strong>₦{{ number_format($payoutRequest->amount_approved, 2) }}</strong></td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Requested Date:</th>
                                    <td>{{ $payoutRequest->created_at->format('M d, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                @if($payoutRequest->approved_at)
                                    <tr>
                                        <th width="40%">Approved Date:</th>
                                        <td>{{ $payoutRequest->approved_at->format('M d, Y g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Approved By:</th>
                                        <td>{{ $payoutRequest->approvedBy->name ?? 'System' }}</td>
                                    </tr>
                                @endif
                                @if($payoutRequest->rejected_at)
                                    <tr>
                                        <th>Rejected Date:</th>
                                        <td>{{ $payoutRequest->rejected_at->format('M d, Y g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Rejected By:</th>
                                        <td>{{ $payoutRequest->rejectedBy->name ?? 'System' }}</td>
                                    </tr>
                                @endif
                                @if($payoutRequest->paid_at)
                                    <tr>
                                        <th>Paid Date:</th>
                                        <td>{{ $payoutRequest->paid_at->format('M d, Y g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Reference:</th>
                                        <td><code>{{ $payoutRequest->payment_reference }}</code></td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($payoutRequest->reason)
                        <hr>
                        <div>
                            <h6>Reason for Request:</h6>
                            <p class="text-muted">{{ $payoutRequest->reason }}</p>
                        </div>
                    @endif

                    @if($payoutRequest->admin_notes)
                        <hr>
                        <div>
                            <h6>Admin Notes:</h6>
                            <div class="alert alert-info">
                                {{ $payoutRequest->admin_notes }}
                            </div>
                        </div>
                    @endif

                    @if($payoutRequest->rejection_reason)
                        <hr>
                        <div>
                            <h6>Rejection Reason:</h6>
                            <div class="alert alert-danger">
                                {{ $payoutRequest->rejection_reason }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bank Details -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Bank Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Bank Name:</strong><br>
                            {{ $payoutRequest->bank_name }}
                        </div>
                        <div class="col-md-4">
                            <strong>Account Number:</strong><br>
                            {{ $payoutRequest->account_number }}
                        </div>
                        <div class="col-md-4">
                            <strong>Account Name:</strong><br>
                            {{ $payoutRequest->account_name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Status Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Request Created -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Request Created</h6>
                                <p class="text-muted mb-0">{{ $payoutRequest->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>

                        @if($payoutRequest->approved_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Request Approved</h6>
                                    <p class="text-muted mb-0">{{ $payoutRequest->approved_at->format('M d, Y g:i A') }}</p>
                                    <small class="text-muted">by {{ $payoutRequest->approvedBy->name ?? 'System' }}</small>
                                </div>
                            </div>
                        @endif

                        @if($payoutRequest->rejected_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Request Rejected</h6>
                                    <p class="text-muted mb-0">{{ $payoutRequest->rejected_at->format('M d, Y g:i A') }}</p>
                                    <small class="text-muted">by {{ $payoutRequest->rejectedBy->name ?? 'System' }}</small>
                                </div>
                            </div>
                        @endif

                        @if($payoutRequest->paid_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Payment Processed</h6>
                                    <p class="text-muted mb-0">{{ $payoutRequest->paid_at->format('M d, Y g:i A') }}</p>
                                    <small class="text-muted">Ref: {{ $payoutRequest->payment_reference }}</small>
                                </div>
                            </div>
                        @endif

                        @if($payoutRequest->status === 'pending')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Awaiting Review</h6>
                                    <p class="text-muted mb-0">Your request is being reviewed by admin</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Commission Context -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Commission Context</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Balance at Request:</span>
                        <strong>₦{{ number_format($payoutRequest->commission_balance, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Current Available:</span>
                        <strong>₦{{ number_format($commissionStats['available_balance'], 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Earned:</span>
                        <strong>₦{{ number_format($commissionStats['total_commission_earned'], 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Paid Out:</span>
                        <strong>₦{{ number_format($commissionStats['total_paid_out'], 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content h6 {
    font-size: 0.9rem;
    color: #495057;
}
</style>
@endsection
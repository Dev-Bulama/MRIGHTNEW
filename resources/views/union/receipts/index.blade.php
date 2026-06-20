@extends('layouts.union')

@section('breadcrumb')
    <li class="breadcrumb-item active">Receipts</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Receipt Management</h1>
            <p class="text-muted">Monitor receipts from your assigned locations</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshData()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Total Receipts</h6>
                            <h3 class="mb-0 text-primary">{{ $stats['total_receipts'] ?? 0 }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Successful</h6>
                            <h3 class="mb-0 text-success">{{ $stats['successful_receipts'] ?? 0 }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Pending</h6>
                            <h3 class="mb-0 text-warning">{{ $stats['pending_receipts'] ?? 0 }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">This Month</h6>
                            <h3 class="mb-0 text-info">{{ $stats['this_month_receipts'] ?? 0 }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipts Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Recent Receipts</h5>
        </div>
        <div class="card-body p-0">
            @if($receipts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Shop Owner</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipts as $receipt)
                                <tr>
                                    <td>
                                        <strong>{{ $receipt->receipt_number }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $receipt->user->name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $receipt->shop->shop_name ?? 'No shop' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $receipt->customer_name }}</strong><br>
                                            <small class="text-muted">{{ $receipt->customer_phone }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>₦{{ number_format($receipt->amount) }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'paid' => 'success',
                                                'pending' => 'warning',
                                                'failed' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$receipt->payment_status] ?? 'secondary' }}">
                                            {{ ucfirst($receipt->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $receipt->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $receipt->created_at->format('h:i A') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer">
                    {{ $receipts->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-receipt text-muted mb-3" style="font-size: 4rem;"></i>
                    <h5 class="text-muted">No Receipts Found</h5>
                    <p class="text-muted">No receipts have been generated in your assigned locations yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function refreshData() {
    location.reload();
}
</script>
@endsection
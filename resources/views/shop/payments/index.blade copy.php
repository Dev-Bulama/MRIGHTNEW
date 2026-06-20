@extends('layouts.app')

@section('title', 'Payment Overview')
@section('page-title', 'Payment Overview')
@section('page-description', 'View your shop payment transactions and history')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-money-check-alt me-2"></i>Your Payment Transactions
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $userReceipts = auth()->user()->receipts()->with('shop')->latest()->get();
                        $totalRevenue = $userReceipts->where('payment_gateway_status', 'successful')->sum('amount');
                        $pendingPayments = $userReceipts->where('payment_gateway_status', 'pending')->count();
                    @endphp
                    
                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>₦{{ number_format($totalRevenue, 2) }}</h3>
                                    <p class="mb-0">Total Revenue</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $pendingPayments }}</h3>
                                    <p class="mb-0">Pending Payments</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $userReceipts->count() }}</h3>
                                    <p class="mb-0">Total Receipts</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    @if($userReceipts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userReceipts->take(10) as $receipt)
                                        <tr>
                                            <td><strong>{{ $receipt->receipt_number }}</strong></td>
                                            <td>{{ $receipt->customer_name }}</td>
                                            <td>₦{{ number_format($receipt->amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $receipt->payment_gateway_status === 'successful' ? 'success' : ($receipt->payment_gateway_status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($receipt->payment_gateway_status ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td>{{ $receipt->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt text-muted mb-3" style="font-size: 4rem;"></i>
                            <h4 class="text-muted">No Transactions Yet</h4>
                            <p class="text-muted">Start generating receipts to see payment transactions here.</p>
                            <a href="{{ route('receipt.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Generate First Receipt
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
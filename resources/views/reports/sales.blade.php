@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
@endsection

@section('content')

<div class="reports-page">

    {{-- Page Header --}}
    <div class="reports-header">

        <div>
            <h1>Sales Report</h1>
            <p>Analyze your sales performance and revenue.</p>
        </div>

    </div>


    {{-- Date Filter --}}
    <div class="report-filter-card">

        <form method="GET" action="{{ route('reports.sales') }}">

            <div class="filter-group">

                <div class="filter-field">
                    <label for="from_date">From Date</label>

                    <input
                        type="date"
                        name="from_date"
                        id="from_date"
                        value="{{ request('from_date') }}"
                    >
                </div>


                <div class="filter-field">
                    <label for="to_date">To Date</label>

                    <input
                        type="date"
                        name="to_date"
                        id="to_date"
                        value="{{ request('to_date') }}"
                    >
                </div>


                <div class="filter-actions">

                    <button type="submit" class="btn-report-filter">
                        <i class="fa-solid fa-filter"></i>
                        Apply Filter
                    </button>

                    <a
                        href="{{ route('reports.sales') }}"
                        class="btn-report-reset"
                    >
                        <i class="fa-solid fa-rotate-left"></i>
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- Summary Cards --}}
    <div class="report-summary-grid">

        <div class="report-summary-card">

            <div class="summary-icon">
                <i class="fa-solid fa-receipt"></i>
            </div>

            <div>
                <span>Total Transactions</span>
                <h2>{{ number_format($totalTransactions) }}</h2>
            </div>

        </div>


        <div class="report-summary-card">

            <div class="summary-icon">
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </div>

            <div>
                <span>Total Revenue</span>
                <h2>₹{{ number_format($totalRevenue, 2) }}</h2>
            </div>

        </div>


        <div class="report-summary-card">

            <div class="summary-icon">
                <i class="fa-solid fa-tag"></i>
            </div>

            <div>
                <span>Total Discount</span>
                <h2>₹{{ number_format($totalDiscount, 2) }}</h2>
            </div>

        </div>


        <div class="report-summary-card">

            <div class="summary-icon">
                <i class="fa-solid fa-percent"></i>
            </div>

            <div>
                <span>Total Tax</span>
                <h2>₹{{ number_format($totalTax, 2) }}</h2>
            </div>

        </div>

    </div>


    {{-- Payment Summary --}}
    <div class="report-section">

        <div class="report-section-header">

            <div>
                <h2>Payment Summary</h2>
                <p>Sales breakdown by payment method.</p>
            </div>

        </div>


        <div class="payment-summary-grid">

            @forelse($paymentSales as $payment)

                <div class="payment-card">

                    <div class="payment-card-top">

                        <div class="payment-icon">
                            @if(strtolower($payment->payment_method) === 'cash')
                                <i class="fa-solid fa-money-bill"></i>
                            @elseif(strtolower($payment->payment_method) === 'upi')
                                <i class="fa-solid fa-mobile-screen-button"></i>
                            @elseif(strtolower($payment->payment_method) === 'card')
                                <i class="fa-solid fa-credit-card"></i>
                            @else
                                <i class="fa-solid fa-wallet"></i>
                            @endif
                        </div>

                        <span class="payment-method">
                            {{ ucfirst($payment->payment_method) }}
                        </span>

                    </div>


                    <div class="payment-card-body">

                        <h3>
                            ₹{{ number_format($payment->amount, 2) }}
                        </h3>

                        <p>
                            {{ number_format($payment->transactions) }}
                            transaction{{ $payment->transactions == 1 ? '' : 's' }}
                        </p>

                    </div>

                </div>

            @empty

                <div class="empty-report">
                    <i class="fa-solid fa-chart-simple"></i>
                    <p>No payment data available.</p>
                </div>

            @endforelse

        </div>

    </div>


    {{-- Sales Table --}}
    <div class="report-section">

        <div class="report-section-header">

            <div>
                <h2>Sales Transactions</h2>
                <p>Detailed list of completed sales.</p>
            </div>

            <span class="report-count">
                {{ $sales->total() }} Records
            </span>

        </div>


        <div class="report-table-wrapper">

            <table class="report-table">

                <thead>

                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Tax</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Date</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($sales as $sale)

                        <tr>

                            <td>
                                <strong>
                                    {{ $sale->invoice_number }}
                                </strong>
                            </td>


                            <td>

                                <div class="customer-info">

                                    <span>
                                        {{ $sale->customer_name ?: 'Walk-in Customer' }}
                                    </span>

                                    @if($sale->phone_number)
                                        <small>
                                            {{ $sale->phone_number }}
                                        </small>
                                    @endif

                                </div>

                            </td>


                            <td>
                                ₹{{ number_format($sale->subtotal, 2) }}
                            </td>


                            <td>
                                ₹{{ number_format($sale->discount, 2) }}
                            </td>


                            <td>
                                ₹{{ number_format($sale->tax, 2) }}
                            </td>


                            <td>
                                <strong>
                                    ₹{{ number_format($sale->total_amount, 2) }}
                                </strong>
                            </td>


                            <td>

                                <span class="payment-badge">
                                    {{ ucfirst($sale->payment_method) }}
                                </span>

                            </td>


                            <td>

                                <div class="date-info">

                                    <span>
                                        {{ $sale->created_at->format('d M Y') }}
                                    </span>

                                    <small>
                                        {{ $sale->created_at->format('h:i A') }}
                                    </small>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8">

                                <div class="empty-table">

                                    <i class="fa-solid fa-receipt"></i>

                                    <h3>No sales found</h3>

                                    <p>
                                        There are no completed sales
                                        for the selected period.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if($sales->hasPages())

            <div class="report-pagination">
                {{ $sales->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
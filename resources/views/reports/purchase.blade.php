@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
@endsection

@section('content')

<div class="reports-page">

    {{-- Page Header --}}
    <div class="reports-header">

        <div>

            <h1>Purchase Report</h1>

            <p>
                Analyze your purchase transactions and inventory spending.
            </p>

        </div>

    </div>


    {{-- Date Filter --}}
    <div class="report-filter-card">

        <form method="GET" action="{{ route('reports.purchase') }}">

            <div class="filter-group">

                <div class="filter-field">

                    <label for="from_date">
                        From Date
                    </label>

                    <input
                        type="date"
                        name="from_date"
                        id="from_date"
                        value="{{ request('from_date') }}"
                    >

                </div>


                <div class="filter-field">

                    <label for="to_date">
                        To Date
                    </label>

                    <input
                        type="date"
                        name="to_date"
                        id="to_date"
                        value="{{ request('to_date') }}"
                    >

                </div>


                <div class="filter-actions">

                    <button
                        type="submit"
                        class="btn-report-filter"
                    >
                        <i class="fa-solid fa-filter"></i>
                        Apply Filter
                    </button>


                    <a
                        href="{{ route('reports.purchase') }}"
                        class="btn-report-reset"
                    >
                        <i class="fa-solid fa-rotate-left"></i>
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- Summary --}}
    <div class="report-summary-grid">


        {{-- Total Purchases --}}
        <div class="report-summary-card">

            <div class="summary-icon">

                <i class="fa-solid fa-cart-shopping"></i>

            </div>

            <div>

                <span>Total Purchases</span>

                <h2>
                    {{ number_format($totalPurchases) }}
                </h2>

            </div>

        </div>


        {{-- Total Spending --}}
        <div class="report-summary-card">

            <div class="summary-icon">

                <i class="fa-solid fa-indian-rupee-sign"></i>

            </div>

            <div>

                <span>Total Purchase Amount</span>

                <h2>
                    ₹{{ number_format($totalAmount, 2) }}
                </h2>

            </div>

        </div>


        {{-- Average Purchase --}}
        <div class="report-summary-card">

            <div class="summary-icon">

                <i class="fa-solid fa-chart-line"></i>

            </div>

            <div>

                <span>Average Purchase</span>

                <h2>
                    ₹{{ $totalPurchases > 0
                        ? number_format(
                            $totalAmount / $totalPurchases,
                            2
                        )
                        : '0.00'
                    }}
                </h2>

            </div>

        </div>


        {{-- Suppliers --}}
        <div class="report-summary-card">

            <div class="summary-icon">

                <i class="fa-solid fa-truck"></i>

            </div>

            <div>

                <span>Suppliers</span>

                <h2>
                    {{ number_format($totalSuppliers) }}
                </h2>

            </div>

        </div>

    </div>


    {{-- Purchase Transactions --}}
    <div class="report-section">

        <div class="report-section-header">

            <div>

                <h2>Purchase Transactions</h2>

                <p>
                    Detailed list of completed purchases.
                </p>

            </div>


            <span class="report-count">

                {{ $purchases->total() }} Records

            </span>

        </div>


        <div class="report-table-wrapper">

            <table class="report-table">

                <thead>

                    <tr>

                        <th>Invoice</th>

                        <th>Supplier</th>

                        <th>Total Amount</th>

                        <th>Purchase Date</th>

                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($purchases as $purchase)

                        <tr>

                            {{-- Invoice --}}
                            <td>

                                <strong>
                                    {{ $purchase->invoice_number }}
                                </strong>

                            </td>


                            {{-- Supplier --}}
                            <td>

                                @if($purchase->supplier)

                                    <div class="customer-info">

                                        <strong>
                                            {{ $purchase->supplier->name }}
                                        </strong>

                                        @if($purchase->supplier->phone)
                                            <small>
                                                {{ $purchase->supplier->phone }}
                                            </small>
                                        @endif

                                    </div>

                                @else

                                    <span>
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- Amount --}}
                            <td>

                                <strong>
                                    ₹{{ number_format(
                                        $purchase->total_amount,
                                        2
                                    ) }}
                                </strong>

                            </td>


                            {{-- Date --}}
                            <td>

                                <div class="date-info">

                                    <span>
                                        {{ \Carbon\Carbon::parse(
                                            $purchase->purchase_date
                                        )->format('d M Y') }}
                                    </span>

                                </div>

                            </td>


                            {{-- Status --}}
                            <td>

                                <span class="payment-badge">
                                    {{ ucfirst($purchase->status) }}
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5">

                                <div class="empty-table">

                                    <i class="fa-solid fa-cart-shopping"></i>

                                    <h3>
                                        No purchases found
                                    </h3>

                                    <p>
                                        There are no completed purchases
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
        @if($purchases->hasPages())

            <div class="report-pagination">

                {{ $purchases->links() }}

            </div>

        @endif

    </div>

</div>

@endsection
@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
@endsection

@section('content')

<div class="reports-page">

    {{-- Page Header --}}
    <div class="reports-header">

        <div>
            <h1>Product Sales Report</h1>

            <p>
                Analyze product-wise sales performance and revenue.
            </p>
        </div>

    </div>


    {{-- Date Filter --}}
    <div class="report-filter-card">

        <form method="GET" action="{{ route('reports.product-sales') }}">

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
                        href="{{ route('reports.product-sales') }}"
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


        {{-- Products --}}
        <div class="report-summary-card">

            <div class="summary-icon">

                <i class="fa-solid fa-box"></i>

            </div>

            <div>

                <span>Products Sold</span>

                <h2>
                    {{ number_format($totalProducts) }}
                </h2>

            </div>

        </div>


        {{-- Quantity --}}
        <div class="report-summary-card">

            <div class="summary-icon">

                <i class="fa-solid fa-cubes"></i>

            </div>

            <div>

                <span>Total Quantity</span>

                <h2>
                    {{ number_format($totalQuantity) }}
                </h2>

            </div>

        </div>


        {{-- Revenue --}}
        <div class="report-summary-card">

            <div class="summary-icon">

                <i class="fa-solid fa-indian-rupee-sign"></i>

            </div>

            <div>

                <span>Total Revenue</span>

                <h2>
                    ₹{{ number_format($totalRevenue, 2) }}
                </h2>

            </div>

        </div>


        {{-- Average --}}
        <div class="report-summary-card">

            <div class="summary-icon">

                <i class="fa-solid fa-chart-line"></i>

            </div>

            <div>

                <span>Avg. Revenue / Product</span>

                <h2>
                    ₹{{ $totalProducts > 0
                        ? number_format($totalRevenue / $totalProducts, 2)
                        : '0.00'
                    }}
                </h2>

            </div>

        </div>

    </div>


    {{-- Product Sales Table --}}
    <div class="report-section">

        <div class="report-section-header">

            <div>

                <h2>Product-wise Sales</h2>

                <p>
                    Sales performance grouped by product.
                </p>

            </div>


            <span class="report-count">

                {{ $productSales->total() }} Products

            </span>

        </div>


        <div class="report-table-wrapper">

            <table class="report-table">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Product</th>

                        <th>SKU</th>

                        <th>Quantity Sold</th>

                        <th>Average Price</th>

                        <th>Total Revenue</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($productSales as $index => $product)

                        <tr>

                            {{-- Number --}}
                            <td>

                                {{ $productSales->firstItem() + $index }}

                            </td>


                            {{-- Product --}}
                            <td>

                                <div class="customer-info">

                                    <strong>
                                        {{ $product->name }}
                                    </strong>

                                </div>

                            </td>


                            {{-- SKU --}}
                            <td>

                                <span class="product-sku">

                                    {{ $product->sku }}

                                </span>

                            </td>


                            {{-- Quantity --}}
                            <td>

                                <span class="quantity-badge">

                                    {{ number_format($product->quantity_sold) }}

                                </span>

                            </td>


                            {{-- Average Price --}}
                            <td>

                                ₹{{ number_format(
                                    $product->average_price,
                                    2
                                ) }}

                            </td>


                            {{-- Revenue --}}
                            <td>

                                <strong>

                                    ₹{{ number_format(
                                        $product->total_revenue,
                                        2
                                    ) }}

                                </strong>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6">

                                <div class="empty-table">

                                    <i class="fa-solid fa-box-open"></i>

                                    <h3>
                                        No product sales found
                                    </h3>

                                    <p>
                                        There are no completed product
                                        sales for the selected period.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if($productSales->hasPages())

            <div class="report-pagination">

                {{ $productSales->links() }}

            </div>

        @endif

    </div>

</div>

@endsection
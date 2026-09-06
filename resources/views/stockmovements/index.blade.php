@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="css/stock-movement.css">
@endsection

@section('content')
<div class="container-fluid px-3">
    <div class="stock-movement-page">
        
        {{-- Page header --}}
        <div class="page-header">
            <div>
                <h3 class="mb-1">
                    Stock Movements
                </h3>

                <p class="text-muted">
                    Track all inventory stock updates.
                </p>
            </div>
        </div>

        {{-- Summary cards --}}
        <div class="stock-summary-grid">

            {{-- Total movements --}}
            <div class="stock-summary-card">
                <div class="stock-summary-content">
                    <p class="stock-summary-label">
                        Total Movements
                    </p>

                    <h3>{{ $totalMovements }}</h3>

                    <span class="stock-summary-text">All inventory updates</span>
                </div>
            </div>

            {{-- Stock Added --}}
            <div class="stock-summary-card">
                <div class="stock-summary-content">
                    <p class="stock-summary-label">
                        Stock Added
                    </p>

                    <h3 class="stock-added">
                        +{{ $stockAdded }}
                    </h3>

                    <span class="stock-summary-text">
                        Total inventory added
                    </span>
                </div>
            </div>

            {{-- Stock Reduced --}}

            <div class="stock-summary-card">
                <div class="stock-summary-content">
                    <p class="stock-summary-label">
                        Stock Reduced
                    </p>

                    <h3 class="stock-reduced">
                        -{{ abs($stockReduced) }}
                    </h3>

                    <p class="stock-summary-text">
                        Total inventory removed
                    </p>
                </div>
            </div>

            {{-- Todays Movement --}}

            <div class="stock-summary-card">
                <div class="stock-summary-content">
                    <p class="stock-summary-label">
                        Today's Movements
                    </p>

                    <h3>{{ $todaysMovement }}</h3>

                    <span class="stock-summary-text">
                        Inventory updates today
                    </span>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="stock-filter-card py-4">
            <div class="filter-header">
                <div>
                    <h5 class="mb-1 fw-semibold">
                        Search & Filter
                    </h5>

                    <p class="text-muted">
                        Filter the stock movement records.
                    </p>
                </div>
            </div>
        <form action="{{ route('stockMovements') }}" method="GET"
        class="stock-movement-filter">

            {{-- Product filter --}}
            
            <div class="filter-field">
                <label for="product_id" class="form-label">
                    Product
                </label>

                <select name="product_id" id="productId"
                class="form-select">
                    <option value="">All Products</option>

                    @foreach ($products as $product)
                        <option value="{{ request('product_id') }}"
                        {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Type filter --}}
            <div class="filter-field">
                <label for="type" class="type">Movement Type</label>

                <select name="type" id="type" class="form-select">
                    <option value="">All Type</option>

                    <option value="purchase"
                    {{ request('type') == 'purchase' ? 'selected' : '' }}>
                        Purchase
                    </option>

                    <option value="purchase_cancel"
                    {{ request('type') == 'purchase_cancel' ? 'selected' : '' }}>
                        Purchase Cancelled
                    </option>
                </select>
            </div>

            {{-- Date from --}}
            <div class="filter-field">
                <label for="date-from">
                    From Date
                </label>

                <input type="date"
                    name="date_from"
                    value="{{ request('from_date') }}"
                    class="form-control"
                    id="date_from"
                    >
            </div>

            {{-- Date to --}}
            <div class="filter-field">
                <label for="date_to" class="form-label">
                    To Date
                </label>

                <input type="date"
                    name="date_to"
                    id="date_to"
                    value="{{ request('date_to') }}"
                    class="form-control">
            </div>

            {{-- Buttons --}}
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    Search
                </button>

                <a href="{{ route('stockMovements') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>
        </div>
        

        {{-- Table section --}}

        <div class="stock-movement-table-section mt-4">
            <div class="d-flex align-items-center justify-content-between mb-3 p-2">
                <div>
                    <h4 class="mb-1 fw-semibold">
                        Movement History
                    </h4>

                    <p class="text-muted mb-0">
                        View all stock additions and reductions.
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table stock-movement-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Reference</th>
                            <th>Note</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($stockMovements as $stock)
                            <tr>
                                {{-- Product name --}}
                                <td>
                                    <span class="product-name">
                                        {{ $stock->product->name }}
                                    </span>
                                </td>

                                {{-- Product type --}}
                                <td>
                                    @if($stock->type === 'purchase')
                                        <span class="movement-badge movement-in">
                                            Purchase
                                        </span>
                                    @elseif ($stock->type === 'purchase_cancel')
                                        <span class="movement-badge movement-out">
                                            Purchase cancelled
                                        </span>
                                    @else
                                        <span class="movement-badge">
                                            {{ ucfirst($stock->type) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Quantity --}}

                                <td>
                                    @if($stock->type === 'purchase')
                                        <span class="quantity-in">
                                            +{{ $stock->quantity }}
                                        </span>
                                    @else
                                        <span class="quantity-out">
                                            {{ $stock->quantity }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Reference --}}
                                <td>
                                    @if($stock->purchase)
                                        <a href="{{ route('purchases.show',$stock->purchase->id) }}"
                                            class="stock-reference">
                                            {{ $stock->purchase->invoice_number }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- Note --}}
                                <td>
                                    {{ $stock->note ??'-' }}
                                </td>

                                {{-- Date --}}

                                <td>
                                    {{ $stock->created_at->format('d M Y, h:i A') }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <p class="text-muted">
                                        No stock movements record found.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>    
</div>
@endsection
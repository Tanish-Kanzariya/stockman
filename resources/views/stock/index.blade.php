@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endsection

@section('content')

<div class="container-fluid px-3">

<div class="stock-page">

    <div class="stock-page-header">
        <div>
            <h3 class="mb-1 ">Stock Management</h1>
            <p class="text-muted">Monitor your inventory levels and stock value.</p>
        </div>
    </div>


    <div class="row g-4 mb-3">

    {{-- Total Stocks --}}
    <div class="col-12 col-md-6">
        <div class="stock-summary-card">
            <div class="stock-card-icon">
                📦
            </div>
            <div class="stock-card-content">
                <p>Total Stock</p>
                <h2>{{ number_format($totalStock) }}</h2>
                <span>Units currently in inventory</span>
            </div>
        </div>
    </div>


    <div class="col-12 col-md-6">
        <div class="stock-summary-card">
            <div class="stock-card-icon">
                ₹
            </div>
            <div class="stock-card-content">
                <p>Inventory Value</p>
                <h2>₹{{ number_format($totalInventoryValue, 2) }}</h2>
                <span>Total value of current stock</span>
            </div>
        </div>
    </div>
</div>

<div class="stock-filter-panel">

    <div class="filter-header">
        <div>
            <h4>Filter & Sort Stock</h4>
            {{-- <p>Find and organize your inventory quickly.</p> --}}
        </div>
    </div>

    <form action="{{ route('stock.index') }}" method="GET">

        <div class="row g-3">

            {{-- Search --}}
            <div class="col-12 col-md-6 col-lg-3">
                <label for="search" class="form-label">
                    Search Product
                </label>

                <input
                    type="text"
                    name="search"
                    id="search"
                    placeholder="Search products..."
                    value="{{ request('search') }}"
                    class="form-control"
                >
            </div>


            {{-- Category --}}
            <div class="col-12 col-md-6 col-lg-3">

                <label for="category" class="form-label">
                    Category
                </label>

                <select
                    name="category"
                    id="category"
                    class="form-select"
                >
                    <option value="">All Categories</option>

                    @foreach ($categories as $category)
                        <option
                            value="{{ $category->id }}"
                            {{ request('category') == $category->id ? 'selected' : '' }}
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach

                </select>

            </div>


            {{-- Status --}}
            <div class="col-12 col-md-6 col-lg-2">

                <label for="status" class="form-label">
                    Status
                </label>

                <select
                    name="status"
                    id="status"
                    class="form-select"
                >
                    <option value="">All Status</option>

                    <option value="in_stock"
                        {{ request('status') == 'in_stock' ? 'selected' : '' }}>
                        In Stock
                    </option>

                    <option value="low_stock"
                        {{ request('status') == 'low_stock' ? 'selected' : '' }}>
                        Low Stock
                    </option>

                    <option value="out_of_stock"
                        {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>
                        Out of Stock
                    </option>

                </select>

            </div>


            {{-- Sort --}}
            <div class="col-12 col-md-6 col-lg-2">

                <label for="sort" class="form-label">
                    Sort By
                </label>

                <select
                    name="sort"
                    id="sort"
                    class="form-select"
                >
                    <option value="">Default</option>

                    <option value="name"
                        {{ request('sort') == 'name' ? 'selected' : '' }}>
                        Product Name
                    </option>

                    <option value="stock_quantity"
                        {{ request('sort') == 'stock_quantity' ? 'selected' : '' }}>
                        Stock Quantity
                    </option>

                    <option value="purchase_price"
                        {{ request('sort') == 'purchase_price' ? 'selected' : '' }}>
                        Purchase Price
                    </option>

                    <option value="stock_value"
                        {{ request('sort') == 'stock_value' ? 'selected' : '' }}>
                        Stock Value
                    </option>

                </select>

            </div>


            {{-- Direction --}}
            <div class="col-12 col-md-6 col-lg-2">

                <label for="direction" class="form-label">
                    Direction
                </label>

                <select
                    name="direction"
                    id="direction"
                    class="form-select"
                >
                    <option value="asc"
                        {{ request('direction', 'asc') == 'asc' ? 'selected' : '' }}>
                        Ascending
                    </option>

                    <option value="desc"
                        {{ request('direction') == 'desc' ? 'selected' : '' }}>
                        Descending
                    </option>

                </select>

            </div>

        </div>


        {{-- Buttons --}}
        <div class="filter-actions">

            <button type="submit" class="btn btn-primary">
                Search
            </button>

            <a
                href="{{ route('stock.index') }}"
                class="btn btn-outline-secondary"
            >
                Clear
            </a>

        </div>

    </form>

</div>
<div class="stock-table-wrapper">
    <div class="table-responsive mt-4">
        <table class="table stock-table table-hover align-middle" id="stock-table">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>Minimum Stock</th>
                    <th>Purchase Price</th>
                    <th>Stock Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                @php
                    $stockValue = $product->stock_quantity * $product->purchase_price;

                    if($product->stock_quantity == 0){
                        $stockStatus = 'Out of stock';
                    }elseif($product->stock_quantity <= $product->minimum_stock){
                        $stockStatus = 'Low stock';
                    }else {
                        $stockStatus = 'In stock';
                    }
                @endphp
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td>{{ $product->minimum_stock }}</td>
                        <td>₹{{ number_format($product->purchase_price,2) }}</td>
                        <td>₹{{ number_format($stockValue,2) }}</td>
                        <td>
                            @if($stockStatus == 'In stock')
                                <span class="badge bg-success">In Stock</span>
                            @elseif ($stockStatus == 'Low stock')
                                <span class="badge bg-warning text-dark">Low stock</span>
                            @else
                                <span class="badge bg-danger">Out of stock</span>
                            @endif
                        </td>
                    </tr>     
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="text-muted">
                                    No Records Found
                                </div>
                            </td>
                        </tr>       
                @endforelse
            </tbody>
        </table>
    </div>
</div>
   
<div class="stock-pagination">
    {{ $products->links() }}
</div>


</div>



</div>

@endsection
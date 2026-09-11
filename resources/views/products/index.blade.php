@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endsection

@section('content')
<div class="container-fluid px-3">

    {{-- Page header --}}

    <div class="page-header">
        <div>
            <h3 class="mt-3">
                Products
            </h3>

            <p class="text-muted mb-3">Manage and monitor your product inventory</p>
        </div>
    </div>
        
        {{-- Statistics --}}

    <div class="row g-3 product-stats">

        {{-- Total Products --}}
        <div class="col-12 col-md-4">
            <div class="product-stat-card">
                <div class="stat-content">
                    <p class="stat-title">
                        Total Products
                    </p>
                    <h3 class="stat-number">
                        {{ $totalProducts }}
                    </h3>
                </div>
            </div>
        </div>

        {{-- Active Products --}}
        <div class="col-12 col-md-4">
            <div class="product-stat-card">
                <div class="stat-content">
                    <p class="stat-title">Active Products</p>

                    <h3 class="stat-number active-number">
                        {{ $activeProducts }}
                    </h3>
                </div>
            </div>
        </div>

        {{-- In-active Products --}}
        <div class="col-12 col-md-4">
            <div class="product-stat-card">
                <div class="stat-content">
                    <p class="stat-title">In-Active Products</p>

                    <h3 class="stat-number inactive-number">
                        {{ $inactiveProducts }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Filters --}}

    <div class="product-filter-section">
        <div class="filter-header">
            <div>
                <h5 class="mb-1 fw-semibold">
                    Search & Filter
                </h5>

                <p class="text-muted mb-0">
                    Find products using search and filters.
                </p>
            </div>
        </div>

        <form action="{{ route('products.index') }}" method="GET" class="product-filter">
            <div class="row g-3 align-items-end">

            {{-- Search by name and sku --}}

            <div class="col-12 col-md-3">
                <label for="search">Search Product</label>
                <input type="text" name="search"
                    placeholder="Search by name or SKU"
                    value="{{ request('search') }}"
                    class="form-control">
            </div>


            {{-- Category  --}}

            <div class="col-12 col-md-2">
                <label for="categories">Categories</label>
                <select type="text"
                    name="category_id"
                    class="form-select">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $category->id == request('category_id') ? 'selected' : '' }}
                            >{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>


            {{-- Status --}}

            <div class="col-12 col-md-2">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All status</option>
                    <option value="1"
                    {{ request('status') === '1' ? 'selected' : '' }}
                    >Active</option>

                    <option value="0"
                    {{ request('status') === '0' ? 'selected' : '' }}
                    >In-active</option>
                </select>
            </div>


            {{-- Stock --}}

            <div class="col-12 col-md-2">
                <label for="stock">Stock</label>
                <select name="stock" id="stock" class="form-select">
                    <option value="">All Stock</option>
                      <option value="low"
                        {{ request('stock') === 'low' ? 'selected' : '' }}>
                        Low stock
                    </option>
                    <option value="out"
                        {{ request('stock') === 'out' ? 'selected' : '' }}>
                        Out of stock
                    </option>
                </select>
            </div>

            {{-- buttons --}}

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Search
                </button>

                <a href="{{ route('products.index') }}"
                class="btn btn-secondary d-flex align-items-center">Clear</a>
            </div>

            </div>
        </form>
    </div>

    {{-- Table --}}

    <div class="product-table-section">
        <div class="product-table-header">

            <div>
                <h4 class="mb-1 fw-semibold">
                    Product List
                </h4>

                <p class="text-muted mb-0">
                    View and manage all products.
                </p>
            </div>

        </div>

        <div class="table-responsive">
            <table class="table product-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Purchase price</th>
                        <th>Selling price</th>
                        <th>Stock</th>
                        <th>Minimum stock</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>
                @php
                    $i = 0;
                @endphp
                    @forelse ($products as $product)
                        @php
                            $i++;
                        @endphp
                        <tr>
                            <td>{{ $i }}</td>
                            {{-- Product Name --}}
                            <td>
                                <span class="product-name">
                                    {{ $product->name }}
                                </span>
                            </td>

                            {{-- SKU --}}

                            <td>
                                <span class="product-sku">
                                    {{ $product->sku }}
                                </span>
                            </td>

                            {{-- Category --}}
                            <td>
                                <span class="product-category">
                                    {{ $product->Categories->name }}
                                </span>
                            </td>

                            {{-- Purchase Price --}}

                            <td>
                                 ₹{{ number_format($product->purchase_price,2) }}
                            </td>

                            {{-- Selling Price --}}

                            <td>
                                 ₹{{ number_format($product->selling_price,2) }}
                            </td>

                            {{-- Stock   --}}
                            <td>
                                @if($product->stock_quantity == 0)
                                    <span class="stock-badge stock-out">
                                        Out of stock
                                    </span>
                                
                                @elseif ($product->stock_quantity <= $product->minimum_stock)
                                    <span class="stock-badge stock-low">
                                        {{ $product->stock_quantity }}
                                    </span>
                                @else   
                                    <span class="stock-badge stock-normal">
                                        {{ $product->stock_quantity }}
                                    </span>
                                @endif
                            </td>

                            {{-- Minimum stock --}}

                            <td>
                                {{ $product->minimum_stock }}
                            </td>

                            {{-- Unit --}}
                            <td>
                                {{ $product->unit }}
                            </td>

                            {{-- Status --}}

                            <td>
                                @if($product->is_active)
                                    <span class="product-status status-active">
                                        Active
                                    </span>
                                @else
                                    <span class="product-status status-inactive">
                                        In-active
                                    </span>
                                @endif
                            </td>

                            {{-- Action --}}

                            <td>
                                <div class="product-actions justify-content-end">
                                    <a href="{{ route('product.update_form', $product->id) }}"
                                    class="btn btn-primary">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="10 text-center py-5">
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
    <div class="product-pagination">
        {{ $products->links() }}
    </div>
</div>

@endsection
@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endsection

@section('content')

<div class="container-fluid px-3">
    <div class="product-form-page">

        {{-- Page header --}}
        <div class="product-form-header d-flex align-items-center justify-content-between">
            <div>
                <h3 class="mb-1mt-3">
                    Update Product
                </h3>

                <p class="text-muted mb-0">
                    Update product information and setting.
                </p>
            </div>

            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <- Back To Purchase
            </a>
        </div>

        {{-- Form-card --}}

        <div class="product-form-card"> 
            
            {{-- Card Header --}}

            <div class="product-form-card-header">
                <div>
                    <h5 class="mb-1 fw-semibold">
                        Product Information
                    </h5>

                    <p class="text-muted mb-0">
                        Update the product details below.
                    </p>
                </div>
            </div>

            <form action="{{ route('product.update',$product->id) }}" method="POST">
                @csrf

                <div class="row g-4">

                    {{-- Product Name --}}

                    <div class="col-12 col-md-6">
                        <label for="productName" class="form-label">Product Name</label>

                        <input type="text" 
                            name="name"
                            value="{{ old('name', $product->name) }}"
                            class="form-control @error('name') is-invalid @enderror"
                        >

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Category --}}

                    <div class="col-12 col-md-6">
                        <label for="category" class="form-label">
                            Category
                        </label>

                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            @foreach ($categories as $category)
                                <option value = {{ $category->id }}
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}</option> 
                            @endforeach
                        </select>    
                        
                        @error('category_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Purchase price --}}

                    <div class="col-12 col-md-6">
                        <label for="purchase_price" class="form-label">
                            Purchase Price
                        </label>

                        <input type="number" 
                        step="0.01"
                         name="purchase_price"
                        value="{{ $product->purchase_price }}"
                        class="form-control bg-light"
                        readonly>

                    </div>

                    {{-- Selling Price --}}

                    <div class="col-12 col-md-6">
                        <label for="selling_price" class="form-label">
                            Selling Price
                        </label>

                        <input type="number"
                            step="0.01" 
                            name="selling_price" 
                            value="{{ $product->selling_price }}"
                            class="form-control @error('selling_price') is-invalid @enderror"
                        >

                        @error('selling_price')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror                        
                    </div>

                    {{-- Stock quantity --}}

                    <div class="col-12 col-md-6">
                        <label for="stock-quantity" class="form-label">
                            Stock Quantity
                        </label>

                        <input type="number"
                         name="stock_quantity"
                        value="{{ old('stock_quantity', $product->stock_quantity) }}"
                        class="form-control bg-light"
                        readonly>

                        <small>Stock is automatically managed through purchases and sales.</small>
                    </div>

                    {{-- Minimum stock --}}

                    <div class="col-12 col-md-6">
                        <label for="minimumStock" class="form-label">
                            Minimum Stock
                        </label>

                        <input type="number"
                            name="minimum_stock" 
                            placeholder="Enter minimum stock"
                            value="{{ old('minimum_stock', $product->minimum_stock) }}"
                            class="form-control @error('minimum_stock') is-invalid @enderror"
                        >

                        @error('minimum_stock')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        
                    </div>

                    {{-- Unit   --}}

                    <div class="col-12 col-md-6">
                        <label for="unit" class="form-label">
                            Label
                        </label>

                        <input type="text" 
                            name="unit" 
                            placeholder="Example: Piece, Kg, Box"
                            value="{{ old('unit', $product->unit) }}"
                            class="form-control @error('unit') is-invalid @enderror"
                        >

                        @error('unit')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Product status --}}
                    <div class="col-12 col-md-6">


                        <div class="form-check form-switch product-status-switch">
                            <input type="checkbox" 
                                name="is_active" 
                                value="1"
                                {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                class="form-check-input @error('is_active') is-invalid @enderror"
                            >
                            <label class="form-check-label"
                                   for="isActive">

                                Active Product

                            </label>
                        </div>
                        <small>Inactive products will not be available for sale.</small>

                    </div>
                </div>

                {{-- Form action --}}

                <div class="product-form-actions">
                    <a href="{{ route('products.index') }}"
                    class="btn btn-secondary">Cancel</a>

                    <button type="submit" class="btn btn-primary">
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
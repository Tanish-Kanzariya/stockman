@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/sale.css') }}">
@endsection

@section('content')
<div class="container-fluid px-3">
    
    {{-- Page header --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h3>New Sale</h3>
            <p>Create a new sale and manage customer billing.</p>
        </div>

        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-big-left-line"></i> Back
        </a>
    </div>

    <div class="row g-4">

        {{-- Left side --}}
        <div class="col-lg-8">

            {{-- Customer information   --}}
            <div class="sale-card">
                <div class="sale-card-header">
                    <h5>Customer Information</h5>
                    <p>Add customer details for this sale.</p>
                </div>

                <div class="sale-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="customer_name" class="form-label">
                                Customer Name
                            </label>

                            <input type="text"
                                id="customerName"
                                class="form-control"
                                placeholder="Enter customer name">
                        </div>

                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">
                                Phone Number
                            </label>

                            <input type="text"
                                id="customerPhone"
                                class="form-control"
                                placeholder="Enter phone number">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Products --}}
            <div class="sale-card mt-4">
                <div class="sale-card-header">
                    <h5>Products</h5>
                    <p>Search and add product to this sale</p>
                </div>

                <div class="sale-card-body">
                    
                    {{-- Product search --}}
                    <div class="product-search-wrapper">
                        <label for="search_products" class="form-label">
                            Search Product
                        </label>

                        <input type="text"
                            name="search"
                            id="productSearch"
                            class="form-control"
                            placeholder="Search by product name or SKU"
                        >
                        <small class="text-muted d-block mt-2">
                            Search for a product and select it to add it to this sale.
                        </small>

                        {{-- Search Results --}}
                        <div id="productSearchResults" class="product-search-results">
                            {{-- Java script --}}
                        </div>

                    </div>

                   
                    {{-- Sale Items --}}

                    <div class="table-responsive mt-4">
                        <table class="table sale-items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody id="saleItemsBody">
                                <tr id="emptySaleRow">
                                    <td colspan="5" class="text-center py-5">
                                        <div class="empty-sale-state">
                                            <div class="empty-sale-icon">
                                                 🛒
                                            </div>

                                            <h6>No products added yet.</h6>

                                            <p class="text-muted">
                                                Search and select products to add them to this sale.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Side --}}
        <div class="col-lg-4">
            <div class="sale-card sale-summary-card">

                <div class="sale-card-header">
                    <h5>Order Summary</h5>
                    <p>Review the sale before completing.</p>
                </div>

                <div class="sale-card-body">

                    {{-- Subtotal --}}
                    <div class="summary-row">
                        <span>Subtotal</span>

                        <strong id="subTotalAmount">
                            ₹0.00
                        </strong>
                    </div>

                    {{-- Discount --}}
                    <div class="summary-field">
                        <label for="discount">Discount</label>

                        <input type="number" id="discount" class="form-control" value="0" min="0">

                    </div>

                    {{-- Tax --}}
                    <div class="summary-field">
                        <label for="tax">Tax</label>

                        <input type="number" id="tax" class="form-control" value="0" min="0">
                    </div>
                    <hr>

                    {{-- Total --}}
                    <div class="summary-row total-row">
                        <span>Total</span>
                        <strong id="totalAmount">
                            ₹0.00
                        </strong>
                    </div>

                    {{-- Payment method --}}
                    <div class="summary-field mt-4">
                        <label for="payment_method">Payment Method</label>

                        <select name="payment_method" id="paymentMethod" class="form-select">
                            <option value="">Select Payment Method</option>

                            <option value="cash">Cash</option>

                            <option value="upi">UPI</option>

                            <option value="card">Card</option>

                        </select>
                    </div>

                    {{-- Complete Sale --}}
                    <button type="button" id="completeSaleBtn"
                    class="btn btn-primary w-100 mt-4" disabled>
                        Complete Sale
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/sales.js') }}"></script>
@endsection
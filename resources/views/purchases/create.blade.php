@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/create_purchase.css') }}">
@endsection

@section('content')
<div class="purchase-create-page">

    <div class="purchase-create-header d-flex align-items-center justify-content-between">
        <div>
            <h3 mb-1>New Purchase</h3>
            <p class="text-muted">
                Create new inventory purchase and add to stock.
            </p>
        </div>

        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-big-left-line"></i> Back</a>
    </div>
    <form action="{{ route('purchase.store') }}" method="POST" id="purchaseForm"
    data-product-submit-url="{{ route('product.submit') }}">
    @csrf

    <div class="purchase-create-card">
        <div class="purchase-create-card-header">
            <div>
                <h2>
                    Purchase Information
                </h2>
                <p>
                    Select the supplier and purchase date
                </p>
            </div>
        </div>

        <div class="purchase-create-form-grid">

            <div class="purchase-field">
                <label for="supplier_id">Supplier</label>

                <select name="supplier_id" class="form-select">
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="purchase-field">
                <label for="purchase_date">Purchase Date</label>

                <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}"
                class="form-control">

            </div>

        </div>
    </div>

    <div class="purchase-create-card purchase-items-section">
        <div class="purchase-items-heading">
            <div>
                <h2>Purchase Items</h2>
                <p>Add the products included in this purchase.</p>
            </div>

            <button type="button" id="addModalBtn" class="btn btn-primary">
                + Add Product
            </button>
        </div>

        <div class="table-responsive">
            <table class="table purchase-create-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody id="purchaseItems">
                    {{-- Grand Total: <span id="grandTotal"></span> --}}
                </tbody>
            </table>
        </div>

        <div class="purchase-total-row">
            <span>Total Purchase</span>
            <strong>
                ₹<span id="grandTotal">0.00</span>
            </strong>
        </div>
    </div>

    
    <input type="hidden" name="purchase_items" id="purchaseItemsInput">
    {{-- <button type="submit">Save Purchase</button> --}}
    <div class="purchase-create-actions">

        <button type="button" onclick="removeAllPurchaseItem()" class="btn btn-outline-secondary">
            Cancel
        </button>

        <button type="submit" class="btn btn-primary">
            Save Purchase
        </button>

    </div>

</form>

        {{-- Modal starts --}}

<div id="productModal" class="purchase-modal"> 

    <div class="purchase-modal-content">

        {{-- Modal Header --}}

        <div class="purchase-modal-header">
            <div>
                <h3>Add Purchase Item</h3>
                <p>Add a product to this purchase.</p>
            </div>

            <button type="button" class="modal-close-btn" id="closeModalBtn">
                &times;
            </button>
        </div>

        {{-- Modal Body --}}

        <div class="purchse-modal-body">

            {{-- product search --}}

            <div class="modal-field">
                <label for="productseach">Product</label>

                <input type="text"
                id="productSearch" 
                placeholder="Search Product...." 
                autocomplete="off"
                class="form-control">

                <div id="searchResults" class="search-results"></div>

            </div>
            
            {{-- Selected Products --}}

            <div class="selected-product" id="selectedProduct"></div>

            <button type="button" id="addNewProductBtn"
            class="add-new-product-btn"
            style="display:none">
                + Add New Product
            </button>

            {{-- Quantity --}}

            <div class="modal-field">
                <label for="itemQuantity">Quantity</label>

            <input type="number" id="itemQuantity" min="1" class="form-control" 
            placeholder="Enter quantity">

            </div>

            {{-- purchase price --}}

            <div class="modal-field">
                <label for="itemPrice">Purchase Price</label>

                <div class="price-input">
                    <span>₹</span>

                    <input type="number" id="itemPrice" min="0" step="0.00" class="form-control"
                    placeholder="0.00">

                </div>
            </div>
        </div>

        {{-- Modal footer --}}

        <div class="purchase-modal-footer">
            {{-- <button type="button"
            id="cancelModalBtn"
            class="btn btn-outline-secondary">
                Cancel
            </button> --}}

            <button type="button" id="addItemBtn" class="btn btn-primary">
                Add Purchase
            </button>
        </div>
    </div>

    {{-- <button type="button" id="addProductBtn">+ Add Product</button> --}}

</div>  
        {{-- Modal Ends --}}

        {{-- New Modal starts --}}
<div id="newProductModal" class="purchase-modal">   

    <div class="purchase-modal-content new-product-modal-content">

        {{-- Modal Header --}}

        <div class="purchase-modal-header">
            <div>
                <h3>Create New Product</h3>
                <p>Add a new product and include it in this purchase.</p>
            </div>

            <button type="button"
            class="modal-close-btn"
            id="closeNewProductTopBtn">
                &times;
            </button>
        </div>

        {{-- Modal Body --}}

        <div class="purchase-modal-body">
            <div class="new-product-form-grid">

                {{-- product name --}}

                <div class="modal-field new-product-full-width">
                    <label for="newProductName">Product Name</label>

                <input type="text"
                        name="product_name"
                        id="newProductName"
                        class="form-control"
                        placeholder="Enter product name">

                </div>

                {{-- Category --}}

                <div class="modal-field">
                    <label for="category">Category</label>

                    <select id="newProductCategory" class="form-select">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Purchase price --}}
                <div class="modal-field">
                    <label for="purchasePrice">Purchase Price</label>

                    <input type="number"
                     id="newProductPurchasePrice"
                     class="form-control"
                     step="0.01"
                     min="0"
                     placeholder="0.00">

                </div>

                {{-- selling price --}}

                <div class="modal-field">
                    <label for="sellingPrice">Selling Price</label>

                    <input type="number"
                     id="newProductSellingPrice"
                     placeholder="0.00"
                     min="0"
                     step="0.01"
                     class="form-control">

                </div>

                {{-- Minimum stock --}}
                <div class="modal-field">
                    <label for="minimumStock">Minimum Stock</label>

                    <input type="number" 
                    id="newProductMinimumStock"
                    placeholder="Enter minimum stock"
                    min="0"
                    class="form-control">
                </div> 
                
                {{-- Unit --}}

                <div class="modal-field">
                    <label for="unit">Unit</label>

                    <input type="string"
                     id="newProductUnit"
                     class="form-control"
                     placeholder="Example: kg, pcs">
                </div>

                {{-- Active Status --}}

                <div class="product-active-field">
                    <div>
                        <h6>Product Status</h6>
                        <p>Enable this product for use in the inventory</p>
                    </div>

                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input"
                        id="newProductActive" checked>
                    </div>
                </div>
            </div>
        </div>
            
            {{-- Modal Footer --}}

            <div class="purchase-modal-footer">
                <button type="button"
                id="closeNewProductBtn"
                class="btn btn-outline-secondary">
                    Cancel
                </button>

                <button type="button"
                id="createNewProductBtn"
                class="btn btn-primary">
                    Create Product
                </button>
            </div>
        {{-- </div> --}}
    </div>
</div>

        {{-- New Modal ends --}}
</div>


<script src="{{ asset('js/purchase.js') }}"></script>

@endsection

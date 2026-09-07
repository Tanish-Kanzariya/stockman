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

        <a href="{{}}" class="btn btn-outline-secondary">
            <- Back to sales
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
                    class="btn btn-primary w-100 mt-4">
                        Complete Sale
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const productSearch = document.getElementById('productSearch');

    const productSearchResults = document.getElementById('productSearchResults');

    const saleItemsBody = document.getElementById('saleItemsBody');

    const subTotalAmount = document.getElementById('subTotalAmount');

    const discountInput = document.getElementById('discount');

    const taxInput = document.getElementById('tax');

    const totalAmount = document.getElementById('totalAmount');

    // Fetching data from server
    productSearch.addEventListener('input',(e)=>{
        const search = e.target.value.trim();

        // If search is empty then remove results
        if(search === ''){
            productSearchResults.innerHTML = '';
            return;
        }

        fetch(`/sales/search-products?search=${encodeURIComponent(search)}`)
            .then(response => response.json())

            .then(products =>{
                productSearchResults.innerHTML = '';

                if(products.length === 0){
                    productSearchResults.innerHTML = `
                    <div class="no-result-found">
                        No products found
                    </div>
                    `;
                    return;
                }
                
                products.forEach(product=>{
                    productSearchResults.innerHTML += `
                        <div class="product-search-item"
                            data-id="${product.id}"
                            data-name="${product.name}"
                            data-sku="${product.sku}"
                            data-price="${product.selling_price}"
                            data-stock="${product.stock_quantity}"
                            data-unit="${product.unit}"
                        >
                            <div>
                                <strong>${product.name}</strong>

                                <small>SKU: ${product.sku}</small>    
                            </div>

                            <div class="product-search-price">
                                ₹${product.selling_price}

                                <small>Stock: ${product.stock_quantity}</small>
                            </div>
                        </div>
    
                    `;
                });
            })
            .catch(error => {
                console.error('Product search error:',error);
            });
    });

    //Selecting and adding the product in the table

    productSearchResults.addEventListener('click',(e)=>{
        const selectedProduct = e.target.closest('.product-search-item');

        if(!selectedProduct){
            return;
        }

        const product = {
            id: selectedProduct.dataset.id,
            name: selectedProduct.dataset.name,
            sku: selectedProduct.dataset.sku,
            price: selectedProduct.dataset.price,
            stock: selectedProduct.dataset.stock,
            unit: selectedProduct.dataset.unit
        }

        // console.log(product);

        const existingRow = saleItemsBody.querySelector(
            `tr[data-product-id="${product.id}"]`
        );

        if(existingRow){
            const quantityInput = existingRow.querySelector('.quantity-input');

            let quantity = parseInt(quantityInput.value);

            const stock = parseInt(existingRow.dataset.stock);

            if(quantity < stock){
                quantity++;

                quantityInput.value = quantity;

                quantityInput.dispatchEvent(new Event('input',{bubbles:true}));
            }

            productSearch.value = '';
            productSearchResults.innerHTML = '';

            return;
        }

        const emptySaleRow = document.getElementById('emptySaleRow');

        if(emptySaleRow){
            emptySaleRow.remove();
        }

        // Add product row in the table

        saleItemsBody.insertAdjacentHTML('beforeend', `
            <tr data-product-id="${product.id}"
                data-price="${product.price}"
                data-stock="${product.stock}"    
            >
                <td>
                    <strong>${product.name}</strong>
                    <small class="d-block text-muted">SKU ${product.sku}</small>
                </td>
                <td>
                    ₹${product.price}
                </td>
                <td>
                    <input type="number" 
                        class="form-control quantity-input"
                        value="1" 
                        min="1" 
                        max="${product.stock}"
                    >
                </td>
                <td class="item-subtotal">
                    ${product.price}
                </td>
                <td>
                    <button class="btn btn-outline-danger remove-item-btn">
                        Remove
                    </button>
                </td>
            </tr>

        `); 
      

        updateSaleSummary();

        productSearch.value = '';

        productSearchResults.innerHTML = '';
    });

    // Calculating the subtotal for every row in saleItemsBody
    saleItemsBody.addEventListener('input', (e)=>{
        if(!e.target.classList.contains('quantity-input')){
            return;
        }

        const quantityInput = e.target;

        const row = quantityInput.closest('tr');

        const price = parseFloat(row.dataset.price);

        const stock = parseInt(row.dataset.stock);

        let quantity = parseInt(quantityInput.value);

        if(isNaN(quantity) || quantity<1){
            quantity = 1;
        }

        if(quantity > stock){
            quantity = stock;
        }

        quantityInput.value = quantity;

        const subtotal = price*quantity;

        row.querySelector('.item-subtotal').textContent = `₹${subtotal.toFixed(2)}`;

        //Update this row subtotal
        updateRowSubtotal(row);

        //Update complete sale summary
        updateSaleSummary();
    });

    //Deleting the row when delete button is pressed

    saleItemsBody.addEventListener('click', (e)=>{
        const removeButton = e.target.closest('.remove-item-btn');

        if(!removeButton){
            return;
        }

        e.preventDefault();

        const row = removeButton.closest('tr');

        if(!row){
            return;
        }
        row.remove();

        if(saleItemsBody.querySelectorAll('tr[data-product-id]').length === 0){
            saleItemsBody.innerHTML = `
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
            `;

            discountInput.value = 0;
            taxInput.value = 0;
            updateSaleSummary();

        }
    });

    // Function to calculate summary subtotal and totals

    function updateSaleSummary(){
        let subtotal = 0;

        const rows = saleItemsBody.querySelectorAll('tr[data-product-id]');

        rows.forEach(row=>{
            const price = parseFloat(row.dataset.price);

            let quantity = parseInt(row.querySelector('.quantity-input').value);

            subtotal += price*quantity;
        });

        let discount = parseFloat(discountInput.value) || 0;

        let tax = parseFloat(taxInput.value) || 0;

        if(discount < 0){
            discount = 0;
            discountInput.value = 0;
        }

        if(tax < 0){
            tax = 0;
            taxInput.value = 0;
        }

        if(discount > subtotal){
            discount = subtotal;
            discountInput.value = subtotal;
        }

        const total = subtotal - discount + tax;

        subTotalAmount.textContent = `₹${subtotal.toFixed(2)}`;

        totalAmount.textContent = `₹${total.toFixed(2)}`;
    }

    function updateRowSubtotal(row){
        const price = parseFloat(row.dataset.price);

        const quantityInput = row.querySelector('.quantity-input');

        const quantity = parseInt(quantityInput.value) || 0;

        const subtotal = price * quantity;

        row.querySelector('.item-subtotal').textContent = `₹${subtotal.toFixed(2)}`;
    }

    discountInput.addEventListener('input', updateSaleSummary);
    taxInput.addEventListener('input', updateSaleSummary);
</script>
@endsection
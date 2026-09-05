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

        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary"><- Back to purchase</a>
    </div>
    <form action="{{ route('purchase.store') }}" method="POST" id="purchaseForm">
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


<script>

    const modalBtn = document.getElementById('addModalBtn');

    const productModal = document.getElementById('productModal');

    const searchResults = document.getElementById('searchResults');

    const selectedProduct = document.getElementById('selectedProduct');

    const closeModal = document.getElementById('closeModalBtn');

    // const cancelModalBtn = document.getElementById('cancelModalBtn');

    const productSearch = document.getElementById('productSearch');

    const itemQuantity = document.getElementById('itemQuantity');

    const itemPrice = document.getElementById('itemPrice');

    const itemBtn = document.getElementById('addItemBtn');

    const purchaseItemTable = document.getElementById('purchaseItems');

    const grandTotal = document.getElementById('grandTotal');

    const addNewProductBtn = document.getElementById('addNewProductBtn');

    const purchaseItemsInput = document.getElementById('purchaseItemsInput');

    const purchaseForm = document.getElementById('purchaseForm');


    let selectedProductId = null;

    let selectedProductName = null;

    let purchaseItem = [];

    let editIndex = null;

    modalBtn.addEventListener('click',()=>{
        productModal.style.display = 'flex';
        itemBtn.textContent = 'Add Purchase';
        modalBtn.style.display = 'none';
        searchResults.style.display = 'block';
    });


    closeModal.addEventListener('click',()=>{
        productModal.style.display = 'none';
        modalBtn.style.display = 'block';
        editIndex = null;
        selectedProductId = null;
        selectedProductName = null;
        selectedProduct.textContent = '';
        productSearch.value = '';
        itemQuantity.value = '';
        itemPrice.value = '';  
    });

    // cancelModalBtn.addEventListener('click',()=>{
    //     editIndex = null;
    //     selectedProductId = null;
    //     selectedProductName = null;
    //     selectedProduct.textContent = '';
    //     productSearch.value = '';
    //     itemQuantity.value = '';
    //     itemPrice.value = '';  
    // })

    productSearch.addEventListener('input',()=>{
        const search = productSearch.value.trim();

        if(search === '')
            return;

        // console.log(search);

        fetch(`/products/search?search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(products =>{
                searchResults.innerHTML = '';
                addNewProductBtn.style.display = 'none';

                if(products.length === 0){
                    searchResults.textContent = 'No result found';
                    addNewProductBtn.style.display = 'block';
                    return;
                }

                products.forEach(product => {
                    const result = document.createElement('div');

                    result.textContent = product.name;
                    result.dataset.id = product.id;
                    result.dataset.price = product.purchase_price;

                    searchResults.appendChild(result);
                });
            });
    });

    searchResults.addEventListener('click',(e)=>{
            const result = e.target;
            const productId = result.dataset.id;
            selectedProductId = productId;
            selectedProductName = result.textContent;
            selectedProduct.textContent = `Selected Product: ${selectedProductName}`;
            itemPrice.value = result.dataset.price;
            searchResults.innerHTML = '';
            productSearch.value = '';
    });


    itemBtn.addEventListener('click',()=>{

        if(selectedProductId === null){
            alert('Please select the product');
            return;
        }
        const quantity = Number(itemQuantity.value);
        const price = Number(itemPrice.value);

        if(quantity <= 0){
            alert('Please enter a valid quantity');
            return;
        }

        if(price < 0){
            alert('Please enter a valid price');
            return;
        }

        if(editIndex !== null){
            purchaseItem[editIndex] = {
                product_id : selectedProductId,
                product_name : selectedProductName,
                quantity : quantity,
                price : price
            }
        }else{
            const alreadyExist = purchaseItem.some(item=>{
                return Number(item.product_id) === Number(selectedProductId);
            });

             if(alreadyExist){
                 alert('This item already exists in the purchase list.');
                 return;
             }
       
        purchaseItem.push({
            product_id: selectedProductId,
            product_name: selectedProductName,
            quantity : quantity,
            price : price
        });
    }

        renderPurchaseItems();
        editIndex = null;
        selectedProductId = null;
        selectedProductName = null;
        selectedProduct.textContent = '';
        productSearch.value = '';
        itemQuantity.value = '';
        itemPrice.value = '';   

        // productSearch.focus();
        productModal.style.display = 'none';
        modalBtn.style.display = 'block';
        
    });

    function renderPurchaseItems(){
        purchaseItemTable.innerHTML = '';

        let total = 0;

        purchaseItem.forEach((item, index) => {
            const itemTotal = item.quantity * item.price;

            total = total + itemTotal;

            const row = document.createElement('tr');

            row.innerHTML = `
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>${item.price.toFixed(2)}</td>
                <td>${itemTotal.toFixed(2)}</td>
                <td>
                    <button onclick='editPurchaseItem(${index})' type="button" id='editBtn' class="edit-btn">Edit</button> |
                    <button onclick='removePurchaseItem(${index})' type="button" class="remove-btn">Remove</button>
                </td>
            `;

            purchaseItemTable.appendChild(row);
        });
        grandTotal.textContent = total.toFixed(2);
    }

    function removePurchaseItem(index){
        purchaseItem.splice(index,1);
        renderPurchaseItems();
    }

    function removeAllPurchaseItem(){
        purchaseItem = [];
        renderPurchaseItems();
    }



    function editPurchaseItem(index){
        editIndex = index;
        productModal.style.display = 'flex';
        const item = purchaseItem[index];
        selectedProductId = item.product_id;
        selectedProductName = item.product_name;

        selectedProduct.textContent = `Selected Product : ${item.product_name}`;
        itemQuantity.value = item.quantity;
        itemPrice.value = item.price;

        itemBtn.textContent = 'Update Purchase';
        modalBtn.style.display = 'none';
    }

    purchaseForm.addEventListener('submit',()=>{
        purchaseItemsInput.value = JSON.stringify(purchaseItem);
    });

    addNewProductBtn.addEventListener('click',() => {
        productModal.style.display = 'none';
    });

    // New Product Modal code

    const newProductBtn = document.getElementById('addNewProductBtn');

    const newProductModal = document.getElementById('newProductModal');

    const closeNewProductBtn = document.getElementById('closeNewProductBtn');

    const closeNewProductTopBtn = document.getElementById('closeNewProductTopBtn');

    const newProductName = document.getElementById('newProductName');

    const newProductCategory = document.getElementById('newProductCategory');

    const newProductPurchasePrice = document.getElementById('newProductPurchasePrice');

    const newProductSellingPrice = document.getElementById('newProductSellingPrice');

    const newProductMinimumStock = document.getElementById('newProductMinimumStock');

    const newProductUnit = document.getElementById('newProductUnit');

    const newProductActive = document.getElementById('newProductActive');

    const createNewProductBtn = document.getElementById('createNewProductBtn');

    const csrftoken = document.querySelector('#purchaseForm input[name="_token"]').value;

    // console.log(csrftoken);

    
    newProductBtn.addEventListener('click',()=>{
        newProductModal.style.display = 'flex';
        return;
    });

    closeNewProductBtn.addEventListener('click', ()=>{
        newProductModal.style.display = 'none';
        modalBtn.style.display = 'block';
        return;
    });

    closeNewProductTopBtn.addEventListener('click',()=>{
        newProductModal.style.display='none';
        modalBtn.style.display='block';
        return;
    })

    createNewProductBtn.addEventListener('click',()=>{

        const name = newProductName.value.trim();

        const category_id = newProductCategory.value;

        const purchasePrice = Number(newProductPurchasePrice.value);

        const sellingPrice = Number(newProductSellingPrice.value);

        const minimumStock = Number(newProductMinimumStock.value);

        const unit = newProductUnit.value.trim();

        const active = newProductActive.checked ? 1 : 0;

       if(name === ''){
        alert('Please enter a product name');
        return;
       }

       if(purchasePrice < 0){
        alert("Please enter the valid purchase price");
        return;
       }

       if(sellingPrice<0){
        alert("Please enter a valid selling price");
        return;
       }

       if(minimumStock<0){
        alert('Please enter a valid minimum stock');
        return;
       }

       if(unit===''){
        alert('Please enter a unit');
        return;
       }

       fetch("{{ route('product.submit') }}",{
        method: 'POST',

        headers: {
            'Content-Type' : 'application/json',
            'X-CSRF-TOKEN' : csrftoken,
            'Accept' : 'application/json'
        },

        credentials : 'same-origin',

        body: JSON.stringify({
            name : name,
            category_id : category_id,
            'purchase_price' : purchasePrice,
            'selling_price' : sellingPrice,
            'stock_quantity' : 0,
            'minimum_stock' : minimumStock,
            'unit' : unit,
            'is_active' : active

        })
        
    })
    .then(response => response.json())
        .then(data => {
            // console.log(data);

            if(data.success){
                selectedProductId = data.product.id;
                selectedProductName = data.product.name;
                let updatedPurchasePrice = data.product.purchase_price;

                selectedProduct.textContent = `Selected Product: ${selectedProductName}`;

                itemPrice.value = updatedPurchasePrice;

                newProductModal.style.display = 'none';

                productModal.style.display = 'flex';
            }
        });

        addNewProductBtn.style.display = 'none';
        productSearch.value = '';
        searchResults.style.display = 'none';


    });
</script>

@endsection

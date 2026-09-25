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

       fetch(purchaseForm.dataset.productSubmitUrl,{
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
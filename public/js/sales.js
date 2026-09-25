    const productSearch = document.getElementById('productSearch');

    const productSearchResults = document.getElementById('productSearchResults');

    const saleItemsBody = document.getElementById('saleItemsBody');

    const subTotalAmount = document.getElementById('subTotalAmount');

    const discountInput = document.getElementById('discount');

    const taxInput = document.getElementById('tax');

    const totalAmount = document.getElementById('totalAmount');

    const paymentMethod = document.getElementById('paymentMethod');

    const completeSaleBtn = document.getElementById('completeSaleBtn');

    const customerName = document.getElementById('customerName');

    const phone_number = document.getElementById('customerPhone');

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
        checkSaleForm();

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
        }
       
            updateSaleSummary();
            checkSaleForm();
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

    // To restrict sale when no sale items exists and payment method is not done

    function checkSaleForm(){
        const products = saleItemsBody.querySelectorAll('tr[data-product-id]');

        const payment = paymentMethod.value;

        if(products.length > 0 && payment !== ''){
            completeSaleBtn.disabled = false;
        }else{
            completeSaleBtn.disabled = true;
        }
    }

    //Complete sale button code

    completeSaleBtn.addEventListener('click', ()=>{
        const rows = saleItemsBody.querySelectorAll('tr[data-product-id]');

        const items = [];

        rows.forEach(row=>{
            const productId = row.dataset.productId;

            const quantity = parseInt(row.querySelector('.quantity-input').value);

            items.push({
                product_id : productId,
                quantity : quantity
            });
        })

        // console.log(items);
        const saleData = {
            customer_name : customerName.value.trim(),

            phone_number : phone_number.value.trim(),

            discount : parseFloat(discountInput.value) || 0,

            tax : parseFloat(taxInput.value) || 0,

            payment_method : paymentMethod.value,

            items : items
        }

        fetch('/sales',{
            method: 'POST',
            headers:{
                'Content-Type':'application/json',
                'Accept' : 'application/json',

                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
            },
            body : JSON.stringify(saleData)
        })
        .then(async response => {

            const data = await response.json();
                if(!response.ok){
                    console.error('laravel error', data);

                    throw new Error(data.message || 'Failed to complete sale')
                }
                return data;
        })
        .then(data =>{
            if(data.success){
                window.location.href= `
                /sales/${data.sale_id}/invoice
                `;
            }

            //Reseting the form after success
            resetSaleForm();
        })
        .catch(error=>{
            console.log('Sale error', error);
            alert('error.message');
        });
    });

    //Reset after sale
    function resetSaleForm(){
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

            //Reset discount
            discountInput.value = 0;

            //Reset tax
            taxInput.value = 0;

            //Reset payment method 
            paymentMethod.value = '';

            productSearch.value = '';
            productSearchResults.innerHTML = '';

            updateSaleSummary();
            checkSaleForm();
    }
    paymentMethod.addEventListener('change', checkSaleForm);
    discountInput.addEventListener('input', updateSaleSummary);
    taxInput.addEventListener('input', updateSaleSummary);
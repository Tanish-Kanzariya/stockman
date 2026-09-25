document.addEventListener('DOMContentLoaded', function () {

    const returnInputs =
        document.querySelectorAll('.return-quantity');

    const refundAmount =
        document.getElementById('refundAmount');

    const processReturnBtn =
        document.getElementById('processReturnBtn');

    const returnPage = document.getElementById('returnPage');

    if(!returnPage){
        return;
    }

    const processUrl = returnPage.dataset.processUrl;

    const salesUrl = returnPage.dataset.salesUrl;

    // Calculate refund
    function calculateRefund() {

        let total = 0;

        returnInputs.forEach(input => {

            let quantity =
                parseInt(input.value) || 0;

            const max =
                parseInt(input.max);

            const price =
                parseFloat(input.dataset.price);

            if (quantity < 0) {
                quantity = 0;
                input.value = 0;
            }

            if (quantity > max) {
                quantity = max;
                input.value = max;
            }

            total += quantity * price;

        });

        refundAmount.textContent =
            total.toFixed(2);
    }


    returnInputs.forEach(input => {

        input.addEventListener(
            'input',
            calculateRefund
        );

    });


    // Process return
    processReturnBtn.addEventListener(
        'click',
        async function () {

            const items = [];

            returnInputs.forEach(input => {

                const quantity =
                    parseInt(input.value) || 0;

                if (quantity > 0) {

                    items.push({
                        sale_item_id:
                            input.dataset.saleItemId,

                        quantity: quantity
                    });

                }

            });


            if (items.length === 0) {

                alert(
                    'Please select at least one product to return.'
                );

                return;
            }


            const reason =
                document.getElementById(
                    'returnReason'
                ).value;


            processReturnBtn.disabled = true;

            processReturnBtn.textContent =
                'Processing...';


            try {

                const response = await fetch(
                    processUrl,
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'X-CSRF-TOKEN':
                                document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .getAttribute('content')
                        },

                        body: JSON.stringify({
                            reason: reason,
                            items: items
                        })
                    }
                );


                const data =
                    await response.json();


                if (!response.ok) {

                    alert(
                        data.message ||
                        'Return failed.'
                    );

                    processReturnBtn.disabled = false;

                    processReturnBtn.textContent =
                        'Process Return';

                    return;
                }


                if (data.success) {

                    alert(
                        data.message +
                        '\nRefund Amount: ₹' +
                        parseFloat(
                            data.refundAmount
                        ).toFixed(2)
                    );

                    window.location.href =
                        salesUrl;
                }

            }
            catch (error) {

                console.error(error);

                alert(
                    'Something went wrong while processing the return.'
                );

                processReturnBtn.disabled = false;

                processReturnBtn.textContent =
                    'Process Return';
            }

        }
    );

});
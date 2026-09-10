@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/return.css') }}">
@endsection
@section('content')

<div class="container-fluid px-3 sales-return-page">

    {{-- Page Header --}}
    <div class="sales-return-header">

        <div>
            <h2>Sales Return</h2>

            <p>
                Process product returns and manage refunds.
            </p>
        </div>

        <a
            href="{{ route('sales.index') }}"
            class="btn btn-outline-secondary"
        >
            ← Back to Sales
        </a>

    </div>


    {{-- Sale Information --}}
    <div class="return-info-card">

        <div class="return-info-header">
            <div>
                <span class="return-label">
                    Invoice Number
                </span>

                <h4>
                    {{ $sale->invoice_number }}
                </h4>
            </div>

            <div class="return-customer">

                <span class="return-label">
                    Customer
                </span>

                <strong>
                    {{ $sale->customer_name ?? 'Walk-in Customer' }}
                </strong>

                @if($sale->phone_number)
                    <small>
                        {{ $sale->phone_number }}
                    </small>
                @endif

            </div>
        </div>

    </div>


    {{-- Return Items --}}
    <div class="return-items-section">

        <div class="return-section-header">

            <div>
                <h5>Sale Items</h5>

                <p>
                    Select the quantity you want to return.
                </p>
            </div>

        </div>


        <div class="table-responsive">

            <table class="table return-items-table">

                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Sold</th>
                        <th>Returned</th>
                        <th>Returnable</th>
                        <th>Price</th>
                        <th>Return Qty</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($sale->sale_items as $saleItem)

                        <tr>

                            {{-- Product --}}
                            <td>
                                <div class="return-product">

                                    <strong>
                                        {{ $saleItem->product->name }}
                                    </strong>

                                    <small>
                                        SKU: {{ $saleItem->product->sku }}
                                    </small>

                                </div>
                            </td>


                            {{-- Sold --}}
                            <td>
                                {{ $saleItem->quantity }}
                            </td>


                            {{-- Already Returned --}}
                            <td>
                                {{ $saleItem->returned_quantity }}
                            </td>


                            {{-- Returnable --}}
                            <td>

                                @if($saleItem->returnable_quantity > 0)

                                    <span class="returnable-badge">
                                        {{ $saleItem->returnable_quantity }}
                                    </span>

                                @else

                                    <span class="returned-badge">
                                        Fully Returned
                                    </span>

                                @endif

                            </td>


                            {{-- Price --}}
                            <td>
                                ₹{{ number_format($saleItem->price, 2) }}
                            </td>


                            {{-- Return Quantity --}}
                            <td>

                                @if($saleItem->returnable_quantity > 0)

                                    <input
                                        type="number"
                                        class="form-control return-quantity"
                                        data-sale-item-id="{{ $saleItem->id }}"
                                        data-price="{{ $saleItem->price }}"
                                        min="0"
                                        max="{{ $saleItem->returnable_quantity }}"
                                        value="0"
                                    >

                                @else

                                    <span class="text-muted">
                                        —
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>


    {{-- Return Summary --}}
    <div class="return-summary-section">

        <div class="return-summary-card">

            <div class="return-summary-header">
                <h5>Return Summary</h5>
            </div>


            <div class="return-summary-row">

                <span>
                    Refund Amount
                </span>

                <strong>
                    ₹<span id="refundAmount">0.00</span>
                </strong>

            </div>


            <div class="return-reason">

                <label for="returnReason">
                    Return Reason
                </label>

                <textarea
                    id="returnReason"
                    class="form-control"
                    rows="3"
                    placeholder="Enter return reason (optional)"
                ></textarea>

            </div>


            <div class="return-summary-actions">

                <a
                    href="{{ route('sales.index') }}"
                    class="btn btn-light"
                >
                    Cancel
                </a>

                <button
                    type="button"
                    id="processReturnBtn"
                    class="btn btn-primary"
                >
                    Process Return
                </button>

            </div>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const returnInputs =
        document.querySelectorAll('.return-quantity');

    const refundAmount =
        document.getElementById('refundAmount');

    const processReturnBtn =
        document.getElementById('processReturnBtn');


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
                    "{{ route('sales.return.process', $sale->id) }}",
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
                        "{{ route('sales.index') }}";
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
</script>

@endsection
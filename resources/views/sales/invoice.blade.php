@extends('layouts.app')

@section('content')

<div class="container py-4">

    <div class="card">

        <div class="card-body">

            <div class="d-flex justify-content-between">

                <div>
                    <h2>STOCKMAN</h2>

                    <p class="text-muted">
                        Sales Invoice
                    </p>
                </div>

                <div class="text-end">

                    <h4>INVOICE</h4>

                    <strong>
                        {{ $sale->invoice_number }}
                    </strong>

                </div>

            </div>

            <hr>


            {{-- Customer Information --}}

            <div class="row mb-4">

                <div class="col-md-6">

                    <h6>Customer Details</h6>

                    <p class="mb-1">

                        <strong>Name:</strong>

                        {{ $sale->customer_name ?? 'Walk-in Customer' }}

                    </p>

                    <p>

                        <strong>Phone:</strong>

                        {{ $sale->phone_number ?? '-' }}

                    </p>

                </div>


                <div class="col-md-6 text-md-end">

                    <p>

                        <strong>Date:</strong>

                        {{ $sale->created_at->format('d M Y') }}

                    </p>

                    <p>

                        <strong>Payment:</strong>

                        {{ strtoupper($sale->payment_method) }}

                    </p>

                </div>

            </div>


            {{-- Products Table --}}

            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>Product</th>

                            <th>Price</th>

                            <th>Quantity</th>

                            <th class="text-end">
                                Subtotal
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($sale->sale_items as $index => $item)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>


                            <td>

                                {{ $item->product->name }}

                                <small class="text-muted d-block">

                                    SKU:
                                    {{ $item->product->sku }}

                                </small>

                            </td>


                            <td>

                                ₹{{ number_format($item->price, 2) }}

                            </td>


                            <td>

                                {{ $item->quantity }}

                            </td>


                            <td class="text-end">

                                ₹{{ number_format($item->subtotal, 2) }}

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Invoice Summary --}}

            <div class="row justify-content-end">

                <div class="col-md-5">

                    <div class="d-flex justify-content-between mb-2">

                        <span>Subtotal</span>

                        <strong>
                            ₹{{ number_format($sale->subtotal, 2) }}
                        </strong>

                    </div>


                    <div class="d-flex justify-content-between mb-2">

                        <span>Discount</span>

                        <strong>
                            ₹{{ number_format($sale->discount, 2) }}
                        </strong>

                    </div>


                    <div class="d-flex justify-content-between mb-2">

                        <span>Tax</span>

                        <strong>
                            ₹{{ number_format($sale->tax, 2) }}
                        </strong>

                    </div>


                    <hr>


                    <div class="d-flex justify-content-between">

                        <h5>Total</h5>

                        <h5>
                            ₹{{ number_format($sale->total_amount, 2) }}
                        </h5>

                    </div>

                </div>

            </div>


            <hr>


            {{-- Buttons --}}

            <div class="d-flex gap-2">

                <a href="{{ route('sales.create') }}"
                   class="btn btn-primary">

                    New Sale

                </a>


                <button onclick="window.print()"
                        class="btn btn-dark">

                    Print Invoice

                </button>

            </div>

        </div>

    </div>

</div>

@endsection
@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')

<div class="purchase-details-page">

    {{-- Purchase header --}}

    <div class="purchase-details-header d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">Purchase Details</h3>
            <p class="text-muted mb-0">
                View complete information about this purchase.
            </p>
        </div>

        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
          <- Back to purchase
        </a>
    </div>

    {{-- Purchase header ends --}}

    {{-- Purchase Information --}}

    <div class="purchase-info-card">
        <div class="purchase-info-header">

            <div>
                <span class="purchase-label">
                    Invoice Number
                </span>

                <h2>
                    {{ $purchase->invoice_number }}
                </h2>
            </div>

            <div>
                @if($purchase->status == 'completed')
                    <span class="badge bg-success">
                        Completed
                    </span>
                @else   
                    <span class="badge bg-danger">
                        Cancelled
                    </span>
                @endif
            </div>
        </div>

        <div class="purchase-info-grid">
            <div>
                <span class="purchase-label">
                    Supplier
                </span>

                <strong>
                    {{ $purchase->supplier->name }}
                </strong>
            </div>

            <div>
                <span class="purchase-label">
                    Purchase Date
                </span>

                <strong>
                    {{ Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
                </strong>
            </div>

            <div>
                <span class="purchase-label">
                    Total Amount
                </span>

                <strong>
                    ₹{{ number_format($purchase->total_amount,2) }}
                </strong>
            </div>
        </div>
    </div>


    {{-- Purchased Items --}}
    <div class="purchase-items-card">
        <div class="purchase-items-header">
            <div>
                <h2>Purchase Items</h2>

                <p>Products included in this purchase.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table purchase-details-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Purchase Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $item)
                     <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->purchase_price,2) }}</td>
                        <td>{{ number_format($item->subtotal,2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Total --}}

    <div class="purchase-total">
        <span>Total Purchase Amount</span>

        <strong>
            ₹{{ number_format($purchase->total_amount,2) }}
        </strong>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')

{{-- Page Header Starts --}}

<div class="page-header d-flex justify-content-between align-items-center mb-2">
    <div>
        <h3 class="mb-1">Purchase Management</h3>
        <p class="text-muted mb-0">
            Manage and track your inventory purchase.
        </p>
    </div>

    <a href="{{ route('createPurchase') }}" class="btn btn-primary">+ Add Purchase</a>
</div>

{{-- Page Header Ends --}}

{{-- Summary cards starts --}}
<div class="row g-4 mt-2">

    <div class="col-12 col-md-6 col-lg-3">
        <div class="purchase-stat-card">
            <h6>Total Purchase Amount</h6>
            <h3>₹{{ number_format($totalAmountPurchase,2) }}</h3>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="purchase-stat-card">
            <h6>Cancelled Amount</h6>
            <h3>₹{{ number_format($totalAmountCancelled,2) }}</h3>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="purchase-stat-card">
            <h6>Completed Purchase</h6>
            <h3>{{ $completedPurchaseCount }}</h3>
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <div class="purchase-stat-card">
            <h6>Cancelled Purchase</h6>
            <h3>{{ $cancelledPurchaseCount }}</h3>
        </div>
    </div>

</div>
{{-- Summary cards ends --}}

{{-- Filter section starts --}}
<div class="purchase-filter-section mt-4">
    
    <div class="filter-header">
        <div>
            <h4>Filter & Search</h4>
            <p>Find and organize your inventory quickly.</p>
        </div>
    </div>
    <form action="{{ route('purchases.index') }}" method="GET">
        <div class="row g-3 mb-4 mt-0">
        
            {{-- Invoice --}}
            <div class="col-12 col-md-6 col-xl-3">
                <label for="invoice" class="form-label">Invoice</label>

                <input type="text" 
                    name="invoice"
                    value="{{ request('invoice') }}"
                    placeholder="Search Invoice"
                    class="form-control"
                >
            </div>

            {{-- Supplier --}}

            <div class="col-12 col-md-6 col-xl-3">
                <label for="supplier" class="form-label">Supplier</label>

                <select name="supplier" id="supplier" class="form-select">
                    <option value="">All Suppliers</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                            {{ request('supplier') == $supplier->id ? 'selected' : '' }}
                            >{{ $supplier->name }}</option>
                        @endforeach
                </select>
            </div>

            {{-- Status --}}

            <div class="col-12 col-md-6 col-xl-3">
                <label for="status" class="form-label">Staus</label>

                <select name="status" id="" class="form-select">
                    <option value="">Status</option>

                    <option value="completed"
                        {{ request('status') == 'completed' ? 'selected' : '' }}>
                        Completed</option>

                    <option value="cancelled"
                        {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                        Cancelled
                    </option>
                </select>
            </div>

            {{-- Date --}}
            <div class="col-12 col-md-6 col-xl-3">
                <label for="date" class="form-label">Date</label>

                <input type="date" name="date" value="{{ request('date') }}" class="form-control">
            </div>

            {{-- Action --}}

            <div class="col-12 col-md-6 col-xl-3">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
       

        </div>
        

    </form>
</div>


{{-- Filter section ends --}}

{{-- Purchase table starts --}}
<div class="purchase-table-section mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3 p-2">
        <div>
            <h4 class="mb-1 fw-semibold">
                Purchase List
            </h4>

            <p class="text-muted mb-0">
                View and manage all purchase records.
            </p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table purchase-table align-middle mb-0" id="purchase-table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchases as $purchase)        
                <tr>
                    {{-- Invoice --}}
                    <td>
                        <span class="purchase-invoice">
                        {{ $purchase->invoice_number }}
                        </span>
                    </td>

                    {{-- Supplier name --}}
                    <td>
                        <div class="supplier-name">
                        {{ $purchase->Supplier->name }}
                        </div>
                    </td>

                    {{-- Purchase date --}}
                    <td>
                        <div class="purchase-date">
                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
                        </div>
                    </td>

                    {{-- Total amount --}}
                    <td>
                        <span class="total-amount">
                        ₹{{ number_format($purchase->total_amount,2) }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td>
                        @if($purchase->status == 'completed')
                            <span class="purchase-completed status-completed">Completed</span>
                        @else
                            <span class="purchase-status status-cancelled">
                                Cancelled
                            </span>
                        @endif
                        {{-- {{ ucfirst($purchase->status) }} --}}
                    </td>

                    {{-- Action --}}
                    <td>
                        <div class="purchase-actions justify-content-end">
                            <a href="{{ route('purchases.show', $purchase->id) }}"
                                class="btn btn-outline-primary">
                                View
                            </a> 

                            @if($purchase->status === 'completed')
                                <form action="{{ route('purchase.cancel',$purchase->id) }}" method="post" style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                        onclick="return confirm('Are you sure you want to cancel the purchase??')"
                                        class="btn btn-sm btn-outline-danger"
                                     >Cancel</button>
                                </form>
                            @else   
                                <span class="text-muted small">
                                    -
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                    <tr>

                        <td colspan="6" class="text-center py-5">

                            <div class="text-muted">
                                No purchase records found.
                            </div>

                        </td>

                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Purchase table ends --}}

{{ $purchases->links() }}

@endsection
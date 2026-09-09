@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/sale.css') }}">
@endsection

@section('content')
<div class="container-fluid px-3 sales-page">

    {{-- Page header --}}
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h3>Sales</h3>
            <p>Manage and track all your sales transation.</p>
        </div>

        <a href="{{ route('sales.create') }}" class="btn btn-primary">+ New sale</a>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4 sales-stas">

        {{-- Total sales --}}
        <div class="col-12 col-xl-3 col-md-6">
            <div class="sales-stat-card">
                <div class="sales-stat-icon">
                    🧾
                </div>

                <div>
                    <span>Total Sales</span>
                    <h4>
                        {{ $totalSales }}
                    </h4>

                    <small>Completed Transactions</small>
                </div>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="col-12 col-xl-3 col-md-6">
            <div class="sales-stat-card">
                <div class="sales-stat-icon">
                    💰
                </div>
                <div>
                    <span>Total Revenue</span>
                    <h4>
                        ₹{{ number_format($totalRevenue,2) }}
                    </h4>
                    <small>
                        Completed Sales
                    </small>
                </div>

            </div>
        </div>

        {{-- Todays sale --}}
        <div class="col-12 col-xl-3 col-md-6">
            <div class="sales-stat-card">
                <div class="sales-stat-icon">
                    📅
                </div>

                <div>
                    <span>Today's sales</span>

                    <h4>
                        {{ $todaysSales }}
                    </h4>
                    <small>Completed Today</small>
                </div>
            </div>
        </div>

        {{-- Cancelled Sales --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="sales-stat-card">
                <div class="sales-stat-icon">
                    ❌
                </div>

                <div>
                    <span>Cancelled Sales</span>

                    <h4>
                        {{ $cancelledSales }}
                    </h4>

                    <small>Cancelled Transactions</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter section --}}
    <div class="sales-filter-section mt-4">
            <div class="sales-filter-header">
                <h5>Filter Sales</h5>
                <p>Search & filter sales record</p>
            </div>
            <form action="{{ route('sales.index') }}" method="GET">
                <div class="row g-3">

                    {{-- Search --}}
                    <div class="col-12 col-md-4">
                        <label for="search" class="form-label">
                            Search
                        </label>

                        <input type="text"
                            name="search"
                            class="form-control"
                            placeholder="Invoice, customer or phone"    
                            value="{{ request('search') }}"
                        >
                    </div>

                    {{-- Payment --}}
                    <div class="col-12 col-md-2">
                        <label for="payment" class="form-label">
                            Payment
                        </label>

                        <select name="payment_method" id="paymentMethod"
                        class="form-select">
                            <option value="">All Payments</option>

                            <option value="cash"
                                @selected(request('payment_method') === 'cash')
                            >
                            Cash
                            </option>

                            <option value="upi"
                            @selected(request('payment_method') === 'upi')>
                            UPI
                        </option>

                        <option value="card"
                            @selected(request('payment_method') === 'card')>
                            Card
                        </option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-12 col-md-2">
                        <label for="status" class="form-label">
                            Status
                        </label>

                        <select name="status" id="status" class="form-select">
                            <option value="">All status</option>

                            <option value="completed"
                            @selected(request('status') === 'completed')>
                                Completed
                            </option>

                            <option value="cancelled"
                            @selected(request('status') === 'cancelled')>
                            Cancelled
                        </option>
                        </select>
                    </div>

                    {{-- Date --}}
                    <div class="col-12 col-md-2">
                        <label for="date" class="form-label">
                            Date
                        </label>

                        <input type="date" name="date" class="form-control"
                        value="{{ request('date') }}">
                    </div>

                    {{-- Action --}}
                    <div class="col-6 col-md-2 d-flex align-items-end gap-2 sales-filter-actions">
                        <button type="submit" class="btn btn-primary">Search</button>

                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        {{-- </div> --}}
    </div>

    {{-- Sales table card --}}
    <div class="sale-card mt-4">

        <div class="sale-card-header">
            <h5>Sales History</h5>
            <p>View and manage all sales.</p>
        </div>

        <div class="sale-card-body" id="saleCardBody">
            <div class="table-responsive">
                <table class="table sale-items-table">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($sales as $sale)
                            <tr>

                                {{-- Invoice --}}
                                <td>
                                    <strong>
                                        {{ $sale->invoice_number }}
                                    </strong>
                                </td>

                                {{-- Customer --}}
                                <td>
                                    <strong>
                                        {{ $sale->customer_name ?? 'walk-in-customer' }}
                                    </strong>

                                    @if($sale->phone_number)
                                        <small class="text-muted d-block">
                                            {{ $sale->phone_number }}
                                        </small>
                                    @endif
                                </td>

                                {{-- Total Amount --}}
                                <td>
                                    ₹{{ number_format($sale->total_amount,2) }}
                                </td>

                                {{-- Payment --}}
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ ucfirst($sale->payment_method) }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($sale->status === 'completed')
                                        <span class="sale-status sale-status-completed"> 
                                            Completed
                                        </span>
                                    @elseif($sale->status === 'cancelled')
                                        <span class="sale-status sale-status-cancelled">
                                            Cancelled
                                        </span>
                                    @else   
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Date --}}

                                <td>
                                    {{ $sale->created_at->format('d M Y') }}

                                    <small class="d-block text-muted">
                                        {{ $sale->created_at->format('h:i A') }}
                                    </small>
                                </td>

                                {{-- Action --}}
                                <td class="sales-actions">
                                    <a href="{{ route('sales.invoice', $sale->id) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>

                                    @if($sale->status === 'completed')  
                                            <a href="{{ route('sales.return',$sale->id) }}"
                                                class="btn btn-sm btn-outline-warning">
                                                Return
                                            </a>


                                            <button type="submit" class="btn btn-sm btn-outline-danger delete-Sale-Btn"
                                            id="deleteSaleBtn"
                                            data-id="{{ $sale->id }}">
                                                Cancel
                                            </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No Sales Found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination  --}}
            {{ $sales->links() }}
        </div>
    </div>
</div>

<div class="modal fade"
    id="deleteSaleModal"
    tabindex="-1" 
    aria-labelledby="deleteSaleLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">

                <h5 class="modal-title" id="deleteSaleLabel">
                    Delete Sale
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>

            {{-- Body --}}
            <div class="modal-body">
                <p class="mb-2">
                    Are you sure you want to cancel this sale?
                </p>
            </div>

            {{-- Footer --}}
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                >No</button>

                <form action="" method="POST"
                    id="deleteSaleForm">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        Yes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        const deleteButtons = document.querySelectorAll('.delete-Sale-Btn');

        const deleteForm = document.getElementById('deleteSaleForm');

        deleteButtons.forEach(button=>{
            button.addEventListener('click', ()=>{
                const saleId = button.dataset.id;

                deleteForm.action = "{{ route('sales.cancel',':sale') }}" 
                .replace(':sale',saleId);
                button.blur();
                const modal = new bootstrap.Modal(document.getElementById('deleteSaleModal'));

                modal.show();
            })
        })
    });
</script>
@endsection

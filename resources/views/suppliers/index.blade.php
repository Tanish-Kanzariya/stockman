@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/supplier.css') }}">
@endsection

@section('content')

    <div class="container-fluid px-3">

        <div class="supplier-page">

            {{-- Page Header --}}
            <div class="page-header">
                <div >
                    <h3 class="mb-1">Suppliers</h1>
                    <p class="text-muted">Manage your suppliers and supplier information.</p>
                </div>

                <div>
                    <a href="{{ route('supplier.create') }}" class="btn btn-primary">
                        + Add Supplier
                    </a>
                </div>
            </div>

            {{-- Filters starts--}}

            <form action="{{ route('supplier.index') }}" method="GET" 
                class="supplier-filter">

                <div class="search-box">
                    <input type="text" name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by name, phone or email"
                    class="form-control">
                </div>
                

                <button type="submit" class="btn btn-primary">Search</button>

                <a href="{{ route('supplier.index') }}"
                class="btn btn-outline-secondary">Clear</a>

            </form>

            {{-- Filters ends --}}


            {{-- Table --}}
            <div class="supplier-table-section mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3 p-2">

            <div>
                <h4 class="mb-1 fw-semibold">
                    Supplier List
                </h4>

                <p class="text-muted mb-0">
                    View and manage all supplier records.
                </p>
            </div>

        </div>


        <div class="table-responsive">

            <table class="table supplier-table align-middle mb-0">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($suppliers as $supplier)

                        <tr>

                            <td>
                                <div class="supplier-name">
                                    {{ $supplier->name }}
                                </div>
                            </td>

                            <td>
                                {{ $supplier->phone }}
                            </td>

                            <td>
                                {{ $supplier->email ?? '-' }}
                            </td>

                            <td>
                                {{ $supplier->address ?? '-' }}
                            </td>

                            <td class="action">

                                <div class="supplier-actions justify-content-end">

                                    <a href="{{ route('supplier.create', $supplier->id) }}"
                                    class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                    

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger delete-supplier-btn"
                                                data-id="{{ $supplier->id }}"
                                                data-name="{{ $supplier->name }}">
                                            Delete

                                        </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="text-center py-5">

                                <div class="text-muted">
                                    No supplier records found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

    {{ $suppliers->links() }}

    <div class="modal fade"
    id="deleteSupplierModal"
    tabindex="-1"
    aria-labelledby="deleteSupplierModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            {{-- Header --}}

            <div class="modal-header">
                <h5 class="modal-title" id="deleteSupplierModalLabel">
                    Delete Supplier
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>

            {{-- Body --}}

            <div class="modal-body">
                <p class="mb-2">
                    Are you sure you want to delete this supplier
                    <strong id="deleteSupplierName"></strong>?
                </p>

                {{-- <p class="text-muted small mb-0">
                    This action cannot be done.
                </p> --}}
            </div>

            {{-- Footer --}}

            <div class="modal-footer">
                <button type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal">
                    Cancel
                </button>

                <form action="" id="deleteSupplierForm" method="POST">
                    @csrf

                    <button type="submit" class="btn btn-danger">
                        Delete Supplier
                    </button>
                </form>
            </div>
        </div>
    </div>

    </div>


    <script>

document.addEventListener('DOMContentLoaded', function () {

    const deleteButtons =
        document.querySelectorAll('.delete-supplier-btn');

    const deleteForm =
        document.getElementById('deleteSupplierForm');

    const supplierName =
        document.getElementById('deleteSupplierName');


    deleteButtons.forEach(button => {

        button.addEventListener('click', function () {

            const supplierId = this.dataset.id;
            const name = this.dataset.name;


            supplierName.textContent = name;


            deleteForm.action =
                "{{ route('supplier.delete', ':id') }}"
                    .replace(':id', supplierId);


            const modal =
                new bootstrap.Modal(
                    document.getElementById('deleteSupplierModal')
                );


            modal.show();

        });

    });

});

</script>
@endsection
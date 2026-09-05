@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/supplier.css') }}">
@endsection
{{-- @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif --}}
@section('content')

<div class="container-fluid px-3">

    <div class="supplier-form-page">

        {{-- Page header --}}
        <div class="supplier-form-header d-flex align-items-center justify-content-between">
            <div>
                <h3 class="mb-1 mt-3">
                    {{ $supplier->exists ? 'Edit Supplier' : 'Add New Supplier' }}
                </h3>

                <p class="text-muted mb-0">
                    {{ $supplier->exists ? 'Update supplier information' :
                    'Enter supplier information to add supplier' }}
                </p>
            </div>

            <a href="{{ route('supplier.index') }}" class="btn btn-outline-secondary">
                <- Back to suppliers
            </a>
        </div>

        {{-- Form card --}}

        <div class="supplier-form-card">
            <div class="supplier-form-card-header">
                <div>
                    <h5 class="mb-1 fw-semibold">
                        Supplier Information
                    </h5>

                    <p class="text-muted mb-0">
                        {{ $supplier->exists ? 'Update the information' : 
                        'Fill the details below'  }}
                    </p>
                </div>
            </div>

            <form action="{{ $supplier ->exists ? route('supplier.update',$supplier->id) : 
            route('supplier.store') }}" method="POST">
                @csrf

                <div class="row g-4">

                    {{-- Name --}}

                    <div class="col-12 col-md-6">
                        <label for="name">Name</label>

                        <input type="text" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Enter supplier name"
                        value="{{ old('name', $supplier->name) }}">

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Phone --}}

                    <div class="col-12 col-md-6">
                        <label for="phone">Phone Number</label>

                        <input type="text"
                        name="phone"
                        class="form-control @error('phone') is-invalid @enderror"
                        placeholder="Enter phone number"
                        value="{{ old('phone', $supplier->phone) }}">

                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>                            
                        @enderror
                    </div>

                    {{-- Email --}}

                    <div class="col-12 col-md-6">
                        <label for="email">Email</label>

                        <input type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="abc@gmail.com"
                        value="{{ old('email', $supplier->email) }}">

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Address --}}

                    <div class="col-12 col-md-6">
                        <label for="Address">Address</label>

                        <textarea name="address" id="textarea"
                        class="form-control @error('address') is-invalid @enderror"
                        rows="4">
                            {{ old('address', $supplier->address) }}
                        </textarea>

                        @error('address')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                {{-- Actions --}}

                <div class="supplier-form-actions">
                    <input type="reset" class="btn btn-outline-secondary" value="Cancel">

                    <button type="submit" class="btn btn-primary">
                        {{ $supplier->exists ? 'Update' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
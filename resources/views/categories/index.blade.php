@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/categories.css') }}">    
@endsection

@section('content')
<div class="container-fluid px-3">
    <div class="category-page d-flex align-items-center justify-content-between">

        {{-- Header --}}

        <div class="category-header">
            <h3>Categories</h3>
            <p>View and manage all categories</p>
        </div>

        <a href="{{ route('categories.create') }}"
        class="btn btn-primary">
            + Add Category
        </a>
    </div>

    {{-- Summary card --}}

    <div class="category-summary-card">
        <p class="text-muted mb-1">Total Categories</p>
        <h3>
            {{ $totalCategories }}
        </h3>
    </div>

    <div class="categories-table">

        <div>
            <h3>
                Categories List
            </h3>
        </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 0;
                @endphp
                @forelse ($categories as $category)
                    @php
                        $i++;
                    @endphp  
                    
                    <tr>
                        <td>{{ $i }}</td>
                        <td>{{ $category->name }}</td>
                        <td>
                            <a href="{{ route('categories.edit', $category->id) }}"
   class="btn btn-primary">
    Edit
</a>
                        </td>
                    </tr>
                @empty
                    
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
</div>
@endsection
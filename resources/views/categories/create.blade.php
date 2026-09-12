@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/categories.css') }}">    
@endsection

@section('content')
<div class="container-fluid px-3">
    <div class="category-header d-flex align-items-center justify-content-between">
        <div>
            <h2>Categories</h2>
            <p>Create the products categories</p>
        </div>

        <a href="{{ route('categories.index') }}"
        class="btn btn-primary">
            <- Back to Categories
        </a>
    </div>

    <form action="{{ isset($category) ? route('categories.update',$category->id) : route('categories.store') }}" method="POST">
        @csrf
        <div class="col-md-4">
            <label for="name">
                Category Name
            </label>

            <input type="text" name="name" class="form-control" id="name"
            
            value="{{ old('name', $category->name ?? '') }}"
            >

             @error('name')
                <span class="text-danger">{{ $message }}</span>
             @enderror
        </div>

        @if(isset($category))
            <button type="submit" class="btn btn-primary">Update</button>
        @else
            <button type="submit" class="btn btn-primary">Create</button>
        @endif
        <button type="reset" class="btn btn-secondary">Reset</button>
    </form>
</div>
@endsection
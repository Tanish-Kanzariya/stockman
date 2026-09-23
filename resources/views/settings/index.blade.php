@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
@endsection

@section('content')
    <div class="settings-page">
        <div class="settings-header">
            <h1>Settings</h1>
            <p>Manage your firm information and logo.</p>
        </div>

        <div class="settings-card">
            <div class="settings-section-title">
                Firm Information
            </div>

            <form action="{{ route('settings.update') }}"
            method="POST" enctype="multipart/form-data">

                @csrf

                {{-- Firm logo --}}

                <div class="mb-4">
                    <label for="logo" class="form-label">
                        Firm Logo
                    </label>

                    <div class="logo-area">
                        <div class="logo-preview">
                            @if($firm->logo)
                                <img src="{{ asset('storage/' . $firm->logo) }}" alt="Firm Logo">
                            @else
                                <div class="logo-placeholder">
                                    No Logo
                                </div>
                            @endif
                        </div>

                        <div class="logo-info">
                            <p>Upload your firm logo.</p>
                        </div>

                        <input type="file" name="logo" class="form-control" accept=".jpeg,.jpg,.png,.webp">

                        <small class="text-muted">
                            JPG, JPEG, PNG or WEBP. Maximum 5 MB.
                        </small>
                    </div>
                </div>

                @error('logo')
                    <div class="text-danger small mt-2">
                        {{ $message }}
                    </div>
                @enderror

                {{-- Firm Name --}}
                <div class="mb-4">
                    <label for="name" class="form-label">
                        Firm Name
                    </label>

                    <input type="text" name="name" id="name" class="form-control @error('name')
                    is-invalid @enderror" value="{{ old('name', $firm->name) }}" required>

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- GSTIN --}}

                <div class="mb-4">
                    <label for="gst" class="form-label">
                        GSTIN
                    </label>

                    <input type="text"
                    name="gstin"
                    id="gstin"
                    class="form-control @error('gstin') is-invalid @enderror"
                    value="{{ old('gstin', $firm->gstin) }}"
                    placeholder="Enter GSTIN">

                    @error('gstin')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Save btn --}}

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>    
@endsection
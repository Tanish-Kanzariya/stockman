@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')

<div class="profile-page">

    <div class="profile-header">
        <h1>Profile</h1>
        <p>Manage your account information and password.</p>
    </div>

    <div class="profile-card">

        <div class="profile-section-title">
            Account Information
        </div>

        <form action="{{ route('profile.update') }}" method="POST">

            @csrf

            {{-- Name --}}
            <div class="mb-4">

                <label for="name" class="form-label">
                    Name
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}"
                    required
                >

                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            {{-- Email --}}
            <div class="mb-4">

                <label for="email" class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    class="form-control readonly-field"
                    value="{{ $user->email }}"
                    readonly
                >

                <div class="password-help">
                    Email is your login credential and cannot be changed.
                </div>

            </div>


            {{-- Password Section --}}
            <div class="password-section">

                <div class="profile-section-title">
                    Change Password
                </div>


                {{-- Current Password --}}
                <div class="mb-4">

                    <label for="current_password" class="form-label">
                        Current Password
                    </label>

                    <input
                        type="password"
                        name="current_password"
                        id="current_password"
                        class="form-control @error('current_password') is-invalid @enderror"
                    >

                    @error('current_password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- New Password --}}
                <div class="mb-4">

                    <label for="password" class="form-label">
                        New Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                    >

                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="password-help">
                        Leave password fields empty if you don't want to change your password.
                    </div>

                </div>


                {{-- Confirm Password --}}
                <div class="mb-4">

                    <label for="password_confirmation" class="form-label">
                        Confirm New Password
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control"
                    >

                </div>

            </div>


            {{-- Save Button --}}
            <div class="d-flex justify-content-end">

                <button
                    type="submit"
                    class="btn btn-primary save-button"
                >
                    Save Changes
                </button>

            </div>

        </form>

    </div>

    <div class="profile-card danger-zone">

        <div class="danger-title">
            Delete Account
        </div>

        <p class="danger-text">
            Permanently delete your StockMan account, firm and all associated data.
            This action cannot be undone.
        </p>

        <button
            type="button"
            class="btn btn-danger"
            data-bs-toggle="modal"
            data-bs-target="#deleteAccountModal"
        >
            Delete Account
        </button>

    </div>

    <div
    class="modal fade"
    id="deleteAccountModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Delete Account
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <div class="modal-body">

                <div class="alert alert-danger">
                    <strong>Warning!</strong>

                    <p class="mb-0 mt-2">
                        This will permanently delete your StockMan account,
                        firm information, products, sales, purchases,
                        stock records and other associated data.
                    </p>
                </div>

                <p>
                    Enter your current password to confirm this action.
                </p>

                <form
                    action="{{ route('profile.destroy') }}"
                    method="POST"
                    id="deleteAccountForm"
                >

                    @csrf
                    @method('DELETE')

                    <div class="mb-3">

                        <label
                            for="delete_current_password"
                            class="form-label"
                        >
                            Current Password
                        </label>

                        <input
                            type="password"
                            name="current_password"
                            id="delete_current_password"
                            class="form-control"
                            required
                        >

                        @error('delete_password')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                        @error('delete_account')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </form>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    form="deleteAccountForm"
                    class="btn btn-danger"
                >
                    Yes, Delete Everything
                </button>

            </div>

        </div>

    </div>

    </div>
</div>

@if($errors->has('delete_password') || $errors->has('delete_account'))
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));

            deleteModal.show();
        })
    </script>
@endif

@endsection
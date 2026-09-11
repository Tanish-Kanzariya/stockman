<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <title>Document</title>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="brand">
                <h1>StockMan</h1>

                <p>Create your business account</p>
            </div>

            <form action="{{ route('register.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">
                        Owner Name
                    </label>

                    <input type="text" name="name" id="name" 
                        value="{{ old('name') }}"
                        placeholder="Enter owner name"
                        required
                    >

                    @error('name')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="firm_name">
                        Firm name
                    </label>

                    <input type="text"
                            name="firm_name"
                            id="firm_name"
                            value="{{ old('firm_name') }}"
                            placeholder="Enter firm name"
                            required
                    >

                    @error('firm_name')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            placeholder="Enter email address"
                            required
                    >

                    @error('email')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">
                        Password
                    </label>

                    <input type="password"
                            name="password"
                            id="password"
                            placeholder="Create a password"
                            required
                    >

                    @error('password')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        Confirm Password
                    </label>

                    <input type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Confirm your password"
                            required
                    >

                </div>

                <button type="submit" class="register-button">Create Account</button>
            </form>
            
            <div class="login-link">
                Already have an account?
                <a href="{{ route('login') }}">Login</a>
            </div>
        </div>
    </div>
</body>
</html>
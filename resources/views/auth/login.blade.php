<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet"
          href="{{ asset('css/auth.css') }}">

    <title>Login - StockMan</title>
</head>

<body>

    <div class="register-container">

        <div class="register-card">

            <div class="brand">
                <h1>StockMan</h1>

                <p>Login to your business account</p>
            </div>


            <form action="{{ route('login.authenticate') }}"
                  method="POST">

                @csrf


                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
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

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password"
                        required
                    >

                    @error('password')
                        <div class="error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="remember-row">

                    <label>
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                        >

                        Remember Me
                    </label>

                </div>


                <button
                    type="submit"
                    class="register-button"
                >
                    Login
                </button>

            </form>


            <div class="login-link">

                Don't have an account?

                <a href="{{ route('register') }}">
                    Create Account
                </a>

            </div>

        </div>

    </div>

</body>
</html>
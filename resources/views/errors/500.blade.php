<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Something Went Wrong - StockMan</title>
    <link rel="stylesheet" href="{{ asset('css/505.css') }}">
    
</head>

<body>

<div class="error-container">

    <div class="error-code">
        500
    </div>

    <h1 class="error-title">
        Something Went Wrong
    </h1>

    <p class="error-message">
        Something unexpected happened. Please try again later.
    </p>

    @auth
        <a href="{{ route('dashboard') }}" class="btn">
            Back to Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="btn">
            Go to Login
        </a>
    @endauth

</div>

</body>
</html>
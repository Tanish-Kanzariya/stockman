<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Page Not Found - StockMan</title>
    <link rel="stylesheet" href="{{ asset('css/404.css') }}">
</head>

<body>

<div class="error-container">

    <div class="error-code">
        404
    </div>

    <h1 class="error-title">
        Page Not Found
    </h1>

    <p class="error-message">
        Sorry, the page you're looking for doesn't exist.
    </p>

    @auth
        <a href="{{ route('dashboard') }}" class="btn">
            Back to Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="btn">
            Go Back
        </a>
    @endauth

</div>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('styles')
    <title>StockMan</title>
</head>
<body>
   <div class="layout">
    <aside class="sidebar" id="sidebar">

        {{-- Sidebar header starts --}}
        <div class="sidebar-header">

            <div class="logo">
                StockMan
            </div>

            <button class="sidebar-close" id="sidebarClose">

                &times;

            </button>
        </div> 
        {{-- Sidebar header ends --}}

        <ul>
            <li>
                <a href="{{ route('dashboard') }}">Dashboard</a>
            </li>

            <li>
                <a href="{{ route('products.index') }}">Products</a>
            </li>

            <li>
                <a href="">Categories</a>
            </li>

            <li>
                <a href="{{ route('supplier.index') }}">Suppliers</a>
            </li>

            <li>
                <a href="{{ route('purchases.index') }}">Purchases</a>
            </li>

            <li>
                <a href="{{ route('sales.create') }}">New Sale</a>
            </li>

            <li>
                <a href="">Sales</a>
            </li>
            
            <li>
                <a href="">Reports</a>
            </li>
        </ul>

    </aside>    {{-- Aside over --}}

    <div class="sidebar-overlay" id="sidebarOverlay"></div> {{-- Sidebar Overlay --}}

    <div class="main">
        
        {{-- Header starts --}}
        <header class="header">

            <div class="header-left">

                <button class="sidebar-toggle" id="sidebarToggle">
                    ☰
                </button>

                <h2>Stock Management System</h2>

            </div>

            <div class="admin">
                Admin
            </div>

        </header>  
         {{-- Header over --}}

        <main class="content">
            <x-flash-message />
            @yield('content')
        </main>
    </div>  {{-- Main over --}}
   </div>   {{-- Layout over --}}

   <script>
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');

    sidebarToggle.addEventListener('click',()=>{
        sidebar.classList.add('active');
        sidebarOverlay.classList.add('active');
    });

    sidebarClose.addEventListener('click', ()=>{
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    });

    sidebarOverlay.addEventListener('click', ()=>{
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    });
   </script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
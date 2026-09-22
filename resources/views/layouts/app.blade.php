<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content = "{{ csrf_token() }}">
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

    <nav class="sidebar-nav">

        <ul>

            {{-- Dashboard --}}
           <li class="nav-item dashboard-item">

                <a
                    href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"
                >

                    <span class="nav-group-left">

                        <span class="nav-icon">⌂</span>

                        <span class="nav-text">
                            Dashboard
                        </span>

                    </span>

                </a>

            </li>


            {{-- SALES --}}
            <li class="nav-group">

                <button type="button" class="nav-group-toggle">

                    <span class="nav-group-left">
                        <span class="nav-icon">▣</span>
                        <span class="nav-text">Sales</span>
                    </span>

                    <span class="nav-arrow">⌄</span>

                </button>

                <ul class="nav-submenu">

                    <li>
                        <a href="{{ route('sales.create') }}">
                            <span class="nav-icon">＋</span>
                            <span class="nav-text">New Sale</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('sales.index') }}">
                            <span class="nav-icon">▤</span>
                            <span class="nav-text">Sales History</span>
                        </a>
                    </li>

                </ul>

            </li>


            {{-- PURCHASE --}}
            <li class="nav-group">

                <button type="button" class="nav-group-toggle">

                    <span class="nav-group-left">
                        <span class="nav-icon">🛒</span>
                        <span class="nav-text">Purchase</span>
                    </span>

                    <span class="nav-arrow">⌄</span>

                </button>

                <ul class="nav-submenu">

                    <li>
                        <a href="{{ route('purchases.index') }}">
                            <span class="nav-icon">▤</span>
                            <span class="nav-text">Purchases</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('supplier.index') }}">
                            <span class="nav-icon">♟</span>
                            <span class="nav-text">Suppliers</span>
                        </a>
                    </li>

                </ul>

            </li>


            {{-- INVENTORY --}}
            <li class="nav-group">

                <button type="button" class="nav-group-toggle">

                    <span class="nav-group-left">
                        <span class="nav-icon">▦</span>
                        <span class="nav-text">Inventory</span>
                    </span>

                    <span class="nav-arrow">⌄</span>

                </button>

                <ul class="nav-submenu">

                    <li>
                        <a href="{{ route('products.index') }}">
                            <span class="nav-icon">▣</span>
                            <span class="nav-text">Products</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('categories.index') }}">
                            <span class="nav-icon">▦</span>
                            <span class="nav-text">Categories</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('stock.index') }}">
                            <span class="nav-icon">▤</span>
                            <span class="nav-text">Stock Management</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('stockMovements') }}">
                            <span class="nav-icon">↕</span>
                            <span class="nav-text">Stock Movements</span>
                        </a>
                    </li>

                </ul>

            </li>


            {{-- REPORTS --}}
            <li class="nav-group">

                <button type="button" class="nav-group-toggle">

                    <span class="nav-group-left">
                        <span class="nav-icon">▥</span>
                        <span class="nav-text">Reports</span>
                    </span>

                    <span class="nav-arrow">⌄</span>

                </button>

                <ul class="nav-submenu">

                    <li>
                        <a href="{{ route('reports.sales') }}">
                            <span class="nav-icon">▤</span>
                            <span class="nav-text">Sales Report</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('reports.product-sales') }}">
                            <span class="nav-icon">▦</span>
                            <span class="nav-text">Product Sales</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('reports.profit') }}">
                            <span class="nav-icon">₹</span>
                            <span class="nav-text">Profit Report</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('reports.purchase') }}">
                            <span class="nav-icon">▣</span>
                            <span class="nav-text">Purchase Report</span>
                        </a>
                    </li>

                </ul>

            </li>


            {{-- SYSTEM --}}
            <li class="nav-group">

                <button type="button" class="nav-group-toggle">

                    <span class="nav-group-left">
                        <span class="nav-icon">⚙</span>
                        <span class="nav-text">System</span>
                    </span>

                    <span class="nav-arrow">⌄</span>

                </button>

                <ul class="nav-submenu">

                    <li>
                        <a href="#">
                            <span class="nav-icon">⚙</span>
                            <span class="nav-text">Settings</span>
                        </a>
                    </li>

                    <li>
                        <a href="#">
                            <span class="nav-icon">◉</span>
                            <span class="nav-text">Profile</span>
                        </a>
                    </li>

                </ul>

            </li>

        </ul>

    </nav>
    </aside>    {{-- Aside over --}}

    <div class="sidebar-overlay" id="sidebarOverlay"></div> {{-- Sidebar Overlay --}}

    <div class="main">
        
        {{-- Header starts --}}
        <header class="header">

            <div class="header-left">

                <button
                    class="desktop-sidebar-toggle"
                    id="desktopSidebarToggle"
                    type="button"
                    title="Collapse sidebar"
                >
                    ☰
                </button>


                <button class="sidebar-toggle" id="sidebarToggle">
                    ☰
                </button>

                {{-- <h2>Stock Management System</h2> --}}

            </div>

            <div class="admin">
                Welcome,
                {{ Auth::user()->name }}

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit">
                    Logout
                </button>
            </form>
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
    const navGroupToggle = document.querySelectorAll('.nav-group-toggle');


    const desktopSidebarToggle = document.getElementById('desktopSidebarToggle')

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

    desktopSidebarToggle.addEventListener('click', ()=>{
        sidebar.classList.toggle('collapsed');
    });

    navGroupToggle.forEach(toggle => {
        toggle.addEventListener('click', ()=>{
            const currentGroup = toggle.closest('.nav-group');

            document.querySelectorAll('.nav-group').forEach(group=>{
                if(group !== currentGroup){
                    group.classList.remove('open');
                }
            })

            currentGroup.classList.toggle('open');
        });
    });

   </script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
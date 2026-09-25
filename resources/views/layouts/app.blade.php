<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content = "{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/3.48.0/tabler-icons.min.css" integrity="sha512-FpfjSBRmQDu3MAAZrjj8j+RwvbASPc9f+gpd2pF/sHXPWPeTbd1OmXpC7CYt+Nnb6kD+Ed0fFH366YcAoV9LNA==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
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
                <a href="{{ route('dashboard') }}">
                    StockMan
                </a>
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

                        <span class="nav-icon">
                            <i class="ti ti-home"></i>
                        </span>

                        <span class="nav-text">
                            Dashboard
                        </span>

                    </span>

                </a>

            </li>


            {{-- SALES --}}
            <li class="nav-group {{ request()->routeIs('sales.*') ? 'open' : '' }}">

                <button type="button" class="nav-group-toggle">

                    <span class="nav-group-left">
                        <span class="nav-icon">
                            <i class="ti ti-receipt-rupee"></i>
                        </span>
                        <span class="nav-text">Sales</span>
                    </span>

                    <span class="nav-arrow">
                        <i class="ti ti-chevron-down"></i>
                    </span>

                </button>

                <ul class="nav-submenu">

                    <li>
                        <a href="{{ route('sales.create') }}"
                        class={{ request()->routeIs('sales.create') ? 'active' : '' }}>
                            <span class="nav-icon">
                                <i class="ti ti-plus"></i>
                            </span>
                            <span class="nav-text">New Sale</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('sales.index') }}"
                        class="{{ request()->routeIs('sales.index','sales.invoice','sales.return')
                        ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="ti ti-history"></i>
                            </span>
                            <span class="nav-text">Sales History</span>
                        </a>
                    </li>

                </ul>

            </li>


            {{-- PURCHASE --}}
            <li class="nav-group {{ request()->routeIs('purchase.*', 
            'purchases.*', 
            'createPurchase',
            'supplier.*') ? 'open' : '' }}">

                <button type="button" class="nav-group-toggle">

                    <span class="nav-group-left">
                        <span class="nav-icon">
                            <i class="ti ti-truck-loading"></i>
                        </span>
                        <span class="nav-text">Purchase</span>
                    </span>

                    <span class="nav-arrow">
                        <i class="ti ti-chevron-down"></i>
                    </span>

                </button>

                <ul class="nav-submenu">

                    <li>
                        <a href="{{ route('purchases.index') }}"
                        class="{{ request()->routeIs('purchases.*', 'createPurchase')
                        ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="ti ti-backpack"></i>
                            </span>
                            <span class="nav-text">Purchases</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('supplier.index') }}"
                        class="{{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="ti ti-truck-delivery"></i>
                            </span>
                            <span class="nav-text">Suppliers</span>
                        </a>
                    </li>

                </ul>

            </li>


            {{-- INVENTORY --}}
           
            <li class="nav-group {{ request()->routeIs('product.*',
            'products.*',
            'singleProduct',
            'categories.*',
            'stock.*',
            'stockMovements') ? 'open' : '' }}">

                <button type="button" class="nav-group-toggle">

                    <span class="nav-group-left">
                        <span class="nav-icon">
                            <i class="ti ti-tools"></i>
                        </span>
                        <span class="nav-text">Inventory</span>
                    </span>

                    <span class="nav-arrow">
                        <i class="ti ti-chevron-down"></i>
                    </span>

                </button>

                <ul class="nav-submenu">

                    <li>
                        <a href="{{ route('products.index') }}"
                        class="{{ request()->routeIs('products.index') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="ti ti-package"></i>
                            </span>
                            <span class="nav-text">Products</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('categories.index') }}"
                        class="{{ request()->routeIs('categories.index') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="ti ti-category"></i>
                            </span>
                            <span class="nav-text">Categories</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('stock.index') }}"
                        class="{{ request()->routeIs('stock.index') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="ti ti-augmented-reality"></i>
                            </span>
                            <span class="nav-text">Stock Management</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('stockMovements') }}"
                        class="{{ request()->routeIs('stockMovements') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="ti ti-arrows-transfer-up-down"></i>
                            </span>
                            <span class="nav-text">Stock Movements</span>
                        </a>
                    </li>

                </ul>

            </li>


            {{-- REPORTS --}}
            <li class="nav-group {{ request()->routeIs('reports.*') ? 'open' : '' }}">

                <button type="button" class="nav-group-toggle">

                    <span class="nav-group-left">
                        <span class="nav-icon">
                            <i class="ti ti-chart-bar-popular"></i>
                        </span>
                        <span class="nav-text">Reports</span>
                    </span>

                    <span class="nav-arrow">
                        <i class="ti ti-chevron-down"></i>
                    </span>

                </button>

                <ul class="nav-submenu">

                    <li>
                        <a href="{{ route('reports.sales') }}"
                        class="{{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="ti ti-report-analytics"></i>
                            </span>
                            <span class="nav-text">Sales Report</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('reports.product-sales') }}"
                        class="{{ request()->routeIs('reports.product-sales') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="ti ti-packages"></i>
                            </span>
                            <span class="nav-text">Product Sales</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('reports.profit') }}"
                        class="{{ request()->routeIs('reports.profit') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="ti ti-arrow-big-up-line"></i>
                            </span>
                            <span class="nav-text">Profit Report</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('reports.purchase') }}"
                        class="{{ request()->routeIs('reports.purchase') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="ti ti-basket-check"></i>
                            </span>
                            <span class="nav-text">Purchase Report</span>
                        </a>
                    </li>

                </ul>

            </li>


            {{-- SYSTEM --}}
            <li class="nav-group {{ request()->routeIs('settings.*', 'profile.*') ? 'open' : '' }}">
                <button type="button" class="nav-group-toggle"
                >

                    <span class="nav-group-left">
                        <span class="nav-icon">
                            <i class="ti ti-settings"></i>
                        </span>
                        <span class="nav-text">System</span>
                    </span>

                    <span class="nav-arrow">
                        <i class="ti ti-chevron-down"></i>
                    </span> 

                </button>

 
                <ul class="nav-submenu">

                    <li>
                        <a href="{{ route('settings.index') }}"

                        class="{{ request()->routeIs('settings.index') ? 'active' : '' }}"
                        >
                            <span class="nav-icon">
                                <i class="ti ti-settings-spark"></i>
                            </span>
                            <span class="nav-text">Settings</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('profile.index') }}"
                        class="{{ request()->routeIs('profile.index') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="ti ti-user-circle"></i>   
                            </span>
                            <span class="nav-text">Profile</span>
                        </a>
                    </li>

                </ul>

            </li>

            <li class="nav-group">
                   

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                        
                            <button type="submit" class="nav-group-toggle">
                                <div class="nav-group-left">
                                     <span class="nav-icon">
                                      <i class="ti ti-logout-2"></i>
                                     </span>

                                     <span class="nav-text">
                                    Logout</span>
                                </div>
                               
                                
                            </button>
                        </form>
                
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

                <div class="firm-header-info">
                    <div class="welcome-text">
                        <span>Welcome, {{ Auth::user()->firm->name }}</span>
                    </div>

                    @if(Auth::user()->firm->logo)
                        <div class="firm-header-logo">
                            <a href="{{ route('settings.index') }}">
                            <img src="{{ asset('storage/'. Auth::user()->firm->logo) }}"
                             alt="Logo">
                             </a>
                        </div>
                    @endif
                </div>
               

            {{-- <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit">
                    Logout
                </button>
            </form> --}}
            </div>
            

        </header>  
         {{-- Header over --}}

        <main class="content">
            <x-flash-message />
            @yield('content')

               <footer class="main-footer">
        {{-- <div class="footer-left">
            <strong>StockMan</strong>
        </div> --}}

        <div class="footer-right">
            Designed and Developed by <strong>Tanish Kanzariya</strong>
        </div>
    </footer>

        </main>
        
        
    </div>  {{-- Main over --}}
    
   </div>   {{-- Layout over --}}


   <!-- StockMan Preloader -->
    <div id="stockman-preloader">
        <div class="stockman-loader">

            <div class="stockman-loader-ring">
                <div class="stockman-loader-progress"></div>
            </div>

            <div class="stockman-loader-logo">
                <span>Stock</span><strong>Man</strong>
            </div>

        </div>
    </div>
<!-- End StockMan Preloader -->
    <script src="{{ asset('js/app.js') }}"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
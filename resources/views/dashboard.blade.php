@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')

<div class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1>Dashboard</h1>
            <p>Welcome to stockman</p>
        </div>
    </div>

    {{-- KPI cards --}}

    <div class="dashboard-kpi-grid">

        {{-- Today's Revenue --}}
        <div class="kpi-card kpi-revenue">
            <div class="kpi-top">
                <div class="kpi-icon">
                    ₹
                </div>
                <div class="kpi-label">
                    Today's Revenue
                </div>
            </div>

            <div class="kpi-value">
                ₹{{ number_format($todayRevenue,2) }}
            </div>

            <div class="kpi-footer">
                Net revenue generated today
            </div>
        </div>

        {{-- Todays return --}}
        <div class="kpi-card kpi-returns">

            <div class="kpi-top">
                <div class="kpi-icon">
                    ↩
                </div>

                <span class="kpi-label">
                    Today's Returns
                </span>
            </div>

            <div class="kpi-value">
                ₹{{ number_format($todayReturn, 2) }}
            </div>

            <div class="kpi-footer">
                Refunds processed today
            </div>

        </div>

        {{-- Todays Profit --}}
        <div class="kpi-card kpi-profit">
            <div class="kpi-top">
                <div class="kpi-icon">
                    ↗
                </div>

                <span class="kpi-label">
                    Today's Profit
                </span>
            </div>

            <div class="kpi-value">
                ₹{{ number_format($todayProfit,2) }}
            </div>

            <div class="kpi-footer">
                Gross profit generated today
            </div>
        </div>

        {{-- Total Products --}}
        <div class="kpi-card kpi-products">
            <div class="kpi-top">
                <div class="kpi-icon">
                    ▣
                </div>
                <span class="kpi-label">
                    Total Products
                </span>
            </div>

            <div class="kpi-value">
                {{ $totalProducts }}
            </div>

            <div class="kpi-footer">
                Products in inventory
            </div>
        </div>

        {{-- Low stock --}}
        <div class="kpi-card kpi-low-stock">

            <div class="kpi-top">
                <div class="kpi-icon">
                    △
                </div>

                <span class="kpi-label">
                    Low Stock
                </span>
            </div>

            <div class="kpi-value">
                <h2>{{ $lowStockCount }}</h2>
            </div>

            <div class="kpi-footer">
                Products need restocking
            </div>
        </div>

        {{-- Out of stock --}}
        <div class="kpi-card kpi-out-stock">
            <div class="kpi-top">
                <div class="kpi-icon">
                    ×
                </div>
                <span class="kpi-label">
                    Out of Stock
                </span>
            </div>

            <div class="kpi-value">
                {{ $outOfStockCount }}
            </div>

            <div class="kpi-footer">
                Products currently unavailable
            </div>
        </div>
    </div>



    {{-- Revenue Chart --}}

    <div class="dashboard-chart-card">

        <div class="chart-header">
            <div>
                <h2>Revenue Overview</h2>
                <p>Net sales revenue for the last 7 days</p>
            </div>

            <span class="chart-period">
                Last 7 Days
            </span>
        </div>

        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>

    </div>

    {{-- Sales-purchases chart --}}

    <div class="dashboard-chart-card">

        <div class="chart-header">

            <div>
                <h2>Sales vs Purchases</h2>

                <p>
                    Compare net sales revenue with purchase spending
                    for the last 7 days
                </p>
            </div>

            <span class="chart-period">
                Last 7 Days
            </span>

        </div>

        <div class="chart-container">

            <canvas id="salesPurchaseChart"></canvas>

        </div>

    </div>

    <div class="dashboard-two-column">
    {{-- Payment method pie chart --}}
    <div class="dashboard-chart-card">
        <div class="chart-header">
            <div>
                <h2>Payment Methods</h2>
                <p>Sales distribution by payment method</p>
            </div>

            <span class="chart-period">
                All completed sales
            </span>
        </div>

        <div class="payment-chart-container">
            <canvas id="paymentMethodChart"></canvas>
        </div>
    </div>

    {{-- Top selling products table --}}
    <div class="dashboard-table-card">
        <div class="table-card-header">
            <div>
                <h2>Top Selling Products</h2>

                <p>Best performing products by units sold</p>
            </div>

            <span class="chart-period">
                Top 5
            </span>
        </div>

        <div class="table-responsive">
            <table class="table dashboard-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Qty Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($productSales  as $index => $product)

                    <tr>
                        <td>
                            {{ $index + 1 }}
                        </td>

                        <td>
                            <strong>
                                {{ $product->name }}
                            </strong>
                        </td>

                        <td>
                            {{ $product->sku }}
                        </td>

                        <td>
                            {{ $product->quantity_sold }}
                        </td>

                        <td>
                            ₹ {{ number_format($product->revenue,2) }}
                        </td>
                    </tr>
                        
                    @empty
                        <tr>
                            <td class="text-muted text-center my-3">
                                No Products Found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>


    {{-- Recent Sales --}}
    <div class="dashboard-table-card">

        <div class="table-card-header">
            <div>
                <h2>Recent Sales</h2>
                <p>Latest completed sales transactions</p>
            </div>

            <a href="{{ route('sales.index') }}" class="view-all-link">
                View All
            </a>
        </div>

        <div class="table-responsive">
            <table class="table dashboard-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @php 
                        $i=0
                    @endphp
                    @forelse ($recentSales as $sale)
                        @php $i++ @endphp
                        <tr>
                            <td>{{ $i }}</td>
                            <td>
                                <strong>
                                    <a href="{{ route('sales.invoice',$sale->id) }}" class="show_invoice">
                                        {{ $sale->invoice_number }}
                                    </a>
                                </strong>    
                            </td>

                            <td>
                                {{ $sale->customer_name ?? 'walk-in customer'  }} 
                            </td>

                            <td>
                                 ₹{{ number_format($sale->total_amount,2) }}
                            </td>

                            <td>
                                {{ ucfirst($sale->payment_method) }}
                            </td>

                            <td>
                                <span class="status-badge status-completed">
                                    Completed
                                </span>
                            </td>

                            <td>
                                {{ $sale->created_at->format('d M Y, h: i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td clas="text-center text-muted my-4" colspan="6">
                                No Sales Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Purchases --}}
    <div class="dashboard-table-card">
        <div class="table-card-header">
            <div>
                <h2>
                    Recent Purchases
                </h2>
                <p>Latest completed purchases from suppliers</p>
            </div>

            <a href="{{ route('purchases.index') }}" class="view-all-link">
                View All
            </a>
        </div>

        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Supplier</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i=0
                    @endphp
                    @forelse ($recentPurchases as $purchase)
                        @php $i++  @endphp

                        <tr>
                            <td>{{ $i }}</td>
                            <td>
                                <strong>
                                    <a href="{{ route('purchases.show',$purchase->id) }}" class="show_invoice">
                                        {{ $purchase->invoice_number }}
                                    </a>
                                </strong>   
                            </td>
                            <td>
                                {{ $purchase->supplier->name ?? 'Unknow-supplier' }}
                            </td>

                            <td>
                                ₹{{ number_format($purchase->total_amount,2) }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse(
                                    $purchase->purchase_date
                                )->format('d M Y') }}
                            </td>

                            <td>
                                <span class="badge-status status-completed">
                                    {{ $purchase->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted my-4">
                                No Purchases Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    const revenueLabels = @json($revenueLabels);

    const revenueValues = @json($revenueValues);


    const revenueChart = document
        .getElementById('revenueChart');


    new Chart(revenueChart, {

        type: 'line',

        data: {

            labels: revenueLabels,

            datasets: [{
                label: 'Net Revenue',

                data: revenueValues,

                tension: 0.4,

                fill: 'origin',

                pointRadius: 3,

                pointHoverRadius: 5,

                borderWidth: 2
            }]
        },


        options: {

            responsive: true,

            maintainAspectRatio: false,

            interaction: {
                intersect: false,
                mode: 'index'
            },


            plugins: {

                legend: {
                    display: false
                },

                tooltip: {

                    callbacks: {

                        label: function(context) {

                            return ' ₹' +
                                Number(
                                    context.raw
                                ).toLocaleString(
                                    'en-IN',
                                    {
                                        minimumFractionDigits: 2
                                    }
                                );
                        }
                    }
                }
            },


            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        callback: function(value) {

                            return '₹' +
                                Number(value)
                                .toLocaleString('en-IN');
                        }
                    }
                }

            }

        }

    });
    // lines chart over

    // Sales-purchase chart

    const salesLabels = @json($revenueLabels);
    const salesValues = @json($salesValues);
    const purchaseValues = @json($purchaseValues);

    const salesPurchaseChart = document.getElementById('salesPurchaseChart');

    new Chart(salesPurchaseChart, {
        type: 'bar',
        data:{
            labels:salesLabels,
            datasets:[
                {
                    label: 'Sales',
                    data: salesValues,

                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 38
                },
                {
                    label: 'Purchases',
                    data: purchaseValues,

                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThikness: 38
                }
            ]
        },
        options:{
            responsive:true,
            maintainAspectRatio: false,
            interaction:{
                    intersect:false,
                    mode:'index'
            },
            plugins:{
                tooltip:{
                    callbacks:{
                        label: function(context){
                            return '₹'+ Number(context.raw).toLocaleString('en-IN',{
                                minimumFractionDigits:2
                            });
                        }
                    }
                },

                legend:{
                    display:true,
                    position:'top',
                    align:'center',
                    labels:{
                        usePointStyle: true,
                        PointStyle: 'rectRounded',
                        padding:18,
                        boxWidth:10
                    }
                }
            },
            

            scales:{
                y:{
                    beginAtZero:true,

                    ticks:{
                        callback:
                            function(value){
                                return '₹'+Number(value).toLocaleString('en-IN')
                            }
                        
                    }
                }
            }
        }
    });
    // Bar chart over

    // pie chart
    const paymentLabels = @json($paymentLabels);
    const paymentValues = @json($paymentValues);

    const paymentMethodChart = document.getElementById('paymentMethodChart');

    new Chart(paymentMethodChart,{
        type: 'doughnut',
        data:{
            labels:paymentLabels,

            datasets:[
                {
                    data: paymentValues,
                    
                    borderWidth: 2
                }
            ]
        },
        options:{
            responsive: true,
            maintainAspectRatio:false,

            plugins:{
                legend:{
                    postion:'bottom'
                },
                tooltip:{
                    callbacks:{
                        label: function(context){
                           const total = paymentValues.reduce(
                            (sum, value) => sum + Number(value),
                            0
                            );

                            const percentage = ((context.raw/total)*100).toFixed(2);

                            return context.label + ': ₹'+Number(context.raw).toLocaleString('en-IN',{
                                minimumFractionDigits:2
                            })+'(' + percentage + '%)';
                        }
                    }
                }
            }
        }
    })
</script>
@endsection
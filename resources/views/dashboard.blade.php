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

    <div class="dashboard-stats">

        {{-- Today's Revenue --}}
        <div class="dashboard-card">
            <div class="card-icon">
                ₹
            </div>

            <div class="card-content">
                <span>Today's Revenue</span>

                <h2>
                    ₹{{ number_format($todayRevenue,2) }}
                </h2>
            </div>
        </div>

        {{-- Todays Profit --}}
        <div class="dashboard-card">
            <div class="card-icon">
                ↗
            </div>

            <div class="card-content">
                <span>Today's Profit</span>

                ₹{{ number_format($todayProfit,2) }}
            </div>
        </div>

        {{-- Total Products --}}
        <div class="dashboard-card">
            <div class="card-icon">
                📦
            </div>

            <div class="card-content">
                <span>Total Products</span>

                {{ $totalProducts }}
            </div>
        </div>

        {{-- Low stock --}}
        <div class="dashboard-card">
            <div class="card-icon">
                 ⚠
            </div>

            <div class="card-content">
                <span>Low Stock</span>

                <h2>{{ $lowStockCount }}</h2>
            </div>
        </div>

        {{-- Inventory alert --}}
        <div class="inventory-alerts">
            <div class="alert-card">
                <span>Low Stock Products</span>
                <strong>{{ $lowStockCount }}</strong>
            </div>

            <div class="alert-card">
                <span>Out of Stock Products</span>
                <strong>{{ $outOfStockCount }}</strong>
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

    <div class="dashboard-table-card">
        <div class="table-card-header">
            <div>
                <h2>Top Selling Products</h2>

                <p>Best performing products by units sold.</p>
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

                fill: true,

                pointRadius: 4,

                pointHoverRadius: 6
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
                    data: salesValues
                },
                {
                    label: 'Purchases',
                    data: purchaseValues
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
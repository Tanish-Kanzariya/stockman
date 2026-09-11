<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Sale_item;
use App\Models\Sale_return_item;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashBoardController extends Controller
{
    public function index(){

    //Revenue chart
    $lastSevenDays = collect();

    for ($i = 6; $i >= 0; $i--) {

        $lastSevenDays->push(
            Carbon::today()->subDays($i)
        );
    }

    $dailySales = Sale_item::query()
        ->join(
            'sales',
            'sale_items.sale_id',
            '=',
            'sales.id'
        )
        ->where('sales.status', 'completed')
        ->whereDate(
            'sales.created_at',
            '>=',
            Carbon::today()->subDays(6)
        )
        ->selectRaw(
            'DATE(sales.created_at) as sale_date'
        )
        ->selectRaw(
            'SUM(sale_items.subtotal) as gross_revenue'
        )
        ->groupBy('sale_date')
        ->orderBy('sale_date')
        ->get();


    

    $dailyReturns = Sale_return_item::query()
        ->join(
            'sale_returns',
            'sale_return_items.sale_return_id',
            '=',
            'sale_returns.id'
        )
        ->join(
            'sales',
            'sale_returns.sale_id',
            '=',
            'sales.id'
        )
        ->where('sale_returns.status', 'completed')
        ->where('sales.status', 'completed')
        ->whereDate(
            'sales.created_at',
            '>=',
            Carbon::today()->subDays(6)
        )
        ->selectRaw(
            'DATE(sales.created_at) as sale_date'
        )
        ->selectRaw(
            'SUM(sale_return_items.subtotal) as returned_amount'
        )
        ->groupBy('sale_date')
        ->orderBy('sale_date')
        ->get();


    $salesByDate = $dailySales->keyBy('sale_date');

    $returnsByDate = $dailyReturns->keyBy('sale_date');

    $revenueLabels = [];

    $revenueValues = [];


    foreach ($lastSevenDays as $date) {

        $dateKey = $date->format('Y-m-d');


        // Gross sales for this date

        $grossRevenue = isset($salesByDate[$dateKey])
            ? (float) $salesByDate[$dateKey]->gross_revenue
            : 0;


        // Returns for this date

        $returnedAmount = isset($returnsByDate[$dateKey])
            ? (float) $returnsByDate[$dateKey]->returned_amount
            : 0;


        // Net revenue

        $netRevenue = $grossRevenue - $returnedAmount;


        // Chart label

        $revenueLabels[] = $date->format('d M');


        // Chart value

        $revenueValues[] = $netRevenue;
    }

    //Purchase V/S Sale chart

    $dailyPurchases = Purchase::query()
    ->where('status','completed')
    ->whereDate('created_at', '>=', Carbon::today()->subDays(6))
    ->selectRaw('DATE(purchase_date) as purchase_date')
    ->selectRaw('SUM(total_amount) as purchase_amount')
    ->groupBy('purchase_date')
    ->orderBy('purchase_date')
    ->get();

    $daily_sales = Sale_item::query()
    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
    ->where('sales.status', 'completed')
    ->whereDate('sales.created_at', '>=', Carbon::today()->subDays(6))
    ->selectRaw('DATE(sales.created_at) as sale_date')
    ->selectRaw('SUM(sale_items.subtotal) as gross_revenue')
    ->groupBy('sale_date')
    ->orderBy('sale_date')
    ->get();

    $daily_returns = Sale_return_item::query()
    ->join('sale_returns', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')
    ->join('sales', 'sale_returns.sale_id', '=', 'sales.id')
    ->where('sales.status', 'completed')
    ->where('sale_returns.status','completed')
    ->whereDate('sales.created_at', '>=', Carbon::today()->subDays(6))
    ->selectRaw('DATE(sales.created_at) as sale_date')
    ->selectRaw('SUM(sale_return_items.subtotal) as returned_amount')
    ->groupBy('sale_date')
    ->orderBy('sale_date')
    ->get();

    $purchaseByDate = $dailyPurchases->keyBy('purchase_date');

    $sales_By_Date = $daily_sales->keyBy('sale_date');
    $return_By_Date = $daily_returns->keyBy('sale_date');


    $salesValues = [];
    $purchaseValues = [];

    foreach($lastSevenDays as $date){
        $dateKey = $date->format('Y-m-d');

        $grossRevenue = isset($sales_By_Date[$dateKey]) ?
        (float) $sales_By_Date[$dateKey]->gross_revenue : 0;

        $returnedAmount = isset($return_By_Date[$dateKey]) ?
        (float) $return_By_Date[$dateKey]->returned_amount : 0;

        $netRevenue = $grossRevenue - $returnedAmount;

        $purchaseAmount = isset($purchaseByDate[$dateKey]) ?
        (float)$purchaseByDate[$dateKey]->purchase_amount : 0;

        $purchaseValues[] = $purchaseAmount;

        $salesValues[] = $netRevenue;
    }

    //Payment method chart

    $paymentMethods = Sale::query()
    ->where('status', 'completed')
    ->select('payment_method')
    ->selectRaw('SUM(total_amount) as total_amount')
    ->groupBy('payment_method')
    ->orderByDesc('payment_method')
    ->get();

    $paymentLabels =[];
    $paymentValues = [];

    foreach($paymentMethods as $payment){
        $paymentLabels[] =$payment->payment_method;

        $paymentValues[] = $payment->total_amount;
    }

    //Top selling products calculation

    $returnsQuery = Sale_return_item::query()
    ->join('sale_returns', 'sale_return_items.sale_return_id', '=','sale_returns.id')
    ->where('sale_returns.status','completed')
    ->select('sale_return_items.sale_item_id')
    ->selectRaw('SUM(sale_return_items.quantity) as returned_quantity')
    ->selectRaw('SUM(sale_return_items.subtotal) as returned_revenue')
    ->groupBy('sale_return_items.sale_item_id');    


    $productSales = Sale_item::query()
    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')

    ->join('products', 'sale_items.product_id', '=', 'products.id')

    ->leftJoinSub(
        $returnsQuery,
        'returns',
        function($join){
            $join->on(
                'sale_items.id',
                '=',
                'returns.sale_item_id'
            );
        }
    )
    ->where('sales.status', 'completed')
    ->select('products.id', 'products.name', 'products.sku')
    ->selectRaw('SUM(sale_items.quantity - COALESCE(returns.returned_quantity,0))
    as quantity_sold')
    ->selectRaw('SUM(sale_items.subtotal - COALESCE(returns.returned_revenue,0))
    as revenue')
    ->groupBy(
        'products.id',
        'products.name',
        'products.sku'
    )
    ->orderByDesc('quantity_sold')
    ->limit(5)
    ->get();
        //Todays sale

        $todaySales = Sale::where('status','completed')
        ->whereDate('created_at', today());

        //Todays gross sale

        $todayGrossSale = Sale_item::query()
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->where('sales.status', 'completed')
        ->whereDate('sales.created_at', today())
        ->sum('sale_items.subtotal');

        //Todays return
        $todayReturn = Sale_return_item::query()
        ->join('sale_returns',
        'sale_return_items.sale_return_id', '=', 'sale_returns.id')
        ->join('sales', 'sale_returns.sale_id', '=', 'sales.id')
        ->where('sales.status', 'completed')
        ->whereDate('sales.created_at', today())
        ->sum('sale_return_items.subtotal');

        $todayRevenue = $todayGrossSale - $todayReturn;

        //Todays original costs

        $todayCost = Sale_item::query()
        ->join(
            'sales',
            'sale_items.sale_id',
            '=',
            'sales.id'
        )
        ->where('sales.status', 'completed')
        ->whereDate('sales.created_at', today())
        ->selectRaw('SUM(sale_items.quantity * sale_items.cost_price) as total_cost')
        ->value('total_cost') ?? 0;
        
        //Todays returned costs

        $todayReturnedCost = Sale_return_item::query()
        ->join('sale_returns',
        'sale_return_items.sale_return_id',
        '=',
        'sale_returns.id')

        ->join('sale_items','sale_return_items.sale_item_id', '=', 'sale_items.id')

        ->join('sales',
        'sale_items.sale_id',
        '=',
        'sales.id')

        ->where('sale_returns.status','completed')
        ->where('sales.status','completed')
        ->whereDate('sales.created_at', today())
        ->selectRaw('SUM(sale_return_items.quantity * sale_items.cost_price)
        as returned_cost')
        ->value('returned_cost') ?? 0;
        
        //Total net cost for today

        $todayNetCost = $todayCost-$todayReturnedCost;

        $todayProfit = $todayRevenue - $todayNetCost;

        //Total products

        $totalProducts = Product::count();

        //Low stock products

        $lowStockCount = Product::whereColumn(
            'stock_quantity', '<=', 'minimum_stock'
        )->where('stock_quantity', '>', 0)
        ->count();

        //Out of stock products

        $outOfStockCount = Product::where('stock_quantity',0)->count();

        return view('dashboard', compact('todayRevenue',
                                        'todayReturn',
                                        'todayProfit',
                                        'totalProducts',
                                        'lowStockCount',
                                        'outOfStockCount',
                                        'revenueLabels',
                                        'revenueValues',
                                        'purchaseValues',
                                        'salesValues',
                                        'paymentLabels',
                                        'paymentValues',
                                        'productSales'
        ));
    }
}

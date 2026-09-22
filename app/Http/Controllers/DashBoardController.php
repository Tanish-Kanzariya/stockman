<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Sale_item;
use App\Models\Sale_return_item;
// use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashBoardController extends Controller
{
    public function index(){

    $firmId = Auth::user()->firm_id;

    //Revenue chart
    $lastSevenDays = collect();

    for ($i = 6; $i >= 0; $i--) {

        $lastSevenDays->push(
            Carbon::today()->subDays($i)
        );
    }

    $dailySales = Sale::query()
    ->where('firm_id', $firmId)
    ->where('status', 'completed')
    ->whereDate('created_at', '>=', Carbon::today()->subDays(6))
    ->selectRaw('DATE(created_at) as sale_date')
    ->selectRaw('SUM(subtotal - discount) as net_revenue')
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
    ->where('sales.firm_id', $firmId)
    ->where('sale_returns.status', 'completed')
    ->where('sales.status', 'completed')
    ->whereDate(
        'sales.created_at',
        '>=',
        Carbon::today()->subDays(6)
    )
    ->selectRaw('DATE(sales.created_at) as sale_date')
    ->selectRaw('SUM(sale_return_items.subtotal) as returned_amount')
    ->groupBy('sale_date')
    ->orderBy('sale_date')
    ->get();

    $salesByDate = $dailySales->keyBy('sale_date');

    $returnsByDate = $dailyReturns->keyBy('sale_date');

    $revenueLabels = [];

    $revenueValues = [];


    foreach ($lastSevenDays as $date) {

        $dateKey = $date->format('Y-m-d');

        $revenueLabels[] = $date->format('d M');

        $netRevenue = $dailySales
        ->where('sale_date', $date->format('Y-m-d'))
        ->sum('net_revenue');

        $returnedAmount = $dailyReturns
        ->where('sale_date', $date->format('Y-m-d'))
        ->sum('returned_amount');

        $netRevenue -= $returnedAmount;

        $revenueValues[] = $netRevenue;
       
    }

    //Purchase V/S Sale chart

    $dailyPurchases = Purchase::query()
    ->where('firm_id', $firmId)
    ->where('status','completed')
    ->whereDate('created_at', '>=', Carbon::today()->subDays(6))
    ->selectRaw('DATE(purchase_date) as purchase_date')
    ->selectRaw('SUM(total_amount) as purchase_amount')
    ->groupBy('purchase_date')
    ->orderBy('purchase_date')
    ->get();

    $daily_sales = Sale::query()
    ->where('firm_id', $firmId)
    ->where('status', 'completed')
    ->whereDate(
        'created_at',
        '>=',
        Carbon::today()->subDays(6)
    )
    ->selectRaw('DATE(created_at) as sale_date')
    ->selectRaw('SUM(subtotal - discount) as net_revenue')
    ->groupBy('sale_date')
    ->orderBy('sale_date')
    ->get();


    $daily_returns = Sale_return_item::query()
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
    ->where('sales.firm_id', $firmId)
    ->where('sale_returns.status', 'completed')
    ->where('sales.status', 'completed')
    ->whereDate(
        'sales.created_at',
        '>=',
        Carbon::today()->subDays(6)
    )
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

        // $revenueLabels[] = $date->format('d M');

        $salesAmount = $daily_sales
        ->where('sale_date', $date->format('Y-m-d'))
        ->sum('net_revenue');

        $returnedAmount = $daily_returns
        ->where('sale_date', $date->format('Y-m-d'))
        ->sum('returned_amount');

        $salesAmount -= $returnedAmount;


        $purchaseAmount = isset($purchaseByDate[$dateKey]) ?
        (float)$purchaseByDate[$dateKey]->purchase_amount : 0;

        $purchaseValues[] = (float) $purchaseAmount;

        $salesValues[] = (float) $salesAmount;
    }

    //Payment method chart

    $paymentMethods = Sale::query()
    ->where('firm_id', $firmId)
    ->where('status', 'completed')
    ->select('payment_method')
    ->selectRaw('SUM(total_amount) as total_amount')
    ->groupBy('payment_method')
    ->orderByDesc('total_amount')
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
    ->join('sales', 'sale_returns.sale_id', '=', 'sales.id')
    ->where('sales.firm_id', $firmId)
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
    ->where('sales.firm_id', $firmId)
    ->where('products.firm_id', $firmId)
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

    //Recent Sales

    $recentSales = Sale::query()
    ->where('firm_id', $firmId)
    ->where('status','completed')
    ->orderByDesc('created_at')
    ->limit(5)
    ->get();

    $recentPurchases = Purchase::query()->with('supplier')
    ->where('firm_id', $firmId)
    ->where('status','completed')
    ->orderByDesc('purchase_date')
    ->limit(5)
    ->get();

    //Todays sale

    $todaySales = Sale::where('status','completed')
    ->where('firm_id', $firmId)
    ->whereDate('created_at', today());

    // Today's net revenue before tax

    $todayRevenue = Sale::query()
        ->where('firm_id', $firmId)
        ->where('status', 'completed')
        ->whereDate('created_at', today())
        ->selectRaw('SUM(subtotal - discount) as net_revenue')
        ->value('net_revenue') ?? 0;

    // Today's returns

    $todayReturn = Sale_return_item::query()
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
        ->where('sales.firm_id', $firmId)
        ->where('sale_returns.status', 'completed')
        ->where('sales.status', 'completed')
        ->whereDate('sales.created_at', today())
        ->sum('sale_return_items.subtotal');

    // Net revenue after returns

    $todayRevenue -= $todayReturn;

        //Todays original costs

    $todayCost = Sale_item::query()
    ->join(
        'sales',
        'sale_items.sale_id',
        '=',
        'sales.id'
    )
    ->where('sales.firm_id', $firmId)
    ->where('sales.status', 'completed')
    ->whereDate('sales.created_at', today())
    ->selectRaw(
        'SUM(sale_items.quantity * sale_items.cost_price) as total_cost'
    )
    ->value('total_cost') ?? 0;


    $todayReturnedCost = Sale_return_item::query()
    ->join(
        'sale_returns',
        'sale_return_items.sale_return_id',
        '=',
        'sale_returns.id'
    )
    ->join(
        'sale_items',
        'sale_return_items.sale_item_id',
        '=',
        'sale_items.id'
    )
    ->join(
        'sales',
        'sale_items.sale_id',
        '=',
        'sales.id'
    )
    ->where('sales.firm_id', $firmId)
    ->where('sale_returns.status', 'completed')
    ->where('sales.status', 'completed')
    ->whereDate('sales.created_at', today())
    ->selectRaw(
        'SUM(
            sale_return_items.quantity * sale_items.cost_price
        ) as returned_cost'
    )
    ->value('returned_cost') ?? 0;


    $todayNetCost = $todayCost - $todayReturnedCost;

    $todayProfit = $todayRevenue - $todayNetCost;

        //Total products

        $totalProducts = Product::where('firm_id', $firmId)->count();

        //Low stock products

        $lowStockCount = Product::where('firm_id', $firmId)->whereColumn(
            'stock_quantity', '<=', 'minimum_stock'
        )->where('stock_quantity', '>', 0)
        ->count();

        //Out of stock products

        $outOfStockCount = Product::where('firm_id', $firmId)->where('stock_quantity',0)->count();

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
                                        'productSales',
                                        'recentSales',
                                        'recentPurchases'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Sale_item;
use App\Models\Sale_return_item;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //Sales report
    public function sales(Request $request){

        $query = Sale::query()->where('status', 'completed');

        //From date filter
        if($request->filled('from_date')){
            $query->whereDate('created_at', '>=' , $request->from_date);
        }
        
        //To date filter
        if($request->filled('to_date')){
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        //Summary
        $totalTransactions = $query->count();

        $totalRevenue = $query->sum('total_amount');

        $totalDiscount = $query->sum('discount');   
        
        $totalTax = $query->sum('tax');

        //Payment-wise sales

        $paymentSales = (clone $query)
            ->selectRaw('payment_method, COUNT(*) as transactions, SUM(total_amount) as amount')
            ->groupBy('payment_method')
            ->orderByDesc('amount')
            ->get();

        $sales = $query->latest()->paginate(10)->withQueryString();

        return view('reports.sales', compact('sales',
                                        'totalTransactions',
                                        'totalDiscount',
                                        'totalTax',
                                        'totalRevenue',
                                        'paymentSales'));
    }

    //Product-wise report

    public function productSales(Request $request){

        $basequery = Sale_item::query()
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->join('products', 'sale_items.product_id', '=', 'products.id')
        ->where('sales.status', 'completed');

        //Date from filter

        if($request->filled('from_date')){
            $basequery->whereDate('sales.created_at', '>=', $request->from_date);
        }

        //Date to filter
        if($request->filled('to_date')){
            $basequery->whereDate('sales.created_at', '<=', $request->to_date);
        }

        //Summary

         $totalProducts = (clone $basequery)->distinct('sale_items.product_id')
        ->count('sale_items.product_id');

        $totalQuantity = (clone $basequery)->sum('sale_items.quantity');

        $totalRevenue = (clone $basequery)->sum('sale_items.subtotal');


        $productSales = $basequery->select(
            'products.id',
            'products.name',
            'products.sku'
        )
        ->selectRaw('SUM(sale_items.quantity) as quantity_sold')
        ->selectRaw('SUM(sale_items.subtotal) as total_revenue')
        ->selectRaw('AVG(sale_items.price) as average_price')
        ->groupBy(
            'products.id',
            'products.name',
            'products.sku'
        )
        ->orderByDesc('total_revenue')
        ->paginate(10)
        ->withQueryString();

       
        return view('reports.product-sales', compact('productSales',
                                                    'totalProducts',
                                                    'totalQuantity',
                                                    'totalRevenue'));
    }


    //Purchase report

    public function purchase(Request $request){

        $query = Purchase::query()->where('status', 'completed');

        //Date from filter
        if($request->filled('from_date')){
            $query->whereDate('purchase_date', '>=', $request->from_date);
        }

        //Date To filter
        if($request->filled('to_date')){
            $query->whereDate('purchase_date', '<=', $request->to_date);
        }

        //Summary

        $totalPurchases = (clone $query)->count();

        $totalAmount = (clone $query)->sum('total_amount');

        $totalSuppliers = (clone $query)->distinct('supplier_id')->count('supplier_id');

        $purchases = $query->with('supplier')
                    ->latest('purchase_date')
                    ->paginate(10)
                    ->withQueryString();

        return view('reports.purchase', compact('purchases',
                                                'totalPurchases',
                                                'totalSuppliers',
                                                'totalAmount'
        ));
    }

    //Profit report
    public function profit(Request $request)
    {
        $returnsQuery = Sale_return_item::query()
            ->join(
                'sale_returns',
                'sale_return_items.sale_return_id',
                '=',
                'sale_returns.id'
            )
            ->where('sale_returns.status', 'completed')
            ->select('sale_return_items.sale_item_id')
            ->selectRaw(
                'SUM(sale_return_items.quantity) as returned_quantity'
            )
            ->selectRaw(
                'SUM(sale_return_items.subtotal) as returned_revenue'
            )
            ->groupBy('sale_return_items.sale_item_id');


        $query = Sale_item::query()
            ->join(
                'sales',
                'sale_items.sale_id',
                '=',
                'sales.id'
            )
            ->join(
                'products',
                'sale_items.product_id',
                '=',
                'products.id'
            )
            ->leftJoinSub(
                $returnsQuery,
                'returns',
                function ($join) {
                    $join->on(
                        'sale_items.id',
                        '=',
                        'returns.sale_item_id'
                    );
                }
            )
            ->where('sales.status', 'completed');

        if ($request->filled('from_date')) {

            $query->whereDate(
                'sales.created_at',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {

            $query->whereDate(
                'sales.created_at',
                '<=',
                $request->to_date
            );
        }


        $totalRevenue = (clone $query)
            ->selectRaw(
                'SUM(
                    sale_items.subtotal
                    - COALESCE(returns.returned_revenue, 0)
                ) as net_revenue'
            )
            ->value('net_revenue') ?? 0;


        $totalCost = (clone $query)
            ->selectRaw(
                'SUM(
                    (
                        sale_items.quantity
                        - COALESCE(returns.returned_quantity, 0)
                    )
                    * sale_items.cost_price
                ) as net_cost'
            )
            ->value('net_cost') ?? 0;

        $grossProfit = $totalRevenue - $totalCost;

        $profitMargin = $totalRevenue > 0
            ? ($grossProfit / $totalRevenue) * 100
            : 0;


        $totalReturned = (clone $query)
            ->selectRaw(
                'SUM(
                    COALESCE(returns.returned_revenue, 0)
                ) as returned_revenue'
            )
            ->value('returned_revenue') ?? 0;

        $productProfits = (clone $query)
            ->select(
                'products.id',
                'products.name',
                'products.sku'
            )

            ->selectRaw(
                'SUM(
                    sale_items.quantity
                    - COALESCE(returns.returned_quantity, 0)
                ) as quantity_sold'
            )

            ->selectRaw(
                'SUM(
                    sale_items.subtotal
                    - COALESCE(returns.returned_revenue, 0)
                ) as revenue'
            )

            ->selectRaw(
                'SUM(
                    (
                        sale_items.quantity
                        - COALESCE(returns.returned_quantity, 0)
                    )
                    * sale_items.cost_price
                ) as cost'
            )

            ->selectRaw(
                'SUM(
                    sale_items.subtotal
                    - COALESCE(returns.returned_revenue, 0)
                )
                -
                SUM(
                    (
                        sale_items.quantity
                        - COALESCE(returns.returned_quantity, 0)
                    )
                    * sale_items.cost_price
                )
                as profit'
            )

            ->groupBy(
                'products.id',
                'products.name',
                'products.sku'
            )

            ->orderByDesc('profit')

            ->paginate(15)

            ->withQueryString();


        return view('reports.profit', compact(
            'productProfits',
            'totalRevenue',
            'totalCost',
            'grossProfit',
            'profitMargin',
            'totalReturned'
        ));
}
}

<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Sale_item;
use App\Models\Sale_return_item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Sale_return;

class ReportController extends Controller
{
    //Sales report
    public function sales(Request $request){

    $firmId = Auth::user()->firm_id;

        $query = Sale::query()
            ->where('firm_id', $firmId)
            ->where('status', 'completed');

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }


        // Total original sales
        $totalSales = (clone $query)->sum('total_amount');


        // Total returned amount
        $returnedAmount = Sale_return::where('status', 'completed')
            ->whereIn('sale_id', (clone $query)->select('id'))
            ->sum('refund_amount');


        // Net revenue after returns
        $totalCollected = $totalSales - $returnedAmount;


        // Other totals
        $totalTransactions = (clone $query)->count();

        $totalDiscount = (clone $query)->sum('discount');

        $totalTax = (clone $query)->sum('tax');


        // Payment summary
        $paymentSales = (clone $query)
            ->selectRaw('payment_method, COUNT(*) as transactions, SUM(total_amount) as amount')
            ->groupBy('payment_method')
            ->orderByDesc('amount')
            ->get();


        // Sales list
        $sales = (clone $query)
            ->latest()
            ->paginate(10)
            ->withQueryString();

            return view('reports.sales', compact(
    'totalTransactions',
    'totalCollected',
    'totalDiscount',
    'totalTax',
    'paymentSales',
    'sales',
    'returnedAmount'
));
    }

    //Product-wise report

    public function productSales(Request $request)
    {
        $firmId = Auth::user()->firm_id;

        // Returned quantity/revenue for each sale item
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


        $basequery = Sale_item::query()
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
            ->where('sales.status', 'completed')
            ->where('sales.firm_id', $firmId)
            ->where('products.firm_id', $firmId);


        // Date from filter
        if ($request->filled('from_date')) {
            $basequery->whereDate(
                'sales.created_at',
                '>=',
                $request->from_date
            );
    }


    // Date to filter
    if ($request->filled('to_date')) {
        $basequery->whereDate(
            'sales.created_at',
            '<=',
            $request->to_date
        );
    }


    // Total products
    $totalProducts = (clone $basequery)
        ->distinct('sale_items.product_id')
        ->count('sale_items.product_id');


    // Total quantity after returns
    $totalQuantity = (clone $basequery)
        ->selectRaw(
            'SUM(
                sale_items.quantity
                - COALESCE(returns.returned_quantity, 0)
            ) as quantity'
        )
        ->value('quantity') ?? 0;


    // Net revenue after discount and returns
    $totalRevenue = (clone $basequery)
        ->selectRaw(
            'SUM(
                sale_items.subtotal

                - CASE
                    WHEN sales.subtotal > 0
                    THEN
                        (sale_items.subtotal / sales.subtotal)
                        * sales.discount
                    ELSE 0
                  END

                - COALESCE(returns.returned_revenue, 0)
            ) as net_revenue'
        )
        ->value('net_revenue') ?? 0;


    // Tax
    $totalTax = (clone $basequery)
        ->selectRaw(
            'SUM(
                CASE
                    WHEN sales.subtotal > 0
                    THEN
                        (sale_items.subtotal / sales.subtotal)
                        * sales.tax
                    ELSE 0
                END
            ) as total_tax'
        )
        ->value('total_tax') ?? 0;


    // Amount actually collected
    $totalCollected = $totalRevenue + $totalTax;


    // Product-wise data
    $productSales = $basequery
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

                - CASE
                    WHEN sales.subtotal > 0
                    THEN
                        (sale_items.subtotal / sales.subtotal)
                        * sales.discount
                    ELSE 0
                  END

                - COALESCE(returns.returned_revenue, 0)
            ) as total_revenue'
        )
        ->selectRaw(
            'AVG(sale_items.price) as average_price'
        )
        ->groupBy(
            'products.id',
            'products.name',
            'products.sku'
        )
        ->orderByDesc('total_revenue')
        ->paginate(10)
        ->withQueryString();


    return view(
        'reports.product-sales',
        compact(
            'productSales',
            'totalCollected',
            'totalProducts',
            'totalQuantity',
            'totalRevenue'
        )
    );
}

    //Purchase report

    public function purchase(Request $request){

        $firmId = Auth::user()->firm_id;

        $query = Purchase::query()
        ->where('firm_id', $firmId)
        ->where('status', 'completed');

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

        $firmId = Auth::user()->firm_id;

        $returnsQuery = Sale_return_item::query()
            ->join(
                'sale_returns',
                'sale_return_items.sale_return_id',
                '=',
                'sale_returns.id'
            )->join(
                'sales',
                'sale_returns.sale_id',
                '=',
                'sales.id'
            )
            ->where('sales.firm_id', $firmId)
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
            ->where('sales.firm_id', $firmId)
            ->where('products.firm_id', $firmId)
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

                    - CASE
                        WHEN sales.subtotal > 0
                        THEN
                            (sale_items.subtotal / sales.subtotal)
                            * sales.discount
                        ELSE 0
                    END

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

            - CASE
                WHEN sales.subtotal > 0
                THEN
                    (sale_items.subtotal / sales.subtotal)
                    * sales.discount
                ELSE 0
            END

            - COALESCE(
                returns.returned_revenue,
                0
            )
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

            - CASE
                WHEN sales.subtotal > 0
                THEN
                    (sale_items.subtotal / sales.subtotal)
                    * sales.discount
                ELSE 0
            END

            - COALESCE(
                returns.returned_revenue,
                0
            )
        )
        -
        SUM(
            (
                sale_items.quantity
                - COALESCE(
                    returns.returned_quantity,
                    0
                )
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

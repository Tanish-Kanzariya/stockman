<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function create(){
        return view('sales.create');
    }

    public function searchProducts(Request $request){
        $search = $request->search;

        $products = Product::where('is_active', 1)
                    ->where('stock_quantity', '>', 0)
                    ->where(function ($query) use ($search){
                        $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "{$search}%");
                    })
                    ->orderBy('name')
                    ->limit(10)
                    ->get([
                        'id',
                        'name',
                        'sku',
                        'selling_price',
                        'stock_quantity',
                        'unit'
                    ]);

                    return response()->json($products);
    }
}

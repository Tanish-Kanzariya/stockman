<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request){

        $query = Product::query();

        //PRODUCT NAME FILTER
        if($request->filled('search')){
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        //CATEGORIES FILTER
        $categories = Categories::all();

        if($request->filled('category')){
            $query->where('category_id', $request->category);
        }

        //STATUS FILTER
        if($request->status === 'out_of_stock'){
            $query->where('stock_quantity', 0);
        }elseif($request->status === 'low_stock'){
            $query->whereColumn('stock_quantity', '<=', 'minimum_stock')
            ->where('stock_quantity','>',0);
        }elseif($request->status === 'in_stock'){
            $query->whereColumn('stock_quantity','>','minimum_stock');
        }

        $totalStock = (clone $query)->sum('stock_quantity');

        $totalInventoryValue = (clone $query)
        ->selectRaw('SUM(stock_quantity * purchase_price) as total')
        ->value('total');


        //SORTING
        $sort = $request->input('sort','id');
        $direction = $request->input('direction','asc');

        $allowedSorts = [
            'name',
            'stock_quantity',
            'purchase_price',
            'stock_value'
        ];

        if(!in_array($sort, $allowedSorts)){
            $sort = 'id';
        }

        if(!in_array($direction, ['asc','desc'])){
            $direction = 'asc';
        }

        if($sort == 'stock_value'){
            $query->orderByRaw("stock_quantity * purchase_price $direction");
        }else{
            $query->orderBy($sort, $direction);
        }

      
        // $query->orderBy($sort,$direction);
        $products = $query->paginate(10)->withQueryString()->fragment('stock-table');

        

        // $totalStockUnits = Product::sum('stock_quantity');

        // $totalInventoryValue = Product::selectRaw(
        //     'SUM(stock_quantity * purchase_price) as total'
        //     )->value('total');

        return view('stock.index',compact(
                'products',
                'totalStock',
                'totalInventoryValue',
                'categories'
                ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request){
        $query = StockMovement::with('product','purchase');

        // Product filter

        if($request->filled('product_id')){
            $query->where('product_id',$request->product_id);
        }

        //Movement Type Filter

        if($request->filled('type')){
            $query->where('type', $request->type);
        }

        //Date From Filter
        
        if($request->filled('date_from')){
            $query->whereDate('created_at', '>=', $request->date_from );
        }

        // Date To Filter

        if($request->filled('date_to')){
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stockMovements = $query->latest('id')
        ->paginate(10)->withQueryString();

        $products = Product::orderBy('name')->get();

        $totalMovements = StockMovement::count();

        $stockAdded = StockMovement::where('quantity', '>', 0)->sum('quantity');

        $stockReduced = StockMovement::where('quantity', '<', 0)->sum('quantity');

        $todaysMovement = StockMovement::whereDate('created_at', today())->count();

        return view('stockmovements.index',
        compact('stockMovements',
        'products',
        'totalMovements',
        'stockAdded',
        'stockReduced',
        'todaysMovement'));
    }
}

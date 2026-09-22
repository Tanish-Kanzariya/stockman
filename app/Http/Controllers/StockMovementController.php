<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockMovementController extends Controller
{
    public function index(Request $request){

        $firmId = Auth::user()->firm_id;

        $query = StockMovement::where('firm_id', $firmId)
        ->with('product','purchase');

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
        ->paginate(10)->withQueryString()->fragment('stock_movement');

        $products = Product::where('firm_id', $firmId)->orderBy('name')->get();

        $totalMovements = StockMovement::where('firm_id', $firmId)->count();

        $stockAdded = StockMovement::where('firm_id', $firmId)
        ->whereIn('type',[
            'purchase',
            'sale_return',
            'sale_cancel'
        ])->sum('quantity');

        $stockReduced = StockMovement::where('firm_id', $firmId)
        ->where('type', 'sale')->sum('quantity');

        $todaysMovement = StockMovement::where('firm_id', $firmId)
        ->whereDate('created_at', today())->count();

        return view('stockmovements.index',
        compact('stockMovements',
        'products',
        'totalMovements',
        'stockAdded',
        'stockReduced',
        'todaysMovement'));
    }
}

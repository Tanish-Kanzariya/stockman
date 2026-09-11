<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Categories;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Request;

class ProductController extends Controller
{
    public function showProducts(Request $request){
        $query = Product::with('Categories');

        //Search filter with product name or SKU

        if($request->filled('search')){
            $search = $request->input('search');
            $query->where(function ($q) use ($search){
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        //Category filter

        if($request->filled('category_id')){
            $query->where('category_id', $request->category_id);
        }

        //Status filter

        if($request->filled('status')){
            $query->where('is_active', $request->status);
        }

        //Stock filter

        if($request->filled('stock')){
            if($request->stock == 'low'){
                $query->whereColumn('stock_quantity', '<=', 'minimum_stock')
                ->where('stock_quantity', '>', 0);
            }

            if($request->stock == 'out'){
                $query->where('stock_quantity',0);
            }
        }

        $products = $query->latest('id')->paginate(10)->withQueryString();

        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active',1)->count();

        $inactiveProducts = Product::where('is_active', 0)->count();

        $categories = Categories::orderBy('name')->get();

        return view('products.index', compact('products',
                                            'categories',
                                            'totalProducts',
                                            'activeProducts',
                                            'inactiveProducts'));
    }


    public function update(Request $request,$id){

        $request->merge([
            'is_active' => $request->boolean('is_active')
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:250',
            'category_id' => 'required|integer',
            'purchase_price' => 'required|decimal:2|min:0',
            'selling_price' => 'required|decimal:2|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $product = Product::findOrFail($id);

        $product->update($validated);

        return redirect()
        ->route('products.index')
        ->with('success','Product Updated Successfully');
        

    }
    public function singleProduct(Product $product){
        // $product = Product::find($id);

        return view('products.show', compact('product'));
    }

    public function create(?int $id = nul){
    
        if($id){
        $product = Product::findOrFail($id);
        }else{
            $product = new Product();
        }

        $categories = Categories::all();

        return view('products.update',compact('product','categories'));
    }

    public function store(Request $req){

        $validated = $req->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            // 'sku' => 'required|string|unique:products,sku',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'is_active' => 'nullable|boolean'
        ]);

        $name = strtoupper(substr(preg_replace('/[^A-Za-z]/','',$req->name),0,4));

        $count = Product::where('sku','like',$name.'%')->count()+1;

        $sku = $name.str_pad($count,3,'0',STR_PAD_LEFT);

        $validated['sku'] = $sku;
        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'purchase_price' => $product->purchase_price
            ]
        ]);


    }

    public function search(Request $req){

        $search = $req->input('search');

        $products = Product::where('name', 'like', '%'. $search . '%')->limit(10)->get();

        return response()->json($products);
    }
}

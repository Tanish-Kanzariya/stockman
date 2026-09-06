<?php

namespace App\Http\Controllers;

use App\Models\Purchase_item;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Categories;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request){
        $query = Purchase::with('supplier');

        //INVOICE FILTER
        if($request->filled('invoice')){
            $query->where('invoice_number', 'like', '%' . $request->invoice . '%');
        }

        //SUPPLIER FILTER
        if($request->filled('supplier')){
            $query->where('supplier_id',$request->supplier);
        }

        //STATUS FILTER
        if($request->filled('status')){
            $query->where('status',$request->status);
        }

        //DATE FILTER
        if($request->filled('date')){
            $query->whereDate('purchase_date',$request->date);
        }

        //Calculating total purchase amount except the cancelled purchase
        $totalAmountPurchase = Purchase::where('status','completed')->sum('total_amount');

        $totalAmountCancelled = Purchase::where('status','cancelled')->sum('total_amount');

        $completedPurchaseCount = Purchase::where('status','completed')->count();

        $cancelledPurchaseCount = Purchase::where('status','cancelled')->count();


        $purchases = $query->latest('id')->paginate(10)->withQueryString()->fragment('purchase-table');

        $suppliers = Supplier::all();

        return view('purchases.index',
         compact(
            'purchases',
            'suppliers',
            'totalAmountPurchase',
            'totalAmountCancelled',
            'completedPurchaseCount',
            'cancelledPurchaseCount'
            ));
    }

    public function show(Purchase $purchase){
        // $purchase = Purchase::with(['suppliers','items.product'])->findOrFail($id);

        $purchase->load(['supplier', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function cancel(Purchase $purchase){
        
        if($purchase->status === 'cancelled'){
            return back()->with('error','This item is already cancelled');
        }

        $purchase->load('items');

        DB::transaction(function () use ($purchase){

             foreach($purchase->items as $item){
            $product = Product::find($item->product_id);

           

            if($product->stock_quantity < $item->quantity){
                throw new \Exception(
                    "Cannot cancel the purchase. Not enough stock for {$product->name}."
                );
            }

            $product->decrement('stock_quantity', $item->quantity);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'purchase_cancel',
                'quantity' => -$item->quantity,
                'reference_id' => $purchase->id,
                'note' => 'Purchase Cancelled'
            ]);

            $latestPurchaseItem = Purchase_item::where('product_id',$item->product_id)
                ->whereHas('purchase',function($query){
                    $query->where('status','completed');
                })
                ->latest('id')
                ->first();
            
            if ($latestPurchaseItem && $latestPurchaseItem->id == $item->id) {
                $product->update([
                'purchase_price' => $item->previous_purchase_price
                ]);
            }   
        }

        $purchase->update([
            'status' => 'cancelled'
        ]);

        });

        return back()->with('success', 'Purchase cancelled successfully');
    }
    public function create(){

        $suppliers = Supplier::all();
        $products = Product::all();
        $categories = Categories::all();
        return view('purchases.create',compact('suppliers','products','categories'));
    }

    public function store(Request $req){
        $validated = $req->validate([
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'purchase_items' => 'required|string'
        ]);

        $items = json_decode($validated['purchase_items'],true);
        if(!is_array($items) || count($items) === 0){
            return back()->withErrors([
                'purchase_items' => 'Please add atleast one item'
            ]);
        }


        $latestPurchase = Purchase::latest('id')->first();

        $nextNum = $latestPurchase ? $latestPurchase->id + 1 : 1;

        $invoiceNumber = 'INV-'.str_pad($nextNum,4,'0',STR_PAD_LEFT);

        DB::transaction(function () use ($validated, $invoiceNumber, $items){

            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'invoice_number' => $invoiceNumber,
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => 0,
                'status' => 'completed'
            ]);


            $total = 0;

            foreach($items as $item){

                validator($item,[
                    'product_id' => 'required|integer|exists:products,id',
                    'quantity' => 'required|integer|min:1',
                    'price' => 'required|numeric|min:0'
                ])->validate();

                $quantity = $item['quantity'];
                $price = $item['price'];
                $product_id = $item['product_id'];

                $itemTotal = $item['quantity'] * $item['price'];

                $total = $total + $itemTotal;


                $product = Product::find($product_id);

                $previousPrice = $product->purchase_price;

                 $purchase_items = Purchase_item::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'purchase_price' => $price,
                    'previous_purchase_price' => $previousPrice,
                    'subtotal' => $itemTotal
                ]);

                $product->increment('stock_quantity',$quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'purchase',
                    'quantity' => $quantity,
                    'reference_id' => $purchase->id,
                    'note' => 'Purchase'.$purchase->invoice_number
                ]);

                $product->update([
                    'purchase_price' => $price
                ]);

            }

            $purchase->update([
                'total_amount' => $total
            ]);
        });

        return redirect()->route('purchases.index');
    }
}

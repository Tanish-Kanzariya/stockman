<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Sale_item;
use App\Models\Sale_return;
use App\Models\Sale_return_item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SaleController extends Controller
{

    public function index(Request $request){

        $query = Sale::with('sale_items.product');

        //Search by invoice, customer name and phone number
        if($request->filled('search')){
            $search = $request->search;
            $query->where(function($q) use ($search){
                $q->where('customer_name', 'like', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%")
                ->orWhere('invoice_number', 'like', "%{$search}%");
            });
        }

        // Payment method filter
        if($request->filled('payment_method')){
            $query->where('payment_method', $request->payment_method);
        }

        //Filter by status
        if($request->filled('status')){
            $query->where('status', $request->status);
        }

        //Filter by date
        if($request->filled('date')){
            $query->whereDate('created_at', $request->date);
        }

        //Summary cards 

        $totalSales = Sale::where('status', 'completed')->count();

        $totalRevenue = Sale::where('status', 'completed')->sum('total_amount');

        $todaysSales = Sale::where('status', 'completed')
                        ->whereDate('created_at', today())
                        ->count();
        
        $cancelledSales = Sale::where('status', 'cancelled')->count();

        $sales = $query->latest()->paginate(10)->withQueryString()->fragment('saleCardBody');

        return view('sales.index',compact('sales',
                                            'totalSales',
                                            'totalRevenue',
                                            'todaysSales',
                                            'cancelledSales'
        ));
    }

    public function create(){
        return view('sales.create');
    }

    public function store(Request $request){

        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',

            'phone_number' => 'nullable|string|max:10',

            'discount' => 'nullable|numeric|min:0',

            'tax' => 'nullable|numeric|min:0',

            'payment_method' => 'required|in:cash,upi,card',

            'items' => 'required|array|min:1',

            'items.*.product_id' => 'required|exists:products,id',

            'items.*.quantity' => 'required|integer|min:1'
        ]);

        return DB::transaction(function () use ($validated, $request){

            // Generating Invoice Number
           $date = now()->format('Ymd');

           $lastSale = Sale::latest('id')->first();

           $nextNumber = $lastSale ? $lastSale->id + 1 : 1;

           $invoiceNumber = "INV-".$date."-".str_pad($nextNumber,4,'0',STR_PAD_LEFT);

           
           //Creating record in the sales table
           $sale = Sale::create([
                'invoice_number' => $invoiceNumber,

                'user_id' => 1,

                'customer_name' => $validated['customer_name'] ?? null,

                'phone_number' => $validated['phone_number'] ?? null,

                'subtotal' => 0,

                'discount' => $validated['discount'] ?? 0,

                'tax' => $validated['tax'] ?? 0,

                'total_amount' =>0,

                'payment_method' => $validated['payment_method'],

                'status' => 'completed'
           ]);

            $saleSubtotal = 0;

            foreach($validated['items'] as $item){
                $product = Product::findOrFail($item['product_id']);

                $quantity = $item['quantity'];

                if($product->stock_quantity < $quantity){
                        throw new \Exception(
                            "Insufficient stock for product: ".$product->name
                    );
                }

                $price = $product->selling_price;

                $subtotal = $price * $quantity;

                $saleSubtotal += $subtotal;

                Sale_item::create([
                    'sale_id' => $sale->id,

                    'product_id' => $product->id,

                    'quantity' => $quantity,

                    'price' => $price,

                    'subtotal' => $subtotal,

                ]);
                //Reduce the saled stock in the product table
                $product->decrement('stock_quantity', $quantity);

                //Inserting record in stock_movement table

                StockMovement::create([
                    'product_id' => $product->id,

                    'type' => 'sale',

                    'quantity' => $quantity,

                    'reference_id' => $sale->id,

                    'note' => 'Stock reduced due to sale'.$sale->invoice_number

                ]);
            }

            $discount = $validated['discount'] ?? 0;
            if($discount > $saleSubtotal){
                $discount = $saleSubtotal;
            }
            $tax = $validated['tax'] ?? 0;

            $totalAmount = $saleSubtotal - $discount + $tax;

            // Updating Sale table
            $sale->update([
                'subtotal' => $saleSubtotal,

                'discount' => $discount,

                'tax' => $tax,

                'total_amount' => $totalAmount
            ]);
            return response()->json([
                'success' => true,

                'message' => 'Sale Completed Successfully',

                'sale_id' => $sale->id,

                'invoice_number' => $sale->invoice_number

            ]);
            
        });
        
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

    public function invoice(Sale $sale){
        $sale->load('sale_items.product');

        return view('sales.invoice', compact('sale'));
    }

    public function cancel(Sale $sale){
        if($sale->status === 'cancelled'){
            return redirect()->route('sales.index')
            ->with('error', 'This sale has already been deleted');
        }

        DB::transaction(function () use ($sale){

            //Loading sale items with products

            $sale->load('sale_items.product');

            foreach($sale->sale_items as $item){
                $product = $item->product;

                $product->increment('stock_quantity', $item->quantity);

                StockMovement::create([
                    'product_id' => $product->id,

                    'type' => 'sale_cancel',

                    'quantity' => $item->quantity,

                    'reference_id' => $sale->id,

                    'note' => 'Stock cancelled '. $sale->invoice_number
                ]);
            }
            $sale->update([
                'status' => 'cancelled'
            ]);
        });

        return redirect()->route('sales.index')->with('success', 'Sale cancelled successfully');
    }

    public function returnForm(Sale $sale){
        $sale->load('sale_items.product');
        
        foreach($sale->sale_items as $saleItem){

            $returnedQuantity = Sale_return_item::where('sale_item_id', $saleItem->id)
            ->sum('quantity');

            $saleItem->returned_quantity = $returnedQuantity;

            $saleItem->returnable_quantity = $saleItem->quantity - $returnedQuantity;
        }

        return view('sales.return', compact('sale'));
    }

    public function processReturn(Request $request, Sale $sale){
        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',

            'items' => 'required|array|min:1',

            'items.*.sale_item_id' => 'required|exists:sale_items,id',

            'items.*.quantity' => 'required|integer|min:1'
        ]);

        return DB::transaction(function () use ($validated, $sale){
            
            //Sale must be completed
            if($sale->status !== 'completed'){
                return response()->json([
                    'success' => false,
                    'message' => 'Only completed sales can be returned'
                ],422);
            }

            $refundAmount = 0;

            foreach($validated['items'] as $item){
                $saleItem = Sale_item::findOrFail($item['sale_item_id']);

                if($saleItem->sale_id !== $sale->id){
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid sale item.'
                    ]);
                }

                $alreadyReturned = Sale_return_item::where('sale_item_id', $saleItem->id)->sum('quantity');

                $returnableQuantity = $saleItem->quantity-$alreadyReturned;

                $returnQuantity = $item['quantity'];

                //Prevent over return

                if($returnQuantity > $returnableQuantity){
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot return more than {$returnableQuantity}'
                    ]);
                }

                //Calculate refund amount
                $subtotal = $saleItem->price * $returnQuantity;

                $refundAmount += $subtotal;
            }

            $saleReturn = Sale_return::create([
                'sale_id' => $sale->id,
                'refund_amount' => $refundAmount,
                'reason' => $validated['reason'] ?? null,
                'status' => 'completed'
            ]);

            //Create sale_return_item

            foreach($validated['items'] as $item){
                $saleItem = Sale_item::findOrFail($item['sale_item_id']);

                $returnQuantity = $item['quantity'];

                $subtotal = $saleItem->price * $returnQuantity;

                Sale_return_item::create([
                    'sale_return_id' => $saleReturn->id,

                    'sale_item_id' => $saleItem->id,

                    'product_id' => $saleItem->product_id,

                    'quantity' => $returnableQuantity,

                    'price' => $saleItem->price,

                    'subtotal' => $subtotal
                ]);

                //Restore products 
                $product = Product::findOrFail($saleItem->product_id);

                $product->increment('stock_quantity', $returnQuantity);

                //Record Stock movement

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'sale_return',
                    'quantity' => $returnableQuantity,
                    'reference_id' => $saleReturn->id,
                    'note' => 'Stock restored due to sales return'.$sale->invoice_number
                ]);
            }

            // Check whether the entire sale has been returned
        $sale->load('sale_items');

        $allItemsReturned = true;

        foreach ($sale->sale_items as $saleItem) {

            $returnedQuantity = Sale_return_item::where(
                'sale_item_id',
                $saleItem->id
            )->sum('quantity');

            if ($returnedQuantity < $saleItem->quantity) {
                $allItemsReturned = false;
                break;
            }
        }

        if ($allItemsReturned) {
            $sale->update([
                'status' => 'cancelled'
            ]);
        }

            return response()->json([
                'success' => true,
                'message' => 'Sale restored successfully',
                'return_id' => $saleReturn->id,
                'refundAmount' => $refundAmount
            ]);
        });
    }
}

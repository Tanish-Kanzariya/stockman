<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Sale_item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SaleController extends Controller
{
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

            foreach($request->items as $item){
                $product = Product::findOrFail($item['product_id']);

                $quantity = $item['quantity'];

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
}

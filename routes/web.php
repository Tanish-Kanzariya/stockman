<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockMovementController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/products', function(){
    $products = Product::all();
    return $products;
});

Route::get('/products',[ProductController::class,'showProducts'])->name('products.index');

Route::get('/product/{product}',[ProductController::class,'singleProduct'])->name('singleProduct');

Route::get('/products/update/{id}', [ProductController::class,'create'])->name('product.update_form');

Route::post('/products/',[ProductController::class,'store'])->name('product.submit');

Route::get('/products/search', [ProductController::class,'search'])->name('products.search');

Route::POST('/product/{id}/edit',[ProductController::class,'update'])->name('product.update');


Route::get('/purchase',[PurchaseController::class,'create'])->name('createPurchase');

Route::post('/purchase',[PurchaseController::class,'store'])->name('purchase.store');

Route::get('/purchases',[PurchaseController::class,'index'])->name('purchases.index');

Route::get('/purchases/{purchase}',[PurchaseController::class,'show'])->name('purchases.show');

Route::post('/purchases/{purchase}/cancel', [PurchaseController::class,'cancel'])->name('purchase.cancel');

Route::get('/stock',[StockController::class,'index'])->name('stock.index');


Route::get('/suppliers',[SupplierController::class,'index'])->name('supplier.index');

Route::get('/create/supplier/{id?}',[SupplierController::class,'create'])->name('supplier.create');

Route::POST('/store/supplier',[SupplierController::class,'store'])->name('supplier.store');

Route::POST('/supplier/update/{id}', [SupplierController::class, 'update'])->name('supplier.update');

Route::POST('/supplier/delete/{id}', [SupplierController::class, 'delete'])->name('supplier.delete');

Route::get('/stock-movements/', [StockMovementController::class,'index'])->name('stockMovements');

<?php

namespace App\Models;

// use App\Models\Categories;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'sku',
        'purchase_price',
        'selling_price',
        'stock_quantity',
        'minimum_stock',
        'unit',
        'is_active'
    ];
    public function Categories(){
        return $this->belongsTo(Categories::class,'category_id');
    }

    public function sale_items(){
        return $this->hasMany(Sale_item::class);
    }

    public function purchase_items(){
        return $this->hasMany(Purchase_item::class);
    }

    public function stock_movements(){
        return $this->hasMany(StockMovement::class);
    }
}

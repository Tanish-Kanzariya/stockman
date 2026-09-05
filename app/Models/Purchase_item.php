<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase_item extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'purchase_price',
        'previous_purchase_price',
        'subtotal'
    ];
    public function purchase(){
        return $this->belongsTo(Purchase::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}

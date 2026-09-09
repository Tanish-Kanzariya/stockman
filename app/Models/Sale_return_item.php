<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale_return_item extends Model
{
    protected $fillable =[

        'sale_return_id',
        'sale_item_id',
        'product_id',
        'quantity',
        'price',
        'subtotal'
    ];

    public function sale_return(){
        return $this->belongsTo(Sale_return::class);
    }

    public function sale_item(){
        return $this->belongsTo(Sale_item::class);
    }

    public function  product(){
        return $this->hasMany(Product::class);
    }
}

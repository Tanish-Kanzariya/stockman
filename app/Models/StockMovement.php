<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'reference_id',
        'note'
    ];
    
    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function purchase(){
        return $this->belongsTo(Purchase::class,'reference_id');
    }
}

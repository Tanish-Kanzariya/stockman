<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale_return extends Model
{
    protected $fillable = [

        'sale_id',
        'refund_amount',
        'reason',
        'status'

    ];

    public function sale(){
        return $this->belongsTo(Sale::class);
    }

    public function sale_return_items(){
        return $this->hasMany(Sale_return_item::class);
    }
}

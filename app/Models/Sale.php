<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_name',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'payment_method'
    ];
    public function sale_items(){
        return $this->hasMany(Sale_item::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'total_amount',
        'status',
        'purchase_date',

    ];
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function items(){
        return $this->hasMany(Purchase_item::class);
    }
}

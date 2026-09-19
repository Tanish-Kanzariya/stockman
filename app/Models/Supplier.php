<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'firm_id',
        'name',
        'phone',
        'email',
        'address'
    ];
    public function purchases(){
        return $this->hasMany(Purchase::class);
    }

    public function firm(){
        return $this->belongsTo(Firm::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $fillable = [
        'firm_id',
        'name'
    ];
    public function product(){
       return  $this->hasMany(Product::class);
    }

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }
}

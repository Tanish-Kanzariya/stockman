<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    public function product(){
       return  $this->hasMany(Product::class);
    }
}

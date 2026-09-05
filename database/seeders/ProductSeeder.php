<?php

namespace Database\Seeders;

use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $grocery = DB::table('categories')->where('name','Grocery')->first();
        $beverage = DB::table('categories')->where('name','Bevarages')->first();

        $snacks = DB::table('categories')->where('name','Snacks')->first();
        $pc = DB::table('categories')->where('name','Personal Care')->first();
        
            // Product::create([
            //     'name' => 'Tata Salt',
            //     'category_id' => $grocery->id,
            //     'sku' => 'SALT001',
            //     'purchase_price' => 20,
            //     'selling_price' => 25,
            //     'stock_quantity' => 30,
            //     'minimum_stock' => 15,
            //     'unit' => 'packet',
            //     'is_active' => true
            // ]);

            // Product::create([
            //     'name' => 'Pepsi',
            //     'category_id' => $beverage->id,
            //     'sku' => 'PEPSI001',
            //     'purchase_price' => 50,
            //     'selling_price' => 65,
            //     'stock_quantity' => 70,
            //     'minimum_stock' => 15,
            //     'unit' => 'bottle',
            //     'is_active' => true
            // ]);

            // Product::create([
            //     'name' => 'Kurkure',
            //     'category_id' => $snacks->id,
            //     'sku' => 'KURKURE001',
            //     'purchase_price' => 10,
            //     'selling_price' => 20,
            //     'stock_quantity' => 100,
            //     'minimum_stock' => 15,
            //     'unit' => 'packet',
            //     'is_active' => true
            // ]);

             Product::create([
                'name' => 'Shampoo',
                'category_id' => $pc->id,
                'sku' => 'SHAMPOO001',
                'purchase_price' => 20,
                'selling_price' => 25,
                'stock_quantity' => 100,
                'minimum_stock' => 15,
                'unit' => 'packet',
                'is_active' => true
            ]);
        
    }
}

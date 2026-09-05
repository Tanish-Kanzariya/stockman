<?php

namespace Database\Seeders;

use App\Models\Supplier;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i=0; $i<=20; $i++){
            Supplier::create([
                'name' => fake()->name(),
                'phone' => fake()->randomNumber(),
                'email' => fake()->email(),
                'address' => fake()->address()
            ]);
        }

    }
}

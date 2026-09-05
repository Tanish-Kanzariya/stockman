<?php

namespace Database\Seeders;

use App\Models\Categories;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categories::create([
            'name' => 'Grocery'
        ]);

        Categories::create([
            'name' => 'Bevarages'
        ]);

        Categories::create([
            'name' => 'Snacks'
        ]);

        Categories::create([
            'name' => 'Personal Care'
        ]);

        Categories::create([
            'name' => 'Stationary'
        ]);
    }
}

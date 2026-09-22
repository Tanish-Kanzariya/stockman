<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_sku_unique');

            $table->unique([
                'firm_id','sku'
            ],'products_firm_sku_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('firm_sku_unique_on_products', function (Blueprint $table) {
            $table->dropUnique('products_firm_sku_unique');

            $table->unique('sku', 'products_sku_unique');
        });
    }
};

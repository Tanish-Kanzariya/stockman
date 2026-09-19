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
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique('sales_invoice_number_unique');

            $table->unique(
                [
                    'firm_id','invoice_number'
                ],
                'sales_firm_invoice_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique('sales_firm_invoice_unique');

            $table->unique('invoice_number', 'sales_invoice_number_unique');
        });
    }
};

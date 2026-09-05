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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')
            ->constrained('users')
            ->restrictOnDelete();

            $table->string('customer_name')->nullable();
            $table->decimal('subtotal',10,2);
            $table->decimal('discount',10,2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total_amount',10,2);
            $table->string('payment_method');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

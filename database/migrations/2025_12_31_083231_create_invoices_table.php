<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Worker / Coordinator
            $table->foreignId('worker_id')
                  ->constrained('workers')
                  ->restrictOnDelete();
            $table->string('worker_name');

            // Invoice info
            $table->string('invoice_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address')->nullable();

            // Exchange phone details
            $table->string('exchange_emi')->nullable();
            $table->string('exchange_phone_type')->nullable();
            $table->string('exchange_colour')->nullable();
            $table->string('exchange_capacity')->nullable();
            $table->decimal('exchange_cost', 10, 2)->nullable();

            // Sold phone details
            $table->string('emi')->nullable();
            $table->string('phone_type')->nullable();
            $table->string('colour')->nullable();
            $table->string('capacity')->nullable();

            // Prices
            $table->decimal('selling_price', 10, 2);
            $table->decimal('accessories_total', 10, 2)->default(0);

            // Final amounts
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('payable_amount', 10, 2)->nullable();

            // Total Commission
            $table->decimal('total_commission', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

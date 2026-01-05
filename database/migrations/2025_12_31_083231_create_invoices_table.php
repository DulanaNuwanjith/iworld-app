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

            // Invoice info (required)
            $table->string('invoice_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address')->nullable();

            // Exchange phone details (optional)
            $table->string('exchange_emi')->nullable();
            $table->string('exchange_phone_type')->nullable();
            $table->string('exchange_colour')->nullable();
            $table->string('exchange_capacity')->nullable();
            $table->decimal('exchange_cost', 10, 2)->nullable();

            // Phone details (required)
            $table->string('emi'); 
            $table->string('phone_type');
            $table->string('colour');
            $table->string('capacity');

            // Selling price (required)
            $table->decimal('selling_price', 10, 2);

            // Accessories (optional)
            $table->decimal('tempered', 10, 2)->nullable();
            $table->decimal('back_cover', 10, 2)->nullable();
            $table->decimal('charger', 10, 2)->nullable();
            $table->decimal('data_cable', 10, 2)->nullable();
            $table->decimal('hand_free', 10, 2)->nullable();
            $table->decimal('cam_glass', 10, 2)->nullable();
            $table->decimal('airpods', 10, 2)->nullable();
            $table->decimal('power_bank', 10, 2)->nullable();

            // Total amount (required, default 0)
            $table->decimal('total_amount', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

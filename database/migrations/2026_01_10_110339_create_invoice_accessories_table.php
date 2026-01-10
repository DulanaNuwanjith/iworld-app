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
        Schema::create('invoice_accessories', function (Blueprint $table) {
            $table->id();

            // Invoice reference
            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnDelete();

            $table->string('invoice_number');

            // Accessory reference
            $table->foreignId('accessory_id')
                ->nullable()
                ->constrained('accessories')
                ->nullOnDelete();

            // Snapshot data
            $table->string('accessory_name');

            // Selling details
            $table->unsignedInteger('quantity');
            $table->decimal('selling_price_accessory', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_accessories');
    }
};

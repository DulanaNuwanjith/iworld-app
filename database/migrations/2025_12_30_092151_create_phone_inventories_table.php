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
        Schema::create('phone_inventories', function (Blueprint $table) {
            $table->id();
            $table->date('date');                      // Date of entry
            $table->string('supplier');                // Supplier name
            $table->string('phone_type');              // Phone model/type
            $table->string('colour');                  // Colour
            $table->string('capacity', 50);            // Capacity (e.g., 64GB)
            $table->string('emi')->default('0');       // EMI as string, default '0'
            $table->decimal('cost', 10, 2);            // Cost
            $table->string('note')->nullable();        // Optional note
            $table->string('stock_type')->nullable();  // Optional stock type
            $table->tinyInteger('status')->default(0); // Status: 0 = unsold, 1 = sold
            $table->timestamps();                      // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_inventories');
    }
};

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
        Schema::create('finance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finance_order_id')->constrained('finance_orders')->onDelete('cascade');
            $table->tinyInteger('installment_number'); // 1, 2, 3
            $table->decimal('amount', 12, 2);
            $table->date('paid_at')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_payments');
    }
};

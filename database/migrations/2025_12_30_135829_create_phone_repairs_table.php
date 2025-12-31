<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('phone_repairs', function (Blueprint $table) {
            $table->id();

            // 🔗 Foreign key to phone_inventories
            $table->foreignId('phone_inventory_id')
                ->constrained('phone_inventories')
                ->cascadeOnDelete();

            // Same EMI as inventory
            $table->string('emi');

            $table->string('repair_reason');
            $table->decimal('repair_cost', 10, 2);

            $table->timestamps();

            $table->index('emi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_repairs');
    }
};

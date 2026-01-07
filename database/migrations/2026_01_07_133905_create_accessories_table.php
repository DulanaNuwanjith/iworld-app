<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->string('supplier');
            $table->decimal('commission', 10, 2)->default(0);

            $table->string('type');
            $table->string('name');
            $table->integer('quantity');
            $table->decimal('cost', 10, 2);

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};


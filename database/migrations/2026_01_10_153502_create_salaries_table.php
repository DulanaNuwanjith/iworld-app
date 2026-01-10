<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('worker_id')->constrained('workers')->restrictOnDelete();
            $table->string('worker_name');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('total_commission', 10, 2)->default(0);
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->integer('invoice_count')->default(0);
            $table->unsignedTinyInteger('month'); // 1-12
            $table->year('year');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};

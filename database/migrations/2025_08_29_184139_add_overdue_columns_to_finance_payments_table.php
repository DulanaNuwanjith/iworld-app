<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_payments', function (Blueprint $table) {
            $table->integer('overdue_days')->default(0);
            $table->decimal('overdue_amount', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('finance_payments', function (Blueprint $table) {
            $table->dropColumn(['overdue_days', 'overdue_amount']);
        });
    }

};

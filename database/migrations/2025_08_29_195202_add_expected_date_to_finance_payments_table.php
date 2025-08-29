<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('finance_payments', function (Blueprint $table) {
            $table->date('expected_date')->nullable()->after('paid_at');
        });
    }

    public function down()
    {
        Schema::table('finance_payments', function (Blueprint $table) {
            $table->dropColumn('expected_date');
        });
    }
};

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
            $table->string('invoice_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address')->nullable();

            $table->string('emi'); // linked phone
            $table->string('phone_type');
            $table->string('colour');
            $table->string('capacity');

            $table->decimal('selling_price', 10, 2)->default(0); // new selling price column

            // accessories
            $table->decimal('tempered', 10, 2)->default(0);
            $table->decimal('back_cover', 10, 2)->default(0);
            $table->decimal('charger', 10, 2)->default(0);
            $table->decimal('data_cable', 10, 2)->default(0);
            $table->decimal('hand_free', 10, 2)->default(0);
            $table->decimal('airpods', 10, 2)->default(0);
            $table->decimal('power_bank', 10, 2)->default(0);

            $table->decimal('total_amount', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

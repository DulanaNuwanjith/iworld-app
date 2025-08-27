<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->date('item_created_date');
            $table->string('buyer_name');
            $table->string('buyer_id');
            $table->text('buyer_address');
            $table->string('phone_1');
            $table->string('phone_2')->nullable();
            $table->string('id_photo')->nullable();
            $table->string('electricity_bill_photo')->nullable();
            $table->string('item_name');
            $table->string('emi_number');
            $table->string('colour');
            $table->string('photo_1')->nullable();
            $table->string('photo_2')->nullable();
            $table->string('photo_about')->nullable();
            $table->string('icloud_mail');
            $table->string('icloud_password');
            $table->string('screen_lock_password');
            $table->string('price')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_orders');
    }
};

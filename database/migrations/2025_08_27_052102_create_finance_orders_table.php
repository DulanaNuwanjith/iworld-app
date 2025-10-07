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
            $table->string('coordinator_name')->nullable();
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
            $table->string('icloud_mail')->nullable();;
            $table->string('icloud_password')->nullable();;
            $table->string('screen_lock_password')->nullable();;
            $table->string('price');
            $table->string('rate');
            $table->string('amount_of_installments');
            $table->string('due_payment');
            $table->string('over_due_payment_fullamount')->nullable();;
            $table->string('paid_amount_fullamount')->nullable();;
            $table->string('remaining_amount')->nullable();;
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_orders');
    }
};

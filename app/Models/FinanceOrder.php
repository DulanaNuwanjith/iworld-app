<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'item_created_date',
        'buyer_name',
        'buyer_id',
        'buyer_address',
        'phone_1',
        'phone_2',
        'id_photo',
        'electricity_bill_photo',
        'item_name',
        'emi_number',
        'colour',
        'photo_1',
        'photo_2',
        'photo_about',
        'icloud_mail',
        'icloud_password',
        'screen_lock_password',
    ];
}

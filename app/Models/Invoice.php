<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_name',
        'customer_phone',
        'customer_address',
        'emi',
        'phone_type',
        'colour',
        'capacity',
        'selling_price',
        'tempered',
        'back_cover',
        'charger',
        'data_cable',
        'hand_free',
        'cam_glass',
        'airpods',
        'power_bank',
        'total_amount',
        'exchange_emi',
        'exchange_phone_type',
        'exchange_colour',
        'exchange_capacity',
        'exchange_cost',
        'payable_amount',
        'worker_id',
        'worker_name',
    ];


    // Default values
    protected $attributes = [
        'selling_price' => 0,
        'tempered' => 0,
        'back_cover' => 0,
        'charger' => 0,
        'data_cable' => 0,
        'hand_free' => 0,
        'cam_glass' => 0,
        'airpods' => 0,
        'power_bank' => 0,
        'payable_amount' => 0,
        'total_amount' => 0,
    ];

    // Dates
    protected $dates = ['created_at', 'updated_at'];

    // Relationships
    public function inventory()
    {
        return $this->belongsTo(PhoneInventory::class, 'emi', 'emi');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }
}

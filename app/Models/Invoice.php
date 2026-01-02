<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    // Fillable fields
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
        'airpods',
        'power_bank',
        'total_amount',
    ];

    // Dates
    protected $dates = ['created_at', 'updated_at'];

    public function inventory()
    {
        return $this->belongsTo(PhoneInventory::class, 'emi', 'emi');
    }
}

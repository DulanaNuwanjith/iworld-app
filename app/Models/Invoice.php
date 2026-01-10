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
        'accessories_total',  
        'total_amount',
        'exchange_emi',
        'exchange_phone_type',
        'exchange_colour',
        'exchange_capacity',
        'exchange_cost',
        'payable_amount',
        'worker_id',
        'worker_name',
        'total_commission',  
    ];

    // Default values
    protected $attributes = [
        'selling_price' => 0,
        'accessories_total' => 0,
        'total_amount' => 0,
        'payable_amount' => 0,
        'exchange_cost' => 0,
        'total_commission' => 0, 
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

    public function accessories()
    {
        return $this->hasMany(InvoiceAccessory::class, 'invoice_id');
    }

    public function invoiceAccessories()
    {
        return $this->hasMany(InvoiceAccessory::class);
    }
}

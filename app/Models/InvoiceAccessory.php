<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAccessory extends Model
{
    use HasFactory;

    // Table name (optional if naming follows Laravel convention)
    protected $table = 'invoice_accessories';

    // Mass assignable fields
    protected $fillable = [
        'invoice_id',
        'invoice_number',
        'accessory_id',
        'accessory_name',
        'quantity',
        'selling_price_accessory',
    ];

    /**
     * Get the invoice this accessory belongs to
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the original accessory (optional, if you want reference to Accessories table)
     */
    public function accessory()
    {
        return $this->belongsTo(Accessory::class);
    }
}

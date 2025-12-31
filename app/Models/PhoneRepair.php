<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneRepair extends Model
{
    use HasFactory;

    protected $table = 'phone_repairs';

    protected $fillable = [
        'phone_inventory_id',
        'emi',
        'repair_reason',
        'repair_cost',
    ];

    protected $casts = [
        'repair_cost' => 'decimal:2',
    ];

    /**
     * Relationship: Repair belongs to one PhoneInventory
     */
    public function inventory()
    {
        return $this->belongsTo(PhoneInventory::class, 'phone_inventory_id');
    }
}

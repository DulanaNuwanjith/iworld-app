<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PhoneRepair;

class PhoneInventory extends Model
{
    use HasFactory;

    // Table name (optional if it matches 'phone_inventories')
    protected $table = 'phone_inventories';

    // Mass assignable fields
    protected $fillable = [
        'date',
        'supplier',
        'phone_type',
        'colour',
        'capacity',
        'emi',  
        'cost',
        'note',
        'stock_type',
    ];

    // Field type casting
    protected $casts = [
        'date' => 'date',
        'cost' => 'decimal:2',
        'emi' => 'string',
    ];

    public function repairs()
    {
        return $this->hasMany(PhoneRepair::class, 'phone_inventory_id');
    }
}

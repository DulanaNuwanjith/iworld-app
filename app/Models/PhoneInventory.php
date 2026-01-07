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
        'commission', 
        'status',
        'supplier_id_front',
        'supplier_id_back',
        'status_availability', 
        'person_name',
    ];

    // Field type casting
    protected $casts = [
        'date' => 'date',
        'cost' => 'decimal:2',
        'emi' => 'string',
        'supplier_id_front' => 'string',
        'supplier_id_back' => 'string', 
        'status_availability' => 'string',
        'person_name' => 'string',
        'commission' => 'decimal:2',
    ];

    public function repairs()
    {
        return $this->hasMany(PhoneRepair::class, 'phone_inventory_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    protected $fillable = [
        'date',
        'supplier',
        'commission',
        'type',
        'name',
        'quantity',
        'cost',
    ];

    protected $casts = [
        'date' => 'datetime', // ensures $accessory->date is a Carbon instance
    ];
}

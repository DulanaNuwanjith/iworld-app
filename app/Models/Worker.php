<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'national_id',
        'address',
        'phone_1',
        'phone_2',
        'joined_date',
        'job_title',
        'basic_salary',
        'note',
    ];
}

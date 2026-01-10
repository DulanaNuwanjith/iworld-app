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

    // Relationships
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'worker_id');
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class, 'worker_id');
    }

}

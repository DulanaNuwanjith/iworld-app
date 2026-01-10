<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'worker_id',
        'worker_name',
        'basic_salary',
        'total_commission',
        'total_sales',
        'invoice_count',
        'month',
        'year',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}

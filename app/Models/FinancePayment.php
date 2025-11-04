<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FinancePayment extends Model
{
    use HasFactory;

    // Fillable fields for mass assignment
    protected $fillable = [
        'finance_order_id',
        'installment_number',
        'amount',
        'paid_amount',
        'paid_at',
        'overdue_amount',
        'overdue_days',
        'expected_date',
    ];

    protected $casts = [
        'expected_date' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'float',
    ];

    // Cast paid_at as a Carbon date object
    protected $dates = ['paid_at'];

    /**
     * Relationship: Payment belongs to a Finance Order
     */
    public function order()
    {
        return $this->belongsTo(FinanceOrder::class, 'finance_order_id');
    }

    /**
     * Check if the payment has been completed
     *
     * @return bool
     */
    public function isPaid()
    {
        return !is_null($this->paid_at);
    }

    /**
     * Get formatted payment date
     *
     * @param string $format
     * @return string|null
     */
    public function getPaidDate($format = 'd M Y')
    {
        return $this->paid_at ? Carbon::parse($this->paid_at)->format($format) : null;
    }

    public function financeOrder()
    {
        return $this->belongsTo(FinanceOrder::class);
    }

}

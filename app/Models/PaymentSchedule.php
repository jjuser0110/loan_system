<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    use HasFactory;

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'interest_paid_amount' => 'decimal:2',
        'late_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'late_paid_amount' => 'decimal:2'
    ];

    protected $fillable = [
        'loan_code',
        'company_id',
        'customer_id',
        'due_date',
        'payment_amount',
        'paid_amount',
        'discount_amount',
        'interest_amount',
        'interest_paid_amount',
        'late_amount',
        'late_paid_amount',
        'closed'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

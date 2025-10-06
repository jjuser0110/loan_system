<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'interest_rate' => 'decimal:4',
        'installment' => 'decimal:2',
        'first_payment' => 'decimal:2',
        'last_payment' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'stamp_fee' => 'decimal:2',
        'capital' => 'decimal:2',
        'payment' => 'decimal:2',
        'paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'interest' => 'decimal:2',
        'interest_paid' => 'decimal:2',
        'interest_balance' => 'decimal:2',
        'late' => 'decimal:2',
        'late_paid' => 'decimal:2',
        'late_balance' => 'decimal:2',
        'discount' => 'decimal:2',
        'outstanding' => 'decimal:2',
        'next_due_amount' => 'decimal:2',
        'total_discount' => 'decimal:2',
      
    ];

    protected $fillable = [
        'loan_code',
        'company_id',
        'customer_id',
        'year_month',
        'interest_group',
        'loan_amount',
        'interest_rate',
        'loan_term',
        'installment',
        'first_payment',
        'last_payment',
        'processing_fee',
        'stamp_fee',
        'capital',
        'payment',
        'paid',
        'balance',
        'interest',
        'interest_paid',
        'interest_balance',
        'late',
        'late_paid',
        'late_balance',
        'discount',
        'outstanding',
        'next_due_date',
        'next_due_amount',
        'alternate_code',
        'receipt_no',
        'updated_by',
        'created_by',
        'closed',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class,'updated_by', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'loan_id', 'id');
    }

    public function payment_schedules(){
        return $this->hasMany(PaymentSchedule::class, 'loan_code', 'loan_code');
    }
}

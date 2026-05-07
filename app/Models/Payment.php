<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'late_paid_amount' => 'decimal:2',
        'interest_paid_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'top_up' => 'decimal:2'
    ];

    protected $fillable = [
        'payment_code',
        'customer_id',
        'loan_id',
        'payment_amount',
        'payment_method_id',
        'late_paid_amount',
        'interest_paid_amount',
        'discount_amount',
        'bank',
        'cheque',
        'top_up',
        'collection_type',
        'created_by',
        'updated_by',
        'remark',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function payment_method_logs()
    {
        return $this->morphMany(PaymentMethodLog::class, 'content');
    }
    
    public function stock_logs()
    {
        return $this->morphMany(StockLog::class, 'content');
    }
}

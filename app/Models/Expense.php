<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    protected $fillable = [
        'expense_code',
        'expense_title',
        'expense_description',
        'amount',
        'payment_method_id',
        'company_id',
        'date',
        'updated_by',
        'expense_type',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function payment_method_logs()
    {
        return $this->morphMany(PaymentMethodLog::class, 'content');
    }
}

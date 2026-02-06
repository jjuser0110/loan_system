<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyReport extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'stock_a',
        'stock_b',
        'stock_bb',
        'company_amount',
        'loan_topup',
        'payment',
        'expenses',
        'account_total_amount',
        'created_date',
    ];
}

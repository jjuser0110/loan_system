<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'content_id',
        'content_type',
        'company_id',
        'loan_id',
        'type',
        'stock_type',
        'description',
        'prev_amount',
        'amount',
        'total',
    ];

    public function content()
    {
        return $this->morphTo();
    }
}

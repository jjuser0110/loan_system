<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethodLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'content_id',
        'content_type',
        'payment_method_id',
        'type',
        'description',
        'prev_amount',
        'amount',
        'total',
    ];
        
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}

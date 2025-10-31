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
    
     protected $appends = ['details'];

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    
    public function getDetailsAttribute()
    {
        if (!$this->content_type || !$this->content) {
            return '-';
        }

        $type = class_basename($this->content_type);

        return match ($type) {
            'Payment' => "Payment<br> #{$this->content->payment_code}",
            'Loan'    => "Loan<br> #{$this->content->loan_code}",
            default   => '-',
        };
    }

    public function content()
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reference extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'references';

    protected $fillable = [
        'customer_id',
        'reference_type',
        'new_ic',
        'name',
        'gender',
        'race',
        'date_of_birth',
        'mobile',
        'telephone',
        'house_ownership',
        'warganegara',
        'address1',
        'address2',
        'postcode',
        'city',
        'state',
        'company_name',
        'biz_type',
        'designation',
        'monthly_income',
        'company_address1',
        'company_address2',
        'company_postcode',
        'company_city',
        'company_state',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

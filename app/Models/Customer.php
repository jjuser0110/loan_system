<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'profile_image',
        'customer_code',
        'company_id',
        'company_code',
        'customer_name',
        'nric_number',
        'gender',
        'race',
        'date_of_birth',
        'address1',
        'address2',
        'postcode',
        'city',
        'state',
        'house_ownership',
        'warganegara',
        'marital_status',
        'email',
        'telephone',
        'mobile',
        'remark',
        'company_name',
        'biz_type',
        'designation',
        'monthly_income',
        'company_address1',
        'company_address2',
        'company_postcode',
        'company_city',
        'company_state',
        'company_telephone',
        'company_mobile',
        'company_fax',
        'vehicle_no',
        'vehicle_model',
        'employer',
        'job_type',
        'start_working_date',
        'end_working_date',
        'salary_date',
        '2nd_salary_date',
    ];

    protected $dates = [
        'date_of_birth',
        'start_working_date',
        'end_working_date',
        'salary_date',
        '2nd_salary_date',
        'deleted_at',
    ];

    public function references()
    {
        return $this->hasMany(Reference::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
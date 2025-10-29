<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'company_id',
        'branch_id',
        'bank_id',
        'account_no',
        'owner_name',
        'is_active',
        'amount',
        'created_by_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
        
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
        
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
        
    public function created_by()
    {
        return $this->belongsTo(User::class,'created_by_id');
    }
}

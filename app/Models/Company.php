<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'company_name',
        'company_code',
        'amount',
        'stocka',
        'stockb',
        'stockbb',
    ];

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch');
    }
}

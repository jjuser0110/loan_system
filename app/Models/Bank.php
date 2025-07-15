<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'bank_name',
        'short_name',
        'created_by_id'
    ];

    public function created_by()
    {
        return $this->belongsTo('App\Models\User','created_by_id');
    }
}

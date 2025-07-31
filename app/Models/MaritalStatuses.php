<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaritalStatuses extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'marital_status',
    ];
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRolesAndAbilities,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'code',
        'password',
        'remember_token',
        'username',
        'role_id',
        'is_active',
        'contact_no',
        'referral_code',
        'wallet',
        'upline',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function user_wallet_logs()
    {
        return $this->morphMany('App\Models\UserWalletLog', 'content');
    }

    public function wallet_logs()
    {
        return $this->hasMany('App\Models\UserWalletLog')->orderBy('created_at','DESC');
    }

    public function package()
    {
        return $this->hasOne('App\Models\Package');
    }

    public function package_invoices()
    {
        return $this->hasMany('App\Models\PackageInvoice');
    }

    public function follow_ups()
    {
        return $this->hasMany('App\Models\FollowUp', 'customer_id');
    }

    public function last_package_invoice()
    {
        return $this->hasOne('App\Models\PackageInvoice')->orderBy('id', 'DESC')->latest();
    }

    public function last_invoice()
    {
        return $this->hasOne('App\Models\Invoice')->orderBy('id', 'DESC')->latest();
    }
}

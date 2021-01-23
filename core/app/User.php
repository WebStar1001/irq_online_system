<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'address' => 'object',
        'ver_code_send_at' => 'datetime'
    ];

    public function login_logs()
    {
        return $this->hasMany(UserLogin::class)->withTrashed();
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function wallets()
    {
        return $this->hasMany('App\Wallet', 'user_id');
    }

    public function docver()
    {
        return $this->hasOne('App\Docver', 'id', 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(Trx::class);
    }


    public function api_keys()
    {
        return $this->hasMany('App\UserApiKey', 'user_id', 'id');
    }


    public function moneyTransfers()
    {
        return $this->hasMany('App\MoneyTransfer', 'user_id')->where('status', '!=', 0);
    }

    public function exchangeMoneys()
    {
        return $this->hasMany('App\ExchangeMoney', 'user_id');
    }

    public function requestMoneys()
    {
        return $this->hasMany('App\RequestMoney', 'sender_id');
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice', 'user_id');
    }

    public function vouchers()
    {
        return $this->hasMany('App\Voucher', 'user_id');
    }


    // SCOPES

    public function getFullnameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function scopeBanned()
    {
        return $this->where('status', 0);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', 0);
    }

    public function scopeSmsUnverified()
    {
        return $this->where('sv', 0);
    }
}

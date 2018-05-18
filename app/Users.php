<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = "users";

    public function Orders()
    {
    	return $this->hasMany('App\Orders','user_id','id');
    }

    public function OrderPendings()
    {
    	return $this->hasMany('App\OrderPendings','user_id','id');
    }

    public function OrderHistories()
    {
    	return $this->hasMany('App\OrderHistories','user_id','id');
    }

    public function UserTradeHistories()
    {
    	return $this->hasMany('App\UserTradeHistories','user_id','id');
    }

    public function Wallets()
    {
    	return $this->hasMany('App\Wallets','user_id','id');
    }

    public function DepositPending()
    {
    	return $this->hasMany('App\DepositPending','user_id','id');
    }

    public function DepositHistories()
    {
    	return $this->hasMany('App\DepositHistories','user_id','id');
    }

     public function WithdrawPending()
    {
    	return $this->hasMany('App\WithdrawPending','user_id','id');
    }

    public function WithdrawHistories()
    {
    	return $this->hasMany('App\WithdrawHistories','user_id','id');
    }   
}

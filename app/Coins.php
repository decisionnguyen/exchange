<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coins extends Model
{
    protected $table = "coins";

    public function Markets()
    {
    	return $this->hasMany('App\Markets','coin_id_first','id');
    }

	public function WithdrawFees()
    {
    	return $this->hasMany('App\WithdrawFees','coin_id','id');
    }

    public function Wallets()
    {
    	return $this->hasMany('App\Wallets','coin_id','id');
    }

    public function DepositPending()
    {
    	return $this->hasMany('App\DepositPending','coin_id','id');
    }

    public function DepositHistories()
    {
    	return $this->hasMany('App\DepositHistories','coin_id','id');
    }

    public function WithdrawPending()
    {
    	return $this->hasMany('App\WithdrawPending','coin_id','id');
    }

    public function WithdrawHistories()
    {
    	return $this->hasMany('App\WithdrawHistories','coin_id','id');
    }


}

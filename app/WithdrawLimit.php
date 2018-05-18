<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawLimit extends Model
{
    protected $table = "withdraw_limit";

   	public function Coins()
    {
    	return $this->belongsTo('App\Coins','coin_id','id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawFees extends Model
{
    protected $table = "withdraw_fees";

    public function Coins()
    {
    	return $this->belongsTo('App\Coins','coin_id','id');
    }
}

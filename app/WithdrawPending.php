<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawPending extends Model
{
    protected $table = "withdraw_pending";

   	public function Coins()
    {
    	return $this->belongsTo('App\Coins','coin_id','id');
    }

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

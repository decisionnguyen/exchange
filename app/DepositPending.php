<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DepositPending extends Model
{
    protected $table = "deposit_pending";

    public function Coins()
    {
    	return $this->belongsTo('App\Coins','coin_id','id');
    }

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

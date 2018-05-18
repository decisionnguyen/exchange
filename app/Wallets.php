<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallets extends Model
{
    protected $table = "wallets";

    public function Coins()
    {
    	return $this->belongsTo('App\Coins','coin_id','id');
    }

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

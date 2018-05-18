<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuyLimit extends Model
{
    protected $table = "buy_limit";

    public function Markets()
    {
    	return $this->belongsTo('App\Markets','market_id','id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellLimit extends Model
{
    protected $table = "sell_limit";

    public function Markets()
    {
    	return $this->belongsTo('App\Markets','market_id','id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarketTradeHistories extends Model
{
     protected $table = "market_trade_histories";

    public function Markets()
    {
    	return $this->belongsTo('App\Markets','market_id','id');
    }
}

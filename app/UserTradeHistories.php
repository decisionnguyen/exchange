<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTradeHistories extends Model
{
    protected $table = "user_trade_histories";

    public function Markets()
    {
    	return $this->belongsTo('App\Markets','market_id','id');
    }

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Markets extends Model
{
    protected $table = "markets";

    public function Orders()
    {
    	return $this->hasMany('App\Orders','market_id','id');
    }

    public function OrderPendings()
    {
    	return $this->hasMany('App\OrderPendings','market_id','id');
    }

    public function OrderHistories()
    {
    	return $this->hasMany('App\OrderHistories','market_id','id');
    }

	public function UserTradeHistories()
    {
    	return $this->hasMany('App\UserTradeHistories','market_id','id');
    }

    public function MarketTradeHistories()
    {
    	return $this->hasMany('App\MarketTradeHistories','market_id','id');
    }

    public function ExchangeFees()
    {
    	return $this->hasMany('App\ExchangeFees','market_id','id');
    }
}

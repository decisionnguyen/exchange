<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExchangeFees extends Model
{
    protected $table = "exchange_fees";

    public function Markets()
    {
    	return $this->belongsTo('App\Markets','market_id','id');
    }
}

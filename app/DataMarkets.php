<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataMarkets extends Model
{
    protected $table = "data_markets";

    public function Markets()
    {
    	return $this->belongsTo('App\Markets','market_id','id');
    }
}
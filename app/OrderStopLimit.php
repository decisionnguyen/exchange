<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderStopLimit extends Model
{
    protected $table = "order_stop_limit";

    public function Markets()
    {
    	return $this->belongsTo('App\Markets','market_id','id');
    }

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

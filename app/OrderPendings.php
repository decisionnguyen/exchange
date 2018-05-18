<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderPendings extends Model
{
     protected $table = "order_pendings";

      public function Markets()
    {
    	return $this->belongsTo('App\Markets','market_id','id');
    }

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

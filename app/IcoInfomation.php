<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IcoInfomation extends Model
{
    protected $table = "ico_infomation";

    public function Markets()
    {
    	return $this->belongsTo('App\Markets','market_id','id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwoAuth extends Model
{
    protected $table = "two_auth";

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
	protected $table = "referral";
	
     public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

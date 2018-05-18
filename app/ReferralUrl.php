<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferralUrl extends Model
{
	protected $table = "referral_url";

     public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

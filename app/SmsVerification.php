<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsVerification extends Model
{
    protected $table = "sms_verification";

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

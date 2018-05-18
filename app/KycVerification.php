<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KycVerification extends Model
{
    protected $table = "kyc_verification";

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

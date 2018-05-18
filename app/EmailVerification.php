<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $table = "email_verification";

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailForgotPassword extends Model
{
    protected $table = "email_forgot_password";

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

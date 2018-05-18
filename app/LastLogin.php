<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LastLogin extends Model
{
    protected $table = "last_login";

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

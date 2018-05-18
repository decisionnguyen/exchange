<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailConfirm extends Model
{
    protected $table = "email_confirm";

    public function Users()
    {
    	return $this->belongsTo('App\Users','user_id','id');
    }
}

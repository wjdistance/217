<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPoints extends Model
{

    //用户
    public function user() {
        return $this->belongsTo('App\Models\User');
    }

}

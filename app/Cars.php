<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    //
    protected $table = 'cars';

    //relacion de muchos a uno
    public function user(){
      return $this->belongsTo('App\User', 'user_id');
    }
}

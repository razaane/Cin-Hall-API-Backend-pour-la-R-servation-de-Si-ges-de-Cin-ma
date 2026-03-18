<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    public function ticket(){
        return $this->hasOne(Ticket::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}

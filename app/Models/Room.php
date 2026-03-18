<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable=[
        'name',
        'type',
        'total_seats'
    ];
    
    public function seances(){
        return $this->hasMany(Seance::class);
    }
}

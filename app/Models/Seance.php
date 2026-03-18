<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seance extends Model
{
    protected $fillable=[
        'room_id',
        'start_time',
        'end_time',
        'type'
    ];
    public function room(){

        return $this->belongsTo(Room::class);
    }

    public function reservations(){
        return $this->hasMany(Reservation::class, 'seance_id', 'id');
    }
}

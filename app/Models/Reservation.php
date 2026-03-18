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
    
    public function seance(){
        return $this->belongsTo(Seance::class);
    }
}

  protected $fillable=[
    'seance_id',
    'user_id',
    'number_of_seats',
    'status'
  ];
  public function Seance(){
    $this->belongsTo(Seance::class);
  }
   public function user()
    {
        return $this->belongsTo(User::class);
    }
    public static function avlblSeats($seance_id){
        $seance=Seance::find($seance_id);
        $reservedSeats = $seance->reservations()->whereIn('status',['pending','confirmed','paid'])->sum('number_of_seats');
        $availableSeats = $seance->room->total_seats - $reservedSeats;
        return $availableSeats;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable=[
        'reservation_id',
        'user_id',
        'qr_code',
        'pdf_path'
    ];

    public function reservation(){
        return $this->belongsTo(Reservation::class);
    }

    public function user(){
        return $this->belongsTo(users::class);
    }
}

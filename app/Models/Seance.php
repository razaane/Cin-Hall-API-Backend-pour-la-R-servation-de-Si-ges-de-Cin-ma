<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Film;

class Seance extends Model
{
     use HasFactory;

    protected $fillable = [
        'film_id',
        'room_id',
        'start_time',
        'language',
        'type'
    ];

    public function film()
    {
        return $this->belongsTo(Film::class);
    }
}

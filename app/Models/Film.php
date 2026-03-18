<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Genre;
use App\Models\Seance;

class Film extends Model
{
    use HasFactory;
    protected $fillable = [
        'genre_id',
        'title',
        'description',
        'image',
        'duration',
        'min_age',
        'trailer_url'
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function seances()
    {
        return $this->hasMany(Seance::class);
    }
}

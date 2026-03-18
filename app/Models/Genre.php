<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Film;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function films()
    {
        return $this->hasMany(Film::class);
    }
}

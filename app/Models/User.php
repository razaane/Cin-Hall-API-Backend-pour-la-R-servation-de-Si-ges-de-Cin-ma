<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar'
    ];

    protected $hidden = [
        'password'
    ];

    // Required by JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // Required by JWT
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }
}
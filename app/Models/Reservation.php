<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    // Constantes pour les statuts
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    // Champs qu'on peut remplir
    protected $fillable = [
        'seance_id',
        'user_id',
        'number_of_seats',
        'status',
        'expires_at'
    ];

    // Conversion automatique des types
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * RELATIONS
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seance()
    {
        return $this->belongsTo(Seance::class);
    }
    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    public function room()
{
    return $this->belongsTo(Room::class);
}

    /**
     * MÉTHODES
     */
    public static function avlblSeats($seance_id)
    {
        $seance = Seance::with('room')->find($seance_id);
        
        if (!$seance || !$seance->room) {
            return 0;
        }

        $reservedSeats = self::where('seance_id', $seance_id)
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_PAID])
            ->sum('number_of_seats');

        return max(0, $seance->room->total_seats - $reservedSeats);
    }

    // Vérifier si la réservation est expirée
    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    // Vérifier si elle est en attente
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    // Vérifier si elle est payée
    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;

    }
}
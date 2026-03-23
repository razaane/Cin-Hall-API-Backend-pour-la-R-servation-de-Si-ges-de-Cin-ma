<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'amount',
        'method',
        'transaction_id',
        'status'
    ];

    // Relation avec Reservation
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    // Helper: Vérifier si paiement réussi
    public function estReussi()
    {
        return $this->status === 'success';
    }

    // Helper: Vérifier si en attente
    public function enAttente()
    {
        return $this->status === 'pending';
    }

    // Helper: Vérifier si échoué
    public function estEchoue()
    {
        return $this->status === 'failed';
    }
}
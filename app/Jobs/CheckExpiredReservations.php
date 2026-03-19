<?php

namespace App\Jobs;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class CheckExpiredReservations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $reservationId;

    public function __construct($reservationId)
    {
        $this->reservationId = $reservationId;
    }

// Ce code vérifie si une réservation est toujours en attente après 15 minutes
// Si oui, il la marque comme "expirée"
    public function handle(): void
    {
        // Cherche la réservation dans la base de données
        $reservation = Reservation::find($this->reservationId);
        
        // Si elle existe ET qu'elle est encore en attente ET qu'elle a expiré
        if ($reservation && $reservation->status === 'pending' && Carbon::now()->gt($reservation->expires_at)) {
            
            // Alors on la marque comme expirée
            $reservation->update(['status' => 'expired']);
        }
    }
}
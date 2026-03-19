<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeReservationRequest;
use App\Models\Reservation;
use App\Models\Seance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ExpireReservation;
   use App\Http\Requests\UpdateReservationRequest;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::with('seance')
        ->where('user_id', auth()->id())
        ->get();

    return response()->json($reservations);
    }

    public function showSeances($room_id)
    {
        $seances = Seance::where('room_id', $room_id)->get(); // ← CORRIGÉ
        return response()->json($seances);
    }
    
    public function store(storeReservationRequest $request)
    {
        $seance = Seance::find($request->seance_id);

        if (!$seance) {
            return response()->json(['error' => 'Séance introuvable'], 404);
        }

        $availableSeats = Reservation::avlblSeats($request->seance_id);
        $numberOfSeats = (int) $request->number_of_seats;

        if ($numberOfSeats > $availableSeats) {
            return response()->json(['error' => 'Pas assez de places disponibles'], 400);
        }

        if (strtolower($seance->type) === 'vip' && $numberOfSeats % 2 !== 0) {
            return response()->json([
                'error' => 'Pour les séances VIP, le nombre de places doit être pair.'
            ], 400);
        }

        $reservation = Reservation::create([
            'seance_id' => $seance->id,
            'user_id' => auth()->id(),
            'status' => 'pending',
            'number_of_seats' => $numberOfSeats,
            'expires_at' => now()->addMinutes(15) // ← AJOUTÉ
        ]);

        ExpireReservation::dispatch($reservation->id);

        return response()->json([
            'message' => 'Réservation créée !',
            'reservation' => $reservation
        ], 201);
    }

    public function show(Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }
        
        return response()->json($reservation->load('seance'));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        if ($reservation->status === 'cancelled' || $reservation->status === 'expired') {
            return response()->json([
                'error' => 'Impossible de modifier cette réservation'
            ], 400);
        }

        $seance = $reservation->seance;
        $numberOfSeats = (int) $request->number_of_seats;

        if (strtolower($seance->type) === 'vip' && $numberOfSeats % 2 !== 0) {
            return response()->json([
                'error' => 'Pour VIP, nombre pair'
            ], 400);
        }

        $availableSeats = Reservation::avlblSeats($seance->id) + $reservation->number_of_seats;

        if ($numberOfSeats > $availableSeats) {
            return response()->json([
                'error' => 'Pas assez de places'
            ], 400);
        }

        $reservation->update([
            'number_of_seats' => $numberOfSeats
        ]);

        return response()->json([
            'message' => 'Réservation mise à jour',
            'reservation' => $reservation
        ]);
    }

    public function destroy(Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }
        
        $reservation->update(['status' => 'cancelled']); // ← CORRIGÉ
        
        return response()->json([    
            'message' => 'Réservation annulée',
            'reservation' => $reservation
        ]);
    }

    // NOUVELLE MÉTHODE
    public function checkExpiration($id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        if ($reservation->status === 'pending' && now()->gt($reservation->expires_at)) {
            $reservation->update(['status' => 'expired']);
        }
        
        return response()->json([
            'reservation' => $reservation,
            'is_expired' => $reservation->status === 'expired',
            'expires_at' => $reservation->expires_at
        ]);
    }
}
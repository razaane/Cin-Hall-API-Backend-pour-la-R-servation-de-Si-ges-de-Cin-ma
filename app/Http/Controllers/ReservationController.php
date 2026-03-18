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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function showSeances($room_id){
        $seances=Seance::where($room_id)->get();
        return response()->json($seances);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(storeReservationRequest $request)
{
    $seance = Seance::find($request->seance_id);

    if (!$seance) {
        return response()->json(['error' => 'Séance introuvable']);
    }

    $availableSeats = Reservation::avlblSeats($request->seance_id);
    $numberOfSeats = (int) $request->number_of_seats;

    if ($numberOfSeats > $availableSeats) {
        return response()->json(['error' => 'Pas assez de places disponibles']);
    }

    if (strtolower($seance->type) === 'vip' && $numberOfSeats % 2 !== 0) {
        return response()->json([
            'error' => 'Pour les séances VIP, le nombre de places doit être pair.'
        ]);
    }

    $reservation = Reservation::create([
        'seance_id' => $seance->id,
        'user_id' => auth()->id(),
        'status' => 'pending',
        'number_of_seats' => $numberOfSeats,
    ]);

    ExpireReservation::dispatch($reservation->id);

    return response()->json([
        'message' => 'Réservation créée !',
        'reservation' => $reservation
    ]);
}



    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
    return response()->json($reservation->load('seance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

public function update(UpdateReservationRequest $request, Reservation $reservation)
{
    if ($reservation->user_id !== auth()->id()) {
        return response()->json([
            'error' => 'Non autorisé'
        ], 403);
    }

    if ($reservation->status === 'cancelled') {
        return response()->json([
            'error' => 'Impossible de modifier cette réservation'
        ]);
    }

    $seance = $reservation->seance;
    $numberOfSeats = (int) $request->number_of_seats;

    if (strtolower($seance->type) === 'vip' && $numberOfSeats % 2 !== 0) {
        return response()->json([
            'error' => 'Pour VIP, nombre pair'
        ]);
    }


    $availableSeats = Reservation::avlblSeats($seance->id) + $reservation->number_of_seats;

    if ($numberOfSeats > $availableSeats) {
        return response()->json([
            'error' => 'Pas assez de places'
        ]);
    }

    $reservation->update([
        'number_of_seats' => $numberOfSeats
    ]);

    return response()->json([
        'message' => 'Réservation mise à jour',
        'reservation' => $reservation
    ]);
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
      $reservation->status='cancelled';
        $reservation->save();
        return response()->json([    'message' => 'Réservation annulée',
            'reservation' => $reservation]);
    }
}

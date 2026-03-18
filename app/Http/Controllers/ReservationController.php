<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeReservationRequest;
use App\Models\Reservation;
use App\Models\Seance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ExpireReservation;
class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $seance=Seance::find($request->seance_id);

        if (!$seance) {
            dd('Séance introuvable');
        }

        $availableSeats=Reservation::avlblSeats($request->seance_id);

        if($request->number_of_seats > $availableSeats)
            return response()->json(['error'=>'pas de places dispo']);

        $reservation=Reservation::create([
            'seance_id' => $seance->id,
            'user_id' => auth()->id(),
            'status' => 'pending',
            'number_of_seats' => $request->number_of_seats,
        ]);

        ExpireReservation::dispatch($reservation->id);

        return response()->json([ 'message' => 'Réservation créée !',
        'reservation' => $reservation]);
    }

    public function cancel(Reservation $reservation){
        $reservation->status='cancelled';
        $reservation->save();
        return response()->json([    'message' => 'Réservation annulée',
            'reservation' => $reservation]);
    }
    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        //
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
    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        //
    }
}

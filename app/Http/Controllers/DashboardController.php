<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Film;
use App\Models\Reservation;
use App\Models\Seance;
use App\Models\Ticket;
use App\Models\Room;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $type = $request->type;

        // ===== Vue d’ensemble =====
        $vueEnsemble = [
            'totalFilms' => Film::count(),
            'totalSeances' => Seance::count(),
            'totalReservations' => Reservation::count(),
            'totalUsers' => User::count(),
        ];

        // ===== Taux d’occupation des séances =====
        // $seancesQuery = Seance::withCount('reservations');

        // if ($from && $to) {
        //     $seancesQuery->whereBetween('start_time', [$from, $to]);
        // }
        // if ($type) {
        //     $seancesQuery->where('type', $type);
        // }

        // $seances = $seancesQuery->with('room')->get()->map(function($s){
        //     return [
        //         'date' => $s->start_time,
        //         'type' => $s->type,
        //         'tickets' => $s->reservations_count,
        //         'taux' => $s->room->total_seats > 0 ? ($s->reservations_count / $s->room->total_seats) * 100 : 0
        //     ];
        // });

        // ===== Nombre de tickets vendus et revenus par film =====
        $films = Film::with(['seances.reservations'])->get()->map(function($film){
                $ticketsVendus = $film->seances->sum(fn($s) => $s->reservations->count());
                $ticketPrice = 50;   
                $revenus = $ticketsVendus * $ticketPrice;
                return [
                    'id' => $film->id,
                    'title' => $film->title,
                    'tickets_vendus' => $ticketsVendus,
                    'revenus' => $revenus
                ];
        });

        //Classement des films populaires (top 5) 
        $filmsPopulaires = $films->sortByDesc('tickets_vendus')->take(5)->values();

        //  Gestion des utilisateurs 
        $users = User::select('id','name','email','role')->get();

        //  Retour JSON 
        return response()->json([
            'vueEnsemble' => $vueEnsemble,
            // 'seances' => $seances,
            'filmsData' => $films,
            'filmsPopulaires' => $filmsPopulaires,
            'users' => $users
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Film;
use App\Models\Reservation;
use App\Models\Seance;
use App\Models\Ticket;
use App\Models\Room;
use OpenApi\Attributes as OA;

class DashboardController extends Controller
{
// #[OA\Get(path: "/api/admin/dashboard",summary: "Get dashboard statistics",description: "Retourne les statistiques globales du système (films, séances, réservations, revenus, utilisateurs)",security: [["bearerAuth" => []]])]
// #[OA\Parameter(name: "from",in: "query",required: false,description: "Date de début (YYYY-MM-DD)",schema: new OA\Schema(type: "string", format: "date"))]
// #[OA\Parameter(name: "to",in: "query",required: false,description: "Date de fin (YYYY-MM-DD)",schema: new OA\Schema(type: "string", format: "date"))]
// #[OA\Parameter(name: "type",in: "query",required: false,description: "Type de séance (normale ou vip)",schema: new OA\Schema(type: "string", enum: ["normale", "vip"]))]
// #[OA\Response(response: 200,description: "Dashboard data retrieved successfully",content: new OA\JsonContent(    properties: [
//             new OA\Property(property: "vueEnsemble",type: "object",
//                 properties: [new OA\Property(property: "totalFilms", type: "integer", example: 10),new OA\Property(property: "totalSeances", type: "integer", example: 20),new OA\Property(property: "totalReservations", type: "integer", example: 50),new OA\Property(property: "totalUsers", type: "integer", example: 5),]),
//             new OA\Property(property: "seances",type: "array",items: new OA\Items(
//                     properties: [new OA\Property(property: "date", type: "string"),new OA\Property(property: "type", type: "string"),new OA\Property(property: "tickets", type: "integer"),new OA\Property(property: "taux", type: "number"),]
//                 )),
//             new OA\Property(property: "filmsData",type: "array",items: new OA\Items(
//                     properties: [new OA\Property(property: "id", type: "integer"),new OA\Property(property: "title", type: "string"),new OA\Property(property: "tickets_vendus", type: "integer"),new OA\Property(property: "revenus", type: "number"),]
//                 )),
//             new OA\Property(property: "filmsPopulaires",type: "array",items: new OA\Items(type: "object")),
//             new OA\Property(property: "users",type: "array",items: new OA\Items(type: "object")),
//         ]
//     )
// )]

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
        $seancesQuery = Seance::withCount('reservations');

        if ($from && $to) {
            $seancesQuery->whereBetween('start_time', [$from, $to]);
        }
        if ($type) {
            $seancesQuery->where('type', $type);
        }

        $seances = $seancesQuery->with('room')->get()->map(function($s){
            return [
                'date' => $s->start_time,
                'type' => $s->type,
                'tickets' => $s->reservations_count,
                'taux' => $s->room->total_seats > 0 ? ($s->reservations_count / $s->room->total_seats) * 100 : 0
            ];
        });

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
            'seances' => $seances,
            'filmsData' => $films,
            'filmsPopulaires' => $filmsPopulaires,
            'users' => $users
        ]);
    }
}
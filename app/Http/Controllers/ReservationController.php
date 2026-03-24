<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\Seance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ExpireReservation;
use OpenApi\Attributes as OA;

class ReservationController extends Controller
{
    /**
     * Liste toutes les réservations de l'utilisateur connecté
     */
    #[OA\Get(
        path: '/api/reservations',
        summary: 'Lister les réservations de l’utilisateur connecté',
        security: [['bearerAuth' => []]],
        tags: ['Reservations'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des réservations',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Reservation')
                )
            ),
            new OA\Response(response: 401, description: 'Non authentifié')
        ]
    )]
    public function index()
    {
        $reservations = Reservation::with('seance')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json($reservations);
    }

    /**
     * Affiche toutes les séances pour une salle spécifique
     */
    #[OA\Get(
        path: '/api/rooms/{room}/seances',
        summary: 'Afficher les séances d’une salle',
        security: [['bearerAuth' => []]],
        tags: ['Seances'],
        parameters: [
            new OA\Parameter(
                name: 'room',
                in: 'path',
                required: true,
                description: 'ID de la salle',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Liste des séances'),
            new OA\Response(response: 404, description: 'Salle introuvable')
        ]
    )]
    public function showSeances($room_id)
    {
        $seances = Seance::where('room_id', $room_id)->get();
        return response()->json($seances);
    }

    /**
     * Crée une réservation
     */
    #[OA\Post(
        path: '/api/reservations',
        summary: 'Créer une réservation',
        security: [['bearerAuth' => []]],
        tags: ['Reservations'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['seance_id', 'number_of_seats'],
                properties: [
                    new OA\Property(property: 'seance_id', type: 'integer', example: 1),
                    new OA\Property(property: 'number_of_seats', type: 'integer', example: 2)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Réservation créée'),
            new OA\Response(response: 400, description: 'Erreur de validation ou places insuffisantes'),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 500, description: 'Erreur serveur')
        ]
    )]
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
            'expires_at' => now()->addMinutes(15)
        ]);

        ExpireReservation::dispatch($reservation->id);

        return response()->json([
            'message' => 'Réservation créée !',
            'reservation' => $reservation
        ], 201);
    }

    /**
     * Affiche une réservation spécifique
     */
    #[OA\Get(
        path: '/api/reservations/{reservation}',
        summary: 'Afficher une réservation',
        security: [['bearerAuth' => []]],
        tags: ['Reservations'],
        parameters: [
            new OA\Parameter(
                name: 'reservation',
                in: 'path',
                required: true,
                description: 'ID de la réservation',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Succès'),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Non autorisé'),
            new OA\Response(response: 404, description: 'Non trouvée')
        ]
    )]
    public function show(Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        return response()->json($reservation->load('seance'));
    }

    /**
     * Met à jour une réservation
     */
    #[OA\Put(
        path: '/api/reservations/{reservation}',
        summary: 'Mettre à jour une réservation',
        security: [['bearerAuth' => []]],
        tags: ['Reservations'],
        parameters: [
            new OA\Parameter(
                name: 'reservation',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['number_of_seats'],
                properties: [
                    new OA\Property(property: 'number_of_seats', type: 'integer', example: 4)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Réservation mise à jour'),
            new OA\Response(response: 400, description: 'Erreur'),
            new OA\Response(response: 401, description: 'Non authentifié')
        ]
    )]
    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        if (in_array($reservation->status, ['cancelled', 'expired'])) {
            return response()->json(['error' => 'Impossible de modifier cette réservation'], 400);
        }

        $seance = $reservation->seance;
        $numberOfSeats = (int) $request->number_of_seats;

        if (strtolower($seance->type) === 'vip' && $numberOfSeats % 2 !== 0) {
            return response()->json(['error' => 'Pour VIP, le nombre de places doit être pair'], 400);
        }

        $availableSeats = Reservation::avlblSeats($seance->id) + $reservation->number_of_seats;

        if ($numberOfSeats > $availableSeats) {
            return response()->json(['error' => 'Pas assez de places'], 400);
        }

        $reservation->update(['number_of_seats' => $numberOfSeats]);

        return response()->json([
            'message' => 'Réservation mise à jour',
            'reservation' => $reservation
        ]);
    }

    /**
     * Annule une réservation
     */
    #[OA\Delete(
        path: '/api/reservations/{reservation}',
        summary: 'Annuler une réservation',
        security: [['bearerAuth' => []]],
        tags: ['Reservations'],
        parameters: [
            new OA\Parameter(
                name: 'reservation',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Réservation annulée'),
            new OA\Response(response: 401, description: 'Non authentifié'),
            new OA\Response(response: 403, description: 'Non autorisé')
        ]
    )]
    public function destroy(Reservation $reservation)
    {
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $reservation->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Réservation annulée',
            'reservation' => $reservation
        ]);
    }

    /**
     * Vérifie l'expiration
     */
    #[OA\Get(
        path: '/api/reservations/{reservation}/expiration',
        summary: 'Vérifie si une réservation a expiré',
        security: [['bearerAuth' => []]],
        tags: ['Reservations'],
        parameters: [
            new OA\Parameter(
                name: 'reservation',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Statut d’expiration')
        ]
    )]
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

<?php
namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Reservation",       
    title: "Reservation",
    description: "Une réservation d’une séance",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "user_id", type: "integer", example: 10),
        new OA\Property(property: "seance_id", type: "integer", example: 5),
        new OA\Property(property: "number_of_seats", type: "integer", example: 2),
        new OA\Property(property: "status", type: "string", example: "pending"),
        new OA\Property(property: "expires_at", type: "string", format: "date-time", example: "2026-03-24T14:30:00Z")
    ]
)]
class ReservationSchema {}

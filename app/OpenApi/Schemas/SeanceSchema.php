<?php
namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Seance",
    title: "Seance",
    description: "Une séance d’une salle",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "room_id", type: "integer", example: 2),
        new OA\Property(property: "type", type: "string", example: "VIP"),
        new OA\Property(property: "start_time", type: "string", format: "date-time", example: "2026-03-25T10:00:00Z"),
        new OA\Property(property: "end_time", type: "string", format: "date-time", example: "2026-03-25T12:00:00Z")
    ]
)]
class SeanceSchema {}

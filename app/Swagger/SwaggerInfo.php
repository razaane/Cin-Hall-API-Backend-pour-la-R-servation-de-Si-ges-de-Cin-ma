<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="CineHall API",
 *     version="1.0.0",
 *     description="Cinema Reservation API"
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000/api",
 *     description="Local server"
 * )
 */
class SwaggerInfo
{
}
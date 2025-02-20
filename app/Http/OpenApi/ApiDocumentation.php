<?php

namespace App\Http\OpenApi;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Blossom Buddy API",
 *     description="API documentation for Blossom Buddy application",
 *     @OA\Contact(
 *         email="contact@example.com"
 *     )
 * )
 * @OA\Server(
 *     description="Local API server",
 *     url="http://localhost:8000/api"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer"
 * )
 */
class ApiDocumentation {
}

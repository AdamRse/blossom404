<?php

namespace App\Http\OpenApi;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Blossom Buddy API Documentation",
 *     description="API documentation for Blossom Buddy plant care application",
 *     @OA\Contact(
 *         email="contact@example.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer"
 * )
 */
class ApiInfo {
}

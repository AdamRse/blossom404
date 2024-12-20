<?php

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

/**
 * @OA\Post(
 *     path="/register",
 *     summary="Register a new user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"firstname","lastname","email","password"},
 *             @OA\Property(property="firstname", type="string", example="John"),
 *             @OA\Property(property="lastname", type="string", example="Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string"),
 *             @OA\Property(property="token_type", type="string", example="Bearer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */

/**
 * @OA\Post(
 *     path="/login",
 *     summary="Login user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string"),
 *             @OA\Property(property="token_type", type="string", example="Bearer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials"
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/plant",
 *     summary="Get all plants",
 *     tags={"Plants"},
 *     @OA\Response(
 *         response=200,
 *         description="List of all plants",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="common_name", type="string"),
 *                 @OA\Property(property="watering_general_benchmark", type="object",
 *                     @OA\Property(property="value", type="string"),
 *                     @OA\Property(property="unit", type="string")
 *                 )
 *             )
 *         )
 *     )
 * )
 */

// Continuez avec la documentation des autres routes...

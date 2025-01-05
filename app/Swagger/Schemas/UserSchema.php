<?php

namespace App\Swagger\Schemas;

use OpenApi\Annotations as OA;

/**
     * @OA\Schema(
     *     schema="User",
     *     type="object",
     *     description="Пользователь",
     *     required={"id", "name", "email"},
     *     @OA\Property(property="id", type="integer", example=1, description="The user's unique identifier"),
 *         @OA\Property(property="name", type="string", example="Jackeline Walsh MD", description="The user's name"),
 *         @OA\Property(property="email", type="string", example="awaelchi@example.com", description="The user's email address"),
 *         @OA\Property(property="email_verified_at", type="string", format="date-time", example="2025-01-05T11:04:39.000000Z", description="Timestamp when the email was verified"),
 *         @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-05T11:04:39.000000Z", description="Timestamp when the user was created"),
 *         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-05T11:04:39.000000Z", description="Timestamp when the user was last updated"),
 *         @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null, description="Timestamp when the user was deleted, null if not deleted")
     * )
     */
    class UserSchema
    {
    
    }
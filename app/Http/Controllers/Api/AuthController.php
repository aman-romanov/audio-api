<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

     /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Авторизация",
     *     description="Авторизация пользователя и создание токена",
     *     operationId="login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password"},
     *                 @OA\Property(property="email", type="string", example="user@example.com", description="User email"),
     *                 @OA\Property(property="password", type="string", format="password", example="password123", description="User password")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Авторизация прошла успешно",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="token_example_here", description="JWT token"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *     )
     * )
     */
    public function login(UserRequest $request){
        $data = $request->validated();
        $user = $this->userService->verifyUser($data);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Выход с аккаунта",
     *     description="Выход и удаление токена",
     *     operationId="logout",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         description="Bearer token",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Вы вышли из аккаунта",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Вы вышли из аккаунта")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function logout(User $user){

        $user->tokens()->delete();

        return response()->json([
            'message' => 'Вы вышли из аккаунта'
        ], 200);

    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Регистрация",
     *     description="Регистрация нового пользователя",
     *     operationId="register",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email", "password", "password_verify"},
     *                 @OA\Property(property="name", type="string", example="John Doe", description="User name"),
     *                 @OA\Property(property="email", type="string", example="newuser@example.com", description="User email"),
     *                 @OA\Property(property="password", type="string", format="password", example="password123", description="User password"),
     *                 @OA\Property(property="password_verify", type="string", format="password", example="password123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Регистрация прошла успешно!",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Регистрация прошла успешно!"),
     *             @OA\Property(property="token", type="string", example="token_example_here"),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */

    public function register(UserRequest $request){

        $data = $request->validated();

        $user = $this->userService->createUser($data);
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Регистрация прошла успешно!',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/user/{user}",
     *     summary="Изменение данных пользователя",
     *     description="Изменение данных пользователя по ID",
     *     operationId="updateUser",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email", "password", "password_verify"},
     *                 @OA\Property(property="name", type="string", example="John Doe", description="User name"),
     *                 @OA\Property(property="email", type="string", example="newuser@example.com", description="User email"),
     *                 @OA\Property(property="password", type="string", format="password", example="password123", description="User password"),
     *                 @OA\Property(property="password_verify", type="string", format="password", example="password123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User data updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Данные изменены")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     )
     * )
     */

    public function update(UserRequest $request, string $id){
        $data = $request->validated();
        $user = $this->userService->updateUser($data, $id);

        return response()->json([
            'message' => 'Данные изменены'
        ], 201);
    }
}

<?php

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;

Route::get('/', function(){
    return redirect()->route('posts.index');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::put('/user/{user}', [AuthController::class, 'update'])->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);

Route::apiResource('posts', PostController::class)->middleware('auth:sanctum');
Route::apiResource('tags', TagController::class)->middleware('auth:sanctum');

Route::get('/user/{user}/posts', [PostController::class, 'userPosts']);
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TareaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Paso 1 - API key: toda la API exige el header X-API-KEY con una clave válida.
Route::middleware('api.key')->group(function () {
    // Paso 2 - JWT: registro y login son públicos (solo piden la API key) y devuelven el token.
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // El resto exige además "Authorization: Bearer <token>".
    // Todavía no se comprueba de quién es cada recurso: eso es el paso de BOLA/roles.
    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);

        // Los usuarios se crean con /register.
        Route::apiResource('users', UserController::class)->except('store');
        Route::apiResource('tareas', TareaController::class);
    });
});

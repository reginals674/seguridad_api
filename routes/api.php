<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TareaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Paso 4 - Rate Limiter: "throttle:api" va primero para contar también las peticiones que fallan
// en la API key o el token (si fuera después, un atacante podría probar claves sin límite).
// Paso 1 - API key: toda la API exige el header X-API-KEY con una clave válida.
Route::middleware(['throttle:api', 'api.key'])->group(function () {
    // Paso 2 - JWT: registro y login son públicos (solo piden la API key) y devuelven el token.
    // Llevan además el límite estricto "login" contra fuerza bruta de contraseñas.
    Route::middleware('throttle:login')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    // El resto exige además "Authorization: Bearer <token>".
    // Paso 3 - BOLA/IDOR: qué recurso puede tocar cada usuario lo deciden las Policies (app/Policies).
    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);

        // Los usuarios se crean con /register.
        Route::apiResource('users', UserController::class)->except('store');
        Route::apiResource('tareas', TareaController::class);
    });
});

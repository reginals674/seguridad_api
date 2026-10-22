<?php

use App\Http\Controllers\Api\TareaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Paso 1 - API key: toda la API exige el header X-API-KEY con una clave válida.
// Todavía no hay usuarios autenticados: quien tenga la clave puede hacer todo.


Route::middleware('api.key')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('tareas', TareaController::class);
});


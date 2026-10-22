<?php

use App\Http\Controllers\Api\TareaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// API sin seguridad: cualquiera puede leer, crear, editar y borrar todo.
Route::apiResource('users', UserController::class);
Route::apiResource('tareas', TareaController::class);

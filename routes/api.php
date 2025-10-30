<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DietaController;
use App\Http\Controllers\ComidaDietaController;
use App\Http\Controllers\ComidaUsuarioController;
use App\Http\Controllers\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas CRUD
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('dietas', DietaController::class);
Route::apiResource('comidas-dietas', ComidaDietaController::class);
Route::apiResource('comidas-usuarios', ComidaUsuarioController::class);

// Rutas LOGIN
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
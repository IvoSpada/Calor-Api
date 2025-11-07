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


// Rutas LOGIN (Públicas)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// --- INICIO DE LA CORRECCIÓN DE SEGURIDAD ---
// Todas las rutas de datos DEBEN estar dentro de la autenticación
Route::middleware('auth:sanctum')->group(function () {
    
    // Rutas CRUD (Ahora protegidas)
    Route::apiResource('usuarios', UsuarioController::class);
    Route::apiResource('dietas', DietaController::class);
    Route::apiResource('comidas-dietas', ComidaDietaController::class);
    Route::apiResource('comidas-usuarios', ComidaUsuarioController::class);

    // Rutas de Auth (Protegidas)
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
// --- FIN DE LA CORRECCIÓN ---

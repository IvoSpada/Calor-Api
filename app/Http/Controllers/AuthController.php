<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Importante para el hashing
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario.
     * POST /api/register
     */
    public function register(Request $request)
    {
        // Validación de los datos recibidos (la misma que en UsuarioController)
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:usuario,email',
            'password' => 'required|string|min:6',
            'edad' => 'required|integer|min:0',
            'altura' => 'nullable|numeric|min:0',
            'peso' => 'nullable|numeric|min:0',
            'genero' => 'nullable|in:masculino,femenino,otro',
            'objetivo' => 'required|string|in:perder_peso,mantener,ganar_peso',
            'patologias' => 'nullable|string',
            'ejercicio' => 'nullable|string',
            'premium' => 'nullable|integer|in:0,1,2',
        ]);

        // Si falla la validación, devolvemos errores
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'status' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Datos validados
        $validated = $validator->validated();

        // --- Hasheamos la contraseña ---
        // Usamos Hash::make() para consistencia con el login (Hash::check)
        $validated['password'] = Hash::make($validated['password']);

        // --- Asignar valor por defecto si 'premium' no se envía ---
        if (!isset($validated['premium'])) {
            $validated['premium'] = 0; // 0 = no premium
        }

        try {
            // Crear el usuario
            $usuario = Usuario::create($validated);
        } catch (\Exception $e) {
            // Si hay error al crear el usuario (ej: DB), devolvemos 500
            return response()->json([
                'message' => 'Error en la creación del usuario',
                'status' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }

        // --- Auto-Login: Crear token después del registro ---
        // (Tu hook useAuth.ts espera este token para el auto-login)
        $token = $usuario->createToken('auth_token')->plainTextToken;

        // Retornamos el usuario y el token con status 201
        return response()->json([
            'usuario' => $usuario,
            'token' => $token,
            'status' => 201,
        ], 201);
    }

    /**
     * Iniciar sesión (Login).
     * POST /api/login
     */
    public function login(Request $request)
    {
        // 1. Validar que vengan email y password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Intentar autenticar usando el guard 'web' (sesiones) NO FUNCIONA EN API.
        // --- CORRECCIÓN: Usar Auth::attempt (que usa Hash::check) ---

        // Buscamos al usuario por email
        $usuario = Usuario::where('email', $request->email)->first();

        // 3. Verificar si el usuario existe Y la contraseña es correcta
        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            // Si falla, devolvemos 401 Unauthorized
            return response()->json([
                'message' => 'Las credenciales proporcionadas son incorrectas.',
                'status' => 401,
            ], 401);
        }

        // 4. Si la autenticación es exitosa, creamos un token de Sanctum
        $token = $usuario->createToken('auth_token')->plainTextToken;

        // 5. Retornamos el usuario y el token
        // (Tu hook useAuth.ts espera esta estructura)
        return response()->json([
            'usuario' => $usuario,
            'token' => $token,
        ], 200);
    }

    /**
     * Obtener el usuario autenticado.
     * GET /api/me
     */
    public function me(Request $request)
    {
        // Sanctum (gracias al middleware) ya identificó al usuario
        return response()->json($request->user(), 200);
    }

    /**
     * Cerrar sesión (Logout).
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        try {
            // Revocar el token actual que se usó para la autenticación
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Sesión cerrada correctamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
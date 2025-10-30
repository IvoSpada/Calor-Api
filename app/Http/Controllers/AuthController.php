<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:usuario,email',
            'password' => 'required|string|confirmed|min:6',
            'peso' => 'required|numeric|min:0',
            'altura' => 'required|numeric|min:0',
            'edad' => 'required|integer|min:0',
            'objetivo' => 'required|string|in:perder_peso,mantener,ganar_peso'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();
        $validated['password'] = Hash::make($validated['password']);

        $usuario = Usuario::create($validated);

        // Crear token de acceso
        $token = $usuario->createToken('authToken')->plainTextToken;

        return response()->json([
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'email' => $usuario->email
            ],
            'token' => $token
        ], 201);
    }

    // Login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Datos inválidos'], 400);
        }

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $usuario->createToken('authToken')->plainTextToken;

        return response()->json([
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'peso' => $usuario->peso,
                'altura' => $usuario->altura,
                'edad' => $usuario->edad,
                'objetivo' => $usuario->objetivo
            ],
            'token' => $token
        ], 200);
    }

    // Usuario autenticado
    public function me(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada'], 200);
    }
}
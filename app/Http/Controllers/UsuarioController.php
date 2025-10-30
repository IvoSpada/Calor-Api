<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    /**
     * Mostrar todos los usuarios
     * GET /api/usuarios
     */
    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json($usuarios, 200);
    }

    /**
     * Crear un nuevo usuario
     * POST /api/usuarios
     */
    public function store(Request $request)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'nombre'   => 'required|string|max:100',
            'email'    => 'required|string|email|max:100|unique:usuario,email',
            'password' => 'required|string|min:6',
            'edad'     => 'required|integer|min:0',
            'altura'   => 'nullable|numeric|min:0',
            'peso'     => 'nullable|numeric|min:0',
            'genero'   => 'nullable|in:masculino,femenino,otro',
            'objetivo' => 'required|string|in:perder_peso,mantener,ganar_peso'
        ]);

        // Si falla la validación, devolvemos errores
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'status'  => 400,
                'errors'  => $validator->errors()
            ], 400);
        }

        // Datos validados
        $validated = $validator->validated();

        // Hasheamos la contraseña antes de guardar
        $validated['password'] = bcrypt($validated['password']);

        try {
            // Crear el usuario
            $usuario = Usuario::create($validated);
        } catch (\Exception $e) {
            // Si hay error al crear el usuario (ej: DB), devolvemos 500
            return response()->json([
                'message' => 'Error en la creación del usuario',
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }

        // Retornamos el usuario creado con status 201
        return response()->json([
            'usuario' => $usuario,
            'status'  => 201
        ], 201);
    }

    /**
     * Mostrar un usuario específico
     * GET /api/usuarios/{id}
     */
    public function show($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json($usuario, 200);
    }

    /**
     * Actualizar un usuario
     * PUT/PATCH /api/usuarios/{id}
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Validación de datos, campos opcionales
        $validated = $request->validate([
            'nombre'   => 'sometimes|required|string|max:100',
            'email'    => 'sometimes|required|string|email|max:100|unique:usuario,email,' . $usuario->id,
            'password' => 'sometimes|required|string|min:6',
            'edad'     => 'sometimes|required|integer|min:0',
            'altura'   => 'nullable|numeric|min:0',
            'peso'     => 'nullable|numeric|min:0',
            'genero'   => 'nullable|in:masculino,femenino,otro',
            'objetivo' => 'sometimes|required|string|in:perder_peso,mantener,ganar_peso'
        ]);

        // Si viene password, hashearlo
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        // Actualizar usuario
        $usuario->update($validated);

        return response()->json($usuario, 200);
    }

    /**
     * Eliminar un usuario
     * DELETE /api/usuarios/{id}
     */
    public function destroy($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $usuario->delete();

        return response()->json([
            'message' => 'Usuario eliminado correctamente'
        ], 200);
    }
}
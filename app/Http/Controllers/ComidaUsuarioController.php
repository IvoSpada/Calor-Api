<?php

namespace App\Http\Controllers;

use App\Models\ComidaUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComidaUsuarioController extends Controller
{
    /**
     * Mostrar todas las comidas consumidas por usuarios
     * GET /api/comidas-usuarios
     */
    public function index(Request $request) // <-- 1. Añadido Request $request
    {
        // --- INICIO DE LA CORRECCIÓN DE SEGURIDAD ---

        // Obtenemos el ID del usuario autenticado (desde el token auth:sanctum)
        $usuarioId = $request->user()->id;

        // Construimos la consulta base, filtrando SIEMPRE por el usuario
        $query = ComidaUsuario::where('usuario_id', $usuarioId);

        // Si el frontend envía un ?fecha=... (como en tu Dashboard)
        if ($request->has('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        $comidasUsuario = $query->with('usuario', 'comidaDieta')->get();
        
        // --- FIN DE LA CORRECCIÓN DE SEGURIDAD ---

        return response()->json($comidasUsuario, 200);
    }

    /**
     * Crear un registro de comida consumida por usuario
     * POST /api/comidas-usuarios
     */
    public function store(Request $request)
    {
        // Este validador fue corregido en nuestra conversación anterior
        // para que coincida con tu migración (fecha, opcion, etc.)
        $validator = Validator::make($request->all(), [
            'usuario_id'      => 'required|exists:usuario,id',
            'comida_dieta_id' => 'required|exists:comida_dieta,id',
            'fecha'           => 'required|date',
            'opcion'          => 'required|in:planificada,alternativa',
            'descripcion'     => 'required|string',
            'calorias'        => 'required|integer',
            'proteinas'       => 'nullable|numeric',
            'carbohidratos'   => 'nullable|numeric',
            'grasas'          => 'nullable|numeric',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'status'  => 400,
                'errors'  => $validator->errors()
            ], 400);
        }

        // --- CORRECCIÓN DE SEGURIDAD (STORE) ---
        // Nos aseguramos de que el usuario_id en el payload
        // sea el mismo que el del usuario autenticado.
        if ($request->usuario_id != $request->user()->id) {
             return response()->json([
                'message' => 'No autorizado',
                'status'  => 403,
            ], 403);
        }
        // --- FIN DE LA CORRECCIÓN ---


        $validated = $validator->validated();

        try {
            $comidaUsuario = ComidaUsuario::create($validated);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar la comida consumida',
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'comida_usuario' => $comidaUsuario,
            'status'         => 201
        ], 201);
    }

    /**
     * Mostrar una comida consumida específica
     * GET /api/comidas-usuarios/{id}
     */
    public function show(Request $request, $id) // <-- Añadido Request
    {
        // --- CORRECCIÓN DE SEGURIDAD (SHOW) ---
        // Nos aseguramos de que el registro pertenezca al usuario autenticado
        $comidaUsuario = ComidaUsuario::with('usuario', 'comidaDieta')
                            ->where('id', $id)
                            ->where('usuario_id', $request->user()->id) // Filtro de seguridad
                            ->first();

        if (!$comidaUsuario) {
            return response()->json([
                'message' => 'Registro no encontrado'
            ], 404);
        }
        // --- FIN DE LA CORRECCIÓN ---

        return response()->json($comidaUsuario, 200);
    }

    /**
     * Actualizar un registro de comida consumida
     * PUT/PATCH /api/comidas-usuarios/{id}
     */
    public function update(Request $request, $id)
    {
        // --- CORRECCIÓN DE SEGURIDAD (UPDATE) ---
        $comidaUsuario = ComidaUsuario::where('id', $id)
                                    ->where('usuario_id', $request->user()->id) // Filtro de seguridad
                                    ->first();

        if (!$comidaUsuario) {
            return response()->json([
                'message' => 'Registro no encontrado'
            ], 404);
        }
        // --- FIN DE LA CORRECCIÓN ---

        $validator = Validator::make($request->all(), [
            'usuario_id'      => 'sometimes|required|exists:usuario,id',
            'comida_dieta_id' => 'sometimes|required|exists:comida_dieta,id',
            'fecha'           => 'sometimes|required|date',
            'opcion'          => 'sometimes|required|in:planificada,alternativa',
            'descripcion'     => 'sometimes|required|string',
            'calorias'        => 'sometimes|required|integer',
            'proteinas'       => 'nullable|numeric',
            'carbohidratos'   => 'nullable|numeric',
            'grasas'          => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'status'  => 400,
                'errors'  => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();

        try {
            $comidaUsuario->update($validated);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el registro',
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json($comidaUsuario, 200);
    }

    /**
     * Eliminar un registro de comida consumida
     * DELETE /api/comidas-usuarios/{id}
     */
    public function destroy(Request $request, $id) // <-- Añadido Request
    {
        // --- CORRECCIÓN DE SEGURIDAD (DESTROY) ---
         $comidaUsuario = ComidaUsuario::where('id', $id)
                                    ->where('usuario_id', $request->user()->id) // Filtro de seguridad
                                    ->first();

        if (!$comidaUsuario) {
            return response()->json([
                'message' => 'Registro no encontrado'
            ], 404);
        }
        // --- FIN DE LA CORRECCIÓN ---

        try {
            $comidaUsuario->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el registro',
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Registro eliminado correctamente'
        ], 200);
    }
}
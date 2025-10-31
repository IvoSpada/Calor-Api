<?php

namespace App\Http\Controllers;

use App\Models\Dieta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Importar Auth

class DietaController extends Controller
{
    /**
     * Mostrar todas las dietas DEL USUARIO AUTENTICADO
     * GET /api/dietas
     */
    public function index(Request $request)
    {
        // FIX DE SEGURIDAD:
        // Filtramos las dietas por el usuario_id del usuario autenticado.
        $usuarioId = $request->user()->id;
        
        $query = Dieta::where('usuario_id', $usuarioId);

        $dietas = $query->with('usuario')->get();
        return response()->json($dietas, 200);
    }

    /**
     * Crear una nueva dieta
     * POST /api/dietas
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_id'   => 'required|exists:usuario,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'origen'       => 'nullable|in:IA,manual',
            'estado'       => 'nullable|in:activa,finalizada',
        ]);

        // FIX DE SEGURIDAD: Asegurarse que el usuario que crea la dieta sea él mismo
        if ($request->usuario_id != Auth::id()) {
             return response()->json(['message' => 'Acceso no autorizado'], 403);
        }

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'status'  => 400,
                'errors'  => $validator->errors()
            ], 400);
        }

        $validated = $validator->validated();
        
        // Asegurar que el estado sea 'activa' si no se provee
        if (!isset($validated['estado'])) {
            $validated['estado'] = 'activa';
        }

        try {
            $dieta = Dieta::create($validated);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error en la creación de la dieta',
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'dieta'  => $dieta,
            'status' => 201
        ], 201);
    }

    /**
     * Mostrar una dieta específica
     * GET /api/dietas/{id}
     */
    public function show($id)
    {
        // FIX DE SEGURIDAD: Asegurarnos que la dieta pertenezca al usuario
        $usuarioId = Auth::id();
        $dieta = Dieta::with('usuario', 'comidasDieta')
                      ->where('id', $id)
                      ->where('usuario_id', $usuarioId)
                      ->first();

        if (!$dieta) {
            return response()->json([
                'message' => 'Dieta no encontrada'
            ], 404);
        }

        return response()->json($dieta, 200);
    }

    /**
     * Actualizar una dieta
     * PUT/PATCH /api/dietas/{id}
     */
    public function update(Request $request, $id)
    {
        // FIX DE SEGURIDAD: Asegurarnos que la dieta pertenezca al usuario
        $usuarioId = Auth::id();
        $dieta = Dieta::where('id', $id)->where('usuario_id', $usuarioId)->first();

        if (!$dieta) {
            return response()->json([
                'message' => 'Dieta no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'usuario_id'   => 'sometimes|required|exists:usuario,id',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin'    => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'origen'       => 'nullable|in:IA,manual',
            'estado'       => 'nullable|in:activa,finalizada'
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
            $dieta->update($validated);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la dieta',
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json($dieta, 200);
    }

    /**
     * Eliminar una dieta
     * DELETE /api/dietas/{id}
     */
    public function destroy($id)
    {
        // FIX DE SEGURIDAD: Asegurarnos que la dieta pertenezca al usuario
        $usuarioId = Auth::id();
        $dieta = Dieta::where('id', $id)->where('usuario_id', $usuarioId)->first();

        if (!$dieta) {
            return response()->json([
                'message' => 'Dieta no encontrada'
            ], 404);
        }

        try {
            $dieta->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la dieta',
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Dieta eliminada correctamente'
        ], 200);
    }
}
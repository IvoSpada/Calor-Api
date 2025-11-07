<?php

namespace App\Http\Controllers;

use App\Models\ComidaDieta;
use App\Models\Dieta; // Importar Dieta
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // <-- Asegúrate de importar Auth

class ComidaDietaController extends Controller
{
    /**
     * Mostrar todas las comidas planificadas DEL USUARIO AUTENTICADO
     * GET /api/comidas-dietas
     */
    public function index(Request $request)
    {
        // FIX DE SEGURIDAD:
        // Filtramos las comidas basándonos en las dietas que pertenecen al usuario
        $usuarioId = $request->user()->id;

        $query = ComidaDieta::whereHas('dieta', function ($q) use ($usuarioId) {
            $q->where('usuario_id', $usuarioId);
        });

        // Si el frontend pide las comidas de UNA dieta específica (para el Dashboard)
        if ($request->has('dieta_id')) {
            // Validamos que esa dieta_id también pertenezca al usuario
            $dieta = Dieta::where('id', $request->dieta_id)
                          ->where('usuario_id', $usuarioId)
                          ->first();
            
            if ($dieta) {
                $query->where('dieta_id', $request->dieta_id);
            } else {
                // Si pide un ID de dieta que no es suyo, devolvemos vacío
                return response()->json([], 200); 
            }
        }

        $comidas = $query->with('dieta')->get();
        return response()->json($comidas, 200);
    }

    /**
     * Crear una nueva comida planificada
     * POST /api/comidas-dietas
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dieta_id'      => 'required|exists:dieta,id',
            'fecha'         => 'required|date',
            'tipo'          => 'required|in:desayuno,almuerzo,cena,snack',
            'descripcion'   => 'required|string',
            'calorias'      => 'required|integer|min:0',
            'proteinas'     => 'nullable|numeric|min:0',
            'carbohidratos' => 'nullable|numeric|min:0',
            'grasas'        => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'status'  => 400,
                'errors'  => $validator->errors()
            ], 400);
        }

        // FIX DE SEGURIDAD: Validar que la dieta_id pertenezca al usuario autenticado
        $usuarioId = Auth::id();
        $dieta = Dieta::where('id', $request->dieta_id)
                      ->where('usuario_id', $usuarioId)
                      ->first();

        if (!$dieta) {
            return response()->json([
                'message' => 'Acceso no autorizado: la dieta no pertenece a este usuario.',
                'status'  => 403,
            ], 403);
        }

        $validated = $validator->validated();

        try {
            $comida = ComidaDieta::create($validated);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la comida',
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'comida' => $comida,
            'status' => 201
        ], 201);
    }

    /**
     * Mostrar una comida planificada específica
     * GET /api/comidas-dietas/{id}
     */
    public function show($id)
    {
        // FIX DE SEGURIDAD:
        $usuarioId = Auth::id();
        $comida = ComidaDieta::with('dieta', 'comidasUsuario')
                            ->where('id', $id)
                            ->whereHas('dieta', function ($q) use ($usuarioId) {
                                $q->where('usuario_id', $usuarioId);
                            })
                            ->first();

        if (!$comida) {
            return response()->json([
                'message' => 'Comida no encontrada'
            ], 404);
        }

        return response()->json($comida, 200);
    }

    /**
     * Actualizar una comida planificada
     * PUT/PATCH /api/comidas-dietas/{id}
     */
    public function update(Request $request, $id)
    {
        // FIX DE SEGURIDAD:
        $usuarioId = Auth::id();
        $comida = ComidaDieta::where('id', $id)
                            ->whereHas('dieta', function ($q) use ($usuarioId) {
                                $q->where('usuario_id', $usuarioId);
                            })
                            ->first();

        if (!$comida) {
            return response()->json([
                'message' => 'Comida no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'dieta_id'      => 'sometimes|required|exists:dieta,id',
            'fecha'         => 'sometimes|required|date',
            'tipo'          => 'sometimes|required|in:desayuno,almuerzo,cena,snack',
            'descripcion'   => 'sometimes|required|string',
            'calorias'      => 'sometimes|required|integer|min:0',
            'proteinas'     => 'nullable|numeric|min:0',
            'carbohidratos' => 'nullable|numeric|min:0',
            'grasas'        => 'nullable|numeric|min:0',
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
            $comida->update($validated);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la comida',
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json($comida, 200);
    }

    /**
     * Eliminar una comida planificada
     * DELETE /api/comidas-dietas/{id}
     */
    public function destroy($id)
    {
        // FIX DE SEGURIDAD:
        $usuarioId = Auth::id();
        $comida = ComidaDieta::where('id', $id)
                            ->whereHas('dieta', function ($q) use ($usuarioId) {
                                $q->where('usuario_id', $usuarioId);
                            })
                            ->first();

        if (!$comida) {
            return response()->json([
                'message' => 'Comida no encontrada'
            ], 404);
        }

        try {
            $comida->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la comida',
                'status'  => 500,
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Comida eliminada correctamente'
        ], 200);
    }
}
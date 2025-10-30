<?php

namespace App\Http\Controllers;

use App\Models\ComidaDieta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComidaDietaController extends Controller
{
    /**
     * Mostrar todas las comidas planificadas
     * GET /api/comidas-dieta
     */
    public function index()
    {
        $comidas = ComidaDieta::with('dieta')->get();
        return response()->json($comidas, 200);
    }

    /**
     * Crear una nueva comida planificada
     * POST /api/comidas-dieta
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
     * GET /api/comidas-dieta/{id}
     */
    public function show($id)
    {
        $comida = ComidaDieta::with('dieta', 'comidasUsuario')->find($id);

        if (!$comida) {
            return response()->json([
                'message' => 'Comida no encontrada'
            ], 404);
        }

        return response()->json($comida, 200);
    }

    /**
     * Actualizar una comida planificada
     * PUT/PATCH /api/comidas-dieta/{id}
     */
    public function update(Request $request, $id)
    {
        $comida = ComidaDieta::find($id);

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
     * DELETE /api/comidas-dieta/{id}
     */
    public function destroy($id)
    {
        $comida = ComidaDieta::find($id);

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
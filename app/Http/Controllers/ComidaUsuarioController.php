<?php

namespace App\Http\Controllers;

use App\Models\ComidaUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComidaUsuarioController extends Controller
{
    /**
     * Mostrar todas las comidas consumidas por usuarios
     * GET /api/comidas-usuario
     */
    public function index()
    {
        $comidasUsuario = ComidaUsuario::with('usuario', 'comidaDieta')->get();
        return response()->json($comidasUsuario, 200);
    }

    /**
     * Crear un registro de comida consumida por usuario
     * POST /api/comidas-usuario
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario_id'    => 'required|exists:usuario,id',
            'comida_dieta_id' => 'required|exists:comida_dieta,id',
            'fecha_consumo' => 'required|date',
            'cantidad'      => 'required|numeric|min:1'
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
     * GET /api/comidas-usuario/{id}
     */
    public function show($id)
    {
        $comidaUsuario = ComidaUsuario::with('usuario', 'comidaDieta')->find($id);

        if (!$comidaUsuario) {
            return response()->json([
                'message' => 'Registro no encontrado'
            ], 404);
        }

        return response()->json($comidaUsuario, 200);
    }

    /**
     * Actualizar un registro de comida consumida
     * PUT/PATCH /api/comidas-usuario/{id}
     */
    public function update(Request $request, $id)
    {
        $comidaUsuario = ComidaUsuario::find($id);

        if (!$comidaUsuario) {
            return response()->json([
                'message' => 'Registro no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'usuario_id'    => 'sometimes|required|exists:usuario,id',
            'comida_dieta_id' => 'sometimes|required|exists:comida_dieta,id',
            'fecha_consumo' => 'sometimes|required|date',
            'cantidad'      => 'sometimes|required|numeric|min:1'
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
     * DELETE /api/comidas-usuario/{id}
     */
    public function destroy($id)
    {
        $comidaUsuario = ComidaUsuario::find($id);

        if (!$comidaUsuario) {
            return response()->json([
                'message' => 'Registro no encontrado'
            ], 404);
        }

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
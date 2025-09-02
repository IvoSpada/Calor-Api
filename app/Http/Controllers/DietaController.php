<?php

namespace App\Http\Controllers;

use App\Models\Dieta;
use Illuminate\Http\Request;

class DietaController extends Controller
{
    // GET /api/dietas
    public function index()
    {
        // Opcional: traer también info del usuario con la dieta
        $dietas = Dieta::with('usuario')->get();
        return response()->json($dietas, 200);
    }

    // POST /api/dietas
    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuario,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'origen' => 'nullable|in:IA,manual',
            'estado' => 'nullable|in:activa,finalizada',
        ]);

        $dieta = Dieta::create($validated);

        return response()->json($dieta, 201);
    }

    // GET /api/dietas/{id}
    public function show($id)
    {
        $dieta = Dieta::with('usuario', 'comidasDieta')->find($id);

        if (!$dieta) {
            return response()->json(['message' => 'Dieta no encontrada'], 404);
        }

        return response()->json($dieta, 200);
    }

    // PUT/PATCH /api/dietas/{id}
    public function update(Request $request, $id)
    {
        $dieta = Dieta::find($id);

        if (!$dieta) {
            return response()->json(['message' => 'Dieta no encontrada'], 404);
        }

        $validated = $request->validate([
            'usuario_id' => 'sometimes|required|exists:usuario,id',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'origen' => 'nullable|in:IA,manual',
            'estado' => 'nullable|in:activa,finalizada',
        ]);

        $dieta->update($validated);

        return response()->json($dieta, 200);
    }

    // DELETE /api/dietas/{id}
    public function destroy($id)
    {
        $dieta = Dieta::find($id);

        if (!$dieta) {
            return response()->json(['message' => 'Dieta no encontrada'], 404);
        }

        $dieta->delete();

        return response()->json(['message' => 'Dieta eliminada correctamente'], 200);
    }
}
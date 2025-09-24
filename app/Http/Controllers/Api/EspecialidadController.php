<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EspecialidadController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::with(['medicosActivos'])
                                    ->where('activo', true)
                                    ->get();

        return response()->json([
            'success' => true,
            'data' => $especialidades
        ]);
    }

    public function store(Request $request)
    {
        // TODO: Implementar verificación de permisos

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:especialidades',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $especialidad = Especialidad::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Especialidad creada exitosamente',
            'data' => $especialidad
        ], 201);
    }

    public function show($id)
    {
        $especialidad = Especialidad::with(['medicosActivos'])->find($id);

        if (!$especialidad) {
            return response()->json([
                'success' => false,
                'message' => 'Especialidad no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $especialidad
        ]);
    }

    public function update(Request $request, $id)
    {
        // TODO: Implementar verificación de permisos

        $especialidad = Especialidad::find($id);

        if (!$especialidad) {
            return response()->json([
                'success' => false,
                'message' => 'Especialidad no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:especialidades,nombre,' . $id,
            'descripcion' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $especialidad->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Especialidad actualizada exitosamente',
            'data' => $especialidad
        ]);
    }

    public function destroy($id)
    {
        // TODO: Implementar verificación de permisos

        $especialidad = Especialidad::find($id);

        if (!$especialidad) {
            return response()->json([
                'success' => false,
                'message' => 'Especialidad no encontrada'
            ], 404);
        }

        // Verificar si tiene médicos asociados
        if ($especialidad->medicos()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la especialidad porque tiene médicos asociados'
            ], 400);
        }

        $especialidad->delete();

        return response()->json([
            'success' => true,
            'message' => 'Especialidad eliminada exitosamente'
        ]);
    }
}
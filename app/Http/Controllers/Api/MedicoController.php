<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicoController extends Controller
{
    public function index(Request $request)
    {
        $query = Medico::conEspecialidad()->activos();

        // Filtro por especialidad
        if ($request->has('especialidad_id')) {
            $query->where('especialidad_id', $request->especialidad_id);
        }

        // Búsqueda por nombre
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('registro_medico', 'like', "%{$search}%");
            });
        }

        $medicos = $query->get();

        return response()->json([
            'success' => true,
            'data' => $medicos
        ]);
    }

    public function store(Request $request)
    {
        // TODO: Implementar verificación de permisos

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|unique:medicos',
            'registro_medico' => 'required|string|unique:medicos',
            'telefono' => 'nullable|string',
            'email' => 'required|email|unique:medicos',
            'especialidad_id' => 'required|exists:especialidades,id',
            'horarios_atencion' => 'required|array',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $medico = Medico::create($request->all());
        $medico->load('especialidad');

        return response()->json([
            'success' => true,
            'message' => 'Médico creado exitosamente',
            'data' => $medico
        ], 201);
    }

    public function show($id)
    {
        $medico = Medico::conEspecialidad()->find($id);

        if (!$medico) {
            return response()->json([
                'success' => false,
                'message' => 'Médico no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $medico
        ]);
    }

    public function update(Request $request, $id)
    {
        // TODO: Implementar verificación de permisos

        $medico = Medico::find($id);

        if (!$medico) {
            return response()->json([
                'success' => false,
                'message' => 'Médico no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|unique:medicos,cedula,' . $id,
            'registro_medico' => 'required|string|unique:medicos,registro_medico,' . $id,
            'telefono' => 'nullable|string',
            'email' => 'required|email|unique:medicos,email,' . $id,
            'especialidad_id' => 'required|exists:especialidades,id',
            'horarios_atencion' => 'required|array',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $medico->update($request->all());
        $medico->load('especialidad');

        return response()->json([
            'success' => true,
            'message' => 'Médico actualizado exitosamente',
            'data' => $medico
        ]);
    }

    public function destroy($id)
    {
        // TODO: Implementar verificación de permisos

        $medico = Medico::find($id);

        if (!$medico) {
            return response()->json([
                'success' => false,
                'message' => 'Médico no encontrado'
            ], 404);
        }

        // Verificar si tiene citas programadas
        if ($medico->citasActivas()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el médico porque tiene citas programadas'
            ], 400);
        }

        $medico->delete();

        return response()->json([
            'success' => true,
            'message' => 'Médico eliminado exitosamente'
        ]);
    }

    public function disponibilidad($id, Request $request)
    {
        $medico = Medico::find($id);

        if (!$medico) {
            return response()->json([
                'success' => false,
                'message' => 'Médico no encontrado'
            ], 404);
        }

        $fecha = $request->get('fecha', now()->format('Y-m-d'));
        
        // Obtener citas del médico para esa fecha
        $citasOcupadas = $medico->citas()
                              ->whereDate('fecha_hora', $fecha)
                              ->whereIn('estado', ['programada', 'confirmada'])
                              ->pluck('fecha_hora')
                              ->map(function($fecha) {
                                  return $fecha->format('H:i');
                              });

        return response()->json([
            'success' => true,
            'data' => [
                'medico' => $medico->nombre_completo,
                'fecha' => $fecha,
                'horarios_atencion' => $medico->horarios_atencion,
                'horas_ocupadas' => $citasOcupadas
            ]
        ]);
    }
}
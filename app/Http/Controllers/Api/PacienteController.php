<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Paciente::activos();

        // Búsqueda por nombre, apellido o cédula
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $pacientes = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $pacientes
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|unique:pacientes',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F,Otro',
            'telefono' => 'required|string',
            'email' => 'required|email|unique:pacientes',
            'direccion' => 'nullable|string',
            'eps' => 'nullable|string',
            'alergias' => 'nullable|string',
            'medicamentos_actuales' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $paciente = Paciente::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Paciente creado exitosamente',
            'data' => $paciente
        ], 201);
    }

    public function show($id)
    {
        $paciente = Paciente::with(['citas.medico.especialidad'])->find($id);

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $paciente
        ]);
    }

    public function update(Request $request, $id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|unique:pacientes,cedula,' . $id,
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F,Otro',
            'telefono' => 'required|string',
            'email' => 'required|email|unique:pacientes,email,' . $id,
            'direccion' => 'nullable|string',
            'eps' => 'nullable|string',
            'alergias' => 'nullable|string',
            'medicamentos_actuales' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $paciente->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Paciente actualizado exitosamente',
            'data' => $paciente
        ]);
    }

    public function destroy($id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado'
            ], 404);
        }

        // Verificar si tiene citas programadas
        if ($paciente->citasActivas()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el paciente porque tiene citas programadas'
            ], 400);
        }

        $paciente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paciente eliminado exitosamente'
        ]);
    }

    public function historialMedico($id)
    {
        $paciente = Paciente::with([
            'citas' => function($query) {
                $query->where('estado', 'completada')
                      ->with(['medico.especialidad'])
                      ->orderBy('fecha_hora', 'desc');
            }
        ])->find($id);

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'paciente' => $paciente,
                'historial_citas' => $paciente->citas
            ]
        ]);
    }
}
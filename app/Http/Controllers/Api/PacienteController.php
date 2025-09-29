<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\User;
use App\Models\Role;
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
            'antecedentes_medicos' => 'nullable|string',
            'contacto_emergencia' => 'nullable|string',
            'telefono_emergencia' => 'nullable|string',
            'activo' => 'boolean',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crear el paciente
        $paciente = Paciente::create($request->except(['password', 'password_confirmation']));

        // Crear el usuario asociado
        $user = User::create([
            'name' => $request->nombre . ' ' . $request->apellido,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cedula' => $request->cedula,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'rol' => 'paciente',
            'activo' => true,
            'paciente_id' => $paciente->id,
            'role_id' => Role::where('slug', 'paciente')->first()->id ?? null
        ]);

        // Cargar la relación del paciente con el usuario
        $paciente->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Paciente y usuario creados exitosamente',
            'data' => $paciente,
            'user' => $user
        ], 201);
    }

    public function show($id)
    {
        $paciente = Paciente::with(['citas.medico.especialidad', 'user'])->find($id);

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

    public function historialMedico(Request $request, $id)
    {
        $user = $request->user();

        // Verificar permisos: médicos pueden ver cualquier historial, pacientes solo el propio
        if ($user->rol === 'medico' || ($user->rol === 'paciente' && $user->paciente_id == $id)) {
            $paciente = Paciente::with([
                'citas' => function($query) {
                    $query->with(['medico.especialidad'])
                          ->orderBy('fecha_hora', 'desc');
                },
                'user'
            ])->find($id);

            if (!$paciente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ], 404);
            }

            // Filtrar citas según el rol
            $citas = $paciente->citas;
            if ($user->rol === 'paciente') {
                // Los pacientes pueden ver todas sus citas (completadas, canceladas, etc.)
                $citas = $citas; // Sin filtro adicional
            } else {
                // Los médicos solo ven citas completadas
                $citas = $citas->where('estado', 'completada');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'paciente' => $paciente,
                    'historial_citas' => $citas
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No tienes permisos para acceder a este historial médico'
        ], 403);
    }
}
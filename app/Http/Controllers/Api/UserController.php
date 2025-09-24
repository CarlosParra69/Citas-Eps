<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        // Solo superadmin puede ver todos los usuarios
        if (!Gate::allows('superadmin-only')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para acceder a esta información'
            ], 403);
        }

        $query = User::query();

        // Filtros
        if ($request->has('rol') && $request->rol !== 'todos') {
            $query->where('rol', $request->rol);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%");
            });
        }

        $usuarios = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $usuarios,
            'message' => 'Usuarios obtenidos correctamente'
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        // Verificar permisos: superadmin puede crear cualquier rol, medico solo pacientes
        $user = auth()->user();

        if ($user->rol === 'medico' && $request->rol !== 'paciente') {
            return response()->json([
                'success' => false,
                'message' => 'Como médico solo puedes crear pacientes'
            ], 403);
        }

        if ($user->rol !== 'superadmin' && $user->rol !== 'medico') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear usuarios'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cedula' => 'nullable|string|max:20|unique:users',
            'telefono' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|in:M,F',
            'rol' => 'required|in:paciente,medico,superadmin',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $usuario = User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'cedula' => $request->cedula,
            'telefono' => $request->telefono,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'rol' => $request->rol,
            'password' => Hash::make($request->password),
            'estado' => 'activo',
        ]);

        return response()->json([
            'success' => true,
            'data' => $usuario,
            'message' => 'Usuario creado correctamente'
        ], 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $usuario)
    {
        // Solo superadmin puede ver detalles de usuarios
        if (!Gate::allows('superadmin-only')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para acceder a esta información'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $usuario,
            'message' => 'Usuario obtenido correctamente'
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $usuario)
    {
        // Solo superadmin puede actualizar usuarios
        if (!Gate::allows('superadmin-only')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para actualizar usuarios'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $usuario->id,
            'cedula' => 'nullable|string|max:20|unique:users,cedula,' . $usuario->id,
            'telefono' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|in:M,F',
            'rol' => 'sometimes|required|in:paciente,medico,superadmin',
            'estado' => 'sometimes|required|in:activo,inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $usuario->update($request->only([
            'nombre', 'apellido', 'email', 'cedula', 'telefono',
            'fecha_nacimiento', 'genero', 'rol', 'estado'
        ]));

        return response()->json([
            'success' => true,
            'data' => $usuario,
            'message' => 'Usuario actualizado correctamente'
        ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $usuario)
    {
        // Solo superadmin puede eliminar usuarios
        if (!Gate::allows('superadmin-only')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar usuarios'
            ], 403);
        }

        // No permitir eliminar al propio usuario
        if ($usuario->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario'
            ], 403);
        }

        $usuario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ]);
    }

    /**
     * Cambiar estado del usuario (activar/desactivar)
     */
    public function cambiarEstado(Request $request, $id)
    {
        // Solo superadmin puede cambiar estados
        if (!Gate::allows('superadmin-only')) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para cambiar el estado de usuarios'
            ], 403);
        }

        $usuario = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:activo,inactivo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Estado inválido',
                'errors' => $validator->errors()
            ], 422);
        }

        $usuario->update(['estado' => $request->estado]);

        return response()->json([
            'success' => true,
            'data' => $usuario,
            'message' => 'Estado del usuario actualizado correctamente'
        ]);
    }

    /**
     * Obtener configuración de notificaciones del usuario
     */
    public function getNotificacionesConfig()
    {
        $user = auth()->user();

        // Por ahora retornamos configuración por defecto
        // En el futuro esto podría venir de una tabla de configuraciones
        $configuracion = [
            'recordatorios_citas' => true,
            'notificaciones_email' => true,
            'notificaciones_push' => false,
            'horario_notificaciones' => [
                'inicio' => '08:00',
                'fin' => '20:00'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $configuracion,
            'message' => 'Configuración de notificaciones obtenida correctamente'
        ]);
    }

    /**
     * Actualizar configuración de notificaciones del usuario
     */
    public function updateNotificacionesConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recordatorios_citas' => 'boolean',
            'notificaciones_email' => 'boolean',
            'notificaciones_push' => 'boolean',
            'horario_notificaciones' => 'array',
            'horario_notificaciones.inicio' => 'date_format:H:i',
            'horario_notificaciones.fin' => 'date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Aquí guardaríamos la configuración en la base de datos
        // Por ahora solo retornamos éxito

        return response()->json([
            'success' => true,
            'data' => $request->all(),
            'message' => 'Configuración de notificaciones actualizada correctamente'
        ]);
    }
}
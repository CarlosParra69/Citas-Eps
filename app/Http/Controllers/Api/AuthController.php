<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        // Middleware is handled in routes, not in constructor for Laravel 11
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|unique:pacientes',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F,Otro',
            'telefono' => 'required|string',
            'email' => 'required|string|email|unique:pacientes',
            'direccion' => 'nullable|string',
            'eps' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $paciente = Paciente::create(array_merge($request->all(), ['activo' => true]));
        
        // Crear el token JWT inmediatamente después del registro
        $token = JWTAuth::fromUser($paciente);

        return response()->json([
            'success' => true,
            'message' => 'Paciente registrado exitosamente',
            'data' => [
                'paciente' => $paciente,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60 // TTL en segundos
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cedula' => 'required|string',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar al paciente con las credenciales proporcionadas
        $paciente = Paciente::where('cedula', $request->cedula)
                           ->where('email', $request->email)
                           ->where('activo', true)
                           ->first();

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        try {
            // Crear el token JWT
            if (!$token = JWTAuth::fromUser($paciente)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo crear el token'
                ], 500);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el token'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'paciente' => $paciente,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60 // TTL en segundos
            ]
        ]);
    }

    public function logout(Request $request)
    {
        try {
            // El middleware ya verificó que el token es válido
            JWTAuth::invalidate(JWTAuth::getToken());
            
            return response()->json([
                'success' => true,
                'message' => 'Logout exitoso'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al hacer logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function me(Request $request)
    {
        // El middleware ya verificó que el usuario está autenticado
        $paciente = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => $paciente
        ]);
    }

    public function refresh()
    {
        try {
            // El middleware ya verificó que el token es válido
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            
            return response()->json([
                'success' => true,
                'message' => 'Token renovado exitosamente',
                'data' => [
                    'access_token' => $newToken,
                    'token_type' => 'Bearer',
                    'expires_in' => config('jwt.ttl') * 60
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo renovar el token',
                'error' => $e->getMessage()
            ], 401);
        }
    }
}
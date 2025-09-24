<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    //Correcion de Auth Controller
    public function __construct()
    {
        
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|unique:users',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F,Otro',
            'telefono' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
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

        // Crear el paciente primero
        $paciente = Paciente::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cedula' => $request->cedula,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'direccion' => $request->direccion,
            'eps' => $request->eps,
            'activo' => true,
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $request->name ?? $request->nombre . ' ' . $request->apellido,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cedula' => $request->cedula,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'paciente',
            'activo' => true,
            'paciente_id' => $paciente->id,
        ]);

        // Crear el token JWT
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60 // TTL en segundos
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar al usuario con las credenciales proporcionadas
        $user = User::where('email', $request->email)
                    ->where('activo', true)
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Verificar la contraseña usando Hash::check()
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        try {
            // Crear el token JWT
            if (!$token = JWTAuth::fromUser($user)) {
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
                'user' => $user,
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
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => $user
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
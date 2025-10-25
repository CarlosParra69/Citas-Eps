<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;

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

        // Cargar las relaciones del usuario según su rol
        $user->load(['paciente', 'medico']);

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

        // Cargar las relaciones del usuario según su rol
        $user->load(['paciente', 'medico']);

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

    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Buscar al usuario por email
            $user = User::where('email', $request->email)
                        ->where('activo', true)
                        ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró un usuario con ese correo electrónico'
                ], 404);
            }

            // Generar token de recuperación
            $token = Str::random(60);
            $expiresAt = Carbon::now()->addHours(1); // Token válido por 1 hora

            // Guardar token en la base de datos
            $user->reset_token = $token;
            $user->reset_token_expires = $expiresAt;
            $user->save();

            // Enviar email con el enlace de recuperación
            Mail::send('emails.password-reset', [
                'user' => $user,
                'token' => $token,
                'resetUrl' => url("/reset-password.html?token={$token}&email=" . urlencode($user->email))
            ], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Recuperación de Contraseña - MediApp');
            });

            return response()->json([
                'success' => true,
                'message' => 'Se ha enviado un correo electrónico con instrucciones para recuperar tu contraseña'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en forgotPassword: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo electrónico. Por favor, inténtalo de nuevo más tarde.',
                'error' => $e->getMessage(),
                'debug_info' => [
                    'email' => $request->email,
                    'user_found' => isset($user),
                    'token_generated' => isset($token),
                    'mail_config' => config('mail')
                ]
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar al usuario con el token y email válidos
        $user = User::where('email', $request->email)
                    ->where('reset_token', $request->token)
                    ->where('reset_token_expires', '>', Carbon::now())
                    ->where('activo', true)
                    ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'El token de recuperación es inválido o ha expirado'
            ], 400);
        }

        // Actualizar contraseña y limpiar token
        $user->password = Hash::make($request->password);
        $user->reset_token = null;
        $user->reset_token_expires = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente'
        ]);
    }
}
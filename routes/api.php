<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EspecialidadController;
use App\Http\Controllers\Api\MedicoController;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\CitaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AvatarController;

/*
|--------------------------------------------------------------------------
| API Routes Citas
|--------------------------------------------------------------------------
*/

// Rutas públicas
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Ruta de prueba para debugging
Route::get('/test-email', function() {
    try {
        \Mail::raw('Este es un email de prueba desde MediApp', function ($message) {
            $message->to('carlosalbertoparrabejarano@gmail.com')
                    ->subject('Email de Prueba - MediApp');
        });

        return response()->json([
            'success' => true,
            'message' => 'Email de prueba enviado correctamente',
            'mail_config' => config('mail'),
            'note' => 'Revisa tu bandeja de entrada, spam, y correos sociales'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'mail_config' => config('mail'),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Ruta para obtener el enlace de recuperación directamente (para desarrollo)
Route::get('/get-reset-link/{email}', function($email) {
    try {
        $user = \App\Models\User::where('email', $email)->where('activo', true)->first();

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        $token = 'DEV-TOKEN-' . time(); // Token de desarrollo
        $resetUrl = "http://localhost:8000/reset-password.html?token={$token}&email=" . urlencode($user->email);

        return response()->json([
            'success' => true,
            'reset_url' => $resetUrl,
            'token' => $token,
            'email' => $user->email,
            'note' => 'Usa este enlace directamente para desarrollo'
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Rutas públicas para consultar especialidades y médicos
Route::get('/especialidades', [EspecialidadController::class, 'index']);
Route::get('/especialidades/{id}', [EspecialidadController::class, 'show']);
Route::get('/medicos', [MedicoController::class, 'index']);
Route::get('/medicos/{id}', [MedicoController::class, 'show']);
Route::get('/medicos/{id}/disponibilidad', [MedicoController::class, 'disponibilidad']);
Route::post('/medicos/{id}/check-availability', [MedicoController::class, 'checkAvailability']);

// Rutas públicas para servir imágenes de avatar
Route::get('/avatar/image/{filename}', [AvatarController::class, 'serveImage']);

// Rutas protegidas con autenticación JWT
Route::middleware(['auth:api'])->group(function () {
    
    // Autenticación
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    
    // Especialidades (CRUD completo)
    Route::apiResource('especialidades', EspecialidadController::class)->except(['index', 'show']);
    
    // Médicos (CRUD completo)
    Route::apiResource('medicos', MedicoController::class)->except(['index', 'show']);
    Route::patch('/medicos/{id}/disponibilidad', [MedicoController::class, 'updateDisponibilidad']);
    
    // Pacientes (CRUD completo)
    Route::apiResource('pacientes', PacienteController::class);
    Route::get('/pacientes/{id}/historial', [PacienteController::class, 'historialMedico']);
    
    // Usuarios (CRUD completo para superadmin)
    Route::apiResource('usuarios', UserController::class);

    // Citas (CRUD completo)
    Route::apiResource('citas', CitaController::class);
    Route::patch('/citas/{id}/estado', [CitaController::class, 'cambiarEstado']);
    Route::get('/citas-hoy', [CitaController::class, 'citasHoy']);
    Route::get('/proximas-citas', [CitaController::class, 'proximasCitas']);
    Route::get('/citas-pendientes/{medicoId}', [CitaController::class, 'citasPendientes']);
    Route::patch('/citas/{id}/aprobar', [CitaController::class, 'aprobar']);
    Route::patch('/citas/{id}/rechazar', [CitaController::class, 'rechazar']);
    Route::patch('/citas/{id}/cancelar', [CitaController::class, 'cancelar']);
    Route::patch('/citas/{id}/confirmar', [CitaController::class, 'confirmar']);
    Route::patch('/citas/{id}/atender', [CitaController::class, 'atender']);
    Route::patch('/citas/{id}/completar', [CitaController::class, 'completar']);
    Route::post('/citas/marcar-no-asistidos', [CitaController::class, 'marcarNoAsistidos']);
    
    // Reportes (Consultas SQL Compuestas)
    Route::prefix('reportes')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Api\ReportesController::class, 'dashboardResumen']);
        Route::get('/dashboard-medico', [App\Http\Controllers\Api\ReportesController::class, 'dashboardMedico']);
        Route::get('/dashboard-paciente', [App\Http\Controllers\Api\ReportesController::class, 'dashboardPaciente']);
        Route::get('/estadisticas-medico/{medicoId?}', [App\Http\Controllers\Api\ReportesController::class, 'estadisticasMedico']);
        Route::get('/medicos-mas-citas', [App\Http\Controllers\Api\ReportesController::class, 'medicosConMasCitas']);
        Route::get('/pacientes-historial', [App\Http\Controllers\Api\ReportesController::class, 'pacientesConHistorialCompleto']);
        Route::get('/disponibilidad-especialidades', [App\Http\Controllers\Api\ReportesController::class, 'analisisDisponibilidadEspecialidades']);
        Route::get('/ingresos-detallado', [App\Http\Controllers\Api\ReportesController::class, 'reporteIngresosDetallado']);
        Route::get('/patrones-citas', [App\Http\Controllers\Api\ReportesController::class, 'analisisPatronesCitas']);
    });

    // Notificaciones
    Route::prefix('notificaciones')->group(function () {
        Route::get('/configuracion', [App\Http\Controllers\Api\UserController::class, 'getNotificacionesConfig']);
        Route::put('/configuracion', [App\Http\Controllers\Api\UserController::class, 'updateNotificacionesConfig']);
    });

    // Avatar
    Route::prefix('avatar')->group(function () {
        Route::post('/upload', [AvatarController::class, 'upload']);
        Route::delete('/delete', [AvatarController::class, 'delete']);
        Route::get('/get', [AvatarController::class, 'get']);
        Route::get('/user/{userId}', [AvatarController::class, 'getByUserId']);
    });
});

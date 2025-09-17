<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EspecialidadController;
use App\Http\Controllers\Api\MedicoController;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\CitaController;

/*
|--------------------------------------------------------------------------
| API Routes Citas
|--------------------------------------------------------------------------
*/

// Rutas públicas
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Rutas públicas para consultar especialidades y médicos
Route::get('/especialidades', [EspecialidadController::class, 'index']);
Route::get('/especialidades/{id}', [EspecialidadController::class, 'show']);
Route::get('/medicos', [MedicoController::class, 'index']);
Route::get('/medicos/{id}', [MedicoController::class, 'show']);
Route::get('/medicos/{id}/disponibilidad', [MedicoController::class, 'disponibilidad']);

// Rutas protegidas con autenticación JWT
Route::middleware('jwt.auth')->group(function () {
    
    // Autenticación
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    
    // Especialidades (CRUD completo)
    Route::apiResource('especialidades', EspecialidadController::class)->except(['index', 'show']);
    
    // Médicos (CRUD completo)
    Route::apiResource('medicos', MedicoController::class)->except(['index', 'show']);
    
    // Pacientes (CRUD completo)
    Route::apiResource('pacientes', PacienteController::class);
    Route::get('/pacientes/{id}/historial', [PacienteController::class, 'historialMedico']);
    
    // Citas (CRUD completo)
    Route::apiResource('citas', CitaController::class);
    Route::patch('/citas/{id}/estado', [CitaController::class, 'cambiarEstado']);
    Route::get('/citas-hoy', [CitaController::class, 'citasHoy']);
    Route::get('/proximas-citas', [CitaController::class, 'proximasCitas']);
    
    // Reportes (Consultas SQL Compuestas)
    Route::prefix('reportes')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Api\ReportesController::class, 'dashboardResumen']);
        Route::get('/medicos-mas-citas', [App\Http\Controllers\Api\ReportesController::class, 'medicosConMasCitas']);
        Route::get('/pacientes-historial', [App\Http\Controllers\Api\ReportesController::class, 'pacientesConHistorialCompleto']);
        Route::get('/disponibilidad-especialidades', [App\Http\Controllers\Api\ReportesController::class, 'analisisDisponibilidadEspecialidades']);
        Route::get('/ingresos-detallado', [App\Http\Controllers\Api\ReportesController::class, 'reporteIngresosDetallado']);
        Route::get('/patrones-citas', [App\Http\Controllers\Api\ReportesController::class, 'analisisPatronesCitas']);
    });
});

// Ruta de test para comprobar el funcionamiento del servidor
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API de Citas Médicas funcionando correctamente',
        'auth_type' => 'JWT',
        'timestamp' => now()
    ]);
});
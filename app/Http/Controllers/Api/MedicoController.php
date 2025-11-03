<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medico;
use App\Models\User;
use App\Models\Role;
use App\Traits\SyncUserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MedicoController extends Controller
{
    use SyncUserData;

    public function index(Request $request)
    {
        $query = Medico::conEspecialidad()->conUsuario();

        // Filtro por estado activo/inactivo
        if ($request->has('activo')) {
            $activo = $request->activo;
            if ($activo == 1) {
                $query->where('activo', true);
            } elseif ($activo == 0) {
                $query->where('activo', false);
            }
            // Si se especifica el filtro (incluso si es null o vacío), no aplicar el scope por defecto
        } else {
            // Por defecto mostrar todos los médicos si no se especifica filtro
            // No aplicar ningún filtro de estado
        }

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

        $medicos = $query->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $medicos
        ]);
    }

    public function store(Request $request)
    {
        // Verificar permisos - solo superadmin puede crear médicos
        if (!Auth::user() || !Auth::user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear médicos'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cedula' => 'required|string|unique:medicos',
            'registro_medico' => 'required|string|unique:medicos',
            'telefono' => 'nullable|string',
            'email' => 'required|email|unique:medicos',
            'especialidad_id' => 'required|exists:especialidades,id',
            'horarios_atencion' => 'required|array',
            'tarifa_consulta' => 'nullable|numeric|min:0',
            'biografia' => 'nullable|string|max:1000',
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

        // Crear el médico
        $medico = Medico::create($request->except(['password', 'password_confirmation']));

        // Crear el usuario asociado
        $user = User::create([
            'name' => $request->nombre . ' ' . $request->apellido,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'cedula' => $request->cedula,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => bcrypt($request->password),
            'rol' => 'medico',
            'activo' => true,
            'medico_id' => $medico->id,
            'role_id' => Role::where('slug', 'medico')->first()->id ?? null
        ]);

        // Cargar las relaciones
        $medico->load('especialidad', 'user');

        return response()->json([
            'success' => true,
            'message' => 'Médico y usuario creados exitosamente',
            'data' => $medico
        ], 201);
    }

    public function show($id)
    {
        $medico = Medico::conEspecialidad()->conUsuario()->find($id);

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
        // Verificar permisos - solo superadmin puede actualizar médicos
        if (!Auth::user() || !Auth::user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para actualizar médicos'
            ], 403);
        }

        $medico = Medico::find($id);

        if (!$medico) {
            return response()->json([
                'success' => false,
                'message' => 'Médico no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255',
            'cedula' => 'sometimes|required|string|unique:medicos,cedula,' . $id,
            'registro_medico' => 'sometimes|required|string|unique:medicos,registro_medico,' . $id,
            'telefono' => 'sometimes|nullable|string',
            'email' => 'sometimes|required|email|unique:medicos,email,' . $id,
            'especialidad_id' => 'sometimes|required|exists:especialidades,id',
            'horarios_atencion' => 'sometimes|required|array',
            'tarifa_consulta' => 'sometimes|nullable|numeric|min:0',
            'biografia' => 'sometimes|nullable|string|max:1000',
            'activo' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $medico->update($request->all());

        // Sincronizar datos con la tabla users usando el trait
        $this->syncUserData($request, $medico->user);

        $medico->load('especialidad', 'user');

        return response()->json([
            'success' => true,
            'message' => 'Médico actualizado exitosamente',
            'data' => $medico
        ]);
    }

    public function destroy($id)
    {
        // Verificar permisos - solo superadmin puede eliminar médicos
        if (!Auth::user() || !Auth::user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar médicos'
            ], 403);
        }

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

        // Eliminar el usuario asociado si existe
        if ($medico->user) {
            $medico->user->delete();
        }

        $medico->delete();

        return response()->json([
            'success' => true,
            'message' => 'Médico y usuario eliminados exitosamente'
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

        // Debug: Log de horarios del médico (comentar en producción)
        // \Log::info("Consultando disponibilidad para médico: " . $medico->nombre_completo . " (ID: " . $medico->id . ")");
        // \Log::info("Horarios de atención del médico: " . json_encode($medico->horarios_atencion));

        // Si el médico no tiene horarios configurados, asignar horarios por defecto
        if (!$medico->horarios_atencion) {
            // \Log::warning("El médico no tiene horarios configurados. Asignando horarios por defecto.", ['medico_id' => $medico->id]);
            $horariosPorDefecto = [
                'lunes' => ['08:00-17:00'],
                'martes' => ['08:00-17:00'],
                'miercoles' => ['08:00-17:00'],
                'jueves' => ['08:00-17:00'],
                'viernes' => ['08:00-17:00'],
                'sabado' => [],
                'domingo' => []
            ];
            $medico->horarios_atencion = $horariosPorDefecto;
            $medico->save();
        }

        // Si el médico no tiene disponibilidad configurada, asignar 'disponible' por defecto
        if (!$medico->disponibilidad) {
            // \Log::info("El médico no tiene disponibilidad configurada. Asignando 'disponible' por defecto.", ['medico_id' => $medico->id]);
            $medico->disponibilidad = 'disponible';
            $medico->save();
        }

        // Obtener citas del médico para esa fecha
        $citasOcupadas = $medico->citas()
                               ->whereDate('fecha_hora', $fecha)
                               ->whereIn('estado', ['programada', 'confirmada'])
                               ->pluck('fecha_hora')
                               ->map(function($fecha) {
                                   return $fecha->format('H:i');
                               });

        $responseData = [
            'medico' => $medico->nombre_completo,
            'fecha' => $fecha,
            'horarios_atencion' => $medico->horarios_atencion,
            'horas_ocupadas' => $citasOcupadas,
            'disponibilidad' => $medico->disponibilidad ?? 'disponible'
        ];

        // \Log::info("Respuesta de disponibilidad: " . json_encode($responseData));

        // Verificar que los horarios estén en el formato correcto
        if ($medico->horarios_atencion && is_array($medico->horarios_atencion)) {
            $diasSinHorarios = [];
            foreach ($medico->horarios_atencion as $dia => $horarios) {
                if (empty($horarios) || !is_array($horarios)) {
                    $diasSinHorarios[] = $dia;
                }
            }
            if (!empty($diasSinHorarios)) {
                \Log::warning("Días sin horarios configurados:", ['dias' => $diasSinHorarios, 'medico_id' => $medico->id]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $responseData
        ]);
    }

    /**
     * Verificar disponibilidad específica de un médico
     */
    public function checkAvailability($id, Request $request)
    {
        // Debug logs (comentar en producción)
        // \Log::info("=== CHECK AVAILABILITY REQUEST ===");
        // \Log::info("Médico ID: " . $id);
        // \Log::info("Request data: " . json_encode($request->all()));

        $medico = Medico::find($id);

        if (!$medico) {
            // \Log::error("Médico no encontrado:", $id);
            return response()->json([
                'success' => false,
                'message' => 'Médico no encontrado'
            ], 404);
        }

        $fechaHora = $request->get('fecha_hora');
        $timezoneInfo = $request->get('timezone_info');

        // \Log::info("Fecha y hora recibida: " . $fechaHora);
        // \Log::info("Información de zona horaria: " . json_encode($timezoneInfo));

        if (!$fechaHora) {
            return response()->json([
                'success' => false,
                'message' => 'Fecha y hora son requeridas'
            ], 400);
        }

        try {
            // Intentar diferentes formatos de fecha
            if (!str_ends_with($fechaHora, 'Z')) {
                // Formato sin Z: YYYY-MM-DDTHH:MM:SS (local)
                try {
                    $fechaCita = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:s', $fechaHora, config('app.timezone'));
                } catch (\Exception $e) {
                    // Si falla, intentar con milisegundos
                    $fechaCita = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:s.u', $fechaHora, config('app.timezone'));
                }
            } else {
                // Es una fecha UTC con Z
                $fechaCita = \Carbon\Carbon::parse($fechaHora)->setTimezone(config('app.timezone'));
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Formato de fecha y hora inválido: ' . $e->getMessage(),
                'fecha_recibida' => $fechaHora,
                'longitud' => strlen($fechaHora)
            ], 400);
        }

        // Verificar si la fecha es futura con buffer de 30 minutos
        // Usar la zona horaria del dispositivo si está disponible
        if ($timezoneInfo && isset($timezoneInfo['offset'])) {
            // El offset de JavaScript getTimezoneOffset() es positivo para zonas horarias negativas
            // Colombia: UTC-5, offset = 300 minutos
            // Para calcular la hora del dispositivo: hora_servidor - offset
            $deviceNow = now()->subMinutes(abs($timezoneInfo['offset']));
            $bufferMinutes = 30;
            $fechaMinima = $deviceNow->copy()->addMinutes($bufferMinutes);

            // Debug logs (comentar en producción)
            // \Log::info("Usando zona horaria del dispositivo:");
            // \Log::info("Offset del dispositivo: " . $timezoneInfo['offset']);
            // \Log::info("Hora del servidor: " . now()->toDateTimeString());
            // \Log::info("Hora del dispositivo: " . $deviceNow->toDateTimeString());
            // \Log::info("Fecha mínima con buffer: " . $fechaMinima->toDateTimeString());
        } else {
            // Fallback a zona horaria del servidor
            $now = now();
            $bufferMinutes = 30;
            $fechaMinima = $now->copy()->addMinutes($bufferMinutes);
        }

        if ($fechaCita < $fechaMinima) {
            return response()->json([
                'success' => true,
                'data' => [
                    'available' => false,
                    'reason' => 'La fecha debe ser futura (mínimo 30 minutos de anticipación)',
                    'fecha_recibida' => $fechaHora,
                    'fecha_convertida' => $fechaCita->toDateTimeString(),
                    'hora_servidor' => now()->toDateTimeString(),
                    'fecha_minima' => $fechaMinima->toDateTimeString(),
                    'buffer_minutes' => $bufferMinutes,
                    'timezone_info' => $timezoneInfo
                ]
            ]);
        }

        // Verificar si está dentro del horario de atención del médico
        $diaSemana = strtolower($fechaCita->format('l'));
        $diasEspanol = [
            'monday' => 'lunes',
            'tuesday' => 'martes',
            'wednesday' => 'miercoles',
            'thursday' => 'jueves',
            'friday' => 'viernes',
            'saturday' => 'sabado',
            'sunday' => 'domingo'
        ];
        $diaEspanol = $diasEspanol[$diaSemana] ?? $diaSemana;

        $horariosAtencion = $medico->horarios_atencion;
        $horarioDelDia = $horariosAtencion[$diaEspanol] ?? null;

        if (!$horarioDelDia) {
            return response()->json([
                'success' => true,
                'data' => [
                    'available' => false,
                    'reason' => 'El médico no atiende este día'
                ]
            ]);
        }

        // Verificar si la hora está dentro del horario de atención
        $horaCita = $fechaCita->format('H:i');
        $horaValida = false;

        // Debug logs (comentar en producción)
        // \Log::info("Verificando horario para médico: " . $medico->id);
        // \Log::info("Horarios del día: " . json_encode(['dia' => $diaEspanol, 'horarios' => $horarioDelDia]));
        // \Log::info("Hora de la cita: " . $horaCita);

        foreach ($horarioDelDia as $horario) {
            // \Log::info("Verificando horario: " . json_encode(['horario' => $horario, 'tipo' => gettype($horario)]));

            if (preg_match('/^(\d{2}:\d{2})-(\d{2}:\d{2})$/', $horario, $matches)) {
                $horaInicio = $matches[1];
                $horaFin = $matches[2];

                // \Log::info("Comparando horarios: " . json_encode([
                //     'hora_cita' => $horaCita,
                //     'hora_inicio' => $horaInicio,
                //     'hora_fin' => $horaFin,
                //     'cumple' => ($horaCita >= $horaInicio && $horaCita <= $horaFin)
                // ]));

                if ($horaCita >= $horaInicio && $horaCita <= $horaFin) {
                    $horaValida = true;
                    // \Log::info("Horario válido encontrado");
                    break;
                }
            } else {
                // \Log::warning("Formato de horario inválido:", ['horario' => $horario]);
            }
        }

        if (!$horaValida) {
            return response()->json([
                'success' => true,
                'data' => [
                    'available' => false,
                    'reason' => 'La hora no está dentro del horario de atención del médico',
                    'debug' => [
                        'hora_cita' => $horaCita,
                        'dia_semana' => $diaEspanol,
                        'horarios_del_dia' => $horarioDelDia,
                        'fecha_cita' => $fechaCita->toDateTimeString()
                    ]
                ]
            ]);
        }

        // Verificar disponibilidad del médico
        if ($medico->disponibilidad === 'no_disponible') {
            return response()->json([
                'success' => true,
                'data' => [
                    'available' => false,
                    'reason' => 'El médico no está disponible actualmente'
                ]
            ]);
        }

        if ($medico->disponibilidad === 'cita_en_curso') {
            return response()->json([
                'success' => true,
                'data' => [
                    'available' => false,
                    'reason' => 'El médico tiene una cita en curso'
                ]
            ]);
        }

        // Verificar si ya tiene una cita en esa fecha y hora
        // Comparar solo fecha y hora, ignorando segundos para evitar problemas de precisión
        $citaExistente = $medico->citas()
                               ->whereDate('fecha_hora', $fechaCita->toDateString())
                               ->whereTime('fecha_hora', $fechaCita->toTimeString())
                               ->whereIn('estado', ['programada', 'confirmada'])
                               ->exists();

        if ($citaExistente) {
            return response()->json([
                'success' => true,
                'data' => [
                    'available' => false,
                    'reason' => 'El médico ya tiene una cita programada en esta fecha y hora',
                    'fecha_cita' => $fechaCita->toDateTimeString()
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'available' => true,
                'message' => 'El médico está disponible en esta fecha y hora'
            ]
        ]);
    }

    /**
     * Actualizar disponibilidad del médico
     */
    public function updateDisponibilidad(Request $request, $id)
    {
        $medico = Medico::find($id);

        if (!$medico) {
            return response()->json([
                'success' => false,
                'message' => 'Médico no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'disponibilidad' => 'required|in:disponible,cita_en_curso,no_disponible'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $medico->update([
            'disponibilidad' => $request->disponibilidad
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Disponibilidad actualizada exitosamente',
            'data' => $medico
        ]);
    }
}
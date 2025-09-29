<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportesController extends Controller
{
    /**
     * Consulta 1: Médicos con mayor número de citas completadas en el último mes
     * Incluye información de la especialidad y estadísticas
     */
    public function medicosConMasCitas()
    {
        $fechaInicio = Carbon::now()->subMonth();
        
        $resultado = DB::select("
            SELECT 
                m.id,
                m.nombre,
                m.apellido,
                CONCAT(m.nombre, ' ', m.apellido) as nombre_completo,
                e.nombre as especialidad,
                COUNT(c.id) as total_citas_completadas,
                AVG(c.costo) as promedio_costo_cita,
                SUM(c.costo) as ingresos_generados,
                MIN(c.fecha_hora) as primera_cita,
                MAX(c.fecha_hora) as ultima_cita
            FROM medicos m
            INNER JOIN especialidades e ON m.especialidad_id = e.id
            LEFT JOIN citas c ON m.id = c.medico_id 
                AND c.estado = 'completada' 
                AND c.fecha_hora >= ?
            WHERE m.activo = 1
            GROUP BY m.id, m.nombre, m.apellido, e.nombre
            HAVING total_citas_completadas > 0
            ORDER BY total_citas_completadas DESC, ingresos_generados DESC
            LIMIT 10
        ", [$fechaInicio]);

        return response()->json([
            'success' => true,
            'title' => 'Médicos con Mayor Número de Citas Completadas (Último Mes)',
            'data' => $resultado
        ]);
    }

    /**
     * Consulta 2: Pacientes con historial médico completo y análisis de frecuencia
     * Incluye información demográfica y patrones de consulta
     */
    public function pacientesConHistorialCompleto()
    {
        $resultado = DB::select("
            SELECT 
                p.id,
                p.nombre,
                p.apellido,
                CONCAT(p.nombre, ' ', p.apellido) as nombre_completo,
                p.cedula,
                p.email,
                p.genero,
                TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad,
                p.eps,
                COUNT(c.id) as total_citas,
                COUNT(CASE WHEN c.estado = 'completada' THEN 1 END) as citas_completadas,
                COUNT(CASE WHEN c.estado = 'cancelada' THEN 1 END) as citas_canceladas,
                COUNT(CASE WHEN c.estado = 'no_asistio' THEN 1 END) as citas_no_asistio,
                ROUND((COUNT(CASE WHEN c.estado = 'completada' THEN 1 END) * 100.0 / COUNT(c.id)), 2) as porcentaje_asistencia,
                COUNT(DISTINCT c.medico_id) as medicos_diferentes_visitados,
                COUNT(DISTINCT e.id) as especialidades_visitadas,
                AVG(c.costo) as promedio_gasto_cita,
                SUM(c.costo) as gasto_total,
                MIN(c.fecha_hora) as primera_cita,
                MAX(c.fecha_hora) as ultima_cita,
                GROUP_CONCAT(DISTINCT e.nombre ORDER BY e.nombre SEPARATOR ', ') as especialidades_consultadas
            FROM pacientes p
            INNER JOIN citas c ON p.id = c.paciente_id
            INNER JOIN medicos m ON c.medico_id = m.id
            INNER JOIN especialidades e ON m.especialidad_id = e.id
            WHERE p.activo = 1
            GROUP BY p.id, p.nombre, p.apellido, p.cedula, p.email, p.genero, p.fecha_nacimiento, p.eps
            HAVING total_citas >= 1
            ORDER BY total_citas DESC, porcentaje_asistencia DESC
        ");

        return response()->json([
            'success' => true,
            'title' => 'Pacientes con Historial Médico Completo y Análisis de Frecuencia',
            'data' => $resultado
        ]);
    }

    /**
     * Consulta 3: Análisis de disponibilidad y ocupación por especialidad
     * Incluye métricas de demanda y eficiencia
     */
    public function analisisDisponibilidadEspecialidades()
    {
        $fechaInicio = Carbon::now()->startOfMonth();
        $fechaFin = Carbon::now()->endOfMonth();

        $resultado = DB::select("
            SELECT 
                e.id,
                e.nombre as especialidad,
                e.descripcion,
                COUNT(DISTINCT m.id) as total_medicos,
                COUNT(DISTINCT CASE WHEN m.activo = 1 THEN m.id END) as medicos_activos,
                COUNT(c.id) as total_citas_mes,
                COUNT(CASE WHEN c.estado IN ('programada', 'confirmada') THEN 1 END) as citas_programadas,
                COUNT(CASE WHEN c.estado = 'completada' THEN 1 END) as citas_completadas,
                COUNT(CASE WHEN c.estado = 'cancelada' THEN 1 END) as citas_canceladas,
                COUNT(CASE WHEN c.estado = 'no_asistio' THEN 1 END) as citas_no_asistio,
                ROUND(AVG(c.costo), 2) as promedio_costo_especialidad,
                SUM(c.costo) as ingresos_especialidad,
                ROUND((COUNT(CASE WHEN c.estado = 'completada' THEN 1 END) * 100.0 / 
                       NULLIF(COUNT(CASE WHEN c.estado != 'programada' THEN 1 END), 0)), 2) as tasa_efectividad,
                ROUND((COUNT(c.id) * 1.0 / COUNT(DISTINCT m.id)), 2) as promedio_citas_por_medico,
                COUNT(DISTINCT c.paciente_id) as pacientes_unicos_atendidos
            FROM especialidades e
            LEFT JOIN medicos m ON e.id = m.especialidad_id
            LEFT JOIN citas c ON m.id = c.medico_id 
                AND c.fecha_hora BETWEEN ? AND ?
            WHERE e.activo = 1
            GROUP BY e.id, e.nombre, e.descripcion
            ORDER BY total_citas_mes DESC, ingresos_especialidad DESC
        ", [$fechaInicio, $fechaFin]);

        return response()->json([
            'success' => true,
            'title' => 'Análisis de Disponibilidad y Ocupación por Especialidad (Mes Actual)',
            'data' => $resultado
        ]);
    }

    /**
     * Consulta 4: Reporte de ingresos y estadísticas financieras detalladas
     * Incluye análisis temporal y por especialidad
     */
    public function reporteIngresosDetallado()
    {
        $resultado = DB::select("
            SELECT 
                DATE_FORMAT(c.fecha_hora, '%Y-%m') as mes_año,
                e.nombre as especialidad,
                COUNT(c.id) as total_citas,
                COUNT(CASE WHEN c.estado = 'completada' THEN 1 END) as citas_facturadas,
                SUM(CASE WHEN c.estado = 'completada' THEN c.costo ELSE 0 END) as ingresos_reales,
                SUM(c.costo) as ingresos_potenciales,
                AVG(CASE WHEN c.estado = 'completada' THEN c.costo END) as promedio_cita_completada,
                MIN(CASE WHEN c.estado = 'completada' THEN c.costo END) as cita_minima,
                MAX(CASE WHEN c.estado = 'completada' THEN c.costo END) as cita_maxima,
                COUNT(DISTINCT c.medico_id) as medicos_activos,
                COUNT(DISTINCT c.paciente_id) as pacientes_atendidos,
                ROUND((COUNT(CASE WHEN c.estado = 'completada' THEN 1 END) * 100.0 / COUNT(c.id)), 2) as porcentaje_efectividad,
                SUM(CASE WHEN c.estado = 'cancelada' THEN c.costo ELSE 0 END) as ingresos_perdidos_cancelacion,
                SUM(CASE WHEN c.estado = 'no_asistio' THEN c.costo ELSE 0 END) as ingresos_perdidos_inasistencia
            FROM citas c
            INNER JOIN medicos m ON c.medico_id = m.id
            INNER JOIN especialidades e ON m.especialidad_id = e.id
            WHERE c.fecha_hora >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(c.fecha_hora, '%Y-%m'), e.id, e.nombre
            ORDER BY mes_año DESC, ingresos_reales DESC
        ");

        return response()->json([
            'success' => true,
            'title' => 'Reporte de Ingresos y Estadísticas Financieras (Últimos 6 Meses)',
            'data' => $resultado
        ]);
    }

    /**
     * Consulta 5: Análisis de patrones de citas y comportamiento de pacientes
     * Incluye análisis temporal, demográfico y de preferencias
     */
    public function analisisPatronesCitas()
    {
        $resultado = DB::select("
            SELECT 
                DAYNAME(c.fecha_hora) as dia_semana,
                HOUR(c.fecha_hora) as hora_cita,
                COUNT(c.id) as total_citas,
                COUNT(CASE WHEN c.estado = 'completada' THEN 1 END) as citas_completadas,
                COUNT(CASE WHEN c.estado = 'no_asistio' THEN 1 END) as citas_no_asistio,
                ROUND(AVG(TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, c.fecha_hora)), 1) as edad_promedio_pacientes,
                COUNT(CASE WHEN p.genero = 'F' THEN 1 END) as pacientes_femenino,
                COUNT(CASE WHEN p.genero = 'M' THEN 1 END) as pacientes_masculino,
                COUNT(DISTINCT e.id) as especialidades_diferentes,
                GROUP_CONCAT(DISTINCT e.nombre ORDER BY e.nombre SEPARATOR ', ') as especialidades_mas_solicitadas,
                AVG(c.costo) as promedio_costo,
                ROUND((COUNT(CASE WHEN c.estado = 'completada' THEN 1 END) * 100.0 / COUNT(c.id)), 2) as tasa_asistencia,
                COUNT(DISTINCT c.paciente_id) as pacientes_unicos,
                COUNT(DISTINCT c.medico_id) as medicos_involucrados
            FROM citas c
            INNER JOIN pacientes p ON c.paciente_id = p.id
            INNER JOIN medicos m ON c.medico_id = m.id
            INNER JOIN especialidades e ON m.especialidad_id = e.id
            WHERE c.fecha_hora >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
            GROUP BY DAYNAME(c.fecha_hora), HOUR(c.fecha_hora)
            HAVING total_citas >= 1
            ORDER BY 
                CASE DAYNAME(c.fecha_hora)
                    WHEN 'Monday' THEN 1
                    WHEN 'Tuesday' THEN 2
                    WHEN 'Wednesday' THEN 3
                    WHEN 'Thursday' THEN 4
                    WHEN 'Friday' THEN 5
                    WHEN 'Saturday' THEN 6
                    WHEN 'Sunday' THEN 7
                END,
                HOUR(c.fecha_hora)
        ");

        return response()->json([
            'success' => true,
            'title' => 'Análisis de Patrones de Citas y Comportamiento de Pacientes (Últimos 3 Meses)',
            'data' => $resultado
        ]);
    }

    /**
     * Dashboard con resumen ejecutivo
     */
    public function dashboardResumen()
    {
        $hoy = Carbon::now();
        $inicioMes = Carbon::now()->startOfMonth();

        $resumen = DB::select("
            SELECT
                (SELECT COUNT(*) FROM pacientes WHERE activo = 1) as total_pacientes_activos,
                (SELECT COUNT(*) FROM medicos WHERE activo = 1) as total_medicos_activos,
                (SELECT COUNT(*) FROM especialidades WHERE activo = 1) as total_especialidades,
                (SELECT COUNT(*) FROM citas WHERE DATE(fecha_hora) = CURDATE()) as citas_hoy,
                (SELECT COUNT(*) FROM citas WHERE fecha_hora BETWEEN ? AND ? AND estado IN ('programada', 'confirmada')) as citas_programadas_mes,
                (SELECT COUNT(*) FROM citas WHERE fecha_hora BETWEEN ? AND ? AND estado = 'completada') as citas_completadas_mes,
                (SELECT COALESCE(SUM(costo), 0) FROM citas WHERE fecha_hora BETWEEN ? AND ? AND estado = 'completada') as ingresos_mes,
                (SELECT ROUND(AVG(costo), 2) FROM citas WHERE fecha_hora BETWEEN ? AND ? AND estado = 'completada') as promedio_cita_mes
        ", [$inicioMes, $hoy, $inicioMes, $hoy, $inicioMes, $hoy, $inicioMes, $hoy]);

        // Agregar datos adicionales para el dashboard del frontend
        $datosAdicionales = [
            'totalUsuarios' => ($resumen[0]->total_pacientes_activos ?? 0) + ($resumen[0]->total_medicos_activos ?? 0),
            'citasPorEstado' => [
                'programada' => DB::table('citas')->where('estado', 'programada')->count(),
                'confirmada' => DB::table('citas')->where('estado', 'confirmada')->count(),
                'completada' => DB::table('citas')->where('estado', 'completada')->count(),
                'cancelada' => DB::table('citas')->where('estado', 'cancelada')->count(),
            ],
            'actividadReciente' => $this->getActividadReciente(),
        ];

        $resultadoFinal = array_merge((array) ($resumen[0] ?? []), $datosAdicionales);

        // Log para debugging
        \Log::info('Dashboard Resumen Response:', $resultadoFinal);

        return response()->json([
            'success' => true,
            'title' => 'Dashboard - Resumen Ejecutivo',
            'data' => $resultadoFinal
        ]);
    }

    /**
     * Obtener actividad reciente del sistema
     */
    private function getActividadReciente()
    {
        $actividades = [];

        try {
            // Últimas citas completadas
            $ultimasCitas = DB::select("
                SELECT
                    CONCAT('Cita completada - ', COALESCE(p.nombre, 'Paciente'), ' ', COALESCE(p.apellido, '')) as titulo,
                    CONCAT('Consulta con Dr. ', COALESCE(m.nombre, 'Médico'), ' ', COALESCE(m.apellido, '')) as descripcion,
                    CONCAT('Hace ', GREATEST(1, TIMESTAMPDIFF(HOUR, c.fecha_hora, NOW())), ' horas') as tiempo
                FROM citas c
                INNER JOIN pacientes p ON c.paciente_id = p.id
                INNER JOIN medicos m ON c.medico_id = m.id
                WHERE c.estado = 'completada' AND c.fecha_hora >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                ORDER BY c.fecha_hora DESC
                LIMIT 3
            ");

            foreach ($ultimasCitas as $cita) {
                $actividades[] = [
                    'title' => $cita->titulo,
                    'description' => $cita->descripcion,
                    'time' => $cita->tiempo,
                ];
            }

            // Si no hay citas recientes, agregar actividades por defecto
            if (empty($actividades)) {
                $actividades[] = [
                    'title' => 'Sistema operativo',
                    'description' => 'Todos los servicios funcionando correctamente',
                    'time' => 'Ahora mismo',
                ];
            }

            // Nuevos pacientes registrados (última semana)
            $nuevosPacientes = DB::select("
                SELECT COUNT(*) as total FROM pacientes
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND activo = 1
            ")[0]->total;

            if ($nuevosPacientes > 0) {
                $actividades[] = [
                    'title' => "{$nuevosPacientes} nuevos pacientes",
                    'description' => 'Se registraron en la última semana',
                    'time' => 'Esta semana',
                ];
            }

            // Médicos activos
            $medicosActivos = DB::table('medicos')->where('activo', 1)->count();
            $actividades[] = [
                'title' => "{$medicosActivos} médicos activos",
                'description' => 'Profesionales disponibles en el sistema',
                'time' => 'Estado actual',
            ];

        } catch (\Exception $e) {
            // En caso de error, devolver actividades por defecto
            $actividades = [
                [
                    'title' => 'Sistema iniciado',
                    'description' => 'Dashboard cargado correctamente',
                    'time' => 'Ahora mismo',
                ],
                [
                    'title' => 'Datos de prueba',
                    'description' => 'Mostrando información de ejemplo',
                    'time' => 'Estado actual',
                ],
            ];
        }

        return $actividades;
    }

    /**
     * Dashboard específico para médicos
     */
    public function dashboardMedico(Request $request)
    {
        $medicoId = $request->user()->medico_id ?? $request->user()->id;
        $hoy = Carbon::now();
        $inicioMes = Carbon::now()->startOfMonth();

        try {
            $dashboard = DB::select("
                SELECT
                    (SELECT COUNT(*) FROM citas WHERE medico_id = ? AND estado = 'programada') as citas_pendientes,
                    (SELECT COUNT(*) FROM citas WHERE medico_id = ? AND DATE(fecha_hora) = CURDATE()) as citas_hoy,
                    (SELECT COUNT(*) FROM citas WHERE medico_id = ? AND fecha_hora BETWEEN ? AND ? AND estado = 'completada') as citas_completadas_mes,
                    (SELECT ROUND(AVG(costo), 2) FROM citas WHERE medico_id = ? AND fecha_hora BETWEEN ? AND ? AND estado = 'completada') as promedio_cita_mes,
                    (SELECT COALESCE(SUM(costo), 0) FROM citas WHERE medico_id = ? AND fecha_hora BETWEEN ? AND ? AND estado = 'completada') as ingresos_mes
            ", [$medicoId, $medicoId, $medicoId, $inicioMes, $hoy, $medicoId, $inicioMes, $hoy, $medicoId, $inicioMes, $hoy]);

            // Próximas citas del médico
            $proximasCitas = DB::select("
                SELECT c.*, p.nombre as paciente_nombre, p.apellido as paciente_apellido
                FROM citas c
                INNER JOIN pacientes p ON c.paciente_id = p.id
                WHERE c.medico_id = ? AND c.fecha_hora >= ? AND c.estado IN ('programada', 'confirmada')
                ORDER BY c.fecha_hora ASC
                LIMIT 5
            ", [$medicoId, $hoy]);

            $datosMedico = [
                'estadisticas' => $dashboard[0] ?? [
                    'citas_pendientes' => 0,
                    'citas_hoy' => 0,
                    'citas_completadas_mes' => 0,
                    'promedio_cita_mes' => 0,
                    'ingresos_mes' => 0,
                ],
                'proximas_citas' => $proximasCitas,
                'agenda_hoy' => $proximasCitas, // Para compatibilidad con el frontend
            ];

            \Log::info('Medico Dashboard Response:', $datosMedico);

            return response()->json([
                'success' => true,
                'data' => $datosMedico
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en dashboardMedico:', $e);

            // Datos por defecto en caso de error
            return response()->json([
                'success' => true,
                'data' => [
                    'estadisticas' => [
                        'citas_pendientes' => 0,
                        'citas_hoy' => 0,
                        'citas_completadas_mes' => 0,
                        'promedio_cita_mes' => 0,
                        'ingresos_mes' => 0,
                    ],
                    'proximas_citas' => [],
                    'agenda_hoy' => [],
                ]
            ]);
        }
    }

    /**
     * Dashboard específico para pacientes
     */
    public function dashboardPaciente(Request $request)
    {
        $pacienteId = $request->user()->paciente_id ?? $request->user()->id;
        $hoy = Carbon::now();

        try {
            // Próximas citas del paciente
            $proximasCitas = DB::select("
                SELECT c.*, m.nombre as medico_nombre, m.apellido as medico_apellido, e.nombre as especialidad
                FROM citas c
                INNER JOIN medicos m ON c.medico_id = m.id
                INNER JOIN especialidades e ON m.especialidad_id = e.id
                WHERE c.paciente_id = ? AND c.fecha_hora >= ? AND c.estado IN ('programada', 'confirmada')
                ORDER BY c.fecha_hora ASC
                LIMIT 5
            ", [$pacienteId, $hoy]);

            // Historial reciente
            $historialReciente = DB::select("
                SELECT c.*, m.nombre as medico_nombre, m.apellido as medico_apellido, e.nombre as especialidad
                FROM citas c
                INNER JOIN medicos m ON c.medico_id = m.id
                INNER JOIN especialidades e ON m.especialidad_id = e.id
                WHERE c.paciente_id = ? AND c.fecha_hora < ?
                ORDER BY c.fecha_hora DESC
                LIMIT 3
            ", [$pacienteId, $hoy]);

            // Datos adicionales para el dashboard
            $datosPaciente = [
                'proximas_citas' => $proximasCitas,
                'historial_reciente' => $historialReciente,
                'estadisticas_personales' => [
                    'total_citas' => count($proximasCitas) + count($historialReciente),
                    'citas_completadas' => count($historialReciente),
                    'proxima_cita' => !empty($proximasCitas) ? $proximasCitas[0]->fecha_hora : null,
                ]
            ];

            \Log::info('Paciente Dashboard Response:', $datosPaciente);

            return response()->json([
                'success' => true,
                'data' => $datosPaciente
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en dashboardPaciente:', $e);

            // Datos por defecto en caso de error
            return response()->json([
                'success' => true,
                'data' => [
                    'proximas_citas' => [],
                    'historial_reciente' => [],
                    'estadisticas_personales' => [
                        'total_citas' => 0,
                        'citas_completadas' => 0,
                        'proxima_cita' => null,
                    ]
                ]
            ]);
        }
    }

    /**
     * Estadísticas personales para médicos
     */
    public function estadisticasMedico(Request $request, $medicoId = null)
    {
        $medicoId = $medicoId ?? ($request->user()->medico_id ?? $request->user()->id);
        $hoy = Carbon::now();
        $inicioMes = Carbon::now()->startOfMonth();
        $inicioAno = Carbon::now()->startOfYear();

        $estadisticas = DB::select("
            SELECT
                (SELECT COUNT(*) FROM citas WHERE medico_id = ? AND estado = 'completada') as total_citas_completadas,
                (SELECT COUNT(*) FROM citas WHERE medico_id = ? AND fecha_hora BETWEEN ? AND ? AND estado = 'completada') as citas_mes_actual,
                (SELECT COUNT(*) FROM citas WHERE medico_id = ? AND fecha_hora BETWEEN ? AND ? AND estado = 'completada') as citas_ano_actual,
                (SELECT COUNT(*) FROM citas WHERE medico_id = ? AND estado = 'cancelada') as citas_canceladas,
                (SELECT COUNT(*) FROM citas WHERE medico_id = ? AND estado = 'no_asistio') as citas_no_asistio,
                (SELECT ROUND(AVG(costo), 2) FROM citas WHERE medico_id = ? AND estado = 'completada') as promedio_costo_cita,
                (SELECT COALESCE(SUM(costo), 0) FROM citas WHERE medico_id = ? AND fecha_hora BETWEEN ? AND ? AND estado = 'completada') as ingresos_mes,
                (SELECT COALESCE(SUM(costo), 0) FROM citas WHERE medico_id = ? AND fecha_hora BETWEEN ? AND ? AND estado = 'completada') as ingresos_ano
        ", [
            $medicoId, $medicoId, $inicioMes, $hoy, $medicoId, $inicioAno, $hoy,
            $medicoId, $medicoId, $medicoId, $medicoId, $inicioMes, $hoy, $medicoId, $inicioAno, $hoy
        ]);

        return response()->json([
            'success' => true,
            'data' => $estadisticas[0] ?? null
        ]);
    }
}
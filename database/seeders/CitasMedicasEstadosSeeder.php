<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use Carbon\Carbon;

class CitasMedicasEstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener especialidades, médicos y pacientes existentes
        $especialidades = Especialidad::all();
        $medicos = Medico::all();
        $pacientes = Paciente::all();
        $users = User::all();

        if ($especialidades->isEmpty() || $medicos->isEmpty() || $pacientes->isEmpty()) {
            $this->command->error('No hay especialidades, médicos o pacientes para crear citas');
            return;
        }

        // Citas con diferentes estados para testing
        $citasEstadosData = [
            // Citas completadas (pasadas)
            [
                'fecha_hora' => Carbon::now()->subDays(5)->setHour(9)->setMinute(0),
                'motivo_consulta' => 'Consulta de seguimiento - Paciente evolucionando favorablemente',
                'estado' => 'completada',
                'costo' => 50000.00,
                'observaciones' => 'Tratamiento completado exitosamente',
            ],
            [
                'fecha_hora' => Carbon::now()->subDays(3)->setHour(14)->setMinute(0),
                'motivo_consulta' => 'Extracción dental - Procedimiento realizado sin complicaciones',
                'estado' => 'completada',
                'costo' => 120000.00,
                'observaciones' => 'Paciente sin dolor post-operatorio',
            ],
            [
                'fecha_hora' => Carbon::now()->subDays(7)->setHour(10)->setMinute(30),
                'motivo_consulta' => 'Terapia de rehabilitación - Sesión de fisioterapia completada',
                'estado' => 'completada',
                'costo' => 70000.00,
                'observaciones' => 'Mejora significativa en movilidad',
            ],

            // Citas canceladas
            [
                'fecha_hora' => Carbon::now()->addDays(2)->setHour(11)->setMinute(0),
                'motivo_consulta' => 'Consulta de rutina - Cita cancelada por el paciente',
                'estado' => 'cancelada',
                'costo' => 0.00,
                'observaciones' => 'Cancelada con 24 horas de anticipación',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(4)->setHour(16)->setMinute(0),
                'motivo_consulta' => 'Análisis de laboratorio - Cancelada por indisposición del médico',
                'estado' => 'cancelada',
                'costo' => 0.00,
                'observaciones' => 'Reprogramar en fecha posterior',
            ],

            // Citas rechazadas
            [
                'fecha_hora' => Carbon::now()->addDays(1)->setHour(15)->setMinute(0),
                'motivo_consulta' => 'Solicitud de cita urgente - Solicitud rechazada por falta de disponibilidad',
                'estado' => 'rechazada',
                'costo' => 0.00,
                'observaciones' => 'Ofrecer alternativas de horario',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(3)->setHour(8)->setMinute(0),
                'motivo_consulta' => 'Cirugía programada - Rechazada por contraindicaciones médicas',
                'estado' => 'rechazada',
                'costo' => 0.00,
                'observaciones' => 'Requiere evaluación previa',
            ],

            // Citas en progreso (para el día actual)
            [
                'fecha_hora' => Carbon::now()->setHour(9)->setMinute(0),
                'motivo_consulta' => 'Consulta médica general - Cita iniciada - evaluación en curso',
                'estado' => 'en_curso',
                'costo' => 50000.00,
                'observaciones' => null,
            ],
            [
                'fecha_hora' => Carbon::now()->setHour(10)->setMinute(0),
                'motivo_consulta' => 'Terapia psicológica - Sesión de terapia en progreso',
                'estado' => 'en_curso',
                'costo' => 70000.00,
                'observaciones' => null,
            ],

            // Citas pendientes de aprobación
            [
                'fecha_hora' => Carbon::now()->addDays(2)->setHour(14)->setMinute(0),
                'motivo_consulta' => 'Resonancia magnética - Requiere autorización previa',
                'estado' => 'pendiente_aprobacion',
                'costo' => 250000.00,
                'observaciones' => 'Esperando aprobación del seguro',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(3)->setHour(9)->setMinute(0),
                'motivo_consulta' => 'Cirugía mayor - Requiere aprobación de junta médica',
                'estado' => 'pendiente_aprobacion',
                'costo' => 500000.00,
                'observaciones' => 'Documentación enviada para revisión',
            ],

            // Citas no asistió
            [
                'fecha_hora' => Carbon::now()->subDays(2)->setHour(11)->setMinute(0),
                'motivo_consulta' => 'Consulta de seguimiento - Paciente no asistió',
                'estado' => 'no_asistio',
                'costo' => 50000.00,
                'observaciones' => 'No se presentó a la cita programada',
            ],
            [
                'fecha_hora' => Carbon::now()->subDays(4)->setHour(15)->setMinute(0),
                'motivo_consulta' => 'Vacunación COVID-19 - Segunda dosis no asistió',
                'estado' => 'no_asistio',
                'costo' => 0.00,
                'observaciones' => 'Reprogramar vacunación pendiente',
            ],

            // Citas con diferentes tipos de procedimientos
            [
                'fecha_hora' => Carbon::now()->addDays(4)->setHour(8)->setMinute(0),
                'motivo_consulta' => 'Colonoscopía - Preparación: dieta especial 48h antes',
                'estado' => 'confirmada',
                'costo' => 180000.00,
                'observaciones' => null,
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(7)->setHour(10)->setMinute(0),
                'motivo_consulta' => 'Inyección intra-articular - Aplicación de medicamento en articulación',
                'estado' => 'confirmada',
                'costo' => 80000.00,
                'observaciones' => null,
            ],

            // Citas de emergencia (históricas)
            [
                'fecha_hora' => Carbon::now()->subDays(10)->setHour(14)->setMinute(30),
                'motivo_consulta' => 'Atención de emergencia - Dolor torácico agudo - atendido inmediatamente',
                'estado' => 'completada',
                'costo' => 150000.00,
                'observaciones' => 'Evolución favorable - dado de alta',
            ],
            [
                'fecha_hora' => Carbon::now()->subDays(15)->setHour(20)->setMinute(0),
                'motivo_consulta' => 'Urgencia pediátrica - Fiebre alta en menor de 5 años',
                'estado' => 'completada',
                'costo' => 120000.00,
                'observaciones' => 'Tratamiento antibiótico iniciado',
            ],

            // Citas con costos variables
            [
                'fecha_hora' => Carbon::now()->addDays(8)->setHour(9)->setMinute(0),
                'motivo_consulta' => 'Aplicación de inyección - Aplicación de medicamento intramuscular',
                'estado' => 'confirmada',
                'costo' => 25000.00,
                'observaciones' => null,
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(9)->setHour(11)->setMinute(0),
                'motivo_consulta' => 'Cirugía mayor ambulatoria - Cirugía con recuperación en domicilio',
                'estado' => 'pendiente_aprobacion',
                'costo' => 750000.00,
                'observaciones' => 'Requiere evaluación pre-anestésica',
            ],
        ];

        // Crear las citas con diferentes estados
        foreach ($citasEstadosData as $index => $citaData) {
            // Asignar especialidad, médico y paciente aleatoriamente
            $especialidad = $especialidades->random();
            $medico = $medicos->where('especialidad_id', $especialidad->id)->random();
            $paciente = $pacientes->random();
            $creadoPor = $users->random();

            Cita::create([
                'fecha_hora' => $citaData['fecha_hora'],
                'motivo_consulta' => $citaData['motivo_consulta'],
                'estado' => $citaData['estado'],
                'costo' => $citaData['costo'],
                'observaciones' => $citaData['observaciones'],
                'paciente_id' => $paciente->id,
                'medico_id' => $medico->id,
            ]);
        }

        $this->command->info('Se crearon ' . count($citasEstadosData) . ' citas médicas con diferentes estados para testing');
    }
}
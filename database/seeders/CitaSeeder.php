<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Medico;
use Carbon\Carbon;

class CitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener pacientes y médicos para asignar a las citas
        $pacientes = Paciente::all();
        $medicos = Medico::all();

        $citas = [
            // Citas programadas (futuras)
            [
                'paciente_id' => $pacientes->where('cedula', '10000001')->first()->id,
                'medico_id' => $medicos->where('email', 'maria.gonzalez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(2)->setHour(10)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Control de hipertensión arterial',
                'observaciones' => 'Paciente con hipertensión controlada, requiere seguimiento mensual',
                'costo' => 50000.00,
                'descuento' => 0.00,
                'total_pagar' => 50000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000002')->first()->id,
                'medico_id' => $medicos->where('email', 'luis.hernandez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(3)->setHour(14)->setMinute(30),
                'estado' => 'programada',
                'motivo_consulta' => 'Control prenatal mes 6',
                'observaciones' => 'Embarazo de bajo riesgo, evolución normal',
                'costo' => 70000.00,
                'descuento' => 5000.00,
                'total_pagar' => 65000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000003')->first()->id,
                'medico_id' => $medicos->where('email', 'carlos.rodriguez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(5)->setHour(9)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Control cardiológico post infarto',
                'observaciones' => 'Paciente estable, requiere ecocardiograma de control',
                'costo' => 80000.00,
                'descuento' => 0.00,
                'total_pagar' => 80000.00,
            ],

            // Citas confirmadas
            [
                'paciente_id' => $pacientes->where('cedula', '10000004')->first()->id,
                'medico_id' => $medicos->where('email', 'sofia.lopez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(1)->setHour(16)->setMinute(0),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Vacunación anual y control de crecimiento',
                'observaciones' => 'Paciente con asma controlada, requiere vacuna antineumocócica',
                'costo' => 55000.00,
                'descuento' => 0.00,
                'total_pagar' => 55000.00,
                'fecha_confirmacion' => Carbon::now()->subHours(2),
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000005')->first()->id,
                'medico_id' => $medicos->where('email', 'ana.martinez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addHours(4)->setMinute(0),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Evaluación de manchas en la piel',
                'observaciones' => 'Manchas sospechosas en brazos y espalda, requiere biopsia',
                'costo' => 60000.00,
                'descuento' => 0.00,
                'total_pagar' => 60000.00,
                'fecha_confirmacion' => Carbon::now()->subHours(1),
            ],

            // Cita en curso (actual)
            [
                'paciente_id' => $pacientes->where('cedula', '10000006')->first()->id,
                'medico_id' => $medicos->where('email', 'fernando.morales@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->subMinutes(30),
                'estado' => 'en_curso',
                'motivo_consulta' => 'Control de hipotiroidismo',
                'observaciones' => 'Paciente con TSH elevada, ajuste de dosis de levotiroxina',
                'costo' => 70000.00,
                'descuento' => 0.00,
                'total_pagar' => 70000.00,
            ],

            // Citas completadas
            [
                'paciente_id' => $pacientes->where('cedula', '10000007')->first()->id,
                'medico_id' => $medicos->where('email', 'patricia.sanchez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->subDays(2)->setHour(15)->setMinute(0),
                'estado' => 'completada',
                'motivo_consulta' => 'Dolor lumbar crónico',
                'observaciones' => 'Lumbalgia mecánica, se indica fisioterapia',
                'diagnostico' => 'Lumbalgia mecánica crónica',
                'tratamiento' => 'Fisioterapia 3 veces por semana, analgésicos según necesidad',
                'receta_medica' => 'Ibuprofeno 400mg c/8h por 7 días',
                'costo' => 75000.00,
                'descuento' => 0.00,
                'total_pagar' => 75000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000008')->first()->id,
                'medico_id' => $medicos->where('email', 'diego.garcia@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->subDays(1)->setHour(11)->setMinute(30),
                'estado' => 'completada',
                'motivo_consulta' => 'Examen visual rutinario',
                'observaciones' => 'Paciente con presbicia incipiente',
                'diagnostico' => 'Presbicia',
                'tratamiento' => 'Lentes para lectura',
                'receta_medica' => 'Lentes +1.00 dioptrías para lectura',
                'costo' => 65000.00,
                'descuento' => 0.00,
                'total_pagar' => 65000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000009')->first()->id,
                'medico_id' => $medicos->where('email', 'carmen.ruiz@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->subDays(3)->setHour(10)->setMinute(0),
                'estado' => 'completada',
                'motivo_consulta' => 'Control de ansiedad',
                'observaciones' => 'Mejoría con tratamiento actual, continuar medicación',
                'diagnostico' => 'Trastorno de ansiedad generalizada',
                'tratamiento' => 'Continuar sertralina 50mg/día, terapia cognitivo-conductual',
                'receta_medica' => 'Sertralina 50mg, 30 comprimidos',
                'costo' => 85000.00,
                'descuento' => 10000.00,
                'total_pagar' => 75000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000010')->first()->id,
                'medico_id' => $medicos->where('email', 'roberto.perez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->subDays(5)->setHour(9)->setMinute(30),
                'estado' => 'completada',
                'motivo_consulta' => 'Dolor de cabeza recurrente',
                'observaciones' => 'Cefalea tensional, se descartan causas orgánicas',
                'diagnostico' => 'Cefalea tensional',
                'tratamiento' => 'Relajantes musculares, técnicas de relajación',
                'receta_medica' => 'Paracetamol 500mg c/6h según necesidad',
                'costo' => 90000.00,
                'descuento' => 0.00,
                'total_pagar' => 90000.00,
            ],

            // Citas canceladas
            [
                'paciente_id' => $pacientes->where('cedula', '10000001')->first()->id,
                'medico_id' => $medicos->where('email', 'carlos.rodriguez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->subDays(1)->setHour(14)->setMinute(0),
                'estado' => 'cancelada',
                'motivo_consulta' => 'Control cardiológico',
                'observaciones' => 'Cita cancelada por el paciente',
                'costo' => 80000.00,
                'descuento' => 0.00,
                'total_pagar' => 0.00,
                'fecha_cancelacion' => Carbon::now()->subDays(2),
                'motivo_cancelacion' => 'Enfermedad del paciente',
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000002')->first()->id,
                'medico_id' => $medicos->where('email', 'ana.martinez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->subDays(3)->setHour(10)->setMinute(0),
                'estado' => 'cancelada',
                'motivo_consulta' => 'Revisión de lunar',
                'observaciones' => 'Cita cancelada por la clínica',
                'costo' => 60000.00,
                'descuento' => 0.00,
                'total_pagar' => 0.00,
                'fecha_cancelacion' => Carbon::now()->subDays(4),
                'motivo_cancelacion' => 'Indisposición del médico',
            ],

            // Cita no asistió
            [
                'paciente_id' => $pacientes->where('cedula', '10000003')->first()->id,
                'medico_id' => $medicos->where('email', 'maria.gonzalez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->subDays(1)->setHour(8)->setMinute(0),
                'estado' => 'no_asistio',
                'motivo_consulta' => 'Control de diabetes',
                'observaciones' => 'Paciente no se presentó a la cita programada',
                'costo' => 50000.00,
                'descuento' => 0.00,
                'total_pagar' => 50000.00,
            ],

            // Citas adicionales para mejor cobertura de pruebas
            [
                'paciente_id' => $pacientes->where('cedula', '10000004')->first()->id,
                'medico_id' => $medicos->where('email', 'diego.garcia@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(7)->setHour(11)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Control de miopía',
                'observaciones' => 'Paciente con miopía progresiva, requiere cambio de lentes',
                'costo' => 65000.00,
                'descuento' => 0.00,
                'total_pagar' => 65000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000005')->first()->id,
                'medico_id' => $medicos->where('email', 'roberto.perez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(4)->setHour(16)->setMinute(30),
                'estado' => 'programada',
                'motivo_consulta' => 'Dolor de cabeza crónico',
                'observaciones' => 'Cefalea tensional de larga evolución, requiere evaluación neurológica',
                'costo' => 90000.00,
                'descuento' => 5000.00,
                'total_pagar' => 85000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000006')->first()->id,
                'medico_id' => $medicos->where('email', 'fernando.morales@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(6)->setHour(9)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Control de tiroides',
                'observaciones' => 'Hipotiroidismo subclínico, seguimiento de TSH',
                'costo' => 70000.00,
                'descuento' => 0.00,
                'total_pagar' => 70000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000007')->first()->id,
                'medico_id' => $medicos->where('email', 'patricia.sanchez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(10)->setHour(15)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Lesión deportiva',
                'observaciones' => 'Esguince de tobillo durante partido de fútbol',
                'costo' => 75000.00,
                'descuento' => 0.00,
                'total_pagar' => 75000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000008')->first()->id,
                'medico_id' => $medicos->where('email', 'carmen.ruiz@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(8)->setHour(10)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Ansiedad por estrés laboral',
                'observaciones' => 'Síntomas de ansiedad moderada, primera evaluación',
                'costo' => 85000.00,
                'descuento' => 10000.00,
                'total_pagar' => 75000.00,
            ],

            // Cita completada adicional con tratamiento detallado
            [
                'paciente_id' => $pacientes->where('cedula', '10000009')->first()->id,
                'medico_id' => $medicos->where('email', 'ana.martinez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->subDays(7)->setHour(14)->setMinute(0),
                'estado' => 'completada',
                'motivo_consulta' => 'Acné severo',
                'observaciones' => 'Acné noduloquístico en rostro y espalda',
                'diagnostico' => 'Acné noduloquístico moderado-severo',
                'tratamiento' => 'Tratamiento combinado con isotretinoína y antibióticos tópicos',
                'receta_medica' => 'Isotretinoína 20mg/día por 6 meses, Peróxido de benzoilo 5% tópico',
                'costo' => 60000.00,
                'descuento' => 0.00,
                'total_pagar' => 60000.00,
            ],

            // Cita cancelada por el médico
            [
                'paciente_id' => $pacientes->where('cedula', '10000010')->first()->id,
                'medico_id' => $medicos->where('email', 'luis.hernandez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->subDays(2)->setHour(10)->setMinute(0),
                'estado' => 'cancelada',
                'motivo_consulta' => 'Control prenatal',
                'observaciones' => 'Cita reprogramada por emergencia médica del paciente',
                'costo' => 70000.00,
                'descuento' => 0.00,
                'total_pagar' => 0.00,
                'fecha_cancelacion' => Carbon::now()->subDays(3),
                'motivo_cancelacion' => 'Emergencia médica del paciente',
            ],
        ];

        foreach ($citas as $cita) {
            Cita::create($cita);
        }
    }
}
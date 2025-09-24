<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Medico;
use Carbon\Carbon;

class CitasPruebasFrontendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener pacientes y médicos para asignar a las citas
        $pacientes = Paciente::all();
        $medicos = Medico::all();

        $citasPruebas = [
            // Citas para hoy - diferentes horarios
            [
                'paciente_id' => $pacientes->where('cedula', '10000001')->first()->id,
                'medico_id' => $medicos->where('email', 'maria.gonzalez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::today()->setHour(8)->setMinute(0),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Control de hipertensión - CITA DE HOY',
                'observaciones' => 'Cita programada para hoy, requiere toma de presión arterial',
                'costo' => 50000.00,
                'descuento' => 0.00,
                'total_pagar' => 50000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000002')->first()->id,
                'medico_id' => $medicos->where('email', 'carlos.rodriguez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::today()->setHour(10)->setMinute(30),
                'estado' => 'en_curso',
                'motivo_consulta' => 'Ecocardiograma - EN CURSO',
                'observaciones' => 'Cita en curso actualmente, paciente en sala de espera',
                'costo' => 150000.00,
                'descuento' => 15000.00,
                'total_pagar' => 135000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000003')->first()->id,
                'medico_id' => $medicos->where('email', 'ana.martinez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::today()->setHour(14)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Control dermatológico - PARA HOY',
                'observaciones' => 'Cita programada para hoy tarde, paciente requiere receta',
                'costo' => 60000.00,
                'descuento' => 0.00,
                'total_pagar' => 60000.00,
            ],

            // Citas próximas - mañana y pasado mañana
            [
                'paciente_id' => $pacientes->where('cedula', '10000004')->first()->id,
                'medico_id' => $medicos->where('email', 'diego.garcia@clinica.com')->first()->id,
                'fecha_hora' => Carbon::tomorrow()->setHour(9)->setMinute(0),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Control de glaucoma - MAÑANA',
                'observaciones' => 'Cita confirmada para mañana, requiere medición de presión intraocular',
                'costo' => 75000.00,
                'descuento' => 0.00,
                'total_pagar' => 75000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000005')->first()->id,
                'medico_id' => $medicos->where('email', 'roberto.perez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::tomorrow()->setHour(15)->setMinute(30),
                'estado' => 'programada',
                'motivo_consulta' => 'Migraña crónica - PASADO MAÑANA',
                'observaciones' => 'Cita para pasado mañana, paciente con cefalea recurrente',
                'costo' => 110000.00,
                'descuento' => 5000.00,
                'total_pagar' => 105000.00,
            ],

            // Citas pendientes de aprobación
            [
                'paciente_id' => $pacientes->where('cedula', '10000006')->first()->id,
                'medico_id' => $medicos->where('email', 'luis.hernandez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(3)->setHour(10)->setMinute(0),
                'estado' => 'pendiente_aprobacion',
                'motivo_consulta' => 'Control prenatal - PENDIENTE APROBACIÓN',
                'observaciones' => 'Cita pendiente de aprobación por el médico, requiere autorización',
                'costo' => 85000.00,
                'descuento' => 10000.00,
                'total_pagar' => 75000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000007')->first()->id,
                'medico_id' => $medicos->where('email', 'patricia.sanchez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(4)->setHour(16)->setMinute(0),
                'estado' => 'pendiente_aprobacion',
                'motivo_consulta' => 'Infiltración articular - PENDIENTE APROBACIÓN',
                'observaciones' => 'Cita pendiente de aprobación, requiere autorización previa',
                'costo' => 200000.00,
                'descuento' => 20000.00,
                'total_pagar' => 180000.00,
            ],

            // Citas rechazadas
            [
                'paciente_id' => $pacientes->where('cedula', '10000008')->first()->id,
                'medico_id' => $medicos->where('email', 'fernando.morales@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(2)->setHour(11)->setMinute(0),
                'estado' => 'rechazada',
                'motivo_consulta' => 'Control de diabetes - RECHAZADA',
                'observaciones' => 'Cita rechazada por el médico, requiere reagendamiento',
                'costo' => 90000.00,
                'descuento' => 0.00,
                'total_pagar' => 0.00,
                'fecha_rechazo' => Carbon::now()->subHours(2),
                'motivo_rechazo' => 'Conflicto de horarios con cirugía programada',
            ],

            // Citas completadas recientes (últimas 24 horas)
            [
                'paciente_id' => $pacientes->where('cedula', '10000009')->first()->id,
                'medico_id' => $medicos->where('email', 'carmen.ruiz@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->subHours(3),
                'estado' => 'completada',
                'motivo_consulta' => 'Terapia psicológica - COMPLETADA HOY',
                'observaciones' => 'Sesión completada exitosamente, paciente muestra mejoría',
                'diagnostico' => 'Trastorno de ansiedad generalizada en remisión parcial',
                'tratamiento' => 'Continuar terapia cognitivo-conductual semanal',
                'costo' => 120000.00,
                'descuento' => 0.00,
                'total_pagar' => 120000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000010')->first()->id,
                'medico_id' => $medicos->where('email', 'sofia.lopez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->subHours(6),
                'estado' => 'completada',
                'motivo_consulta' => 'Vacunación infantil - COMPLETADA HOY',
                'observaciones' => 'Vacunación completada sin complicaciones',
                'diagnostico' => 'Vacunación al día según esquema nacional',
                'tratamiento' => 'Próxima vacuna en 2 meses',
                'costo' => 70000.00,
                'descuento' => 5000.00,
                'total_pagar' => 65000.00,
            ],

            // Citas con diferentes costos y descuentos
            [
                'paciente_id' => $pacientes->where('cedula', '10000001')->first()->id,
                'medico_id' => $medicos->where('email', 'carlos.rodriguez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(7)->setHour(8)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Prueba de esfuerzo - COSTO ALTO',
                'observaciones' => 'Prueba de esfuerzo con protocolo de Bruce',
                'costo' => 250000.00,
                'descuento' => 25000.00,
                'total_pagar' => 225000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000002')->first()->id,
                'medico_id' => $medicos->where('email', 'ana.martinez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(8)->setHour(14)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Consulta dermatológica - COSTO BAJO',
                'observaciones' => 'Consulta básica de dermatología',
                'costo' => 40000.00,
                'descuento' => 0.00,
                'total_pagar' => 40000.00,
            ],

            // Citas para diferentes especialidades
            [
                'paciente_id' => $pacientes->where('cedula', '10000003')->first()->id,
                'medico_id' => $medicos->where('email', 'diego.garcia@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(5)->setHour(9)->setMinute(30),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Cirugía refractiva - OFTALMOLOGÍA',
                'observaciones' => 'Evaluación para cirugía LASIK',
                'costo' => 500000.00,
                'descuento' => 50000.00,
                'total_pagar' => 450000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000004')->first()->id,
                'medico_id' => $medicos->where('email', 'roberto.perez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(6)->setHour(11)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'EEG de control - NEUROLOGÍA',
                'observaciones' => 'Electroencefalograma de control',
                'costo' => 180000.00,
                'descuento' => 0.00,
                'total_pagar' => 180000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000005')->first()->id,
                'medico_id' => $medicos->where('email', 'luis.hernandez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(9)->setHour(10)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Ecografía obstétrica - GINECOLOGÍA',
                'observaciones' => 'Ecografía morfológica del segundo trimestre',
                'costo' => 120000.00,
                'descuento' => 10000.00,
                'total_pagar' => 110000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000006')->first()->id,
                'medico_id' => $medicos->where('email', 'patricia.sanchez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(10)->setHour(15)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Resonancia magnética - TRAUMATOLOGÍA',
                'observaciones' => 'RMN de rodilla por lesión deportiva',
                'costo' => 350000.00,
                'descuento' => 35000.00,
                'total_pagar' => 315000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000007')->first()->id,
                'medico_id' => $medicos->where('email', 'fernando.morales@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(11)->setHour(8)->setMinute(30),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Curva de glucemia - ENDOCRINOLOGÍA',
                'observaciones' => 'Curva de glucemia de 5 puntos',
                'costo' => 95000.00,
                'descuento' => 0.00,
                'total_pagar' => 95000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000008')->first()->id,
                'medico_id' => $medicos->where('email', 'carmen.ruiz@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(12)->setHour(16)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Terapia familiar - PSIQUIATRÍA',
                'observaciones' => 'Sesión de terapia familiar',
                'costo' => 150000.00,
                'descuento' => 15000.00,
                'total_pagar' => 135000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000009')->first()->id,
                'medico_id' => $medicos->where('email', 'sofia.lopez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(13)->setHour(9)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Control pediátrico - PEDIATRÍA',
                'observaciones' => 'Control de crecimiento y desarrollo',
                'costo' => 60000.00,
                'descuento' => 0.00,
                'total_pagar' => 60000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000010')->first()->id,
                'medico_id' => $medicos->where('email', 'maria.gonzalez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(14)->setHour(11)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Chequeo preventivo - MEDICINA GENERAL',
                'observaciones' => 'Chequeo médico anual completo',
                'costo' => 80000.00,
                'descuento' => 8000.00,
                'total_pagar' => 72000.00,
            ],
        ];

        foreach ($citasPruebas as $cita) {
            Cita::create($cita);
        }

        $this->command->info('Citas de pruebas para frontend creadas exitosamente con diferentes estados y escenarios.');
    }
}
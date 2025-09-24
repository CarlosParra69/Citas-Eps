<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use Carbon\Carbon;

class CitasMedicasSeeder extends Seeder
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

        // Datos de citas médicas realistas
        $citasData = [
            [
                'fecha_hora' => Carbon::now()->addDays(1)->setHour(9)->setMinute(0),
                'motivo_consulta' => 'Consulta general de medicina interna - Paciente con síntomas de fatiga y dolor de cabeza',
                'estado' => 'programada',
                'costo' => 50000.00,
                'observaciones' => 'Primera consulta con el paciente',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(1)->setHour(10)->setMinute(30),
                'motivo_consulta' => 'Control prenatal - Paciente en tercer trimestre de embarazo',
                'estado' => 'confirmada',
                'costo' => 75000.00,
                'observaciones' => 'Control rutinario del embarazo',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(2)->setHour(14)->setMinute(0),
                'motivo_consulta' => 'Evaluación cardiológica - Paciente con hipertensión arterial',
                'estado' => 'programada',
                'costo' => 80000.00,
                'observaciones' => 'Evaluación cardiovascular completa',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(2)->setHour(16)->setMinute(0),
                'motivo_consulta' => 'Consulta dermatológica - Revisión de lunares y manchas en la piel',
                'estado' => 'confirmada',
                'costo' => 60000.00,
                'observaciones' => 'Examen dermatológico preventivo',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(3)->setHour(8)->setMinute(30),
                'motivo_consulta' => 'Terapia física - Sesión de fisioterapia para lesión de rodilla',
                'estado' => 'programada',
                'costo' => 70000.00,
                'observaciones' => 'Rehabilitación de lesión deportiva',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(3)->setHour(11)->setMinute(0),
                'motivo_consulta' => 'Consulta pediátrica - Control de crecimiento y desarrollo infantil',
                'estado' => 'confirmada',
                'costo' => 55000.00,
                'observaciones' => 'Control pediátrico mensual',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(4)->setHour(9)->setMinute(0),
                'motivo_consulta' => 'Cirugía menor - Extracción de quiste sebáceo',
                'estado' => 'programada',
                'costo' => 150000.00,
                'observaciones' => 'Procedimiento ambulatorio',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(4)->setHour(15)->setMinute(0),
                'motivo_consulta' => 'Consulta oftalmológica - Examen de la vista y prescripción de lentes',
                'estado' => 'confirmada',
                'costo' => 65000.00,
                'observaciones' => 'Evaluación visual completa',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(5)->setHour(10)->setMinute(0),
                'motivo_consulta' => 'Vacunación - Aplicación de vacuna contra la influenza',
                'estado' => 'programada',
                'costo' => 30000.00,
                'observaciones' => 'Vacunación estacional',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(5)->setHour(14)->setMinute(30),
                'motivo_consulta' => 'Consulta nutricional - Plan de alimentación para control de peso',
                'estado' => 'confirmada',
                'costo' => 55000.00,
                'observaciones' => 'Consulta nutricional inicial',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(6)->setHour(8)->setMinute(0),
                'motivo_consulta' => 'Endoscopía - Endoscopía digestiva alta programada',
                'estado' => 'programada',
                'costo' => 200000.00,
                'observaciones' => 'Procedimiento endoscópico',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(6)->setHour(16)->setMinute(0),
                'motivo_consulta' => 'Consulta psicológica - Terapia para manejo de estrés y ansiedad',
                'estado' => 'confirmada',
                'costo' => 70000.00,
                'observaciones' => 'Terapia psicológica semanal',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(7)->setHour(9)->setMinute(30),
                'motivo_consulta' => 'Ecografía - Ecografía abdominal completa',
                'estado' => 'programada',
                'costo' => 90000.00,
                'observaciones' => 'Estudio de diagnóstico por imágenes',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(7)->setHour(11)->setMinute(0),
                'motivo_consulta' => 'Consulta odontológica - Revisión dental y limpieza',
                'estado' => 'confirmada',
                'costo' => 45000.00,
                'observaciones' => 'Mantenimiento dental preventivo',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(8)->setHour(14)->setMinute(0),
                'motivo_consulta' => 'Rehabilitación - Sesión de rehabilitación post-operatoria',
                'estado' => 'programada',
                'costo' => 80000.00,
                'observaciones' => 'Rehabilitación post-quirúrgica',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(8)->setHour(17)->setMinute(0),
                'motivo_consulta' => 'Consulta ginecológica - Control ginecológico anual',
                'estado' => 'confirmada',
                'costo' => 60000.00,
                'observaciones' => 'Control ginecológico preventivo',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(9)->setHour(9)->setMinute(0),
                'motivo_consulta' => 'Análisis clínicos - Toma de muestras para análisis de sangre',
                'estado' => 'programada',
                'costo' => 40000.00,
                'observaciones' => 'Estudios de laboratorio',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(9)->setHour(15)->setMinute(30),
                'motivo_consulta' => 'Consulta traumatológica - Evaluación de lesión deportiva',
                'estado' => 'confirmada',
                'costo' => 65000.00,
                'observaciones' => 'Evaluación traumatológica',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(10)->setHour(10)->setMinute(0),
                'motivo_consulta' => 'Cirugía ambulatoria - Cirugía de cataratas programada',
                'estado' => 'programada',
                'costo' => 300000.00,
                'observaciones' => 'Cirugía oftalmológica ambulatoria',
            ],
            [
                'fecha_hora' => Carbon::now()->addDays(10)->setHour(16)->setMinute(0),
                'motivo_consulta' => 'Medicina alternativa - Sesión de acupuntura para dolor crónico',
                'estado' => 'confirmada',
                'costo' => 55000.00,
                'observaciones' => 'Terapia alternativa para dolor',
            ],
        ];

        // Crear las citas médicas
        foreach ($citasData as $index => $citaData) {
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

        $this->command->info('Se crearon ' . count($citasData) . ' citas médicas con datos realistas');
    }
}
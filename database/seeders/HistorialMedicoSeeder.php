<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HistorialMedico;
use App\Models\Paciente;
use Carbon\Carbon;

class HistorialMedicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pacientes = Paciente::all();
        $medicos = \App\Models\Medico::all();

        $historiales = [
            [
                'paciente_id' => $pacientes->where('cedula', '10000001')->first()->id,
                'medico_id' => $medicos->first()->id,
                'fecha_consulta' => Carbon::now()->subDays(30),
                'motivo_consulta' => 'Control de hipertensión arterial',
                'diagnostico' => 'Hipertensión arterial controlada',
                'tratamiento' => 'Losartán 50mg/día, cambios en estilo de vida',
                'observaciones' => 'Presión arterial estable en 130/80 mmHg',
                'peso' => 75.5,
                'altura' => 1.70,
                'presion_sistolica' => 130,
                'presion_diastolica' => 80,
                'frecuencia_cardiaca' => 72,
                'temperatura' => 36.5,
                'recomendaciones' => 'Paciente responde bien al tratamiento actual',
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000002')->first()->id,
                'medico_id' => $medicos->first()->id,
                'fecha_consulta' => Carbon::now()->subDays(15),
                'motivo_consulta' => 'Control prenatal mes 6',
                'diagnostico' => 'Embarazo de 6 meses - evolución normal',
                'tratamiento' => 'Suplementos prenatales, controles mensuales',
                'observaciones' => 'Ecografía normal, bebé con crecimiento adecuado',
                'peso' => 68.0,
                'altura' => 1.65,
                'presion_sistolica' => 110,
                'presion_diastolica' => 70,
                'frecuencia_cardiaca' => 78,
                'temperatura' => 36.2,
                'recomendaciones' => 'Embarazo de bajo riesgo, continuar controles regulares',
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000003')->first()->id,
                'medico_id' => $medicos->first()->id,
                'fecha_consulta' => Carbon::now()->subDays(45),
                'motivo_consulta' => 'Control cardiológico post infarto',
                'diagnostico' => 'Infarto de miocardio - seguimiento post alta',
                'tratamiento' => 'Aspirina 100mg/día, Atorvastatina 20mg/día, rehabilitación cardíaca',
                'observaciones' => 'Evolución favorable, sin dolor precordial',
                'peso' => 82.0,
                'altura' => 1.75,
                'presion_sistolica' => 125,
                'presion_diastolica' => 75,
                'frecuencia_cardiaca' => 68,
                'temperatura' => 36.4,
                'recomendaciones' => 'Paciente estable, continuar con plan de rehabilitación',
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000004')->first()->id,
                'medico_id' => $medicos->first()->id,
                'fecha_consulta' => Carbon::now()->subDays(10),
                'motivo_consulta' => 'Control de asma infantil',
                'diagnostico' => 'Asma infantil controlada',
                'tratamiento' => 'Budesonida inhalada 200mcg/día',
                'observaciones' => 'Sin crisis asmáticas en los últimos 3 meses',
                'peso' => 45.0,
                'altura' => 1.45,
                'presion_sistolica' => 100,
                'presion_diastolica' => 60,
                'frecuencia_cardiaca' => 85,
                'temperatura' => 36.3,
                'recomendaciones' => 'Control adecuado del asma, continuar tratamiento',
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000005')->first()->id,
                'medico_id' => $medicos->first()->id,
                'fecha_consulta' => Carbon::now()->subDays(20),
                'motivo_consulta' => 'Rinitis alérgica crónica',
                'diagnostico' => 'Rinitis alérgica crónica',
                'tratamiento' => 'Loratadina 10mg/día, budesonida nasal',
                'observaciones' => 'Mejoría significativa de síntomas nasales',
                'peso' => 70.0,
                'altura' => 1.72,
                'presion_sistolica' => 120,
                'presion_diastolica' => 75,
                'frecuencia_cardiaca' => 75,
                'temperatura' => 36.5,
                'recomendaciones' => 'Respuesta favorable al tratamiento antialérgico',
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000006')->first()->id,
                'medico_id' => $medicos->first()->id,
                'fecha_consulta' => Carbon::now()->subDays(25),
                'motivo_consulta' => 'Control de hipotiroidismo',
                'diagnostico' => 'Hipotiroidismo subclínico',
                'tratamiento' => 'Levotiroxina 50mcg/día',
                'observaciones' => 'TSH en rango normal, síntomas controlados',
                'peso' => 65.0,
                'altura' => 1.60,
                'presion_sistolica' => 115,
                'presion_diastolica' => 70,
                'frecuencia_cardiaca' => 70,
                'temperatura' => 36.4,
                'recomendaciones' => 'Función tiroidea estable con tratamiento actual',
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000007')->first()->id,
                'medico_id' => $medicos->first()->id,
                'fecha_consulta' => Carbon::now()->subDays(35),
                'motivo_consulta' => 'Control de enfermedad renal',
                'diagnostico' => 'Enfermedad renal crónica estadio 2',
                'tratamiento' => 'Control de presión arterial, dieta baja en proteínas',
                'observaciones' => 'Creatinina estable, sin progresión de la enfermedad',
                'peso' => 78.0,
                'altura' => 1.68,
                'presion_sistolica' => 135,
                'presion_diastolica' => 85,
                'frecuencia_cardiaca' => 72,
                'temperatura' => 36.6,
                'recomendaciones' => 'Enfermedad estable, continuar seguimiento estrecho',
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000008')->first()->id,
                'medico_id' => $medicos->first()->id,
                'fecha_consulta' => Carbon::now()->subDays(12),
                'motivo_consulta' => 'Dolor lumbar',
                'diagnostico' => 'Lumbalgia mecánica',
                'tratamiento' => 'Fisioterapia, antiinflamatorios no esteroideos',
                'observaciones' => 'Mejoría del dolor con tratamiento fisioterapéutico',
                'peso' => 62.0,
                'altura' => 1.58,
                'presion_sistolica' => 110,
                'presion_diastolica' => 70,
                'frecuencia_cardiaca' => 76,
                'temperatura' => 36.3,
                'recomendaciones' => 'Respuesta favorable a tratamiento conservador',
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000009')->first()->id,
                'medico_id' => $medicos->first()->id,
                'fecha_consulta' => Carbon::now()->subDays(40),
                'motivo_consulta' => 'Ansiedad y estrés',
                'diagnostico' => 'Trastorno de ansiedad generalizada',
                'tratamiento' => 'Sertralina 50mg/día, terapia cognitivo-conductual',
                'observaciones' => 'Reducción significativa de síntomas ansiosos',
                'peso' => 67.0,
                'altura' => 1.70,
                'presion_sistolica' => 120,
                'presion_diastolica' => 75,
                'frecuencia_cardiaca' => 74,
                'temperatura' => 36.5,
                'recomendaciones' => 'Mejoría progresiva con tratamiento combinado',
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000010')->first()->id,
                'medico_id' => $medicos->first()->id,
                'fecha_consulta' => Carbon::now()->subDays(18),
                'motivo_consulta' => 'Problemas de visión para lectura',
                'diagnostico' => 'Presbicia',
                'tratamiento' => 'Lentes correctivos para lectura',
                'observaciones' => 'Adaptación adecuada a los lentes prescritos',
                'peso' => 73.0,
                'altura' => 1.75,
                'presion_sistolica' => 125,
                'presion_diastolica' => 80,
                'frecuencia_cardiaca' => 70,
                'temperatura' => 36.4,
                'recomendaciones' => 'Corrección visual adecuada, seguimiento anual',
            ],
        ];

        foreach ($historiales as $historial) {
            HistorialMedico::create($historial);
        }
    }
}
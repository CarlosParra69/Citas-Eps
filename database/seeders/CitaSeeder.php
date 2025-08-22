<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cita;
use Carbon\Carbon;

class CitaSeeder extends Seeder
{
    public function run(): void
    {
        $citas = [
            [
                'paciente_id' => 1,
                'medico_id' => 1,
                'fecha_hora' => Carbon::now()->addDays(1)->setTime(9, 0),
                'estado' => 'programada',
                'motivo_consulta' => 'Control de presión arterial',
                'observaciones' => 'Paciente refiere dolor de cabeza ocasional',
                'diagnostico' => null,
                'tratamiento' => null,
                'costo' => 50000.00
            ],
            [
                'paciente_id' => 2,
                'medico_id' => 2,
                'fecha_hora' => Carbon::now()->addDays(2)->setTime(10, 30),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Dolor en el pecho',
                'observaciones' => 'Dolor intermitente desde hace una semana',
                'diagnostico' => null,
                'tratamiento' => null,
                'costo' => 80000.00
            ],
            [
                'paciente_id' => 3,
                'medico_id' => 3,
                'fecha_hora' => Carbon::now()->subDays(5)->setTime(14, 0),
                'estado' => 'completada',
                'motivo_consulta' => 'Revisión de lunares',
                'observaciones' => 'Paciente con antecedentes familiares de cáncer de piel',
                'diagnostico' => 'Nevus benigno',
                'tratamiento' => 'Control anual recomendado',
                'costo' => 60000.00
            ],
            [
                'paciente_id' => 4,
                'medico_id' => 4,
                'fecha_hora' => Carbon::now()->addDays(3)->setTime(16, 0),
                'estado' => 'programada',
                'motivo_consulta' => 'Control ginecológico anual',
                'observaciones' => 'Última citología hace 2 años',
                'diagnostico' => null,
                'tratamiento' => null,
                'costo' => 70000.00
            ],
            [
                'paciente_id' => 6,
                'medico_id' => 5,
                'fecha_hora' => Carbon::now()->addDays(1)->setTime(8, 0),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Control de crecimiento y desarrollo',
                'observaciones' => 'Niña de 13 años, desarrollo normal',
                'diagnostico' => null,
                'tratamiento' => null,
                'costo' => 45000.00
            ],
            [
                'paciente_id' => 1,
                'medico_id' => 2,
                'fecha_hora' => Carbon::now()->subDays(10)->setTime(11, 0),
                'estado' => 'completada',
                'motivo_consulta' => 'Dolor en el pecho',
                'observaciones' => 'Electrocardiograma normal',
                'diagnostico' => 'Dolor muscular intercostal',
                'tratamiento' => 'Relajantes musculares y fisioterapia',
                'costo' => 85000.00
            ],
            [
                'paciente_id' => 5,
                'medico_id' => 1,
                'fecha_hora' => Carbon::now()->subDays(3)->setTime(15, 30),
                'estado' => 'no_asistio',
                'motivo_consulta' => 'Dolor abdominal',
                'observaciones' => 'Paciente no se presentó a la cita',
                'diagnostico' => null,
                'tratamiento' => null,
                'costo' => null
            ],
            [
                'paciente_id' => 2,
                'medico_id' => 3,
                'fecha_hora' => Carbon::now()->addDays(7)->setTime(9, 30),
                'estado' => 'programada',
                'motivo_consulta' => 'Consulta por acné',
                'observaciones' => 'Acné moderado en rostro',
                'diagnostico' => null,
                'tratamiento' => null,
                'costo' => 55000.00
            ],
            // Citas adicionales para tener más datos de prueba
            [
                'paciente_id' => 1,
                'medico_id' => 3,
                'fecha_hora' => Carbon::now()->subDays(15)->setTime(14, 30),
                'estado' => 'completada',
                'motivo_consulta' => 'Revisión de piel',
                'observaciones' => 'Paciente con manchas en la piel',
                'diagnostico' => 'Melasma leve',
                'tratamiento' => 'Crema despigmentante',
                'costo' => 65000.00
            ],
            [
                'paciente_id' => 2,
                'medico_id' => 1,
                'fecha_hora' => Carbon::now()->subDays(20)->setTime(10, 0),
                'estado' => 'completada',
                'motivo_consulta' => 'Control médico general',
                'observaciones' => 'Paciente se siente bien',
                'diagnostico' => 'Estado de salud normal',
                'tratamiento' => 'Continuar con hábitos saludables',
                'costo' => 45000.00
            ],
            [
                'paciente_id' => 3,
                'medico_id' => 2,
                'fecha_hora' => Carbon::now()->subDays(8)->setTime(11, 30),
                'estado' => 'completada',
                'motivo_consulta' => 'Dolor en el pecho ocasional',
                'observaciones' => 'Electrocardiograma solicitado',
                'diagnostico' => 'Arritmia leve',
                'tratamiento' => 'Medicamento antiarrítmico',
                'costo' => 90000.00
            ],
            [
                'paciente_id' => 1,
                'medico_id' => 4,
                'fecha_hora' => Carbon::now()->addDays(5)->setTime(16, 30),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Consulta de seguimiento',
                'observaciones' => 'Control post-tratamiento',
                'diagnostico' => null,
                'tratamiento' => null,
                'costo' => 55000.00
            ],
            [
                'paciente_id' => 3,
                'medico_id' => 5,
                'fecha_hora' => Carbon::now()->subDays(25)->setTime(8, 30),
                'estado' => 'completada',
                'motivo_consulta' => 'Consulta familiar',
                'observaciones' => 'Consulta para hijo menor',
                'diagnostico' => 'Desarrollo normal',
                'tratamiento' => 'Vitaminas',
                'costo' => 40000.00
            ]
        ];

        foreach ($citas as $cita) {
            Cita::create($cita);
        }
    }
}
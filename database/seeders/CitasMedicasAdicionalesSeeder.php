<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Medico;
use Carbon\Carbon;

class CitasMedicasAdicionalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener pacientes y médicos para asignar a las citas
        $pacientes = Paciente::all();
        $medicos = Medico::all();

        $citasAdicionales = [
            // Citas de Cardiología
            [
                'paciente_id' => $pacientes->where('cedula', '10000001')->first()->id,
                'medico_id' => $medicos->where('email', 'carlos.rodriguez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(10)->setHour(8)->setMinute(30),
                'estado' => 'programada',
                'motivo_consulta' => 'Control de arritmias cardíacas',
                'observaciones' => 'Paciente con fibrilación auricular paroxística, requiere Holter',
                'costo' => 120000.00,
                'descuento' => 0.00,
                'total_pagar' => 120000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000002')->first()->id,
                'medico_id' => $medicos->where('email', 'carlos.rodriguez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(15)->setHour(10)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Ecocardiograma de control',
                'observaciones' => 'Control post tratamiento de valvulopatía mitral',
                'costo' => 150000.00,
                'descuento' => 15000.00,
                'total_pagar' => 135000.00,
            ],

            // Citas de Dermatología
            [
                'paciente_id' => $pacientes->where('cedula', '10000003')->first()->id,
                'medico_id' => $medicos->where('email', 'ana.martinez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(3)->setHour(11)->setMinute(0),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Crioterapia para verrugas',
                'observaciones' => 'Múltiples verrugas plantares, tratamiento con nitrógeno líquido',
                'costo' => 80000.00,
                'descuento' => 0.00,
                'total_pagar' => 80000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000004')->first()->id,
                'medico_id' => $medicos->where('email', 'ana.martinez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(8)->setHour(14)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Control de psoriasis',
                'observaciones' => 'Psoriasis en placas estable, requiere fototerapia',
                'costo' => 95000.00,
                'descuento' => 5000.00,
                'total_pagar' => 90000.00,
            ],

            // Citas de Oftalmología
            [
                'paciente_id' => $pacientes->where('cedula', '10000005')->first()->id,
                'medico_id' => $medicos->where('email', 'diego.garcia@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(6)->setHour(9)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Cirugía de cataratas',
                'observaciones' => 'Catarata nuclear en ojo izquierdo, requiere facoemulsificación',
                'costo' => 850000.00,
                'descuento' => 50000.00,
                'total_pagar' => 800000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000006')->first()->id,
                'medico_id' => $medicos->where('email', 'diego.garcia@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(12)->setHour(15)->setMinute(30),
                'estado' => 'programada',
                'motivo_consulta' => 'Control de glaucoma',
                'observaciones' => 'Glaucoma de ángulo abierto, presión intraocular controlada',
                'costo' => 75000.00,
                'descuento' => 0.00,
                'total_pagar' => 75000.00,
            ],

            // Citas de Neurología
            [
                'paciente_id' => $pacientes->where('cedula', '10000007')->first()->id,
                'medico_id' => $medicos->where('email', 'roberto.perez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(4)->setHour(10)->setMinute(0),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Migraña con aura',
                'observaciones' => 'Cefalea migrañosa con aura visual, requiere profilaxis',
                'costo' => 110000.00,
                'descuento' => 0.00,
                'total_pagar' => 110000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000008')->first()->id,
                'medico_id' => $medicos->where('email', 'roberto.perez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(9)->setHour(16)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Control de epilepsia',
                'observaciones' => 'Epilepsia focal bien controlada con levetiracetam',
                'costo' => 95000.00,
                'descuento' => 5000.00,
                'total_pagar' => 90000.00,
            ],

            // Citas de Ginecología
            [
                'paciente_id' => $pacientes->where('cedula', '10000009')->first()->id,
                'medico_id' => $medicos->where('email', 'luis.hernandez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(7)->setHour(9)->setMinute(30),
                'estado' => 'programada',
                'motivo_consulta' => 'Control prenatal mes 8',
                'observaciones' => 'Embarazo de 32 semanas, evolución normal, requiere ecografía',
                'costo' => 85000.00,
                'descuento' => 10000.00,
                'total_pagar' => 75000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000010')->first()->id,
                'medico_id' => $medicos->where('email', 'luis.hernandez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(14)->setHour(11)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Colocación de DIU',
                'observaciones' => 'Paciente solicita método anticonceptivo de larga duración',
                'costo' => 180000.00,
                'descuento' => 0.00,
                'total_pagar' => 180000.00,
            ],

            // Citas de Traumatología
            [
                'paciente_id' => $pacientes->where('cedula', '10000001')->first()->id,
                'medico_id' => $medicos->where('email', 'patricia.sanchez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(5)->setHour(14)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Artrosis de rodilla',
                'observaciones' => 'Gonartrosis bilateral grado II, requiere infiltración',
                'costo' => 200000.00,
                'descuento' => 20000.00,
                'total_pagar' => 180000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000002')->first()->id,
                'medico_id' => $medicos->where('email', 'patricia.sanchez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(11)->setHour(10)->setMinute(30),
                'estado' => 'programada',
                'motivo_consulta' => 'Fractura de muñeca',
                'observaciones' => 'Fractura de Colles consolidada, control radiológico',
                'costo' => 120000.00,
                'descuento' => 0.00,
                'total_pagar' => 120000.00,
            ],

            // Citas de Endocrinología
            [
                'paciente_id' => $pacientes->where('cedula', '10000003')->first()->id,
                'medico_id' => $medicos->where('email', 'fernando.morales@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(2)->setHour(15)->setMinute(0),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Diabetes gestacional',
                'observaciones' => 'Diabetes gestacional controlada con dieta, requiere curva glucémica',
                'costo' => 90000.00,
                'descuento' => 0.00,
                'total_pagar' => 90000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000004')->first()->id,
                'medico_id' => $medicos->where('email', 'fernando.morales@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(13)->setHour(8)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Hipotiroidismo',
                'observaciones' => 'Hipotiroidismo subclínico, seguimiento de TSH y T4 libre',
                'costo' => 80000.00,
                'descuento' => 5000.00,
                'total_pagar' => 75000.00,
            ],

            // Citas de Psiquiatría
            [
                'paciente_id' => $pacientes->where('cedula', '10000005')->first()->id,
                'medico_id' => $medicos->where('email', 'carmen.ruiz@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(1)->setHour(17)->setMinute(0),
                'estado' => 'confirmada',
                'motivo_consulta' => 'Depresión mayor',
                'observaciones' => 'Episodio depresivo mayor moderado, respuesta parcial a ISRS',
                'costo' => 120000.00,
                'descuento' => 0.00,
                'total_pagar' => 120000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000006')->first()->id,
                'medico_id' => $medicos->where('email', 'carmen.ruiz@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(16)->setHour(14)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Trastorno bipolar',
                'observaciones' => 'Trastorno bipolar tipo II, eutímico con tratamiento actual',
                'costo' => 130000.00,
                'descuento' => 10000.00,
                'total_pagar' => 120000.00,
            ],

            // Citas de Pediatría
            [
                'paciente_id' => $pacientes->where('cedula', '10000007')->first()->id,
                'medico_id' => $medicos->where('email', 'sofia.lopez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(3)->setHour(16)->setMinute(30),
                'estado' => 'programada',
                'motivo_consulta' => 'Control de crecimiento',
                'observaciones' => 'Niño con talla baja constitucional, seguimiento de percentilos',
                'costo' => 70000.00,
                'descuento' => 0.00,
                'total_pagar' => 70000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000008')->first()->id,
                'medico_id' => $medicos->where('email', 'sofia.lopez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(9)->setHour(9)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Bronquiolitis',
                'observaciones' => 'Lactante con bronquiolitis leve, tratamiento sintomático',
                'costo' => 65000.00,
                'descuento' => 5000.00,
                'total_pagar' => 60000.00,
            ],

            // Citas de Medicina General
            [
                'paciente_id' => $pacientes->where('cedula', '10000009')->first()->id,
                'medico_id' => $medicos->where('email', 'maria.gonzalez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(6)->setHour(11)->setMinute(30),
                'estado' => 'programada',
                'motivo_consulta' => 'Síndrome gripal',
                'observaciones' => 'Cuadro gripal con fiebre y tos, requiere tratamiento sintomático',
                'costo' => 50000.00,
                'descuento' => 0.00,
                'total_pagar' => 50000.00,
            ],
            [
                'paciente_id' => $pacientes->where('cedula', '10000010')->first()->id,
                'medico_id' => $medicos->where('email', 'maria.gonzalez@clinica.com')->first()->id,
                'fecha_hora' => Carbon::now()->addDays(18)->setHour(8)->setMinute(0),
                'estado' => 'programada',
                'motivo_consulta' => 'Chequeo general',
                'observaciones' => 'Chequeo médico preventivo anual, incluye análisis de laboratorio',
                'costo' => 120000.00,
                'descuento' => 10000.00,
                'total_pagar' => 110000.00,
            ],
        ];

        foreach ($citasAdicionales as $cita) {
            Cita::create($cita);
        }

        $this->command->info('Citas médicas adicionales creadas exitosamente con datos variados por especialidad.');
    }
}
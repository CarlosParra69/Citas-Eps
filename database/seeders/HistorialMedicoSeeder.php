<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\HistorialMedico;
use Carbon\Carbon;

class HistorialMedicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear pacientes de prueba si no existen
        $pacientes = [
            [
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'cedula' => '1234567890',
                'fecha_nacimiento' => '1985-05-15',
                'genero' => 'M',
                'telefono' => '3001234567',
                'email' => 'juan.perez@example.com',
                'direccion' => 'Calle 123 #45-67',
                'eps' => 'Sura',
                'alergias' => 'Ninguna',
                'medicamentos_actuales' => 'Ninguno',
                'antecedentes_medicos' => 'Hipertensión',
                'contacto_emergencia' => 'María Pérez',
                'telefono_emergencia' => '3009876543',
                'activo' => true,
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'García',
                'cedula' => '0987654321',
                'fecha_nacimiento' => '1990-08-20',
                'genero' => 'F',
                'telefono' => '3012345678',
                'email' => 'ana.garcia@example.com',
                'direccion' => 'Carrera 89 #12-34',
                'eps' => 'Sanitas',
                'alergias' => 'Penicilina',
                'medicamentos_actuales' => 'Anticonceptivos',
                'antecedentes_medicos' => 'Asma',
                'contacto_emergencia' => 'Carlos García',
                'telefono_emergencia' => '3018765432',
                'activo' => true,
            ],
        ];

        foreach ($pacientes as $pacienteData) {
            Paciente::firstOrCreate(
                ['cedula' => $pacienteData['cedula']],
                $pacienteData
            );
        }

        // Crear medicos de prueba si no existen
        $medicos = [
            [
                'nombre' => 'Carlos',
                'apellido' => 'Rodríguez',
                'cedula' => '1122334455',
                'registro_medico' => 'RM001',
                'telefono' => '3023456789',
                'email' => 'carlos.rodriguez@clinica.com',
                'especialidad_id' => 1, // Medicina General
                'horarios_atencion' => json_encode([
                    'lunes' => ['08:00', '16:00'],
                    'martes' => ['08:00', '16:00'],
                    'miercoles' => ['08:00', '16:00'],
                    'jueves' => ['08:00', '16:00'],
                    'viernes' => ['08:00', '16:00'],
                ]),
                'tarifa_consulta' => 50000.00,
                'biografia' => 'Médico general con 10 años de experiencia.',
                'activo' => true,
                'disponibilidad' => 'disponible',
            ],
            [
                'nombre' => 'Laura',
                'apellido' => 'Martínez',
                'cedula' => '2233445566',
                'registro_medico' => 'RM002',
                'telefono' => '3034567890',
                'email' => 'laura.martinez@clinica.com',
                'especialidad_id' => 2, // Cardiología
                'horarios_atencion' => json_encode([
                    'lunes' => ['09:00', '17:00'],
                    'martes' => ['09:00', '17:00'],
                    'miercoles' => ['09:00', '17:00'],
                    'jueves' => ['09:00', '17:00'],
                    'viernes' => ['09:00', '17:00'],
                ]),
                'tarifa_consulta' => 80000.00,
                'biografia' => 'Cardióloga especializada en enfermedades del corazón.',
                'activo' => true,
                'disponibilidad' => 'disponible',
            ],
        ];

        foreach ($medicos as $medicoData) {
            Medico::firstOrCreate(
                ['cedula' => $medicoData['cedula']],
                $medicoData
            );
        }

        // Obtener pacientes y medicos creados
        $paciente1 = Paciente::where('cedula', '1234567890')->first();
        $paciente2 = Paciente::where('cedula', '0987654321')->first();
        $medico1 = Medico::where('cedula', '1122334455')->first();
        $medico2 = Medico::where('cedula', '2233445566')->first();

        // Crear historiales médicos de prueba
        $historiales = [
            [
                'paciente_id' => $paciente1->id,
                'medico_id' => $medico1->id,
                'fecha_consulta' => Carbon::now()->subDays(30),
                'motivo_consulta' => 'Dolor de cabeza recurrente',
                'sintomas' => 'Dolor de cabeza, náuseas',
                'diagnostico' => 'Migraña',
                'tratamiento' => 'Analgésicos y reposo',
                'receta_medica' => 'Ibuprofeno 400mg cada 8 horas',
                'observaciones' => 'Paciente con historial de migrañas',
                'recomendaciones' => 'Evitar estrés y mantener horarios regulares',
                'peso' => 70.5,
                'altura' => 1.75,
                'presion_sistolica' => 120,
                'presion_diastolica' => 80,
                'temperatura' => 36.5,
                'frecuencia_cardiaca' => 72,
                'examenes_solicitados' => 'Ninguno',
                'resultados_examenes' => null,
                'proxima_cita' => Carbon::now()->addDays(15),
            ],
            [
                'paciente_id' => $paciente1->id,
                'medico_id' => $medico2->id,
                'fecha_consulta' => Carbon::now()->subDays(15),
                'motivo_consulta' => 'Control de hipertensión',
                'sintomas' => 'Ninguno',
                'diagnostico' => 'Hipertensión controlada',
                'tratamiento' => 'Continuar con medicamento actual',
                'receta_medica' => 'Losartán 50mg diario',
                'observaciones' => 'Presión arterial estable',
                'recomendaciones' => 'Dieta baja en sal y ejercicio regular',
                'peso' => 71.0,
                'altura' => 1.75,
                'presion_sistolica' => 118,
                'presion_diastolica' => 78,
                'temperatura' => 36.6,
                'frecuencia_cardiaca' => 70,
                'examenes_solicitados' => 'Análisis de sangre',
                'resultados_examenes' => 'Resultados normales',
                'proxima_cita' => Carbon::now()->addDays(30),
            ],
            [
                'paciente_id' => $paciente2->id,
                'medico_id' => $medico1->id,
                'fecha_consulta' => Carbon::now()->subDays(20),
                'motivo_consulta' => 'Consulta general',
                'sintomas' => 'Fatiga y tos',
                'diagnostico' => 'Resfriado común',
                'tratamiento' => 'Reposo y medicamentos sintomáticos',
                'receta_medica' => 'Paracetamol y jarabe para la tos',
                'observaciones' => 'Paciente con asma, monitorear síntomas',
                'recomendaciones' => 'Evitar cambios de temperatura',
                'peso' => 65.0,
                'altura' => 1.65,
                'presion_sistolica' => 115,
                'presion_diastolica' => 75,
                'temperatura' => 37.2,
                'frecuencia_cardiaca' => 75,
                'examenes_solicitados' => 'Ninguno',
                'resultados_examenes' => null,
                'proxima_cita' => null,
            ],
        ];

        foreach ($historiales as $historialData) {
            HistorialMedico::create($historialData);
        }
    }
}
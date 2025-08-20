<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medico;

class MedicoSeeder extends Seeder
{
    public function run(): void
    {
        $medicos = [
            [
                'nombre' => 'Carlos',
                'apellido' => 'Rodríguez',
                'cedula' => '12345678',
                'registro_medico' => 'RM001',
                'telefono' => '3001234567',
                'email' => 'carlos.rodriguez@hospital.com',
                'especialidad_id' => 1, // Medicina General
                'horarios_atencion' => [
                    'lunes' => ['08:00', '12:00', '14:00', '18:00'],
                    'martes' => ['08:00', '12:00', '14:00', '18:00'],
                    'miercoles' => ['08:00', '12:00'],
                    'jueves' => ['08:00', '12:00', '14:00', '18:00'],
                    'viernes' => ['08:00', '12:00', '14:00', '18:00']
                ],
                'activo' => true
            ],
            [
                'nombre' => 'María',
                'apellido' => 'González',
                'cedula' => '87654321',
                'registro_medico' => 'RM002',
                'telefono' => '3007654321',
                'email' => 'maria.gonzalez@hospital.com',
                'especialidad_id' => 2, // Cardiología
                'horarios_atencion' => [
                    'lunes' => ['09:00', '13:00', '15:00', '19:00'],
                    'martes' => ['09:00', '13:00', '15:00', '19:00'],
                    'miercoles' => ['09:00', '13:00', '15:00', '19:00'],
                    'jueves' => ['09:00', '13:00'],
                    'viernes' => ['09:00', '13:00', '15:00', '19:00']
                ],
                'activo' => true
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'cedula' => '11223344',
                'registro_medico' => 'RM003',
                'telefono' => '3001122334',
                'email' => 'ana.martinez@hospital.com',
                'especialidad_id' => 3, // Dermatología
                'horarios_atencion' => [
                    'lunes' => ['08:30', '12:30', '14:30', '17:30'],
                    'martes' => ['08:30', '12:30', '14:30', '17:30'],
                    'miercoles' => ['08:30', '12:30', '14:30', '17:30'],
                    'jueves' => ['08:30', '12:30', '14:30', '17:30'],
                    'viernes' => ['08:30', '12:30']
                ],
                'activo' => true
            ],
            [
                'nombre' => 'Luis',
                'apellido' => 'Hernández',
                'cedula' => '55667788',
                'registro_medico' => 'RM004',
                'telefono' => '3005566778',
                'email' => 'luis.hernandez@hospital.com',
                'especialidad_id' => 4, // Ginecología
                'horarios_atencion' => [
                    'lunes' => ['10:00', '14:00', '16:00', '20:00'],
                    'martes' => ['10:00', '14:00', '16:00', '20:00'],
                    'miercoles' => ['10:00', '14:00'],
                    'jueves' => ['10:00', '14:00', '16:00', '20:00'],
                    'viernes' => ['10:00', '14:00', '16:00', '20:00']
                ],
                'activo' => true
            ],
            [
                'nombre' => 'Patricia',
                'apellido' => 'López',
                'cedula' => '99887766',
                'registro_medico' => 'RM005',
                'telefono' => '3009988776',
                'email' => 'patricia.lopez@hospital.com',
                'especialidad_id' => 5, // Pediatría
                'horarios_atencion' => [
                    'lunes' => ['07:00', '11:00', '13:00', '17:00'],
                    'martes' => ['07:00', '11:00', '13:00', '17:00'],
                    'miercoles' => ['07:00', '11:00', '13:00', '17:00'],
                    'jueves' => ['07:00', '11:00', '13:00', '17:00'],
                    'viernes' => ['07:00', '11:00']
                ],
                'activo' => true
            ]
        ];

        foreach ($medicos as $medico) {
            Medico::create($medico);
        }
    }
}
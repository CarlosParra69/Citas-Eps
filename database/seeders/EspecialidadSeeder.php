<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidad;

class EspecialidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $especialidades = [
            [
                'nombre' => 'Medicina General',
                'descripcion' => 'Atención primaria de salud y medicina preventiva',
                'codigo' => 'MED001',
                'tarifa_base' => 50000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Cardiología',
                'descripcion' => 'Especialidad médica que se ocupa del corazón y sus enfermedades',
                'codigo' => 'CAR001',
                'tarifa_base' => 80000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Dermatología',
                'descripcion' => 'Especialidad médica que estudia la piel y sus enfermedades',
                'codigo' => 'DER001',
                'tarifa_base' => 60000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Ginecología',
                'descripcion' => 'Especialidad médica que trata el sistema reproductor femenino',
                'codigo' => 'GIN001',
                'tarifa_base' => 70000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Pediatría',
                'descripcion' => 'Especialidad médica dedicada a la salud de los niños',
                'codigo' => 'PED001',
                'tarifa_base' => 55000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Oftalmología',
                'descripcion' => 'Especialidad médica que estudia los ojos y sus enfermedades',
                'codigo' => 'OFT001',
                'tarifa_base' => 65000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Traumatología',
                'descripcion' => 'Especialidad médica que trata lesiones del aparato locomotor',
                'codigo' => 'TRA001',
                'tarifa_base' => 75000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Neurología',
                'descripcion' => 'Especialidad médica que trata el sistema nervioso',
                'codigo' => 'NEU001',
                'tarifa_base' => 90000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Psiquiatría',
                'descripcion' => 'Especialidad médica que trata los trastornos mentales',
                'codigo' => 'PSI001',
                'tarifa_base' => 85000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Endocrinología',
                'descripcion' => 'Especialidad médica que trata las glándulas endocrinas',
                'codigo' => 'END001',
                'tarifa_base' => 70000.00,
                'activo' => true,
            ],
        ];

        foreach ($especialidades as $especialidad) {
            Especialidad::firstOrCreate(
                ['nombre' => $especialidad['nombre']],
                $especialidad
            );
        }
    }
}
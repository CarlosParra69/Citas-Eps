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
            [
                'nombre' => 'Gastroenterología',
                'descripcion' => 'Especialidad médica que trata el aparato digestivo',
                'codigo' => 'GAS001',
                'tarifa_base' => 72000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Nefrología',
                'descripcion' => 'Especialidad médica que trata los riñones y vías urinarias',
                'codigo' => 'NEF001',
                'tarifa_base' => 78000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Neumología',
                'descripcion' => 'Especialidad médica que trata el aparato respiratorio',
                'codigo' => 'NEU002',
                'tarifa_base' => 76000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Reumatología',
                'descripcion' => 'Especialidad médica que trata enfermedades de huesos y articulaciones',
                'codigo' => 'REU001',
                'tarifa_base' => 73000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Otorrinolaringología',
                'descripcion' => 'Especialidad médica que trata oído, nariz y garganta',
                'codigo' => 'ORL001',
                'tarifa_base' => 68000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Urología',
                'descripcion' => 'Especialidad médica que trata el aparato urinario masculino',
                'codigo' => 'URO001',
                'tarifa_base' => 71000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Oncología',
                'descripcion' => 'Especialidad médica que trata el cáncer',
                'codigo' => 'ONC001',
                'tarifa_base' => 95000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Hematología',
                'descripcion' => 'Especialidad médica que trata enfermedades de la sangre',
                'codigo' => 'HEM001',
                'tarifa_base' => 82000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Infectología',
                'descripcion' => 'Especialidad médica que trata enfermedades infecciosas',
                'codigo' => 'INF001',
                'tarifa_base' => 74000.00,
                'activo' => true,
            ],
            [
                'nombre' => 'Medicina Interna',
                'descripcion' => 'Especialidad médica que trata enfermedades de adultos',
                'codigo' => 'MIN001',
                'tarifa_base' => 69000.00,
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
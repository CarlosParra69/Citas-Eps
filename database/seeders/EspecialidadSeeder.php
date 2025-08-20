<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidad;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            [
                'nombre' => 'Medicina General',
                'descripcion' => 'Atención médica integral y preventiva para toda la familia',
                'activo' => true
            ],
            [
                'nombre' => 'Cardiología',
                'descripcion' => 'Especialidad médica que se encarga del estudio, diagnóstico y tratamiento de las enfermedades del corazón',
                'activo' => true
            ],
            [
                'nombre' => 'Dermatología',
                'descripcion' => 'Especialidad médica que se encarga del estudio de la estructura y función de la piel',
                'activo' => true
            ],
            [
                'nombre' => 'Ginecología',
                'descripcion' => 'Especialidad médica que se encarga de la salud del sistema reproductor femenino',
                'activo' => true
            ],
            [
                'nombre' => 'Pediatría',
                'descripcion' => 'Especialidad médica que se encarga de la salud de bebés, niños y adolescentes',
                'activo' => true
            ],
            [
                'nombre' => 'Neurología',
                'descripcion' => 'Especialidad médica que se encarga del estudio del sistema nervioso',
                'activo' => true
            ],
            [
                'nombre' => 'Ortopedia',
                'descripcion' => 'Especialidad médica que se encarga del sistema musculoesquelético',
                'activo' => true
            ],
            [
                'nombre' => 'Psiquiatría',
                'descripcion' => 'Especialidad médica que se encarga de la salud mental',
                'activo' => true
            ]
        ];

        foreach ($especialidades as $especialidad) {
            Especialidad::create($especialidad);
        }
    }
}
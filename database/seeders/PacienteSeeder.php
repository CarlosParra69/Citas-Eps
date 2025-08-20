<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paciente;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = [
            [
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'cedula' => '1010101010',
                'fecha_nacimiento' => '1985-03-15',
                'genero' => 'M',
                'telefono' => '3101010101',
                'email' => 'juan.perez@email.com',
                'direccion' => 'Calle 123 #45-67, Bogotá',
                'eps' => 'Sura EPS',
                'alergias' => 'Penicilina',
                'medicamentos_actuales' => 'Losartán 50mg',
                'activo' => true
            ],
            [
                'nombre' => 'Laura',
                'apellido' => 'García',
                'cedula' => '2020202020',
                'fecha_nacimiento' => '1990-07-22',
                'genero' => 'F',
                'telefono' => '3202020202',
                'email' => 'laura.garcia@email.com',
                'direccion' => 'Carrera 45 #12-34, Medellín',
                'eps' => 'Compensar EPS',
                'alergias' => null,
                'medicamentos_actuales' => null,
                'activo' => true
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Ramírez',
                'cedula' => '3030303030',
                'fecha_nacimiento' => '1978-11-08',
                'genero' => 'M',
                'telefono' => '3303030303',
                'email' => 'pedro.ramirez@email.com',
                'direccion' => 'Avenida 68 #23-45, Cali',
                'eps' => 'Nueva EPS',
                'alergias' => 'Aspirina, Mariscos',
                'medicamentos_actuales' => 'Metformina 850mg, Atorvastatina 20mg',
                'activo' => true
            ],
            [
                'nombre' => 'Carmen',
                'apellido' => 'Torres',
                'cedula' => '4040404040',
                'fecha_nacimiento' => '1995-01-30',
                'genero' => 'F',
                'telefono' => '3404040404',
                'email' => 'carmen.torres@email.com',
                'direccion' => 'Calle 50 #78-90, Barranquilla',
                'eps' => 'Sanitas EPS',
                'alergias' => null,
                'medicamentos_actuales' => 'Anticonceptivos orales',
                'activo' => true
            ],
            [
                'nombre' => 'Roberto',
                'apellido' => 'Silva',
                'cedula' => '5050505050',
                'fecha_nacimiento' => '1982-09-12',
                'genero' => 'M',
                'telefono' => '3505050505',
                'email' => 'roberto.silva@email.com',
                'direccion' => 'Transversal 15 #34-56, Bucaramanga',
                'eps' => 'Famisanar EPS',
                'alergias' => 'Polen',
                'medicamentos_actuales' => 'Omeprazol 20mg',
                'activo' => true
            ],
            [
                'nombre' => 'Isabella',
                'apellido' => 'Moreno',
                'cedula' => '6060606060',
                'fecha_nacimiento' => '2010-05-18',
                'genero' => 'F',
                'telefono' => '3606060606',
                'email' => 'isabella.moreno@email.com',
                'direccion' => 'Calle 80 #12-34, Pereira',
                'eps' => 'Coomeva EPS',
                'alergias' => null,
                'medicamentos_actuales' => null,
                'activo' => true
            ]
        ];

        foreach ($pacientes as $paciente) {
            Paciente::create($paciente);
        }
    }
}
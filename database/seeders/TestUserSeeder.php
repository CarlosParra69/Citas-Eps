<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Paciente;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear paciente de prueba
        $paciente = Paciente::create([
            'nombre' => 'Usuario',
            'apellido' => 'Prueba',
            'cedula' => '9999999999',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'telefono' => '3001234567',
            'email' => 'usuario.prueba.test@example.com',
            'direccion' => 'Calle de prueba 123',
            'eps' => 'EPS Prueba',
            'activo' => true,
        ]);

        // Crear usuario de prueba
        User::create([
            'name' => 'Usuario Prueba',
            'nombre' => 'Usuario',
            'apellido' => 'Prueba',
            'cedula' => '9999999999',
            'email' => 'usuario.prueba.test@example.com',
            'password' => Hash::make('password123'),
            'tipo' => 'paciente',
            'rol' => 'paciente',
            'activo' => true,
            'paciente_id' => $paciente->id,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Superadmin',
                'slug' => 'superadmin',
                'description' => 'Administrador del sistema con acceso completo a todas las funcionalidades',
                'is_active' => true,
            ],
            [
                'name' => 'Médico',
                'slug' => 'medico',
                'description' => 'Profesional médico con acceso a funcionalidades de atención y gestión de pacientes',
                'is_active' => true,
            ],
            [
                'name' => 'Usuario',
                'slug' => 'paciente',
                'description' => 'Usuario paciente con acceso a agendamiento de citas y gestión de su información médica',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }
    }
}

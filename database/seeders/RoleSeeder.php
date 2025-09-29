<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Administrador',
                'slug' => 'superadmin',
                'description' => 'Tiene acceso completo a todo el sistema',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Médico',
                'slug' => 'medico',
                'description' => 'Puede gestionar citas, pacientes y historiales médicos',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paciente',
                'slug' => 'paciente',
                'description' => 'Puede agendar citas y ver su historial médico',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}
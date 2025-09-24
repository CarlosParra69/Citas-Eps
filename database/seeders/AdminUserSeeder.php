<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario admin si no existe
        if (!User::where('email', 'admin@citasmedicas.com')->exists()) {
            $superadminRole = \App\Models\Role::where('slug', 'superadmin')->first();

            User::create([
                'name' => 'Administrador Sistema',
                'nombre' => 'Administrador',
                'apellido' => 'Sistema',
                'cedula' => '123456789',
                'email' => 'admin@citasmedicas.com',
                'password' => Hash::make('admin123'),
                'rol' => 'superadmin',
                'activo' => true,
                'role_id' => $superadminRole ? $superadminRole->id : null,
            ]);
        }
    }
}
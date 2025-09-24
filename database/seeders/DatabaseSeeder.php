<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            EspecialidadSeeder::class,
            MedicoSeeder::class,
            PacienteSeeder::class,
            CitaSeeder::class,
            CitasMedicasAdicionalesSeeder::class,
            CitasPruebasFrontendSeeder::class,
            CitasMedicasSeeder::class,
            CitasMedicasEstadosSeeder::class,
            HistorialMedicoSeeder::class,
        ]);
    }
}
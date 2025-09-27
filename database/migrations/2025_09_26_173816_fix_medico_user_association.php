<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Medico;
use App\Models\User;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Buscar médicos sin usuario asociado usando consulta directa
        $medicosSinUsuario = \DB::table('medicos')
            ->leftJoin('users', 'medicos.id', '=', 'users.medico_id')
            ->whereNull('users.medico_id')
            ->select('medicos.*')
            ->get();

        echo "Médicos sin usuario asociado encontrados: " . count($medicosSinUsuario) . "\n";

        foreach ($medicosSinUsuario as $medico) {
            // Verificar si ya existe un usuario con el mismo email
            $existingUser = User::where('email', $medico->email)->first();

            if (!$existingUser) {
                // Crear usuario para el médico
                $user = User::create([
                    'name' => $medico->nombre . ' ' . $medico->apellido,
                    'nombre' => $medico->nombre,
                    'apellido' => $medico->apellido,
                    'cedula' => $medico->cedula,
                    'email' => $medico->email,
                    'password' => bcrypt('password123'), // Contraseña temporal
                    'rol' => 'medico',
                    'activo' => true,
                    'medico_id' => $medico->id,
                    'role_id' => Role::where('slug', 'medico')->first()->id ?? null
                ]);

                echo "Usuario creado para médico {$medico->nombre} {$medico->apellido} (ID: {$medico->id})\n";
            } else {
                // Si ya existe el usuario, solo asociarlo
                $existingUser->medico_id = $medico->id;
                $existingUser->save();

                echo "Usuario existente asociado al médico {$medico->nombre} {$medico->apellido} (ID: {$medico->id})\n";
            }
        }

        // Verificar específicamente el médico con ID 11
        $medico11 = Medico::find(11);
        if ($medico11) {
            $user11 = User::where('email', $medico11->email)->first();
            if (!$user11) {
                $user11 = User::create([
                    'name' => $medico11->nombre . ' ' . $medico11->apellido,
                    'nombre' => $medico11->nombre,
                    'apellido' => $medico11->apellido,
                    'cedula' => $medico11->cedula,
                    'email' => $medico11->email,
                    'password' => bcrypt('password123'),
                    'rol' => 'medico',
                    'activo' => true,
                    'medico_id' => $medico11->id,
                    'role_id' => Role::where('slug', 'medico')->first()->id ?? null
                ]);
                echo "Usuario creado específicamente para médico ID 11\n";
            } else {
                $user11->medico_id = $medico11->id;
                $user11->save();
                echo "Usuario existente asociado específicamente al médico ID 11\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No implementamos rollback para evitar eliminar usuarios accidentalmente
        echo "No se puede hacer rollback de esta migración para evitar pérdida de datos\n";
    }
};

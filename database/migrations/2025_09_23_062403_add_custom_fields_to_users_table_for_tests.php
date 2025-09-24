<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Solo agregar si no existen las columnas
            if (!Schema::hasColumn('users', 'nombre')) {
                $table->string('nombre')->after('name');
            }
            if (!Schema::hasColumn('users', 'apellido')) {
                $table->string('apellido')->after('nombre');
            }
            if (!Schema::hasColumn('users', 'cedula')) {
                $table->string('cedula')->unique()->after('apellido');
            }
            if (!Schema::hasColumn('users', 'rol')) {
                $table->enum('rol', ['superadmin', 'medico', 'paciente'])->default('paciente')->after('cedula');
            }
            if (!Schema::hasColumn('users', 'activo')) {
                $table->boolean('activo')->default(true)->after('rol');
            }
            if (!Schema::hasColumn('users', 'medico_id')) {
                $table->unsignedBigInteger('medico_id')->nullable()->after('activo');
            }
            if (!Schema::hasColumn('users', 'paciente_id')) {
                $table->unsignedBigInteger('paciente_id')->nullable()->after('medico_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'apellido', 'cedula', 'rol', 'activo', 'medico_id', 'paciente_id']);
        });
    }
};

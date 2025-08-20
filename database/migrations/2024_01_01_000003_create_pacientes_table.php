<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('cedula')->unique();
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['M', 'F', 'Otro']);
            $table->string('telefono');
            $table->string('email')->unique();
            $table->text('direccion')->nullable();
            $table->string('eps')->nullable();
            $table->text('alergias')->nullable();
            $table->text('medicamentos_actuales')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
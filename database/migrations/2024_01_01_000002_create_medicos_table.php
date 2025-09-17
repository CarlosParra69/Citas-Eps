<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('cedula')->unique();
            $table->string('registro_medico')->unique();
            $table->string('telefono')->nullable();
            $table->string('email')->unique();
            $table->foreignId('especialidad_id')->constrained('especialidades')->onDelete('restrict');
            $table->json('horarios_atencion'); // JSON con días y horas
            $table->decimal('tarifa_consulta', 10, 2)->nullable();
            $table->text('biografia')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índices para optimización
            $table->index(['especialidad_id', 'activo']);
            $table->index(['cedula', 'activo']);
            $table->index(['email', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};
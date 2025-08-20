<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes');
            $table->foreignId('medico_id')->constrained('medicos');
            $table->dateTime('fecha_hora');
            $table->enum('estado', ['programada', 'confirmada', 'en_curso', 'completada', 'cancelada', 'no_asistio'])->default('programada');
            $table->text('motivo_consulta');
            $table->text('observaciones')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('tratamiento')->nullable();
            $table->decimal('costo', 10, 2)->nullable();
            $table->timestamps();
            
            // Índices para optimización
            $table->index(['fecha_hora', 'medico_id']);
            $table->index(['paciente_id', 'fecha_hora']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
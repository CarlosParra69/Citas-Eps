<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historiales_medicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('medico_id')->constrained('medicos')->onDelete('restrict');
            $table->foreignId('cita_id')->nullable()->constrained('citas')->onDelete('set null');
            $table->date('fecha_consulta');
            $table->text('motivo_consulta');
            $table->text('sintomas')->nullable();
            $table->text('diagnostico');
            $table->text('tratamiento')->nullable();
            $table->text('receta_medica')->nullable();
            $table->text('observaciones')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('altura', 5, 2)->nullable();
            $table->integer('presion_sistolica')->nullable();
            $table->integer('presion_diastolica')->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->text('examenes_solicitados')->nullable();
            $table->text('resultados_examenes')->nullable();
            $table->date('proxima_cita')->nullable();
            $table->timestamps();
            
            // Índices para optimización
            $table->index(['paciente_id', 'fecha_consulta']);
            $table->index(['medico_id', 'fecha_consulta']);
            $table->index(['cita_id']);
            $table->index(['fecha_consulta']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historiales_medicos');
    }
};
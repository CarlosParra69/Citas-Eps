<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('especialidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->string('codigo')->unique()->nullable(); // Código de la especialidad
            $table->decimal('tarifa_base', 10, 2)->nullable(); // Tarifa base de la especialidad
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índices para optimización
            $table->index(['nombre', 'activo']);
            $table->index('codigo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidades');
    }
};
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
        // Crear tabla roles primero
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Índices para optimización
            $table->index(['slug']);
            $table->index(['is_active']);
        });

        // Crear tabla users con todos los campos necesarios
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('tipo', ['admin', 'medico', 'paciente'])->default('admin');
            $table->boolean('activo')->default(true);
            $table->rememberToken();
            $table->timestamps();

            // Campos adicionales agregados posteriormente
            $table->string('nombre')->after('name');
            $table->string('apellido')->after('nombre');
            $table->string('cedula')->unique()->after('apellido');
            $table->enum('rol', ['superadmin', 'medico', 'paciente'])->default('paciente')->after('cedula');
            $table->unsignedBigInteger('medico_id')->nullable()->after('activo');
            $table->unsignedBigInteger('paciente_id')->nullable()->after('medico_id');
            $table->foreignId('role_id')->nullable()->after('paciente_id')->constrained('roles')->onDelete('set null');
            $table->string('foto')->nullable()->after('role_id');

            // Índices para optimización
            $table->index(['email', 'activo']);
            $table->index('tipo');
            $table->index(['role_id']);
        });

        // Crear tabla especialidades
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

        // Crear tabla medicos
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
            $table->boolean('activo')->default(true);
            $table->enum('disponibilidad', ['disponible', 'cita_en_curso', 'no_disponible'])
                  ->default('disponible')
                  ->after('activo');
            $table->timestamps();

            // Índices para optimización
            $table->index(['especialidad_id', 'activo']);
            $table->index(['cedula', 'activo']);
            $table->index(['email', 'activo']);
        });

        // Crear tabla pacientes
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
            $table->text('antecedentes_medicos')->nullable();
            $table->string('contacto_emergencia')->nullable();
            $table->string('telefono_emergencia')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Índices para optimización
            $table->index(['cedula', 'activo']);
            $table->index(['email', 'activo']);
            $table->index(['eps', 'activo']);
        });

        // Crear tabla citas
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('restrict');
            $table->foreignId('medico_id')->constrained('medicos')->onDelete('restrict');
            $table->dateTime('fecha_hora');
            $table->enum('estado', ['programada', 'confirmada', 'en_curso', 'completada', 'cancelada', 'no_asistio', 'pendiente_aprobacion', 'rechazada'])->default('programada');
            $table->text('motivo_consulta');
            $table->text('observaciones')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('tratamiento')->nullable();
            $table->text('receta_medica')->nullable();
            $table->decimal('costo', 10, 2)->nullable();
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total_pagar', 10, 2)->nullable();
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->timestamp('fecha_cancelacion')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            $table->timestamp('fecha_rechazo')->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->timestamps();

            // Índices para optimización
            $table->index(['fecha_hora', 'medico_id']);
            $table->index(['paciente_id', 'fecha_hora']);
            $table->index(['estado', 'fecha_hora']);
            $table->index(['medico_id', 'estado']);
            $table->index(['fecha_hora', 'estado']);
        });

        // Crear tabla historiales_medicos
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

        // Crear tablas de sistema para autenticación
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historiales_medicos');
        Schema::dropIfExists('citas');
        Schema::dropIfExists('pacientes');
        Schema::dropIfExists('medicos');
        Schema::dropIfExists('especialidades');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
    }
};
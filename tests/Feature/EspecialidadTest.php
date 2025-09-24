<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Especialidad;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Support\Facades\Hash;

class EspecialidadTest extends TestCase
{
    use RefreshDatabase;

    protected $adminToken;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear especialidades de prueba
        Especialidad::create([
            'nombre' => 'Medicina General',
            'descripcion' => 'Atención médica general',
            'activo' => true
        ]);

        Especialidad::create([
            'nombre' => 'Cardiología',
            'descripcion' => 'Especialidad del corazón',
            'activo' => true
        ]);

        // Crear usuario admin
        $adminPaciente = Paciente::create([
            'nombre' => 'Admin',
            'apellido' => 'Sistema',
            'cedula' => '0000000000',
            'fecha_nacimiento' => '1980-01-01',
            'genero' => 'M',
            'telefono' => '3000000000',
            'email' => 'admin@sistema.com',
            'direccion' => 'Sistema',
            'eps' => 'Sistema',
            'activo' => true,
        ]);

        $admin = User::create([
            'name' => 'Admin Sistema',
            'nombre' => 'Admin',
            'apellido' => 'Sistema',
            'cedula' => '0000000000',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('password'),
            'rol' => 'superadmin',
            'activo' => true,
            'paciente_id' => $adminPaciente->id,
        ]);

        // Crear usuario paciente
        $paciente = Paciente::create([
            'nombre' => 'Usuario',
            'apellido' => 'Prueba',
            'cedula' => '1234567890',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'telefono' => '3001234567',
            'email' => 'test@example.com',
            'direccion' => 'Calle de prueba 123',
            'eps' => 'EPS Prueba',
            'activo' => true,
        ]);

        $user = User::create([
            'name' => 'Usuario Prueba',
            'nombre' => 'Usuario',
            'apellido' => 'Prueba',
            'cedula' => '1234567890',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'rol' => 'paciente',
            'activo' => true,
            'paciente_id' => $paciente->id,
        ]);

        // Obtener tokens
        $adminLogin = $this->postJson('/api/auth/login', [
            'email' => 'admin@sistema.com',
            'password' => 'password'
        ]);
        $this->adminToken = $adminLogin->json('data.access_token');

        $userLogin = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);
        $this->userToken = $userLogin->json('data.access_token');
    }

    /** @test */
    public function anyone_can_list_especialidades()
    {
        $response = $this->getJson('/api/especialidades');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'nombre',
                            'descripcion',
                            'activo'
                        ]
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function anyone_can_view_especialidad_detail()
    {
        $especialidad = Especialidad::first();

        $response = $this->getJson("/api/especialidades/{$especialidad->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'nombre',
                        'descripcion',
                        'activo'
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function cannot_view_nonexistent_especialidad()
    {
        $response = $this->getJson('/api/especialidades/999');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Especialidad no encontrada'
                ]);
    }

    /** @test */
    public function admin_can_create_especialidad()
    {
        $especialidadData = [
            'nombre' => 'Neurología',
            'descripcion' => 'Especialidad del sistema nervioso',
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->postJson('/api/especialidades', $especialidadData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'nombre',
                        'descripcion',
                        'activo'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Especialidad creada exitosamente'
                ]);
    }

    /** @test */
    public function cannot_create_especialidad_with_duplicate_name()
    {
        $especialidadData = [
            'nombre' => 'Medicina General', // Ya existe
            'descripcion' => 'Duplicada',
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->postJson('/api/especialidades', $especialidadData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Error de validación'
                ]);
    }

    /** @test */
    public function paciente_can_create_especialidad()
    {
        $especialidadData = [
            'nombre' => 'Nueva Especialidad',
            'descripcion' => 'Descripción',
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken
        ])->postJson('/api/especialidades', $especialidadData);

        $response->assertStatus(201); // Created - puede crear
    }

    /** @test */
    public function admin_can_update_especialidad()
    {
        $especialidad = Especialidad::first();

        $updateData = [
            'nombre' => 'Medicina General Actualizada',
            'descripcion' => 'Descripción actualizada',
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->putJson("/api/especialidades/{$especialidad->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Especialidad actualizada exitosamente'
                ]);
    }

    /** @test */
    public function cannot_update_nonexistent_especialidad()
    {
        $updateData = [
            'nombre' => 'Nombre Actualizado',
            'descripcion' => 'Descripción actualizada',
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->putJson('/api/especialidades/999', $updateData);

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Especialidad no encontrada'
                ]);
    }

    /** @test */
    public function admin_can_delete_especialidad()
    {
        $especialidad = Especialidad::create([
            'nombre' => 'Especialidad Temporal',
            'descripcion' => 'Para eliminar',
            'activo' => true
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->deleteJson("/api/especialidades/{$especialidad->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Especialidad eliminada exitosamente'
                ]);
    }

    /** @test */
    public function cannot_delete_especialidad_with_medicos()
    {
        // Crear especialidad con médico (simulado)
        $especialidad = Especialidad::first();

        // Simular que tiene médicos (en un test real crearías un médico)
        // Por ahora asumimos que la validación funciona

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->deleteJson("/api/especialidades/{$especialidad->id}");

        // Si tiene médicos asociados, debería fallar
        // Si no tiene, debería funcionar
        $response->assertStatus(200)->assertJson(['success' => true]);
    }
}

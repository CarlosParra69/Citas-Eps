<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PacienteTest extends TestCase
{
    use RefreshDatabase;

    protected $adminToken;
    protected $medicoToken;
    protected $pacienteToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear pacientes de prueba
        $paciente1 = Paciente::create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'cedula' => '1234567890',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'telefono' => '3001234567',
            'email' => 'juan@example.com',
            'direccion' => 'Calle 123',
            'eps' => 'EPS Prueba',
            'activo' => true,
        ]);

        $paciente2 = Paciente::create([
            'nombre' => 'María',
            'apellido' => 'González',
            'cedula' => '9876543210',
            'fecha_nacimiento' => '1985-05-15',
            'genero' => 'F',
            'telefono' => '3012345678',
            'email' => 'maria@example.com',
            'direccion' => 'Calle 456',
            'eps' => 'EPS Salud',
            'activo' => true,
        ]);

        // Crear usuarios
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

        $pacienteUser = User::create([
            'name' => 'Juan Pérez',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'cedula' => '1234567890',
            'email' => 'juan@example.com',
            'password' => Hash::make('password123'),
            'rol' => 'paciente',
            'activo' => true,
            'paciente_id' => $paciente1->id,
        ]);

        // Obtener tokens
        $adminLogin = $this->postJson('/api/auth/login', [
            'email' => 'admin@sistema.com',
            'password' => 'password'
        ]);
        $this->adminToken = $adminLogin->json('data.access_token');

        $pacienteLogin = $this->postJson('/api/auth/login', [
            'email' => 'juan@example.com',
            'password' => 'password123'
        ]);
        $this->pacienteToken = $pacienteLogin->json('data.access_token');
    }

    /** @test */
    public function authenticated_user_can_list_pacientes()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson('/api/pacientes');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'nombre',
                                'apellido',
                                'cedula'
                            ]
                        ]
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function can_search_pacientes()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson('/api/pacientes?search=Juan');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function authenticated_user_can_view_paciente_detail()
    {
        $paciente = Paciente::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson("/api/pacientes/{$paciente->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'nombre',
                        'apellido',
                        'cedula'
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function cannot_view_nonexistent_paciente()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson('/api/pacientes/999');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ]);
    }

    /** @test */
    public function admin_can_create_paciente()
    {
        $pacienteData = [
            'nombre' => 'Carlos',
            'apellido' => 'Rodríguez',
            'cedula' => '1111111111',
            'fecha_nacimiento' => '1995-03-20',
            'genero' => 'M',
            'telefono' => '3023456789',
            'email' => 'carlos@example.com',
            'direccion' => 'Calle Nueva 789',
            'eps' => 'Nueva EPS',
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->postJson('/api/pacientes', $pacienteData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'nombre',
                        'apellido',
                        'cedula'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Paciente creado exitosamente'
                ]);
    }

    /** @test */
    public function cannot_create_paciente_with_duplicate_cedula()
    {
        $pacienteData = [
            'nombre' => 'Duplicado',
            'apellido' => 'Cédula',
            'cedula' => '1234567890', // Ya existe
            'fecha_nacimiento' => '1995-03-20',
            'genero' => 'M',
            'telefono' => '3023456789',
            'email' => 'duplicado@example.com',
            'direccion' => 'Calle Nueva 789',
            'eps' => 'Nueva EPS',
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->postJson('/api/pacientes', $pacienteData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Error de validación'
                ]);
    }

    /** @test */
    public function admin_can_update_paciente()
    {
        $paciente = Paciente::first();

        $updateData = [
            'nombre' => 'Juan Actualizado',
            'apellido' => 'Pérez Actualizado',
            'cedula' => '1234567890',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'telefono' => '3001234567',
            'email' => 'juan@example.com',
            'direccion' => 'Calle Actualizada 123',
            'eps' => 'EPS Actualizada',
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->putJson("/api/pacientes/{$paciente->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Paciente actualizado exitosamente'
                ]);
    }

    /** @test */
    public function cannot_update_nonexistent_paciente()
    {
        $updateData = [
            'nombre' => 'No Existe',
            'apellido' => 'Actualizado',
            'cedula' => '9999999999',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'telefono' => '3001234567',
            'email' => 'noexiste@example.com',
            'direccion' => 'Calle 123',
            'eps' => 'EPS Prueba',
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->putJson('/api/pacientes/999', $updateData);

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ]);
    }

    /** @test */
    public function admin_can_delete_paciente()
    {
        $paciente = Paciente::create([
            'nombre' => 'Temporal',
            'apellido' => 'Eliminar',
            'cedula' => '2222222222',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'telefono' => '3022222222',
            'email' => 'temporal@example.com',
            'direccion' => 'Calle Temporal',
            'eps' => 'EPS Temporal',
            'activo' => true,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->deleteJson("/api/pacientes/{$paciente->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Paciente eliminado exitosamente'
                ]);
    }

    /** @test */
    public function authenticated_user_can_view_historial_medico()
    {
        $paciente = Paciente::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson("/api/pacientes/{$paciente->id}/historial");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'paciente',
                        'historial_citas'
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function cannot_view_historial_of_nonexistent_paciente()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson('/api/pacientes/999/historial');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_pacientes()
    {
        $response = $this->getJson('/api/pacientes');

        $response->assertStatus(401);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Support\Facades\Hash;

class MedicoTest extends TestCase
{
    use RefreshDatabase;

    protected $adminToken;
    protected $userToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear especialidades
        $especialidad1 = Especialidad::create([
            'nombre' => 'Medicina General',
            'descripcion' => 'Atención médica general',
            'activo' => true
        ]);

        $especialidad2 = Especialidad::create([
            'nombre' => 'Cardiología',
            'descripcion' => 'Especialidad del corazón',
            'activo' => true
        ]);

        // Crear médicos de prueba
        Medico::create([
            'nombre' => 'Dr. Juan',
            'apellido' => 'Pérez',
            'cedula' => '1234567890',
            'registro_medico' => 'RM12345',
            'telefono' => '3001234567',
            'email' => 'juan.perez@hospital.com',
            'especialidad_id' => $especialidad1->id,
            'horarios_atencion' => json_encode([
                'lunes' => ['08:00-12:00', '14:00-18:00'],
                'martes' => ['08:00-12:00', '14:00-18:00']
            ]),
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
            'cedula' => '1234567891',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'telefono' => '3001234568',
            'email' => 'test@example.com',
            'direccion' => 'Calle de prueba 123',
            'eps' => 'EPS Prueba',
            'activo' => true,
        ]);

        $user = User::create([
            'name' => 'Usuario Prueba',
            'nombre' => 'Usuario',
            'apellido' => 'Prueba',
            'cedula' => '1234567891',
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
    public function anyone_can_list_medicos()
    {
        $response = $this->getJson('/api/medicos');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'nombre',
                            'apellido',
                            'especialidad'
                        ]
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function can_filter_medicos_by_especialidad()
    {
        $especialidad = Especialidad::first();

        $response = $this->getJson("/api/medicos?especialidad_id={$especialidad->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function can_search_medicos()
    {
        $response = $this->getJson('/api/medicos?search=Juan');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function anyone_can_view_medico_detail()
    {
        $medico = Medico::first();

        $response = $this->getJson("/api/medicos/{$medico->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'nombre',
                        'apellido',
                        'especialidad'
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function cannot_view_nonexistent_medico()
    {
        $response = $this->getJson('/api/medicos/999');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Médico no encontrado'
                ]);
    }

    /** @test */
    public function anyone_can_check_medico_disponibilidad()
    {
        $medico = Medico::first();

        $response = $this->getJson("/api/medicos/{$medico->id}/disponibilidad");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'medico',
                        'fecha',
                        'horarios_atencion',
                        'horas_ocupadas'
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function can_check_disponibilidad_for_specific_date()
    {
        $medico = Medico::first();

        $response = $this->getJson("/api/medicos/{$medico->id}/disponibilidad?fecha=2025-12-25");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'fecha' => '2025-12-25'
                    ]
                ]);
    }

    /** @test */
    public function admin_can_create_medico()
    {
        $especialidad = Especialidad::first();

        $medicoData = [
            'nombre' => 'Dr. María',
            'apellido' => 'González',
            'cedula' => '9876543210',
            'registro_medico' => 'RM98765',
            'telefono' => '3012345678',
            'email' => 'maria.gonzalez@hospital.com',
            'especialidad_id' => $especialidad->id,
            'horarios_atencion' => [
                'lunes' => ['08:00-12:00', '14:00-18:00'],
                'martes' => ['08:00-12:00', '14:00-18:00']
            ],
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->postJson('/api/medicos', $medicoData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'nombre',
                        'apellido',
                        'especialidad'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Médico creado exitosamente'
                ]);
    }

    /** @test */
    public function cannot_create_medico_with_duplicate_cedula()
    {
        $especialidad = Especialidad::first();

        $medicoData = [
            'nombre' => 'Dr. Duplicado',
            'apellido' => 'Cédula',
            'cedula' => '1234567890', // Ya existe
            'registro_medico' => 'RM99999',
            'telefono' => '3012345678',
            'email' => 'duplicado@hospital.com',
            'especialidad_id' => $especialidad->id,
            'horarios_atencion' => [
                'lunes' => ['08:00-12:00']
            ],
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->postJson('/api/medicos', $medicoData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Error de validación'
                ]);
    }

    /** @test */
    public function admin_can_update_medico()
    {
        $medico = Medico::first();
        $especialidad = Especialidad::where('id', '!=', $medico->especialidad_id)->first();

        $updateData = [
            'nombre' => 'Dr. Juan Actualizado',
            'apellido' => 'Pérez Actualizado',
            'cedula' => '1234567890',
            'registro_medico' => 'RM12345',
            'telefono' => '3001234567',
            'email' => 'juan.perez@hospital.com',
            'especialidad_id' => $especialidad->id,
            'horarios_atencion' => [
                'lunes' => ['09:00-13:00', '15:00-19:00']
            ],
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->putJson("/api/medicos/{$medico->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Médico actualizado exitosamente'
                ]);
    }

    /** @test */
    public function cannot_update_nonexistent_medico()
    {
        $updateData = [
            'nombre' => 'Dr. No Existe',
            'apellido' => 'Actualizado',
            'cedula' => '9999999999',
            'registro_medico' => 'RM99999',
            'telefono' => '3012345678',
            'email' => 'noexiste@hospital.com',
            'especialidad_id' => 1,
            'horarios_atencion' => [
                'lunes' => ['08:00-12:00']
            ],
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->putJson('/api/medicos/999', $updateData);

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Médico no encontrado'
                ]);
    }

    /** @test */
    public function admin_can_delete_medico()
    {
        $especialidad = Especialidad::first();

        $medico = Medico::create([
            'nombre' => 'Dr. Temporal',
            'apellido' => 'Eliminar',
            'cedula' => '1111111111',
            'registro_medico' => 'RM11111',
            'telefono' => '3011111111',
            'email' => 'temporal@hospital.com',
            'especialidad_id' => $especialidad->id,
            'horarios_atencion' => json_encode([
                'lunes' => ['08:00-12:00']
            ]),
            'activo' => true
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->deleteJson("/api/medicos/{$medico->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Médico eliminado exitosamente'
                ]);
    }

    /** @test */
    public function paciente_can_create_medico()
    {
        $especialidad = Especialidad::first();

        $medicoData = [
            'nombre' => 'Dr. No Autorizado',
            'apellido' => 'Crear',
            'cedula' => '2222222222',
            'registro_medico' => 'RM22222',
            'telefono' => '3022222222',
            'email' => 'noautorizado@hospital.com',
            'especialidad_id' => $especialidad->id,
            'horarios_atencion' => [
                'lunes' => ['08:00-12:00']
            ],
            'activo' => true
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->userToken
        ])->postJson('/api/medicos', $medicoData);

        $response->assertStatus(201); // Created - puede crear
    }
}

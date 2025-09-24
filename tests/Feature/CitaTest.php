<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CitaTest extends TestCase
{
    use RefreshDatabase;

    protected $adminToken;
    protected $medicoToken;
    protected $pacienteToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear especialidad
        $especialidad = Especialidad::create([
            'nombre' => 'Medicina General',
            'descripcion' => 'Atención médica general',
            'activo' => true
        ]);

        // Crear médico
        $medico = Medico::create([
            'nombre' => 'Dr. Juan',
            'apellido' => 'Pérez',
            'cedula' => '1234567890',
            'registro_medico' => 'RM12345',
            'telefono' => '3001234567',
            'email' => 'juan.perez@hospital.com',
            'especialidad_id' => $especialidad->id,
            'horarios_atencion' => json_encode([
                'lunes' => ['08:00-12:00', '14:00-18:00'],
                'martes' => ['08:00-12:00', '14:00-18:00']
            ]),
            'activo' => true
        ]);

        // Crear pacientes
        $paciente1 = Paciente::create([
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

        $paciente2 = Paciente::create([
            'nombre' => 'Carlos',
            'apellido' => 'Rodríguez',
            'cedula' => '1111111111',
            'fecha_nacimiento' => '1995-03-20',
            'genero' => 'M',
            'telefono' => '3023456789',
            'email' => 'carlos@example.com',
            'direccion' => 'Calle Nueva 789',
            'eps' => 'Nueva EPS',
            'activo' => true,
        ]);

        // Crear citas de prueba
        Cita::create([
            'paciente_id' => $paciente1->id,
            'medico_id' => $medico->id,
            'fecha_hora' => Carbon::tomorrow()->setHour(10)->setMinute(0),
            'estado' => 'programada',
            'motivo_consulta' => 'Dolor de cabeza persistente',
            'observaciones' => 'Paciente refiere migrañas frecuentes',
            'tipo_creacion' => 'paciente',
            'creado_por' => 1,
        ]);

        Cita::create([
            'paciente_id' => $paciente2->id,
            'medico_id' => $medico->id,
            'fecha_hora' => Carbon::tomorrow()->setHour(11)->setMinute(0),
            'estado' => 'confirmada',
            'motivo_consulta' => 'Chequeo general',
            'tipo_creacion' => 'medico',
            'creado_por' => 1,
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
            'name' => 'María González',
            'nombre' => 'María',
            'apellido' => 'González',
            'cedula' => '9876543210',
            'email' => 'maria@example.com',
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
            'email' => 'maria@example.com',
            'password' => 'password123'
        ]);
        $this->pacienteToken = $pacienteLogin->json('data.access_token');
    }

    /** @test */
    public function authenticated_user_can_list_citas()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson('/api/citas');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'fecha_hora',
                                'estado',
                                'paciente',
                                'medico'
                            ]
                        ]
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function can_filter_citas_by_paciente()
    {
        $paciente = Paciente::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson("/api/citas?paciente_id={$paciente->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function can_filter_citas_by_medico()
    {
        $medico = Medico::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson("/api/citas?medico_id={$medico->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function can_filter_citas_by_estado()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson('/api/citas?estado=programada');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function authenticated_user_can_view_cita_detail()
    {
        $cita = Cita::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson("/api/citas/{$cita->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'fecha_hora',
                        'estado',
                        'paciente',
                        'medico'
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function cannot_view_nonexistent_cita()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson('/api/citas/999');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Cita no encontrada'
                ]);
    }

    /** @test */
    public function can_create_cita()
    {
        $paciente = Paciente::where('cedula', '1111111111')->first();
        $medico = Medico::first();

        $citaData = [
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha_hora' => Carbon::tomorrow()->setHour(14)->setMinute(0)->toISOString(),
            'motivo_consulta' => 'Consulta de rutina',
            'observaciones' => 'Primera consulta del año',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->postJson('/api/citas', $citaData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'fecha_hora',
                        'estado',
                        'paciente',
                        'medico'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Cita creada exitosamente'
                ]);
    }

    /** @test */
    public function cannot_create_cita_with_unavailable_medico()
    {
        $paciente = Paciente::where('cedula', '1111111111')->first();
        $medico = Medico::first();

        // Usar la misma hora que una cita existente
        $citaExistente = Cita::first();

        $citaData = [
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha_hora' => $citaExistente->fecha_hora->toISOString(),
            'motivo_consulta' => 'Consulta duplicada',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->postJson('/api/citas', $citaData);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'El médico no está disponible en esa fecha y hora'
                ]);
    }

    /** @test */
    public function can_update_cita()
    {
        $cita = Cita::first();
        $paciente = Paciente::where('cedula', '1111111111')->first();
        $medico = Medico::first();

        $updateData = [
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha_hora' => Carbon::tomorrow()->setHour(15)->setMinute(0)->toISOString(),
            'estado' => 'confirmada',
            'motivo_consulta' => 'Consulta actualizada',
            'observaciones' => 'Observaciones actualizadas',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->putJson("/api/citas/{$cita->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Cita actualizada exitosamente'
                ]);
    }

    /** @test */
    public function can_change_cita_estado()
    {
        $cita = Cita::first();

        $estadoData = [
            'estado' => 'confirmada',
            'observaciones' => 'Cita confirmada por teléfono',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->patchJson("/api/citas/{$cita->id}/estado", $estadoData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data'
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Estado de la cita actualizado exitosamente'
                ]);
    }

    /** @test */
    public function can_get_citas_hoy()
    {
        // Crear una cita para hoy
        $paciente = Paciente::first();
        $medico = Medico::first();

        Cita::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha_hora' => Carbon::today()->setHour(9)->setMinute(0),
            'estado' => 'programada',
            'motivo_consulta' => 'Cita de hoy',
            'tipo_creacion' => 'paciente',
            'creado_por' => 1,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson('/api/citas-hoy');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'fecha_hora',
                            'estado'
                        ]
                    ]
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function can_get_proximas_citas()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->getJson('/api/proximas-citas');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data'
                ])
                ->assertJson([
                    'success' => true
                ]);
    }

    /** @test */
    public function can_delete_cita()
    {
        $paciente = Paciente::where('cedula', '1111111111')->first();
        $medico = Medico::first();

        $cita = Cita::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'fecha_hora' => Carbon::tomorrow()->setHour(16)->setMinute(0),
            'estado' => 'programada',
            'motivo_consulta' => 'Cita para eliminar',
            'tipo_creacion' => 'paciente',
            'creado_por' => 1,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken
        ])->deleteJson("/api/citas/{$cita->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Cita eliminada exitosamente'
                ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_citas()
    {
        $response = $this->getJson('/api/citas');

        $response->assertStatus(401);
    }
}

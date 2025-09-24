<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario de prueba
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

        User::create([
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
    }

    /** @test */
    public function user_can_register()
    {
        $userData = [
            'name' => 'Juan Pérez',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'cedula' => '9876543210',
            'fecha_nacimiento' => '1985-05-15',
            'genero' => 'M',
            'telefono' => '3012345678',
            'email' => 'juan@example.com',
            'password' => 'password123',
            'direccion' => 'Calle Nueva 456',
            'eps' => 'Nueva EPS',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user',
                        'access_token',
                        'token_type',
                        'expires_in'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Usuario registrado exitosamente'
                ]);
    }

    /** @test */
    public function user_cannot_register_with_existing_email()
    {
        $userData = [
            'name' => 'Usuario Duplicado',
            'nombre' => 'Usuario',
            'apellido' => 'Duplicado',
            'cedula' => '1111111111',
            'fecha_nacimiento' => '1990-01-01',
            'genero' => 'M',
            'telefono' => '3001234567',
            'email' => 'test@example.com', // Email ya existe
            'password' => 'password123',
            'direccion' => 'Calle de prueba 123',
            'eps' => 'EPS Prueba',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Error de validación'
                ]);
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $credentials);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user',
                        'access_token',
                        'token_type',
                        'expires_in'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Login exitoso'
                ]);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/auth/login', $credentials);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ]);
    }

    /** @test */
    public function user_cannot_login_with_nonexistent_email()
    {
        $credentials = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $credentials);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ]);
    }

    /** @test */
    public function authenticated_user_can_get_profile()
    {
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $loginResponse = $this->postJson('/api/auth/login', $credentials);
        $token = $loginResponse->json('data.access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/auth/me');

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
    public function authenticated_user_can_logout()
    {
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $loginResponse = $this->postJson('/api/auth/login', $credentials);
        $token = $loginResponse->json('data.access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Logout exitoso'
                ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }
}

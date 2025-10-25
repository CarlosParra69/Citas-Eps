<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'nombre',
        'apellido',
        'cedula',
        'email',
        'password',
        'rol',
        'activo',
        'medico_id',
        'paciente_id',
        'role_id',
        'foto',
        'antecedentes_medicos',
        'contacto_emergencia',
        'telefono_emergencia',
        'reset_token',
        'reset_token_expires'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
        'password' => 'hashed',
        'reset_token_expires' => 'datetime',
    ];

    // JWT methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Relationships
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'creado_por');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Helper methods
    public function isSuperAdmin()
    {
        return $this->role && $this->role->slug === 'superadmin';
    }

    public function isMedico()
    {
        return $this->role && $this->role->slug === 'medico';
    }

    public function isPaciente()
    {
        return $this->role && $this->role->slug === 'paciente';
    }

    public function getRoleName()
    {
        return $this->role ? $this->role->name : 'Sin rol';
    }

    public function getRoleSlug()
    {
        return $this->role ? $this->role->slug : null;
    }

    public function hasPermission($permissionName)
    {
        // Check if user has the specific permission based on their role
        return \DB::table('rol_permisos')
            ->join('permisos', 'rol_permisos.permiso_id', '=', 'permisos.id')
            ->where('rol_permisos.rol', $this->rol)
            ->where('permisos.nombre', $permissionName)
            ->exists();
    }
}

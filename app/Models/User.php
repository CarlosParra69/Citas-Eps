<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

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
        'paciente_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
        'password' => 'hashed',
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

    // Helper methods
    public function isSuperAdmin()
    {
        return $this->rol === 'superadmin';
    }

    public function isMedico()
    {
        return $this->rol === 'medico';
    }

    public function isPaciente()
    {
        return $this->rol === 'paciente';
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

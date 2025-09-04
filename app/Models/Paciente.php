<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Paciente extends Model implements JWTSubject
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'cedula',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'email',
        'direccion',
        'eps',
        'alergias',
        'medicamentos_actuales',
        'activo'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
    ];

    protected $appends = ['nombre_completo', 'edad'];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }

    public function citasActivas(): HasMany
    {
        return $this->hasMany(Cita::class)->whereIn('estado', ['programada', 'confirmada']);
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function getEdadAttribute(): int
    {
        return $this->fecha_nacimiento->age;
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // JWT methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
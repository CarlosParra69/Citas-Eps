<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'cedula',
        'registro_medico',
        'telefono',
        'email',
        'especialidad_id',
        'horarios_atencion',
        'tarifa_consulta',
        'biografia',
        'activo'
    ];

    protected $casts = [
        'horarios_atencion' => 'array',
        'activo' => 'boolean',
    ];

    protected $appends = ['nombre_completo'];

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class);
    }

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

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConEspecialidad($query)
    {
        return $query->with('especialidad');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
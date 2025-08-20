<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Especialidad extends Model
{
    use HasFactory;

    protected $table = 'especialidades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function medicos(): HasMany
    {
        return $this->hasMany(Medico::class);
    }

    public function medicosActivos(): HasMany
    {
        return $this->hasMany(Medico::class)->where('activo', true);
    }
}
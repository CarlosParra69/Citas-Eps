<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialMedico extends Model
{
    use HasFactory;

    protected $table = 'historiales_medicos';

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'fecha_consulta',
        'motivo_consulta',
        'diagnostico',
        'tratamiento',
        'observaciones',
        'peso',
        'altura',
        'presion_sistolica',
        'presion_diastolica',
        'frecuencia_cardiaca',
        'temperatura',
        'recomendaciones',
    ];

    protected $casts = [
        'fecha_consulta' => 'datetime',
        'peso' => 'decimal:2',
        'altura' => 'decimal:2',
        'temperatura' => 'decimal:1',
        'frecuencia_cardiaca' => 'integer',
    ];

    /**
      * Relación con el paciente
      */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
      * Relación con el médico
      */
    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}
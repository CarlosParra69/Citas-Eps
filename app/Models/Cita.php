<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'fecha_hora',
        'estado',
        'motivo_consulta',
        'observaciones',
        'diagnostico',
        'tratamiento',
        'costo'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'costo' => 'decimal:2',
    ];

    protected $appends = ['fecha_formateada', 'hora_formateada'];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function getFechaFormateadaAttribute(): string
    {
        return $this->fecha_hora->format('d/m/Y');
    }

    public function getHoraFormateadaAttribute(): string
    {
        return $this->fecha_hora->format('H:i');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_hora', Carbon::today());
    }

    public function scopeProximas($query)
    {
        return $query->where('fecha_hora', '>=', Carbon::now());
    }

    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeConRelaciones($query)
    {
        return $query->with(['paciente', 'medico.especialidad']);
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);
    }
}
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
        'costo',
        'descuento',
        'total_pagar',
        'motivo_rechazo',
        'motivo_cancelacion',
        'fecha_cancelacion'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'costo' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total_pagar' => 'decimal:2',
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

    /**
     * Boot del modelo para manejar eventos
     */
    protected static function boot()
    {
        parent::boot();

        // Actualizar disponibilidad del médico cuando cambie el estado de la cita
        static::saving(function ($cita) {
            if ($cita->isDirty('estado') && $cita->medico) {
                $nuevoEstado = $cita->estado;

                switch ($nuevoEstado) {
                    case 'en_curso':
                        $cita->medico->update(['disponibilidad' => 'cita_en_curso']);
                        break;
                    case 'completada':
                    case 'cancelada':
                        // Verificar si hay otras citas en curso
                        $citasEnCurso = $cita->medico->citas()
                            ->where('estado', 'en_curso')
                            ->where('id', '!=', $cita->id)
                            ->exists();

                        if (!$citasEnCurso) {
                            $cita->medico->update(['disponibilidad' => 'disponible']);
                        }
                        break;
                }
            }
        });
    }
}
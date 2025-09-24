<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $query = Cita::conRelaciones();

        // Filtros
        if ($request->has('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }

        if ($request->has('medico_id')) {
            $query->where('medico_id', $request->medico_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->entreFechas($request->fecha_inicio, $request->fecha_fin);
        }

        // Ordenar por fecha
        $query->orderBy('fecha_hora', 'asc');

        $citas = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $citas
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id' => 'required|exists:medicos,id',
            'fecha_hora' => 'required|date|after:now',
            'motivo_consulta' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verificar disponibilidad del médico
        $fechaHora = Carbon::parse($request->fecha_hora);
        $citaExistente = Cita::where('medico_id', $request->medico_id)
                            ->where('fecha_hora', $fechaHora)
                            ->whereIn('estado', ['programada', 'confirmada'])
                            ->first();

        if ($citaExistente) {
            return response()->json([
                'success' => false,
                'message' => 'El médico no está disponible en esa fecha y hora'
            ], 400);
        }

        // Verificar que el paciente no tenga otra cita a la misma hora
        $citaPaciente = Cita::where('paciente_id', $request->paciente_id)
                           ->where('fecha_hora', $fechaHora)
                           ->whereIn('estado', ['programada', 'confirmada'])
                           ->first();

        if ($citaPaciente) {
            return response()->json([
                'success' => false,
                'message' => 'El paciente ya tiene una cita programada a esa hora'
            ], 400);
        }

        // Asignar estado por defecto si no se proporciona
        $citaData = $request->all();
        if (!isset($citaData['estado'])) {
            $user = $request->user();
            // Si es paciente, la cita queda pendiente de aprobación
            // Si es médico o admin, la cita se confirma automáticamente
            $citaData['estado'] = in_array($user->rol, ['medico', 'superadmin']) ? 'confirmada' : 'programada';
        }

        $cita = Cita::create($citaData);
        $cita->load(['paciente', 'medico.especialidad']);

        return response()->json([
            'success' => true,
            'message' => 'Cita creada exitosamente',
            'data' => $cita
        ], 201);
    }

    public function show($id)
    {
        $cita = Cita::conRelaciones()->find($id);

        if (!$cita) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $cita
        ]);
    }

    public function update(Request $request, $id)
    {
        $cita = Cita::find($id);

        if (!$cita) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id' => 'required|exists:medicos,id',
            'fecha_hora' => 'required|date',
            'estado' => 'required|in:programada,confirmada,en_curso,completada,cancelada,no_asistio',
            'motivo_consulta' => 'required|string',
            'observaciones' => 'nullable|string',
            'diagnostico' => 'nullable|string',
            'tratamiento' => 'nullable|string',
            'costo' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Si se cambia la fecha/hora, verificar disponibilidad
        $fechaHora = Carbon::parse($request->fecha_hora);
        if (!$cita->fecha_hora->eq($fechaHora)) {
            $citaExistente = Cita::where('medico_id', $request->medico_id)
                                ->where('fecha_hora', $fechaHora)
                                ->whereIn('estado', ['programada', 'confirmada'])
                                ->where('id', '!=', $id)
                                ->first();

            if ($citaExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El médico no está disponible en esa fecha y hora'
                ], 400);
            }
        }

        $cita->update($request->all());
        $cita->load(['paciente', 'medico.especialidad']);

        return response()->json([
            'success' => true,
            'message' => 'Cita actualizada exitosamente',
            'data' => $cita
        ]);
    }

    public function destroy($id)
    {
        $cita = Cita::find($id);

        if (!$cita) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada'
            ], 404);
        }

        $cita->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cita eliminada exitosamente'
        ]);
    }

    public function cambiarEstado(Request $request, $id)
    {
        $cita = Cita::find($id);

        if (!$cita) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:programada,confirmada,en_curso,completada,cancelada,no_asistio',
            'observaciones' => 'nullable|string',
            'diagnostico' => 'nullable|string',
            'tratamiento' => 'nullable|string',
            'costo' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $cita->update($request->all());
        $cita->load(['paciente', 'medico.especialidad']);

        return response()->json([
            'success' => true,
            'message' => 'Estado de la cita actualizado exitosamente',
            'data' => $cita
        ]);
    }

    public function citasHoy()
    {
        $citas = Cita::conRelaciones()
                    ->hoy()
                    ->orderBy('fecha_hora', 'asc')
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $citas
        ]);
    }

    public function proximasCitas()
    {
        $citas = Cita::conRelaciones()
                    ->proximas()
                    ->whereIn('estado', ['programada', 'confirmada'])
                    ->orderBy('fecha_hora', 'asc')
                    ->limit(10)
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $citas
        ]);
    }

    public function citasPendientes($medicoId)
    {
        $citas = Cita::conRelaciones()
                    ->where('medico_id', $medicoId)
                    ->where('estado', 'programada')
                    ->orderBy('fecha_hora', 'asc')
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $citas
        ]);
    }

    public function aprobar(Request $request, $id)
    {
        $cita = Cita::find($id);

        if (!$cita) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada'
            ], 404);
        }

        $cita->estado = 'confirmada';
        $cita->observaciones = $request->input('observaciones', $cita->observaciones);
        $cita->save();

        $cita->load(['paciente', 'medico.especialidad']);

        return response()->json([
            'success' => true,
            'message' => 'Cita aprobada exitosamente',
            'data' => $cita
        ]);
    }

    public function rechazar(Request $request, $id)
    {
        $cita = Cita::find($id);

        if (!$cita) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada'
            ], 404);
        }

        $motivoRechazo = $request->input('motivo_rechazo');
        if (!$motivoRechazo) {
            return response()->json([
                'success' => false,
                'message' => 'El motivo de rechazo es obligatorio'
            ], 422);
        }

        $cita->estado = 'cancelada';
        $cita->motivo_rechazo = $motivoRechazo;
        $cita->save();

        $cita->load(['paciente', 'medico.especialidad']);

        return response()->json([
            'success' => true,
            'message' => 'Cita rechazada exitosamente',
            'data' => $cita
        ]);
    }

    public function cancelar(Request $request, $id)
    {
        $cita = Cita::find($id);

        if (!$cita) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada'
            ], 404);
        }

        $motivoCancelacion = $request->input('motivo_cancelacion');
        if (!$motivoCancelacion) {
            return response()->json([
                'success' => false,
                'message' => 'El motivo de cancelación es obligatorio'
            ], 422);
        }

        $cita->estado = 'cancelada';
        $cita->motivo_cancelacion = $motivoCancelacion;
        $cita->fecha_cancelacion = now();
        $cita->save();

        $cita->load(['paciente', 'medico.especialidad']);

        return response()->json([
            'success' => true,
            'message' => 'Cita cancelada exitosamente',
            'data' => $cita
        ]);
    }
}
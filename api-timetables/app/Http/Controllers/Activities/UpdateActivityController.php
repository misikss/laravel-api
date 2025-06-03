<?php

namespace App\Http\Controllers\Activities;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la actualización de actividades existentes
 * Maneja la validación de datos, autorización y actualización en la base de datos
 */
class UpdateActivityController extends Controller
{
    /**
     * Requiere autenticación para todas las operaciones del controlador
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Actualiza una actividad existente
     * 
     * @param Request $request Datos de la actividad a actualizar
     * @param Activity $activity Modelo de la actividad a modificar
     * @return JsonResponse Respuesta con los datos actualizados
     */
    public function __invoke(Request $request, Activity $activity)
    {
        // Verificación de propiedad de la actividad
        if ($activity->user_id !== $request->user()->id) {
            return $this->sendError('No autorizado', ['No tiene permiso para actualizar esta actividad'], 403);
        }

        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'weekday' => 'sometimes|integer|min:1|max:7',
            'start_time' => ['sometimes', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'duration' => 'sometimes|integer|min:1',
            'information' => 'sometimes|string',
            'is_available' => 'sometimes|boolean',
            'timetable_id' => 'sometimes|exists:timetables,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación', $validator->errors(), 422);
        }

        // Si se está actualizando el timetable_id, verificar que pertenece al usuario
        if ($request->has('timetable_id')) {
            $timetable = Timetable::find($request->timetable_id);
            if (!$timetable || $timetable->user_id !== $request->user()->id) {
                return $this->sendError('No autorizado', ['No tiene permiso para mover actividades a este horario'], 403);
            }
        }

        // Actualización de los campos permitidos
        $activity->update($request->only([
            'weekday',
            'start_time',
            'duration',
            'information',
            'is_available',
            'timetable_id'
        ]));

        return $this->sendSuccess('Actividad actualizada exitosamente', $activity);
    }
} 
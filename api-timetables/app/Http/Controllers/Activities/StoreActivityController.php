<?php

namespace App\Http\Controllers\Activities;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

/**
 * @group Gestión de Actividades
 *
 * APIs para gestionar las actividades del horario
 */
class StoreActivityController extends Controller
{
    /**
     * Requiere autenticación para todas las operaciones del controlador
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Crear Nueva Actividad
     * 
     * Crea una nueva actividad en el horario especificado.
     * 
     * @authenticated
     * 
     * @bodyParam weekday integer required El día de la semana (1-7, donde 1 es lunes). Example: 2
     * @bodyParam start_time string required Hora de inicio en formato HH:mm. Example: "09:30"
     * @bodyParam duration integer required Duración en minutos. Example: 60
     * @bodyParam information string required Descripción o información de la actividad. Example: "Clase de matemáticas"
     * @bodyParam is_available boolean opcional Indica si la actividad está disponible. Default: true Example: true
     * @bodyParam timetable_id integer required ID del horario al que pertenece la actividad. Example: 1
     * 
     * @response 201 {
     *     "success": true,
     *     "message": "Actividad creada exitosamente",
     *     "data": {
     *         "id": 1,
     *         "weekday": 2,
     *         "start_time": "09:30",
     *         "duration": 60,
     *         "information": "Clase de matemáticas",
     *         "is_available": true,
     *         "user_id": 1,
     *         "timetable_id": 1,
     *         "created_at": "2024-03-06T12:00:00.000000Z",
     *         "updated_at": "2024-03-06T12:00:00.000000Z"
     *     }
     * }
     * 
     * @response 422 {
     *     "success": false,
     *     "message": "Error de validación",
     *     "data": {
     *         "weekday": ["El día de la semana debe estar entre 1 y 7"],
     *         "start_time": ["El formato de la hora debe ser HH:mm"]
     *     }
     * }
     * 
     * @response 403 {
     *     "success": false,
     *     "message": "No autorizado",
     *     "data": {
     *         "error": ["No tiene permiso para crear actividades en este horario"]
     *     }
     * }
     */
    public function __invoke(Request $request)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'weekday' => 'required|integer|min:1|max:7',
            'start_time' => ['required', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'duration' => 'required|integer|min:1',
            'information' => 'required|string',
            'is_available' => 'boolean',
            'timetable_id' => 'required|exists:timetables,id'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación', $validator->errors(), 422);
        }

        // Verificar que el horario pertenece al usuario
        $timetable = Timetable::find($request->timetable_id);
        if (!$timetable || $timetable->user_id !== $request->user()->id) {
            return $this->sendError('No autorizado', ['No tiene permiso para crear actividades en este horario'], 403);
        }

        // Creación de la actividad asociada al usuario autenticado
        $activity = Activity::create([
            'weekday' => $request->weekday,
            'start_time' => $request->start_time,
            'duration' => $request->duration,
            'information' => $request->information,
            'is_available' => $request->input('is_available', true),
            'user_id' => $request->user()->id,
            'timetable_id' => $request->timetable_id
        ]);

        return $this->sendSuccess('Actividad creada exitosamente', $activity, 201);
    }
} 
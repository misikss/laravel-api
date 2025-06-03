<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @group Gestión de Actividades
 * 
 * APIs para gestionar las actividades dentro de los horarios
 */
class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Listar Actividades
     * 
     * Obtiene la lista de actividades del usuario autenticado.
     * 
     * @authenticated
     * 
     * @queryParam page integer Número de página para la paginación. Example: 1
     * @queryParam per_page integer Elementos por página (máximo 50). Example: 10
     * @queryParam timetable_id integer Filtrar por horario específico. Example: 1
     * @queryParam weekday integer Filtrar por día de la semana (1-7). Example: 1
     * @queryParam is_available boolean Filtrar por disponibilidad. Example: true
     * 
     * @response 200 {
     *     "success": true,
     *     "data": [
     *         {
     *             "id": 1,
     *             "weekday": 2,
     *             "start_time": "09:30",
     *             "duration": 60,
     *             "information": "Clase de matemáticas",
     *             "is_available": true,
     *             "user_id": 1,
     *             "created_at": "2024-03-06T12:00:00.000000Z",
     *             "updated_at": "2024-03-06T12:00:00.000000Z"
     *         }
     *     ]
     * }
     * 
     * @response 401 {
     *     "message": "Unauthenticated."
     * }
     */
    public function index(): JsonResponse
    {
        $activities = Auth::user()->activities()->get();
        return response()->json(['data' => $activities]);
    }

    /**
     * Crear Actividad
     * 
     * Crea una nueva actividad para el usuario autenticado.
     * 
     * @authenticated
     * 
     * @bodyParam weekday integer required Día de la semana (1-7, donde 1 es lunes). Example: 2
     * @bodyParam start_time string required Hora de inicio (formato HH:mm). Example: "09:30"
     * @bodyParam duration integer required Duración en minutos (mínimo 1). Example: 60
     * @bodyParam information string required Descripción de la actividad. Example: "Clase de matemáticas"
     * @bodyParam is_available boolean Estado de disponibilidad de la actividad. Example: true
     * 
     * @response 201 {
     *     "message": "Actividad creada exitosamente",
     *     "data": {
     *         "id": 1,
     *         "weekday": 2,
     *         "start_time": "09:30",
     *         "duration": 60,
     *         "information": "Clase de matemáticas",
     *         "is_available": true,
     *         "user_id": 1,
     *         "created_at": "2024-03-06T12:00:00.000000Z",
     *         "updated_at": "2024-03-06T12:00:00.000000Z"
     *     }
     * }
     * 
     * @response 422 {
     *     "message": "The given data was invalid.",
     *     "errors": {
     *         "weekday": ["El día de la semana debe estar entre 1 y 7"],
     *         "start_time": ["El formato de hora debe ser HH:mm"],
     *         "duration": ["La duración debe ser al menos 1 minuto"],
     *         "information": ["La descripción es requerida"]
     *     }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'weekday' => ['required', 'integer', 'min:1', 'max:7'],
            'start_time' => ['required', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'duration' => ['required', 'integer', 'min:1'],
            'information' => ['required', 'string'],
            'is_available' => ['boolean'],
        ]);

        $activity = Auth::user()->activities()->create($validated);

        return response()->json([
            'message' => 'Actividad creada exitosamente',
            'data' => $activity
        ], 201);
    }

    /**
     * Obtener Actividad
     * 
     * Muestra los detalles de una actividad específica.
     * 
     * @authenticated
     * 
     * @urlParam activity integer required ID de la actividad. Example: 1
     * 
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "weekday": 2,
     *         "start_time": "09:30",
     *         "duration": 60,
     *         "information": "Clase de matemáticas",
     *         "is_available": true,
     *         "user_id": 1,
     *         "created_at": "2024-03-06T12:00:00.000000Z",
     *         "updated_at": "2024-03-06T12:00:00.000000Z"
     *     }
     * }
     * 
     * @response 403 {
     *     "message": "No autorizado"
     * }
     * 
     * @response 404 {
     *     "message": "No query results for model [App\\Models\\Activity]."
     * }
     */
    public function show(Activity $activity): JsonResponse
    {
        if ($activity->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json(['data' => $activity]);
    }

    /**
     * Actualizar Actividad
     * 
     * Actualiza los datos de una actividad existente.
     * 
     * @authenticated
     * 
     * @urlParam activity integer required ID de la actividad. Example: 1
     * @bodyParam weekday integer Día de la semana (1-7). Example: 6
     * @bodyParam start_time string Hora de inicio (formato HH:mm). Example: "11:00"
     * @bodyParam duration integer Duración en minutos (mínimo 1). Example: 51
     * @bodyParam information string Descripción de la actividad. Example: "Clase de programación"
     * @bodyParam is_available boolean Estado de disponibilidad. Example: false
     * 
     * @response 200 {
     *     "message": "Actividad actualizada exitosamente",
     *     "data": {
     *         "id": 1,
     *         "weekday": 6,
     *         "start_time": "11:00",
     *         "duration": 51,
     *         "information": "Clase de programación",
     *         "is_available": false,
     *         "user_id": 1,
     *         "created_at": "2024-03-06T12:00:00.000000Z",
     *         "updated_at": "2024-03-06T12:00:00.000000Z"
     *     }
     * }
     * 
     * @response 403 {
     *     "message": "No autorizado"
     * }
     * 
     * @response 422 {
     *     "message": "The given data was invalid.",
     *     "errors": {
     *         "weekday": ["El día de la semana debe estar entre 1 y 7"],
     *         "start_time": ["El formato de hora debe ser HH:mm"],
     *         "duration": ["La duración debe ser al menos 1 minuto"]
     *     }
     * }
     */
    public function update(Request $request, Activity $activity): JsonResponse
    {
        if ($activity->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'weekday' => ['sometimes', 'integer', 'min:1', 'max:7'],
            'start_time' => ['sometimes', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'duration' => ['sometimes', 'integer', 'min:1'],
            'information' => ['sometimes', 'string'],
            'is_available' => ['sometimes', 'boolean'],
        ]);

        $activity->update($validated);

        return response()->json([
            'message' => 'Actividad actualizada exitosamente',
            'data' => $activity
        ]);
    }

    /**
     * Eliminar Actividad
     * 
     * Elimina una actividad existente.
     * 
     * @authenticated
     * 
     * @urlParam activity integer required ID de la actividad. Example: 1
     * 
     * @response 200 {
     *     "message": "Actividad eliminada exitosamente"
     * }
     * 
     * @response 403 {
     *     "message": "No autorizado"
     * }
     * 
     * @response 404 {
     *     "message": "No query results for model [App\\Models\\Activity]."
     * }
     */
    public function destroy(Activity $activity): JsonResponse
    {
        if ($activity->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $activity->delete();

        return response()->json([
            'message' => 'Actividad eliminada exitosamente'
        ]);
    }
} 
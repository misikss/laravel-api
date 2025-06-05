<?php

namespace App\Http\Controllers\Activities;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @group Gestión de Actividades
 *
 * APIs para gestionar las actividades de los horarios.
 * Cada actividad pertenece a un horario específico y solo puede ser gestionada por su propietario.
 */
class IndexActivityController extends Controller
{
    /**
     * Requiere autenticación para todas las operaciones del controlador
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Listar Actividades
     * 
     * Obtiene todas las actividades del usuario autenticado.
     * Los resultados están paginados y pueden ser filtrados por varios criterios.
     * 
     * @authenticated
     * 
     * @queryParam page integer Número de página para la paginación. Mínimo: 1. Example: 1
     * @queryParam per_page integer Elementos por página. Mínimo: 1, Máximo: 50. Example: 10
     * @queryParam timetable_id integer Filtrar actividades por horario específico. Example: 1
     * @queryParam weekday integer Filtrar por día de la semana (1=Lunes, 7=Domingo). Example: 1
     * @queryParam is_available boolean Filtrar por disponibilidad (true=disponible, false=no disponible). Example: true
     * 
     * @response 200 {
     *     "success": true,
     *     "message": "Lista de actividades",
     *     "data": {
     *         "data": [
     *             {
     *                 "id": 1,
     *                 "weekday": 2,
     *                 "start_time": "09:30",
     *                 "duration": 60,
     *                 "information": "Clase de matemáticas",
     *                 "is_available": true,
     *                 "user_id": 1,
     *                 "timetable_id": 1,
     *                 "created_at": "2024-03-06T12:00:00.000000Z",
     *                 "updated_at": "2024-03-06T12:00:00.000000Z"
     *             }
     *         ],
     *         "current_page": 1,
     *         "first_page_url": "http://localhost:8000/api/activities?page=1",
     *         "from": 1,
     *         "last_page": 1,
     *         "last_page_url": "http://localhost:8000/api/activities?page=1",
     *         "links": [
     *             {
     *                 "url": null,
     *                 "label": "&laquo; Anterior",
     *                 "active": false
     *             },
     *             {
     *                 "url": "http://localhost:8000/api/activities?page=1",
     *                 "label": "1",
     *                 "active": true
     *             },
     *             {
     *                 "url": null,
     *                 "label": "Siguiente &raquo;",
     *                 "active": false
     *             }
     *         ],
     *         "next_page_url": null,
     *         "path": "http://localhost:8000/api/activities",
     *         "per_page": 10,
     *         "prev_page_url": null,
     *         "to": 1,
     *         "total": 1
     *     }
     * }
     * 
     * @response 401 {
     *     "message": "Unauthenticated."
     * }
     * 
     * @response 400 {
     *     "success": false,
     *     "message": "Error en los parámetros de consulta",
     *     "errors": {
     *         "weekday": ["El día de la semana debe estar entre 1 y 7"],
     *         "per_page": ["El número de elementos por página no puede ser mayor a 50"]
     *     }
     * }
     */
    public function __invoke(Request $request)
    {
        $query = Activity::where('user_id', $request->user()->id);

        // Filtrar por horario específico
        if ($request->has('timetable_id')) {
            $query->where('timetable_id', $request->timetable_id);
        }

        // Filtrar por día de la semana
        if ($request->has('weekday')) {
            $query->where('weekday', $request->weekday);
        }

        // Filtrar por disponibilidad
        if ($request->has('is_available')) {
            $query->where('is_available', $request->is_available);
        }

        $activities = $query->paginate($request->input('per_page', 10));
        return $this->sendSuccess('Lista de actividades', $activities);
    }
} 
<?php

namespace App\Http\Controllers\Timetables;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Traits\ApiFeedbackSender;
use Illuminate\Http\Request;

/**
 * @group Gestión de Horarios
 *
 * APIs para gestionar los horarios del usuario
 */
class IndexTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Listar Horarios
     * 
     * Obtiene todos los horarios del usuario autenticado.
     * 
     * @authenticated
     * 
     * @queryParam page integer Número de página para la paginación. Example: 1
     * @queryParam per_page integer Elementos por página (máximo 50). Example: 10
     * 
     * @response 200 {
     *     "success": true,
     *     "message": "Lista de horarios",
     *     "data": [
     *         {
     *             "id": 1,
     *             "name": "Horario de Clases",
     *             "description": "Horario del semestre actual",
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
    public function __invoke(Request $request)
    {
        $timetables = Timetable::where('user_id', $request->user()->id)->get();
        return $this->sendSuccess('Lista de horarios', $timetables);
    }
} 
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
class ShowTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Obtener Horario
     * 
     * Obtiene los detalles de un horario específico.
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID del horario. Example: 1
     * 
     * @response 200 {
     *     "success": true,
     *     "message": "Horario encontrado",
     *     "data": {
     *         "id": 1,
     *         "name": "Horario de Clases",
     *         "description": "Horario del semestre actual",
     *         "user_id": 1,
     *         "created_at": "2024-03-06T12:00:00.000000Z",
     *         "updated_at": "2024-03-06T12:00:00.000000Z"
     *     }
     * }
     * 
     * @response 404 {
     *     "success": false,
     *     "message": "Horario no encontrado",
     *     "data": {
     *         "No se encontró el recurso solicitado"
     *     }
     * }
     */
    public function __invoke(Request $request, $id)
    {
        $timetable = Timetable::where('user_id', $request->user()->id)
            ->find($id);

        if (!$timetable) {
            return $this->sendError('Horario no encontrado', ['No se encontró el recurso solicitado'], 404);
        }

        return $this->sendSuccess('Horario encontrado', $timetable);
    }
} 
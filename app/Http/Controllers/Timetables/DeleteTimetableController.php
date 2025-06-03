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
class DeleteTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Eliminar Horario
     * 
     * Elimina un horario existente y todas sus actividades asociadas.
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID del horario. Example: 1
     * 
     * @response 200 {
     *     "success": true,
     *     "message": "Horario eliminado exitosamente",
     *     "data": null
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

        $timetable->delete();
        return $this->sendSuccess('Horario eliminado exitosamente', null);
    }
} 
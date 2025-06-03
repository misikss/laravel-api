<?php

namespace App\Http\Controllers\Timetables;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Traits\ApiFeedbackSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Gestión de Horarios
 *
 * APIs para gestionar los horarios del usuario
 */
class UpdateTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Actualizar Horario
     * 
     * Actualiza la información de un horario existente.
     * 
     * @authenticated
     * 
     * @urlParam id integer required ID del horario. Example: 1
     * @bodyParam name string required Nombre del horario (máximo 50 caracteres). Example: Horario Actualizado
     * @bodyParam description string required Descripción del horario (máximo 300 caracteres). Example: Nueva descripción del horario
     * 
     * @response 200 {
     *     "success": true,
     *     "message": "Horario actualizado exitosamente",
     *     "data": {
     *         "id": 1,
     *         "name": "Horario Actualizado",
     *         "description": "Nueva descripción del horario",
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
     * 
     * @response 422 {
     *     "success": false,
     *     "message": "Error de validación",
     *     "data": {
     *         "name": [
     *             "El nombre es requerido",
     *             "El nombre no puede tener más de 50 caracteres"
     *         ],
     *         "description": [
     *             "La descripción es requerida",
     *             "La descripción no puede tener más de 300 caracteres"
     *         ]
     *     }
     * }
     */
    public function __invoke(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:300'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación', $validator->errors());
        }

        $timetable = Timetable::where('user_id', $request->user()->id)
            ->find($id);

        if (!$timetable) {
            return $this->sendError('Horario no encontrado', ['No se encontró el recurso solicitado'], 404);
        }

        $timetable->update($validator->validated());

        return $this->sendSuccess('Horario actualizado exitosamente', $timetable);
    }
} 
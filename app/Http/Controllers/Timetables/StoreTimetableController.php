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
class StoreTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Crear Horario
     * 
     * Crea un nuevo horario para el usuario autenticado.
     * 
     * @authenticated
     * 
     * @bodyParam name string required Nombre del horario (máximo 50 caracteres). Example: Horario de Clases
     * @bodyParam description string required Descripción del horario (máximo 300 caracteres). Example: Horario del semestre actual
     * 
     * @response 201 {
     *     "success": true,
     *     "message": "Horario creado exitosamente",
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
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:300'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación', $validator->errors(), 422);
        }

        $timetable = Timetable::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => $request->user()->id
        ]);

        return $this->sendSuccess('Horario creado exitosamente', $timetable, 201);
    }
} 
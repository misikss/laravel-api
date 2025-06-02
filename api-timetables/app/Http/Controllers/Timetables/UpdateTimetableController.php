<?php

namespace App\Http\Controllers\Timetables;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Traits\ApiFeedbackSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __invoke(Request $request, $id)
    {
        $timetable = Timetable::find($id);
        
        if (!$timetable) {
            return $this->sendError('Horario no encontrado', ['No se encontró el recurso solicitado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:300'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación', $validator->errors());
        }

        $timetable->update($validator->validated());
        return $this->sendSuccess($timetable, 'Horario actualizado exitosamente');
    }
} 
<?php

namespace App\Http\Controllers\Timetables;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Traits\ApiFeedbackSender;
use Illuminate\Http\Request;

class DeleteTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __invoke(Request $request, $id)
    {
        $timetable = Timetable::find($id);
        
        if (!$timetable) {
            return $this->sendError('Horario no encontrado', ['No se encontró el recurso solicitado'], 404);
        }

        $timetable->delete();
        return $this->sendSuccess(null, 'Horario eliminado exitosamente');
    }
} 
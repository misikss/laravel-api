<?php

namespace App\Http\Controllers\Timetables;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Traits\ApiFeedbackSender;
use Illuminate\Http\Request;

class ShowTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function __invoke(Request $request, $id)
    {
        $timetable = Timetable::where('user_id', $request->user()->id)
            ->with('activities')
            ->find($id);
        
        if (!$timetable) {
            return $this->sendError('Horario no encontrado', ['No se encontró el recurso solicitado'], 404);
        }

        return $this->sendSuccess('Horario encontrado', $timetable);
    }
} 
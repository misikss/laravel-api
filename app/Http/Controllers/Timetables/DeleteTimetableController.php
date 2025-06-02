<?php

namespace App\Http\Controllers\Timetables;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Traits\ApiFeedbackSender;

class DeleteTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __invoke(Timetable $timetable)
    {
        $timetable->delete();
        return $this->sendSuccess(null, 'Horario eliminado exitosamente');
    }
} 
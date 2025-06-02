<?php

namespace App\Http\Controllers\Timetables;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Traits\ApiFeedbackSender;

class IndexTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __invoke()
    {
        $timetables = Timetable::all();
        return $this->sendSuccess($timetables);
    }
} 
<?php

namespace App\Http\Controllers\Timetables;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Traits\ApiFeedbackSender;

class ShowTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __invoke(Timetable $timetable)
    {
        return $this->sendSuccess($timetable);
    }
} 
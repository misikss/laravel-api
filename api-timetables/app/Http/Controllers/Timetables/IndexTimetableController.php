<?php

namespace App\Http\Controllers\Timetables;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Traits\ApiFeedbackSender;
use Illuminate\Http\Request;

class IndexTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function __invoke(Request $request)
    {
        $timetables = Timetable::where('user_id', $request->user()->id)->get();
        return $this->sendSuccess('Lista de horarios', $timetables);
    }
} 
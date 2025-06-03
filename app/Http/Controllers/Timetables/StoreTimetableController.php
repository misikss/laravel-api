<?php

namespace App\Http\Controllers\Timetables;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Traits\ApiFeedbackSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreTimetableController extends Controller
{
    use ApiFeedbackSender;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

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
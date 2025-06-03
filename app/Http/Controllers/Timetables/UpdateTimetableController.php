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

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

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
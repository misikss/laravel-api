<?php

namespace App\Http\Controllers\Activities;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function __invoke(Request $request, Activity $activity)
    {
        if ($activity->user_id !== $request->user()->id) {
            return $this->sendError('No autorizado', ['No tiene permiso para actualizar esta actividad'], 403);
        }

        $validator = Validator::make($request->all(), [
            'weekday' => 'sometimes|integer|min:1|max:7',
            'start_time' => ['sometimes', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'duration' => 'sometimes|integer|min:1',
            'information' => 'sometimes|string',
            'is_available' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación', $validator->errors(), 422);
        }

        $activity->update($request->only([
            'weekday',
            'start_time',
            'duration',
            'information',
            'is_available'
        ]));

        return $this->sendSuccess('Actividad actualizada exitosamente', $activity);
    }
} 
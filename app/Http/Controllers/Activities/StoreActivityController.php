<?php

namespace App\Http\Controllers\Activities;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'weekday' => 'required|integer|min:1|max:7',
            'start_time' => ['required', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'duration' => 'required|integer|min:1',
            'information' => 'required|string',
            'is_available' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación', $validator->errors());
        }

        $activity = Activity::create([
            'weekday' => $request->weekday,
            'start_time' => $request->start_time,
            'duration' => $request->duration,
            'information' => $request->information,
            'is_available' => $request->input('is_available', true),
            'user_id' => $request->user()->id
        ]);

        return $this->sendSuccess('Actividad creada exitosamente', $activity, 201);
    }
} 
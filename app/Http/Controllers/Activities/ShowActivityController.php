<?php

namespace App\Http\Controllers\Activities;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ShowActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function __invoke(Request $request, Activity $activity)
    {
        if ($activity->user_id !== $request->user()->id) {
            return $this->sendError('No autorizado', ['No tiene permiso para ver esta actividad'], 403);
        }

        return $this->sendSuccess('Actividad encontrada', $activity);
    }
} 
<?php

namespace App\Http\Controllers\Activities;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class DeleteActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function __invoke(Request $request, Activity $activity)
    {
        if ($activity->user_id !== $request->user()->id) {
            return $this->sendError('No autorizado', ['No tiene permiso para eliminar esta actividad'], 403);
        }

        $activity->delete();

        return $this->sendSuccess('Actividad eliminada exitosamente', null);
    }
} 
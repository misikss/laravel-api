<?php

namespace App\Http\Controllers\Activities;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class IndexActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function __invoke(Request $request)
    {
        $activities = Activity::where('user_id', $request->user()->id)->get();
        return $this->sendSuccess('Lista de actividades', $activities);
    }
} 
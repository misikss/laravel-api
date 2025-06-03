<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $activities = Auth::user()->activities()->get();
        return response()->json(['data' => $activities]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'weekday' => ['required', 'integer', 'min:1', 'max:7'],
            'start_time' => ['required', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'duration' => ['required', 'integer', 'min:1'],
            'information' => ['required', 'string'],
            'is_available' => ['boolean'],
        ]);

        $activity = Auth::user()->activities()->create($validated);

        return response()->json([
            'message' => 'Actividad creada exitosamente',
            'data' => $activity
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity): JsonResponse
    {
        if ($activity->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return response()->json(['data' => $activity]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity): JsonResponse
    {
        if ($activity->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'weekday' => ['sometimes', 'integer', 'min:1', 'max:7'],
            'start_time' => ['sometimes', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'duration' => ['sometimes', 'integer', 'min:1'],
            'information' => ['sometimes', 'string'],
            'is_available' => ['sometimes', 'boolean'],
        ]);

        $activity->update($validated);

        return response()->json([
            'message' => 'Actividad actualizada exitosamente',
            'data' => $activity
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity): JsonResponse
    {
        if ($activity->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $activity->delete();

        return response()->json([
            'message' => 'Actividad eliminada exitosamente'
        ]);
    }
} 
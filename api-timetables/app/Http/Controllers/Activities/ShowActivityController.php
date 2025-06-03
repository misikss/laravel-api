<?php

namespace App\Http\Controllers\Activities;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

/**
 * Controlador para mostrar los detalles de una actividad específica
 * Maneja la autorización y presentación de una actividad individual
 */
class ShowActivityController extends Controller
{
    /**
     * Requiere autenticación para todas las operaciones del controlador
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Muestra los detalles de una actividad específica
     * Verifica que el usuario tenga permiso para ver la actividad
     * 
     * @param Request $request Solicitud HTTP
     * @param Activity $activity Actividad a mostrar (inyección de modelo)
     * @return JsonResponse Detalles de la actividad o error de autorización
     */
    public function __invoke(Request $request, Activity $activity)
    {
        if ($activity->user_id !== $request->user()->id) {
            return $this->sendError('No autorizado', ['No tiene permiso para ver esta actividad'], 403);
        }

        return $this->sendSuccess('Actividad encontrada', $activity);
    }
} 
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiFeedbackSender;
use Illuminate\Http\Request;

/**
 * @group Autenticación
 *
 * APIs para gestionar la autenticación de usuarios
 */
class LogoutController extends Controller
{
    use ApiFeedbackSender;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Cerrar Sesión
     * 
     * Cierra la sesión del usuario actual y revoca el token de acceso.
     * 
     * @authenticated
     * 
     * @response 200 {
     *     "success": true,
     *     "message": "Sesión cerrada exitosamente",
     *     "data": []
     * }
     * 
     * @response 401 {
     *     "message": "Unauthenticated."
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendSuccess('Sesión cerrada exitosamente', []);
    }
} 
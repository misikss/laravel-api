<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->sendSuccess('Sesión cerrada exitosamente');
        } catch (\Exception $e) {
            return $this->sendError('Error al cerrar sesión', [], 500);
        }
    }
} 
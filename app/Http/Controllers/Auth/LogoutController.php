<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiFeedbackSender;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    use ApiFeedbackSender;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendSuccess('Sesión cerrada exitosamente', []);
    }
} 
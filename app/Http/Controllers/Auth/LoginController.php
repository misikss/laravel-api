<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiFeedbackSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use ApiFeedbackSender;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación', $validator->errors(), 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->sendError('Credenciales inválidas', ['Las credenciales proporcionadas son incorrectas'], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendSuccess('Inicio de sesión exitoso', [
            'user' => $user,
            'token' => $token
        ]);
    }
} 
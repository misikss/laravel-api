<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->sendError('Credenciales inválidas', [], 401);
            }

            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendSuccess('Inicio de sesión exitoso', [
                'user' => $user,
                'token' => $token
            ]);
        } catch (ValidationException $e) {
            return $this->sendError('Error de validación', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->sendError('Error al iniciar sesión', [], 500);
        }
    }
} 
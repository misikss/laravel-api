<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * @group Autenticación
 *
 * APIs para gestionar la autenticación de usuarios
 */
class LoginController extends Controller
{
    /**
     * Iniciar Sesión
     * 
     * Inicia sesión con las credenciales proporcionadas y devuelve un token de acceso.
     *
     * @bodyParam email string required El correo electrónico del usuario. Example: usuario@ejemplo.com
     * @bodyParam password string required La contraseña del usuario. Example: contraseña123
     *
     * @response 200 {
     *     "success": true,
     *     "message": "Inicio de sesión exitoso",
     *     "data": {
     *         "user": {
     *             "id": 1,
     *             "name": "Usuario Ejemplo",
     *             "email": "usuario@ejemplo.com",
     *             "created_at": "2024-03-06T12:00:00.000000Z",
     *             "updated_at": "2024-03-06T12:00:00.000000Z"
     *         },
     *         "token": "1|laravel_sanctum_token_example"
     *     }
     * }
     * 
     * @response 401 {
     *     "success": false,
     *     "message": "Credenciales inválidas",
     *     "data": {
     *         "email": ["Las credenciales proporcionadas son incorrectas"]
     *     }
     * }
     * 
     * @response 422 {
     *     "success": false,
     *     "message": "Error de validación",
     *     "data": {
     *         "email": ["El campo email es obligatorio"],
     *         "password": ["El campo password es obligatorio"]
     *     }
     * }
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->sendError('Credenciales inválidas', ['email' => ['Las credenciales proporcionadas son incorrectas']], 401);
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
            return $this->sendError('Error al iniciar sesión', ['error' => [$e->getMessage()]], 500);
        }
    }
} 
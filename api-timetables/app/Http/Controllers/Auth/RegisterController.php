<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Autenticación
 *
 * APIs para gestionar la autenticación de usuarios
 */
class RegisterController extends Controller
{
    /**
     * Registrar Usuario
     * 
     * Crea una nueva cuenta de usuario y devuelve un token de acceso.
     *
     * @bodyParam name string required Nombre del usuario. Example: Juan Pérez
     * @bodyParam email string required Correo electrónico del usuario. Debe ser único. Example: juan@ejemplo.com
     * @bodyParam password string required Contraseña del usuario. Mínimo 8 caracteres. Example: contraseña123
     *
     * @response 201 {
     *     "success": true,
     *     "message": "Usuario registrado exitosamente",
     *     "data": {
     *         "user": {
     *             "id": 1,
     *             "name": "Juan Pérez",
     *             "email": "juan@ejemplo.com",
     *             "created_at": "2024-03-06T12:00:00.000000Z",
     *             "updated_at": "2024-03-06T12:00:00.000000Z"
     *         },
     *         "token": "1|laravel_sanctum_token_example"
     *     }
     * }
     * 
     * @response 422 {
     *     "success": false,
     *     "message": "Error de validación",
     *     "data": {
     *         "email": ["El correo electrónico ya está en uso"],
     *         "password": ["La contraseña debe tener al menos 8 caracteres"]
     *     }
     * }
     * 
     * @response 500 {
     *     "success": false,
     *     "message": "Error al registrar usuario",
     *     "data": []
     * }
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users|max:255',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->sendSuccess('Usuario registrado exitosamente', [
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (ValidationException $e) {
            return $this->sendError('Error de validación', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->sendError('Error al registrar usuario', [], 500);
        }
    }
} 
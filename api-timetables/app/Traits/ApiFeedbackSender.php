<?php

namespace App\Traits;

trait ApiFeedbackSender
{
    /**
     * Envía una respuesta de éxito
     *
     * @param string $message Mensaje de éxito
     * @param array $data Datos adicionales (opcional)
     * @param int $status Código de estado HTTP (opcional)
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendSuccess($data, string $message = 'Operación exitosa', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Envía una respuesta de error
     *
     * @param string $message Mensaje de error
     * @param array $errors Lista de errores
     * @param int $status Código de estado HTTP (opcional)
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendError(string $message, $errors = [], int $code = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
} 
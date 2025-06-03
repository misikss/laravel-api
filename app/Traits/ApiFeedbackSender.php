<?php

namespace App\Traits;

trait ApiFeedbackSender
{
    /**
     * Envía una respuesta de éxito
     *
     * @param string $message Mensaje de éxito
     * @param mixed $data Datos adicionales (opcional)
     * @param int $code Código de estado HTTP
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendSuccess(string $message, $data = [], int $code = 200)
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
     * @param int $code Código de estado HTTP
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendError(string $message, $errors = [], int $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
} 
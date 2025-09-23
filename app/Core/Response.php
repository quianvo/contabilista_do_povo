<?php

namespace App\Core;

class Response
{
    /**
     * Envia uma resposta JSON
     * 
     * @param mixed $data Dados para enviar como JSON
     * @param int $statusCode Código HTTP de status
     */
    public static function json($data, int $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * Retorna uma resposta de sucesso
     * 
     * @param mixed $data Dados da resposta
     * @param string $message Mensagem de sucesso
     * @param int $statusCode Código HTTP de status
     */
    public static function success($data = null, string $message = 'Success', int $statusCode = 200)
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Retorna uma resposta de erro
     * 
     * @param string $message Mensagem de erro
     * @param int $statusCode Código HTTP de status
     * @param mixed $errors Erros detalhados
     */
    public static function error(string $message = 'Error', int $statusCode = 400, $errors = null)
    {
        self::json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    /**
     * Redireciona para uma URL
     * 
     * @param string $url URL para redirecionar
     */
    public static function redirect(string $url)
    {
        header("Location: $url");
        exit;
    }
}
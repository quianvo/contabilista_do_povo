<?php

namespace App\Core;

class Request
{
    /**
     * Captura a instância da requisição atual
     */
    public static function capture(): self
    {
        return new self();
    }

    /**
     * Obtém dados JSON do corpo da requisição
     */
    public static function getJsonData(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        return is_array($data) ? $data : [];
    }

    /**
     * Obtém todos os inputs (GET + POST + JSON)
     */
    public function all(): array
    {
        return array_merge(
            $_GET,
            $_POST,
            $this->getJsonData()
        );
    }

    /**
     * Obtém um valor específico da requisição
     */
    public function input(string $key, $default = null)
    {
        return $this->all()[$key] ?? $default;
    }

        public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function getPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return $path === false ? '/' : $path;
    }

}
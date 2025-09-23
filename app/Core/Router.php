<?php

namespace App\Core;

class Router
{
    private $routes = [];

    public function add(string $method, string $path, array $handler)
    {
        // Converter padrão {param} para regex
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        $this->routes[$method][$pattern] = $handler;
    }

    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            if (preg_match("#^$route$#", $uri, $matches)) {
                // Extrair parâmetros
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Chamar o handler com parâmetros
                call_user_func_array($handler, $params);
                return;
            }
        }
        // Se rota não encontrada
        http_response_code(404);
        echo json_encode([
            "message" => "Not Found",
            "method" => $method,
            "uri" => $uri,
            "available_routes" => array_map(function ($r) {
                return [
                    'method' => $r['method'],
                    'path' => $r['path']
                ];
            }, $this->routes)
        ]);
    }
}

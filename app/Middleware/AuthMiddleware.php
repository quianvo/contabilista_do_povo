<?php

namespace App\Middleware;

use App\Core\Response;
use App\Core\Auth;
use App\Core\Request;

class AuthMiddleware extends \App\Core\Middleware
{
    // Rotas públicas que não requerem autenticação
    protected array $publicRoutes = [
        'POST:/auth/register',
        'POST:/auth/login',
        'GET:/posts',
        'GET:/posts/{id}',
        'GET:/posts/category/{category}',
        'GET:/categories',
        'GET:/categories/{id}',
        'POST:/contacts',
    ];

    public function handle($request, $next)
    {
        // Verifica se a rota atual está nas rotas públicas
        if ($this->isPublicRoute($request)) {
            return $this->next($request, $next);
        }

        // Verifica autenticação para rotas protegidas
        if (!Auth::check()) {
            Response::error('Unauthorized', 401);
        }

        return $this->next($request, $next);
    }

    /**
     * Verifica se a rota atual está na lista de rotas públicas
     */
    protected function isPublicRoute(Request $request): bool
    {
        $method = $request->getMethod();
        $uri    = $request->getPath();

        foreach ($this->publicRoutes as $route) {
            [$routeMethod, $routePath] = explode(':', $route);

            if ($routeMethod !== $method) {
                continue;
            }

            // converte {param} em regex
            $pattern = preg_replace('/\{(\w+)\}/', '[^/]+', $routePath);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri)) {
                return true;
            }
        }

        return false;
    }
}

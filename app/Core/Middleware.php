<?php

namespace App\Core;

abstract class Middleware
{
    /**
     * Método principal do middleware
     * 
     * @param mixed $request
     * @param callable $next
     * @return mixed
     */
    abstract public function handle($request, $next);

    /**
     * Método para executar o próximo middleware/controller
     * 
     * @param mixed $request
     * @param callable $next
     * @return mixed
     */
    protected function next($request, $next)
    {
        return $next($request);
    }
}
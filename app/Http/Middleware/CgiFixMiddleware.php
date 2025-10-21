<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CgiFixMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Fix para CGI - headers de autenticación
        if (php_sapi_name() === 'cgi-fcgi') {
            // Pasar Authorization header
            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $request->headers->set('Authorization', $_SERVER['HTTP_AUTHORIZATION']);
            }
            
            // Asegurar Content-Type
            if (isset($_SERVER['HTTP_CONTENT_TYPE'])) {
                $request->headers->set('Content-Type', $_SERVER['HTTP_CONTENT_TYPE']);
            }
            
            // Asegurar Content-Length
            if (isset($_SERVER['HTTP_CONTENT_LENGTH'])) {
                $request->headers->set('Content-Length', $_SERVER['HTTP_CONTENT_LENGTH']);
            }
        }
        
        return $next($request);
    }
}
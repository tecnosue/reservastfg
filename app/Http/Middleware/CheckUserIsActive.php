<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       // Excluir rutas específicas de la verificación de usuario activo
        $excludedRoutes = ['pendiente', 'logout', 'login'];
        
        if (in_array($request->route()->getName(), $excludedRoutes)) {
            return $next($request);
        }
        
        // Si el usuario está autenticado pero NO activo
        if (Auth::check() && !Auth::user()->activo) {
            return redirect()->route('pendiente');
        }



        return $next($request);
    }
}

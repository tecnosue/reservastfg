<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //Verificamos si el usuario está logueado y si su rol es 'administrador'.
        if (!Auth::check() || Auth::user()->rol !== 'administrador') {
            // 2. Si no lo es, le denegamos el acceso con un error 403.
            abort(403, 'ACCESO NO AUTORIZADO.');
        }
        return $next($request);
    }
}

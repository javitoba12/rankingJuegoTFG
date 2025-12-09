<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChequeoTema
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $usuario = auth()->user();
        // $view=[];

         if (!$request->is('/inicio/principal/inventario') && !$request->is('/inicio/principal/bajas')  && !$request->is('/inicio/principal/detalle')  
           && !$request->is('/inicio/login') && !$request->is('/inicio/registro') && (!$request->is('/inicio') || !$request->is('/'))) {

        view()->share('tema', [
        'bgColor'   => $usuario?->tema === 'oscuro' ? 'bg-dark' : 'bg-light',
        'textColor' => $usuario?->tema === 'oscuro' ? 'text-white' : 'text-dark'
    ]);

}

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\User;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ChequeoRol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
        public function handle(Request $request, Closure $next, ?string $rol = null): Response
        {
           
            if ($rol !== null && Auth::check() && Auth::user()->rol !== $rol) {//Miro que el rol sea 
                //distinto a null, que el usuario este logueado, y ademas tambien compruebo que el rol
                //del usuario no sea distinto al que tengo como parametro.

                // Si el rol no coincide con el requerido, redirige a la página principal
                return redirect()->route('principal');
            }

            return $next($request);
        

        
    }
}


 // Si llegamos a la ruta 'dashboard', redirigimos según el rol
           /*     if ($request->routeIs('principal') && Auth::check()) {
                    if (Auth::user()->rol === 'usuario') {
                        return redirect()->route('principal');
                    } else {
                        return redirect()->route('principal');
                    }
        }

        */
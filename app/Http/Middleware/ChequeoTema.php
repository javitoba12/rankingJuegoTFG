<?php

/*namespace App\Http\Middleware;

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
   /* public function handle(Request $request, Closure $next): Response
    {
         $usuario = auth()->user();
        // $view=[];

        if($usuario){
         if (!$request->is('/inicio/principal/inventario') && !$request->is('/inicio/principal/bajas')  && !$request->is('/inicio/principal/detalle')  
           && !$request->is('/inicio/login') && !$request->is('/inicio/registro') && (!$request->is('/inicio') || !$request->is('/'))) {

        view()->share('tema', [
       // 'bgColor'   => $usuario?->tema == 'oscuro' ? 'bg-dark' : 'bg-primary',
       'bgColor'   => $usuario?->tema == 'oscuro' ? 'bg-dark' : 'container-claro',
        'textColor' => $usuario?->tema == 'oscuro' ? 'text-white' : 'text-dark',
        'navbarColor' => $usuario->tema == 'oscuro' ? 'navbar-dark' : 'navbar-light'
    ]);

}else{

    view()->share('tema', [
       // 'bgColor'   => $usuario?->tema == 'oscuro' ? 'bg-dark' : 'bg-primary',
       'bgColor'   => 'bg-dark',
        'textColor' => 'text-white', 
        'navbarColor' =>  'navbar-dark' 
    ]);

}

}

        return $next($request);
    }
}*/

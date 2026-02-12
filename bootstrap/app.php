<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) { 
        
        $middleware->trustProxies(at: '*'); //Con esto le indico a Laravel que confie en cualquier servidor intermediario (o proxy que envie trafico a Laravel)
        /*Sin esta configuración, Laravel pensaría que el "cliente" es el servidor de Railway, no el usuario real. Esto causa problemas comunes como

    Protocolo incorrecto: Laravel cree que la web es http en lugar de https, rompiendo los enlaces CSS o JS. (en este caso rompia los enlaces de las imagenes que se subian a
     la web) 
    
    Redirecciones infinitas: Intentos de forzar HTTPS que fallan porque Laravel no detecta que el Proxy ya cifró la conexión.
    
    Al poner at: '*', le indico a Laravel que confíe en todas las direcciones IP
    
    Confiar en * es seguro siempre y cuando la aplicación esté protegida dentro de una red privada como el caso de Railway*/
        $middleware->web(append:[//Aqui se llaman a los middlewares creados que quiero que laravel utilice al navegar por la web o cargar las paginas
            App\Http\Middleware\ChequeoRol::class,
           // App\Http\Middleware\ChequeoTema::class
        ]);

        $middleware->alias([
            'rol' => \App\Http\Middleware\ChequeoRol::class,
           // 'tema' => \App\Http\Middleware\ChequeoRol::class
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

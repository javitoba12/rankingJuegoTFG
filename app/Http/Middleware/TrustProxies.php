<?php

namespace App\Http\Middleware;

use Fideloper\Proxy\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Los proxies de confianza para la aplicación.
     *
     * @var array|string|null
     */
    protected $proxies = '*'; // Acepta cualquier proxy (útil para Railway, Heroku, etc.)

    /**
     * Los encabezados a usar para detectar proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}

?>
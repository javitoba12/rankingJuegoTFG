<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Counter;
use App\Livewire\Login;
use App\Livewire\Registro;
use App\Livewire\Principal;
//use App\Livewire\AdminComponent;
use App\Livewire\Admin\Administracion;
 



    Route::get('/', \App\Livewire\Inicio::class)->name('inicio');
    Route::get('/inicio', \App\Livewire\Inicio::class)->name('inicio');

//Route::get('/counter', Counter::class);

/*Route::get('/inicio', function(){

    return view('inicio');

})->name('inicio');*/



Route::get('/inicio/login', \App\Livewire\Login::class)->name('login');



Route::get('/inicio/registro', \App\Livewire\Registro::class)->name('registro');

Route::get('/inicio/principal', \App\Livewire\Principal::class)->name('principal');//Comentar la linea
//de arriba y descomentar esta si no funciona principal

Route::get('/inicio/principal/perfil', \App\Livewire\Perfil::class)->name('perfil');

Route::get('/inicio/principal/inventario', \App\Livewire\Inventario::class)->name('inventario');

Route::get('/inicio/principal/bajas', \App\Livewire\Bajas::class)->name('bajas');

Route::get('/inicio/principal/detalle', \App\Livewire\Detalle::class)->name('detalle');

Route::get('/inicio/principal/perfil/partidas', \App\Livewire\PartidasGuardadas::class)->name('partidasGuardadas');



//RUTAS PARA LOS ADMIN


Route::middleware(['auth', 'rol:admin'])->prefix('admin')->group(function () {
    //Este auth al que llamo con la funcion middleware, se encarga automaticamente
    //de comprobar antes de mirar el rol del usuario, que dicho usuario este logueado, si no lo esta,
    //lo lleva directamente a la ruta de login
    Route::get('/inicio/principal/perfil/Administrar', Administracion::class)
        ->name('Administracion.dashboard');
});





/*-Una ruta para el perfil --> hecho
  -Una ruta para incio --> hecho
  -Una ruta para el inventario --> hecho
  -Una ruta para enemigos vencidos-->hecho
  -Una ruta para detalle en el objeto seleccionado --> hecho
  -Una ruta para detalle en el enemigo seleccionado --> hecho
  -Una ruta para puntuaciones --> hecho
  -Una ruta posible ruta para partidas guardadas*/

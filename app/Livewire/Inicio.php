<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Inicio extends Component
{
    public $mensaje;

    public function mount(){

        if(session()->has('aviso')){
            $this->mensaje=session()->get('aviso');
            //session()->forget('aviso');
        }

        $this->cargarNoticias();

    }



    public function cargarNoticias(){

        //La api donde steam publica las noticias relacionadas con cada videojuego
        $respuestaApi=Http::get('https://api.steampowered.com/ISteamNews/GetNewsForApp/v2/',
        [
            'appid' => 582010, //El id relacionado con el juego (monster hunter)
            'count' => 5, //El numero de noticias que pido a la api que me devuelva
            'maxlength' => 300 // El maximo de caracteres por noticia, esto sirve para recortar el numero de caracteres
            //en el campo contents de noticias, el cual almacena el cuerpo entero de la noticia, por lo cual una noticia con un cuerpo de
            //1000 caracteres, quedaria recortado a 300, esto es algo ideal para maquetar las noticias como entradas en un pequeño div o section
            //de manera que queden bien estructuradas y sin descuadrar. (nota: que no se olvide ponerle overflow auto al div de las noticias).
        ]
    );


        $respuestaApiJson=$respuestaApi->json();

        Log::info($respuestaApi);
        
    }


    public function render()
    {
        return view('livewire.inicio');
    }
}

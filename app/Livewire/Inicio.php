<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Inicio extends Component
{
    public $mensaje;
    public $noticias;

    public function mount(){

        if(session()->has('aviso')){
            $this->mensaje=session()->get('aviso');
            //session()->forget('aviso');
        }

        $this->cargarNoticias();

    }



    public function cargarNoticias(){

        $url='https://api.steampowered.com/ISteamNews/GetNewsForApp/v2/';//La api donde steam publica las noticias relacionadas con cada videojuego

        
        $respuestaApi=Http::get($url,
        [//Esto son parametros que las consultas a la api de steam ofrece y se pueden configurar

            'appid' => 582010, //El id relacionado con el juego (monster hunter)
            'count' => 5, //El numero de noticias que pido a la api que me devuelva
            'maxlength' => 300 //(SOLO AFECTA AL ATRIBUTO CONTENTS) El maximo de caracteres por noticia, esto sirve para recortar el numero de caracteres
            //en el campo contents de noticias, el cual almacena el cuerpo entero de la noticia, por lo cual una noticia con un cuerpo de
            //1000 caracteres, quedaria recortado a 300, esto es algo ideal para maquetar las noticias como entradas en un pequeño div o section
            //de manera que queden bien estructuradas y sin descuadrar. (nota: que no se olvide ponerle overflow auto al div de las noticias).
        ]

        
    );

    if($respuestaApi->ok()){

    
        $this->noticias=collect($respuestaApi->json()['appnews']['newsitems'])->map( function($noticia) {//Uso a partir de la coleccion existente, la funcion map
        //para crear una nueva coleccion donde extraer y quedarme con los datos en los que estoy interesado y formatearlos para hacerlos mas legibles

            return [
                'titulo' => $noticia['title'],
                'descripcion' => strip_tags($noticia['contents']),//strip tags para eliminar elementos o etiquetas html dentro del texto
                'fecha' => Carbon::createFromTimestamp($noticia['date']),//Carbon para formatear la fecha
                'url' => $noticia['url']
                
            ];
        });

            Log::info($respuestaApi);
            
        }else{
            Log::warning('Error al extraer las noticias',[ 
                
                'status' => $respuestaApi->status(),
                'url' => $url,
                
            ]);

            $respuestaApi=collect();//En caso de que la API no responda, creo una coleccion vacia, para inicializar la variable publica y evitar errores

        }


        
        
    }


    public function render()
    {
        return view('livewire.inicio');
    }
}

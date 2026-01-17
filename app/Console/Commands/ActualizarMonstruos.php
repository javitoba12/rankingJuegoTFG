<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Enemigo;
use Illuminate\Support\Facades\Http;

class ActualizarMonstruos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:actualizar-monstruos';// El nombre con el que laravel identifica el comando

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los monstruos de la tabla enemigo automaticamente llamando a la API de MHW';//La descripcion del comando

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //Las ordenes que ejecuta el comando
        //Log::info('Hola, hoy es: '.now());
        $this->extraerMonstruosApi();
    }

    private function extraerMonstruosApi(){
        $url='https://mhw-db.com/monsters';
        $respuestaApi=Http::timeout(10)->retry(3, 200)->get($url);//Hago una peticion GET a la API
        
        //Http::get($url)

        if($respuestaApi->ok()){//Compruebo que la respuesta de la peticion a la API sea exitosa
            $respuestaApiJson=$respuestaApi->json();//Si la respuesta es exitosa, creo una coleccion de laravel con los 
            //enemigos convertidos de formato json a un formato manipulable para laravel ( de formato json en este caso, a un array de enemigos)

            

                foreach ($respuestaApiJson as $enemigoApi) {//Recorro todos los enemigos de la API
                    Enemigo::updateOrCreate(
                 ['enemigo_api_id' => $enemigoApi['id']],//La condicion con la que busco la fila o las filas en la BD
                [
                    'enemigo_api_id' => $enemigoApi['id'], 
                    'nombre_enemigo' => $enemigoApi['name'],
                    'tipo_monstruo' => $enemigoApi['type'],
                    'especie' => $enemigoApi['species'],
                ]);
                    
                }

                Log::info('Se han sincronizado los enemigos con exito.');

            

            
        }else{//Si ocurre algun fallo al contactar con la API

            Log::warning('Error al extraer los monstruos',[ 
                
                'status' => $respuestaApi->status(),
                'url' => $url,
                
            ]);

        }
    }
}
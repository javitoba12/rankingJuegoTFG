<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
//use App\Models\Enemigo;
use App\Models\EnemigoUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class EnemigoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $repertorioEnemigos=collect(Http::get('https://mhw-db.com/monsters')->json());
        

        $this->totalUsuarios=User::pluck('id')->toArray();
        $this->totalTiposEnemigos=$repertorioEnemigos->count();//El numero de enemigos que extraigo de la API

        $faker = \Faker\Factory::create();//Como ya no uso un factory, me veo obligado a importar manualmente
       //la libreria faker

        //Genero un id de usuario y un id de enemigo comprendido entre 1 y el total de filas de cada
        //tabla, teniendo en cuenta que los ids siempre empiezan en 1 y son autoincrementables
       /* $nuevoUsuario=array_rand($this->totalUsuarios);
        
        $nuevaMision=array_rand($this->totalMisiones);*/

        foreach ($this->totalUsuarios as $usuario) {
            $categoriasEnemigosVencidos=$faker->numberBetween(1,$this->totalTiposEnemigos);
            $enemigosVencidos=$repertorioEnemigos->shuffle()->take($categoriasEnemigosVencidos)->pluck('id')
            ->toArray();
            //Con la funcion inRandomOrder hago un shuffle(o barajado) de las filas registradas en la tabla 
            //enemigo_users, una vez he barajado el orden de las filas de las tablas, usando limit, me llevo
            //solamente un numero de misiones restringido, en funcion del random generado como numMisionesCompletadas
            //una vez he barajado el orden de las filas, y de entre esas filas me he llevado un numero 
            //reducido de ellas, uso pluck para extraer solamente el campo id de cada fila, y convierto la coleccion
            //de ids que me devuelve esta consulta en un array
            //Mission::random($numMisionesCompletadas)->pluck('id')->toArray();
            
            
            foreach ($enemigosVencidos as $enemigo) {
            
                if(!EnemigoUser::where('user_id',$usuario)
                ->where('enemigo_api_id',$enemigo)->exists()){
            //Miro que en la tabla enemigo_users no exista ya una fila que tenga la misma
            //combinacion de id de usuario e id de mision, que las que acabo de generar automaticamente
            //de no constar dicha combinacion aun en la tabla....

                    $bajas=$faker->numberBetween(1,300);
                    
                    EnemigoUser::aniadirBajas($usuario,$enemigo,$bajas);

        //
    
    }
                
            }
        }
    }
}

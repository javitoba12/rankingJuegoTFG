<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MissionUser;
use App\Models\User;
use App\Models\Mission;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class MissionUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        

        //Extraigo todos los ids de Users y Missions, los convierto ambos, de colecciones a arrays
        $this->totalUsuarios=User::pluck('id')->toArray();
        $this->totalMisiones=Mission::contarMisiones();
       // $this->totalMisiones=Mission::pluck('id')->toArray();

       $faker = \Faker\Factory::create();//Como ya no uso un factory, me veo obligado a importar manualmente
       //la libreria faker

        //Genero un id de usuario y un id de mision comprendido entre 1 y el total de filas de cada
        //tabla, teniendo en cuenta que los ids siempre empiezan en 1 y son autoincrementables
       /* $nuevoUsuario=array_rand($this->totalUsuarios);
        
        $nuevaMision=array_rand($this->totalMisiones);*/

        foreach ($this->totalUsuarios as $usuario) {
            $numMisionesCompletadas=$faker->numberBetween(1,$this->totalMisiones);
            $misionesCompletadas=Mission::inRandomOrder()->limit($numMisionesCompletadas)->pluck('id')->toArray();
            //Con la funcion inRandomOrder hago un shuffle(o barajado) de las filas registradas en la tabla 
            //mission_users, una vez he barajado el orden de las filas de las tablas, usando limit, me llevo
            //solamente un numero de misiones restringido, en funcion del random generado como numMisionesCompletadas
            //una vez he barajado el orden de las filas, y de entre esas filas me he llevado un numero 
            //reducido de ellas, uso pluck para extraer solamente el campo id de cada fila, y convierto la coleccion
            //de ids que me devuelve esta consulta en un array
            //Mission::random($numMisionesCompletadas)->pluck('id')->toArray();
            
            
            foreach ($misionesCompletadas as $mision) {
            
                if(!MissionUser::where('user_id',$usuario)
                ->where('mission_id',$mision)->exists()){
            //Miro que en la tabla mission_users no exista ya una fila que tenga la misma
            //combinacion de id de usuario e id de mision, que las que acabo de generar automaticamente
            //de no constar dicha combinacion aun en la tabla....

                    $nuevaPuntuacion=$faker->numberBetween(1000,7000);
                    $nuevaMarca=$faker->numberBetween(5,60);
                    MissionUser::aniadirPuntuacion($usuario,$mision,$nuevaPuntuacion,$nuevaMarca);

        //
       // MissionUser::factory()->count(4)->create();
    }
                
            }
        }

        
    }
}

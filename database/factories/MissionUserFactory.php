<?php

namespace Database\Factories;
use App\Models\User;
use App\Models\Mission;
use App\Models\MissionUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MissionUser>
 */

//MissionUser factory YA NO ESTA EN USO!, estoy usando el seeder de MissionUser, para poder controlar
//las filas que voy insertando y evitar duplicados de ids.

class MissionUserFactory extends Factory
{

  public $totalUsuarios,$totalMisiones;

  

    use HasFactory,Notifiable;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        //Para generar filas en la tabla mission_users dentro del rango de usuarios y misiones existentes..


        //Extraigo todos los ids de Users y Missions, los convierto ambos, de colecciones a arrays
        $this->totalUsuarios=User::pluck('id')->toArray();
        $this->totalMisiones=Mission::pluck('id')->toArray();

        

        //Genero un id de usuario y un id de mision comprendido entre 1 y el total de filas de cada
        //tabla, teniendo en cuenta que los ids siempre empiezan en 1 y son autoincrementables
        $nuevoUsuario=array_rand($this->totalUsuarios);
        
        $nuevaMision=array_rand($this->totalMisiones);

        if(!MissionUser::where('user_id',$this->totalUsuarios[$nuevoUsuario])
        ->where('mission_id',$this->totalMisiones[$nuevaMision])->exists()){
            //Miro que en la tabla mission_users no exista ya una fila que tenga la misma
            //combinacion de id de usuario e id de mision, que las que acabo de generar automaticamente
            //de no constar dicha combinacion aun en la tabla....

        return [//Paso a crear una nueva fila con en mission_users con toda la informacion necesaria
            //Por alguna razon, se duplican los usuarios al usar User y Mission Factory aqui
            'user_id' => $this->totalUsuarios[$nuevoUsuario],
            'mission_id' => $this->totalMisiones[$nuevaMision],
            'puntuacion' => $this->faker->numberBetween(1000,7000),
            'marca_tiempo' => $this->faker->numberBetween(5,60)//En minutos
        ];
        }

    }


    /*public function definition(): array
    {

        //Para generar filas en la tabla mission_users dentro del rango de usuarios y misiones existentes..


        //Cuento cuantos usuarios y misiones hay disponible en la BD
        $this->totalUsuarios=User::contarUsusarios();
        $this->totalMisiones=Mission::contarMisiones();

        

        //Genero un id de usuario y un id de mision comprendido entre 1 y el total de filas de cada
        //tabla, teniendo en cuenta que los ids siempre empiezan en 1 y son autoincrementables
        $nuevoUsuario=$this->faker->numberBetween(1,$this->totalUsuarios);
        
        $nuevaMision=$this->faker->numberBetween(1,$this->totalMisiones);

        if(!MissionUser::where('user_id',$nuevoUsuario)->where('mission_id',$nuevaMision)->exists()){
            //Miro que en la tabla mission_users no exista ya una fila que tenga la misma
            //combinacion de id de usuario e id de mision, que las que acabo de generar automaticamente
            //de no constar dicha combinacion aun en la tabla....

        return [//Paso a crear una nueva fila con en mission_users con toda la informacion necesaria
            //Por alguna razon, se duplican los usuarios al usar User y Mission Factory aqui
            'user_id' => $nuevoUsuario,
            'mission_id' => $nuevaMision,
            'puntuacion' => $this->faker->numberBetween(1000,7000),
            'marca_tiempo' => $this->faker->numberBetween(5,60)//En minutos
        ];
        }

    }*/
}

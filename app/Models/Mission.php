<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Mission extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [//Los campos que permito al usuario rellenar para la tabla usuarios en este caso
        'nombre',
        'tipo',
        'objetivo',
        'recompensa_puntos_base',
    ];


    public function users(){
        return $this->belongsToMany(User::class)->using(MissionUser::class);
        //Con esto extraigo todos las puntuaciones  y sus marcas de tiempo de los 
        // usuarios asociados a la mision actual, ademas de la fecha en la que se insertaron y la fecha en 
        // la que se actualizo la fila por ultima vez,todo esto lo extraigo de la tabla pivote mission_user
        //Por supuesto, al formar parte de la clave primaria, sabemos exactamente a que usuario pertenece
        //cada puntuacion asociada a una mision concreta

       // return $this->belongsToMany(User::class)->using(MissionUser::class);

       /*
       RELACION ANTIGUA
       return $this->belongsToMany(User::class,'mission_user')->withPivot('puntuacion','marca_tiempo')
        ->withTimestamps();*/
    }


    public static function contarMisiones(){
       return self::count();//Esto no devuelve una fila, devuelve un numero
    }
    
}

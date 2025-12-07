<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;//Necesario para algunas consultas a BD, en especial cuando se usa
//raw

class MissionUser extends Model
{

   /* public $incrementing = false; // No hay campo id autoincremental
    protected $primaryKey = null; // No hay una primary key simple
    public $timestamps = true; */   // Si que hay un timestamps

    use HasFactory,Notifiable;

    protected $fillable = [//Los campos que permito rellenar para la tabla en este caso
        'user_id',
        'mission_id',
        'puntuacion',
        'marca_tiempo',
    ];

//Debido a la relacion muchos a muchos entre la tabla users y missions
//Declaro que un id de usuario pertenece a un usuario y un id de mision pertenece a una mision


    public function user()
{
    return $this->belongsTo(User::class);
}

public function mission()
{
    return $this->belongsTo(Mission::class);
}


public static function getMisionesUser($userId)
    {
        return self::where('mission_users.user_id', $userId)//Selecciono todos los campos, donde la id del usuario
        //coincida con la id introducida por parametro

                    ->join('missions', 'mission_users.mission_id', '=', 'missions.id')
                    //Hago un Join y junto las tablas missions y mission_user a raiz del campo id de las 
                    // misiones, el cual existe en ambas tablas
                    

                    ->select('missions.nombre', 'mission_users.puntuacion', 'mission_users.marca_tiempo','missions.id')
                    //De esta manera extraigo el nombre de la mission de la tabla misssions, y la puntuacion
                    // y marca de tiempo del usuario en cada mision asociada al id de dicho usuario
                    ->get();
                    //La funcion get devuelve una coleccion de filas que coinciden con los requisitos que
                    //pido en la consulta, en este caso obtendre una coleccion filas que contendran
                    //el nombre de mision, puntuacion asociada a esa mision completada por el usuario,
                    //y la marca de tiempo del usuario en esa mision

    //$misiones = MissionUser::getMisionesUser(1); Si yo llamara a este metodo en otro modelo o controlador
    //obtendria todas las puntuaciones y marcas de tiempo de las misiones completadas por el usuario con
    //id 1
    }


public static function getDiezMejores(){

    return self::select('users.nick',DB::raw('SUM(mission_users.puntuacion) as puntuacion_total'))
    //laravel tiene una funcion sum(), pero esta solo puede devolver un resultado(en este caso un solo numero que representa
    // la suma de las puntuaciones en lugar de devolver una fila), no devuelve una coleccion de filas, 
    // y por ello en este caso uso DB::raw para SUM, porque raw si devuelve una coleccion con las 
    // puntuaciones totales de cada usuario
    ->join('users' ,'mission_users.user_id','=','users.id')
    ->groupBy('users.nick')
    ->orderByDesc('puntuacion_total')
    ->limit(10)
    ->get();


}


public static function getMaxPuntuacionUser($userId){
    return self::where('user_id',$userId)
    ->join('missions', 'mission_users.mission_id', '=', 'missions.id')//En laravel es recomendable
    //declarar los joins antes de select cuando se necesita usar join
    ->select('missions.nombre','missions.tipo','mission_users.puntuacion','mission_users.marca_tiempo')
    ->orderByDesc('mission_users.puntuacion')
    ->first();//En este caso no uso get() porque en esta consulta se y quiero que se me devuelva 
    //solamente una fila con la informacion de la mision con mayor puntuacion del usuario actual.
    //Por ello utilizo first, que en orden desc devolvera la primera fila que coincida con los requisitos 
    // que pido en la consulta
}

public static function actualizarPuntuacion($userId,$missionId,$puntuacion,$marca_tiempo){
    //Con esta consulta, se actualiza una sola fila concreto, donde coincida el id del usuario y de la 
    //puntuacion, con los id que se han pasado como parametros
    self::where('user_id',$userId)
    ->where('mission_id',$missionId)->update([//Para los campos que se van a actualizar, en laravel
        //a update se le pasa un array asociativo de clave y valor, donde la clave es el nombre 
        //del campo que voy a actualizar en una fila, y valor es el nuevo valor que recibira esa fila

        'puntuacion' => $puntuacion,
        'marca_tiempo' => $marca_tiempo

    ]);
}

public static function eliminarPuntuacionesUsuario($userId):bool{//Elimina todas las filas de la tabla
        //mission_users donde se encuentre el id del usuario

    $exito=false;

    $filasBorradas=self::where('user_id',$userId)->delete();//Ejecuto la consulta

    if($filasBorradas > 0){//Si al menos se ha borrado una fila
        $exito=true;//exito pasa a true, pues se ha conseguido ejecutar la consulta correctamente
    }

    return $exito;;//Devuelvo exito
}

public static function aniadirPuntuacion($userId,$missionId,$puntuacion,$marca_tiempo):bool{

    $isExiste=self::where('user_id',$userId)
    ->where('mission_id',$missionId)->exists();//Miro que no exista ya una fila con el mismo id de usuario
    //y de puntuacion

    if(!$isExiste){//Si no existe, creo la nueva fila

    self::create([
        'user_id' => $userId,
        'mission_id' => $missionId,
        'puntuacion' => $puntuacion,
        'marca_tiempo' => $marca_tiempo
    ]);
    }

    return $isExiste;
}



   

}




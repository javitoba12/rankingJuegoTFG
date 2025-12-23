<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;//Necesario para algunas consultas a BD, en especial cuando se usa
//raw

class EnemigoUser extends Pivot
{

    protected $table = 'enemigo_users';

    protected $fillable = [
        'user_id',
        'enemigo_id',
        'numero_bajas',


    ];


    //Las tablas a la que la relacion pertence (Usuario/Enemigo)

    //Debido a la relacion muchos a muchos entre la tabla users y enemigos
//Declaro que un id de usuario pertenece a un usuario y un id de enemigo pertenece a una enemigo

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enemigo()
    {
        return $this->belongsTo(Enemigo::class);
    }

    public static function getBajasUsuario($id_user){//Devuelve una coleccion con el nombre del usuario
        //y todas sus filas en la tabla enemigo_users

       return self::where('user_id',$id_user)
        ->join('enemigos' ,'enemigo_users.enemigo_id', '=','enemigos.id')
        ->select('enemigos.id','enemigos.nombre_enemigo','enemigos.tipo_daño','enemigo_users.numero_bajas')
        ->get();
    }

    public static function getDiezUsuariosConMasBajas(){//Devuelve una coleccion con el nombre de cada 
    // usuario y la suma total de sus bajas 
        return DB::table('enemigo_users')->join('users' ,'enemigo_users.user_id', '=','users.id')
        -> select('users.nick',DB::raw('SUM(enemigo_users.numero_bajas) as bajas_totales'))
        ->groupBy('users.nick')
        ->orderByDesc('bajas_totales')
        ->limit(10)
        ->get();
    }

    public static function aniadirBajas($id_user,$id_enemigo,$bajas):bool{
        //Para la insercion de filas en la tabla enemigo_users

        $isExiste=self::where('user_id',$id_user)
    ->where('enemigo_api_id',$id_enemigo)->exists();//Compruebo antes si ya existe una fila con el id del 
    //usuario y del enemigo

    if(!$isExiste){//Si no existe la fila...

        self::create([//La inserto
            'user_id' => $id_user,
            'enemigo_api_id' =>$id_enemigo,
            'numero_bajas' => $bajas,
        ]);
    
        }

        return $isExiste;
    }

    public static function eliminarBajasUsuario($id_user):bool{//Elimina todas las filas de la tabla
        //enemigo_users donde se encuentre el id del usuario
        $exito=false;

        $filasAfectadas=self::where('user_id',$id_user)->delete();//Ejecuto la consulta

        if($filasAfectadas > 0){//Si al menos se ha borrado una fila
            $exito=true;//exito pasa a true, pues se ha conseguido ejecutar la consulta correctamente
        }

        return $exito;//Devuelvo exito
    }
}

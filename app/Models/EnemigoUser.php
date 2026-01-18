<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Enemigo;
use Illuminate\Support\Facades\DB;//Necesario para algunas consultas a BD, en especial cuando se usa
//raw

class EnemigoUser extends Pivot
{

    protected $table = 'enemigo_users';

    protected $fillable = [
        'user_id',
        'enemigo_api_id',
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
        //return $this->belongsTo(Enemigo::class);

        return $this->belongsTo(
        Enemigo::class,
        'enemigo_api_id',      // FK en enemigo_users
        'enemigo_api_id'       // clave única en enemigos(pero no clave primaria)
    );
    }

    public static function getEnemigosUsuario($id_user){

        return self::where('user_id',$id_user)->select('user_id','enemigo_api_id','numero_bajas')->get();

    }

    public static function calcularTotalBajasUsuario($idUsuario){
        return self::where('user_id', $idUsuario)
        ->sum('numero_bajas');
    }


    public static function getBajasUsuario($id_user){//Devuelve una coleccion con el nombre del usuario
        //y todas sus filas en la tabla enemigo_users

        return self::with('enemigo')//llamo a la relacion entre enemigos y usuarios , o lo que es igual, voy a extraer las filas de la tabla enemigo_users
        ->where('user_id', $id_user)//Solo quiero aquellas filas que contengan el id del usuario actual
        ->get()//extraigo la informacion de la bd
        ->filter(fn ($baja) => $baja->enemigo !== null)//Filtro todas aquellas bajas que coincidan entre ambas tablas(enemigos, enemigo_users) con el campo enemigo_api_id,
        //dicho de otra manera, de todas las bajas relacionadas con el usuario, solo quiero quedarme con aquellos bajas cuyos enemigos contengan informacion en la tabla
        //enemigos
        ->map(function ($baja) {//mapeo la informacion de cada enemigo y sus bajas, para que sea mas comoda trabajar con ella y evitar problemas o conflictos con livewire
        //ya que a veces livewire guarda estados de relaciones y consultas de laravel con la BD, y esto puede dar a casos donde la informacion guardada en los estados de
        //livewire se quede anticuada en comparacion con las nuevas relaciones , consultas, nuevos campos en tablas etc, que yo haya podido establecer o configurar 
        // posteriormente (nota: map el metodo map a secas devuele una coleccion en laravel, si quiero crear un array como en js debo usar la funcion all() tras map())
            return [
                'enemigoId'      => $baja->enemigo_api_id,
                'numero_bajas'   => $baja->numero_bajas,
                'nombre_enemigo' => $baja->enemigo->nombre_enemigo,
                'tipo_monstruo'  => $baja->enemigo->tipo_monstruo,
                'especie'        => $baja->enemigo->especie,
            ];
        })
        ->values();//Esto reindexa la coleccion de nuevo, por lo cual los indices de cada elemento vuelven a empezar en 0,1,2,3 ...
        //Uso value para reindexar las claves de nuevo, ya que tras usar filter la coleccion se queda desordenada al haberme quedado solamente con x elementos de la coleccion
        //original.

      

    }

   /*
   
   
   
   return self::with('enemigo')
        ->where('user_id', $id_user)
        ->get()
        ->filter(fn ($baja) => $baja->enemigo !== null)
        ->map(function ($baja) {
            return [
                'enemigoId'      => $baja->enemigo_api_id,
                'numero_bajas'   => $baja->numero_bajas,
                'nombre_enemigo' => $baja->enemigo->nombre_enemigo,
                'tipo_monstruo'  => $baja->enemigo->tipo_monstruo,
                'especie'        => $baja->enemigo->especie,
            ];
        })
        ->values(); */

    /*return self::where('user_id',$id_user)
        ->join('enemigos' ,'enemigo_users.enemigo_api_id', '=','enemigos.enemigo_api_id')
        ->select('enemigo_users.enemigo_api_id','enemigos.nombre_enemigo','enemigos.tipo_monstruo','enemigos.especie','enemigo_users.numero_bajas')
        ->get(); */

    /*public static function getBajasUsuario($id_user){//Devuelve una coleccion con el nombre del usuario
        //y todas sus filas en la tabla enemigo_users

       return self::where('user_id',$id_user)
        ->join('enemigos' ,'enemigo_users.enemigo_api_id', '=','enemigos.id')
        ->select('enemigos.id','enemigos.nombre_enemigo','enemigos.tipo_daño','enemigo_users.numero_bajas')
        ->get();
    }*/

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

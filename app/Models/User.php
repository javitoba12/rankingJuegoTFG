<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [//Los campos que permito al usuario rellenar para la tabla usuarios en este caso
        'nick',
        'email',
        'password',
        'fecha_alta',
        'tiempo_juego',
        'rol',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [//Para ocultar ciertos campos y evitar exponer datos sensibles, por ejemplo
        //cuando se hacen peticiones json a tu base de datos, como podria pasar si una API te pide
        //alguna informacion.
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {//Convierte los atributos o campos de la tabla, a ciertos tipos como fechas,arrays,boolean etc
        return [
            'email_verified_at' => 'datetime',//Al hashear a datetime, puedo formatear las fechas mas
            //comodamente

            'password' => 'hashed',//Esto hace que laravel hashea las claves automaticamente por mi,
            //aunque yo de por si use hash:make que seria la manera manual de hacer el proceso de hasheo,
            //igualmente aunque ya lo haga yo de manera manual, nunca viene mal tener definido este hashed
            //en casts como medida de seguridad adicional
        ];
    }

    public function missions(){
        return $this->belongsToMany(Mission::class)->using(MissionUser::class);//->withPivot('puntuacion','marca_tiempo')
        //->withTimestamps();//Con esta funcion puedo extraer todas las puntuaciones y marcas de tiempo
        //  asociadas al usuario actual junto con la fecha en la que se insertaron y la fecha en la que 
        // se actualizo la fila por ultima vez,todo esto lo extraigo de la tabla pivote mission_user
        //Por supuesto, al formar parte de la clave primaria, sabemos exactamente a que mision pertenece
        //cada puntuacion del usuario

        //RELACION ANTIGUA
        // return $this->belongsToMany(Mission::class,'mission_user')->withPivot('puntuacion','marca_tiempo')
        //->withTimestamps();
    }

    public function items(){
        return $this->belongsToMany(Item::class)->withPivot('cantidad')
        //Aqui especifico a la tabla que no solo me traiga los campos que componen el id de cada fila
        //si no que ademas tambien rescate el campo de cantidad en cada fila

        ->withTimestamps();//Con esto tambien consigo rescatar los campos con fecha de created_ad y
        //updated_at para mas informacion adicional, ademas de conseguir que laravel los gestione automa
        //ticamente en cada fila, cuando se produzca un insert o un update(en este caso se modificaria
        //el campo update_at)
    }

    public function enemigos(){
        return $this->belongsToMany(Enemigo::class,'enemigo_users')
        ->withPivot('numero_bajas')
        ->using(EnemigoUser::class);
    }

    public function savedGames(){
    return $this->hasMany(SavedGame::class);
}

public static function actualizarUsuario($id,$nick,$clave){
   
    $exito=false;

    if(!empty($id) && !empty($nick) && !empty($clave)){
        
       $filasAfectadas = self::where('id',$id)->update(['nick' => $nick,'password' => Hash::make($clave)]);
        //El metodo update de laravel necesita que se le pase un array asociativo para aplicar los cambios de 
        // una fila en la BD, de otra manera este metodo no funcionara.

        //Busco al usuario por su id, y actualizo sus campos nick y clave.

        //Recordatorio: usar siempre Hash::make para encriptar las claves a la hora de insertar o
        //actualizar un usuario.

        //He guardado en una variable filasAfectadas mi consulta update, porque la consulta update
        //devuelve un numero (un count) con las filas afectadas por la configuracion.

        if($filasAfectadas > 0){
            $exito=true;
        }
    }

    return $exito;
}


public static function cambiarAvatar($id,$avatar){
    
    $exito=false;
    
    $filasAfectadas=self::where('id',$id)->update(['avatar'=> $avatar]);

    if($filasAfectadas > 0){
        $exito=true;
    }

    return $exito;
}

public static function isNickRepetido($id,$nick):bool{
  return self::where('nick',$nick)//Busca en aquellas filas con un id diferente al que he pasado como
  //parametro, si existe un nick coincidente con el que tambien te he pasado como parametro

  ->where('id','!=',$id)//Este segundo where funciona como un AND en sql, asi que la consulta seria algo
  //asi: SELECT * FOR users WHERE nick = $nick AND id != $id
  
  ->exists();//Aunque confunde un poco, este exists debe ir al final porque es el momento en el que laravel
  //ejecuta la consulta, que comprueba si existe un nick en igual que el que he pasado por parametro en los
  //campos con una id diferente a la actual
}

public static function borrarUsuario($id):bool{

    $exito=false;

   $filasBorradas= self::where('id',$id)->delete();
   //Borro el usuario que coincida con la id pasada por parametro

   if($filasBorradas > 0){
        $exito=true;
   }

   return $exito;
}

/*public static function devolverItemAdquirido($idUser,$idItem){
    return self::find($idUser)->items()->where('id',$idItem);
}*/

public static function extraerClientes(){
    $resultado = self::where('rol','usuario')->orderBy('id')->paginate(10);
    //Esta consulta almacena los usuarios con rol usuario, ordenados por la id
    //de 10 en 10, para que sea mas comodo y optimo para la web y el usuario,
    //pero no devuelve una coleccion con el resultado de la consulta, no exactamente o mas bien no es 
    // lo unico que devuelve, si no que devuelve un objeto con varios atributos.


    //Esta consulta generara un objeto LengthAwarePaginator, el cual se considera un objeto complejo,
    //porque tiene tanto atributos como funciones que gestionan el tema de la paginacion entre resultados
    /*

    Atributos del objeto:

    $items: La colección de elementos actuales (en este caso, los 10 usuarios de la página actual
    en la que me encuentro).

    $total: El número total de usuarios o filas en la tabla que coinciden con la consulta(el total de verdad
    ,no estoy contando el total por pagina).

    $perPage: El número de elementos por página (en este caso, 10 usuarios por pagina).

    $currentPage: El número de la página actual.

    $lastPage: El número total de páginas.(en realidad el ultimo numero de la pagina, pero sabiendo
    //la ultima pagina, se tambien el total de paginas)

    $nextPageUrl(): El enlace para la siguiente página de resultados con usuarios.

    $previousPageUrl(): El enlace para la página anterior de resultados con usuarios, si estas en un
    enlace posterior al primero.

    $links(): La funcion para generar los enlaces HTML para la paginación.Tambien botones,span,texto que 
    indica la pagina actual y el total de resultados etc, para que pueda funcionar en la web.
    
    Este objeto también incluye métodos para obtener información sobre el estado de la 
    paginación (de en que pagina me encuentro), como el número de la página actual y los enlaces a las 
    páginas de anterior y siguiente
    
    */

    return $resultado;
}

public static function buscarUsuario($nick){
   return self::where('nick',$nick)->first();
   //Busco un usuario con un nick que coincida con el nick pasado por parametro,
   //recojo solamente el primer resultado usando first en lugar de get, para evitar
   //que laravel me devuelva una coleccion con un solo objeto, ya que solo busco un usuario
}

public static function contarUsusarios(){
   return self::count();//Esto no devuelve una fila, devuelve un numero
}


/*
    Si en algún momento necesitas acceder a los datos ordenados por puntuación o marca de tiempo, 
    puedes encadenar métodos al usar la relación:

    $user->missions()->orderBy('pivot_puntuacion', 'desc')->get();
    */

}

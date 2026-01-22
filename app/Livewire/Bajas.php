<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Enemigo;
use App\Models\EnemigoUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Bajas extends Component
{

    const MAX_TIPOS_ENEMIGOS_VENCIDOS=6;
    public $bajas;
    public $usuario;
    public $bajasTotales;

    public function mount(){

        $this->bajas=collect();//Inicializo bajas


        if(Auth::check()){
            $this->usuario=Auth::user();//Compruebo al usuario logueado
           // $this->recuperarInventario();
            
            if(session()->has('aviso')){//Compruebo si hay avisos
                $this->aviso=session()->get('aviso');
            }

             $this->bajas = EnemigoUser::getBajasUsuario($this->usuario->id);
            /*$this->bajas->map(function($enemigoBd) {//Para evitar errores con livewire, dado que a veces guarda los campos antiguos de las tablas y modelos en lugar de los nuevos,
                //convierto en array la coleccion de filas de enemigos, usando la funcion map()
                return [
                    'enemigoId' => $enemigoBd->enemigo_api_id,
                    'nombre_enemigo' => $enemigoBd->nombre_enemigo,
                    'tipo_monstruo' => $enemigoBd->tipo_monstruo,
                    'especie' => $enemigoBd->especie,
                    'numero_bajas' => $enemigoBd->numero_bajas
                ];
            });*/
    
            //Extraigo a los enemigos de la tabla de cache

            if($this->bajas->isEmpty()){//Si no hay enemigos que coincidan en el cache con los que el usuario ha vencido
                $this->cargarDatosEnemigos();//llamo a cargar enemigos

                //Log::info($this->bajas);
            }

            $this->bajasTotales=EnemigoUser::calcularTotalBajasUsuario($this->usuario->id);

        }else{
            return redirect()->route('inicio');
        }

        
    }

    private function cargarDatosEnemigos(){

        if($this->usuario){

        $url='https://mhw-db.com/monsters';
        $enemigosVencidos=EnemigoUser::getEnemigosUsuario($this->usuario->id);//Recojo todos los registros de enemigoUsers donde figure el id del usuario logueado
        $respuestaApiMonstruos=Http::get($url);
        $this->bajas=collect();

        if($respuestaApiMonstruos->ok()){

        $respuestaApiMonstruosJson=collect(Http::get('https://mhw-db.com/monsters')->json())->keyBy('id');
        //Recojo todos los monstruos disponibles en la API, los convierto de json a una coleccion de laravel, y en dicha coleccion
        //indexo cada monstruo por su id, en lugar de usar un indice generico (por eso estoy usando keyBy('id))

               foreach($enemigosVencidos as $enemigo){//Recorro todos los enemigos vencidos por el usuario
               // $respuestaApi=collect(Http::get('https://mhw-db.com/monsters/' . $enemigo->enemigo_api_id)->json());

                $monstruo = $respuestaApiMonstruosJson[$enemigo->enemigo_api_id] ?? null;
                //En monstruo almaceno el monstruo de la API, cuya id coincida con el id del enemigo actual vencido por el usuario,
                //si la id del enemigo vencido por el usuario no coincide con ningun monstruo, la variable pasara a valer null

                if($monstruo){//Si monstruo tiene un valor dentro en lugar de null...
                    
                

                    $nuevaBaja= [//almaceno toda la informacion importante del enemigo vencido en la variable nuevaBaja
                        'enemigoId' => $enemigo->enemigo_api_id,
                        'nombre_enemigo' => $monstruo['name'],
                        'tipo_monstruo' => $monstruo['type'],
                        'especie' => $monstruo['species'],
                        'numero_bajas' => $enemigo->numero_bajas
                    ];

                    $this->bajas->push($nuevaBaja);//Almaceno la nueva baja en la coleccion de bajas

                    $this->aniadirEnemigo($nuevaBaja);//Añado el nuevo enemigo a la tabla enemigos que actua como cache de los enemigos en la web
                
            }
        }

        Log::info('Datos de los monstruos extraidos correctamente de la API.');

    }else{
        Log::warning('Error al extraer los monstruos',[ 
                
                'status' => $respuestaApi->status(),
                'url' => $url,
                
            ]);
    }

        }


    }

    private function aniadirEnemigo($nuevoEnemigo){

    if($this->usuario){

        Enemigo::updateOrCreate(
            
            ['enemigo_api_id' => $nuevoEnemigo['enemigoId']],
            
            [//Con esta funcion, creo un nuevo enemigo en la tabla cache, si dicho enemigo no existia ya previamente en la tabla,
            //en caso contrario, simplemente se actualiza la informacion ya existente en la tabla

            'enemigo_api_id' => $nuevoEnemigo['enemigoId'],
            'nombre_enemigo' => $nuevoEnemigo['nombre_enemigo'],
            'tipo_monstruo' => $nuevoEnemigo['tipo_monstruo'],
            'especie' => $nuevoEnemigo['especie']
            
        ]);

    }

    }
    
    public function detalles($idEnemigo){
        session()->put('idSeleccionado',$idEnemigo);//Guardo el id del enemigo seleccionado en la session
        session()->put('isItem',false);//Como voy a mostrar el detalle de un enemigo y no un item,
        //declaro isItem a false en la sesion
        return redirect()->route('detalle');//Navego a detalle
    }


    public function importarBajas(){

        $respuestaApi=Http::get('https://mhw-db.com/monsters');
        $repertorioEnemigos=collect($respuestaApi->json());
        
        //$maxEnemigos=count($arrayRepertorioEnemigos);
       
        $tiposEnemigosVencidos=rand(1,self::MAX_TIPOS_ENEMIGOS_VENCIDOS);
        //Los tipos de enemigo que puede haber vencido el jugador

       // $repertorioEnemigos=$repertorioEnemigos->shuffle()->take($tiposEnemigosVencidos)->pluck('id');
       $repertorioEnemigos=$repertorioEnemigos->shuffle()->take($tiposEnemigosVencidos);
        //Uso shuffle en lugar de inRandomOrder porque la coleccion que uso aqui es una coleccion ya esta cargada en memoria
        //InRandomOrder se usa cuando estas haciendo una consulta a los datos de la BD, pero esta coleccion aun no ha llegado a laravel
        //En este caso ademas, extraigo los datos de una API y no una BD.

        /*
        
        shuffle() se utiliza porque los datos ya están cargados en memoria como una colección.
        inRandomOrder() solo es aplicable cuando se trabaja directamente con consultas a base de datos.
        
        */

        //barajo todas la filas de la tabla enemigos, me quedo solo con un numero limitado de filas (en 
        //funcion del random generado, me quedare con mas o menos filas), extraigo la id unicamente de cada 
        // fila
        
        

        foreach($repertorioEnemigos as $enemigoSeleccionado){
            //Recorro todos los enemigos que he extraido de manera aleatoria de la tabla enemigos

             Enemigo::firstOrCreate(//Si el enemigo seleccionado no existe previamente en la tabla enemigos, creo un nuevo registro para ese enemigo
                ['enemigo_api_id' => $enemigoSeleccionado['id']],
                [
                    'nombre_enemigo' => $enemigoSeleccionado['name'],
                    'tipo_monstruo'  => $enemigoSeleccionado['type'],
                    'especie'        => $enemigoSeleccionado['species'],
                ]
            );
           

            $nuevasBajas=rand(1,300);//Genero una nueva cantidad de bajs para el enemigo actual, 
            // comprendida entre 1 y 300

            $enemigoVencido=$this->usuario->enemigos()->wherePivot('enemigo_api_id',$enemigoSeleccionado['id'])->first();
            //Hago una consulta a enemigo_user ya que estoy usuando los parentesis junto a enemigos
            //Lo cual quiere decir que esto NO devuelve una coleccion de los enemigos asociados al usuario
            //segun que requisitos, si no que estoy pidiendo en la tabla enemigos_users que me devuelva
            //la fila con la que coincidan el id del usuario logueado, y el id del enemigo actual 
            //sobre el que estoy iterando, si existe una fila con la combinacion actual user_id/enemigo_id,
            //en $enemigoVencido se almacena un objeto con la informacion de la fila

            

            if($enemigoVencido !== null){//Si la fila existe... , uso $enemigoVencido !== null en lugar de !empty($enemigoVencido) para evitar falsos positivos con
            //el modelo enemigo al usar empty()

                $enemigoActualNumBajas=$enemigoVencido->pivot->numero_bajas;

                if($nuevasBajas > $enemigoActualNumBajas){
                    //Cuando necesito acceder a un campo de la tabla pivote y estoy accediendo
                    //usando un modelo que conforma la relacion N-N pero no es el modelo personalizado
                    //(El modelo personalizado es EnemigoUser, y yo estoy accediando a a su tabla pivote desde
                    //fuera con User) necesito usar pivot, para indicar a laravel que quiero acceder a uno
                    //de los campos de la tabla pivote que no esta relacionado con ninguna id o clave prima-
                    //ria o foranea.

                    //Si no usase pivot para comprobar el numero de bajas en la fila que tengo guardada
                    //en enemigoVencido, recibiria un null, porque laravel no sabe a que campo me estoy
                    //refiriendo

                    /*Nota: pivot en realidad es un objeto que hace referencia a la tabla enemigo_user 
                    //la cual funciona como relacion entre enemigo y user, este objeto almacena los campos
                    //de la fila seleccionada, para los campos que tienen relacion directa con user
                    //enemigo (como las claves foraneas user_id o enemigo_id) no es necesario llamar a pivot(aunque se puede hacer)
                    //para extraer la informacion de dichos campos, pero aquellos campos adicionales que
                    //son unicos de la tabla enemigo_user y no provienen de las tablas users o enemigos,
                    //si es necesario llamar a pivot para extraer su informacion(como es el caso del campo
                    // numero_bajas)
                    // 
                    // 
                    // Cuando accedo a la relación entre User y Enemigo, Laravel automáticamente gestiona 
                    // las claves foráneas (como user_id y enemigo_id) sin necesidad de llamarlas 
                    // explícitamente desde el objeto pivot. Esto es porque Laravel sabe que estas claves 
                    // foráneas están relacionadas a las tablas principales users y enemigos.*/
                    
                    $this->usuario->enemigos()->updateExistingPivot($enemigoVencido->pivot->enemigo_api_id, ['numero_bajas' => $nuevasBajas]);//Si es mayor que 0, actualizo la fila y 
                    // con la  nueva cantidad.

                    Log::info('Actualizando bajas', [
                        'user' => $this->usuario->id,
                        'enemigo' => $enemigoSeleccionado,
                        'bajas' => $nuevasBajas
                    ]);

                    //$enemigoVencido->pivot->enemigo_api_id: para indicar de manera correcta que quiero buscar la fila en la BD donde el id del usuario
                    // y el id del enemigo sean los seleccionados actualmente en el bucle. 
                    // 
                    // Ademas con pivot me aseguro de especificar a laravel explicitamente que apunte al campo enemigo_api_id de la tabla enemigo_users
                    // en la fila relacionada al usuario actual (en lugar del campo enemigo_api_id de enemigos, dado que en ambas tablas se usa el mismo nombre de campo 
                    // para identificar el id del enemigo, por ejemplo, como caso de que haya algunos nombres comunes entre campos de diferentes tablas).
                    

                    //Esto es como hacer un UPDATE enemigo_user SET cantidad=':cantida'
                    //where use_id=:user_id AND enemigo_api_id =:enemigo_id
                
                }

                

            }else{//Si el enemigo no estaba asociado al usuario en la tabla item_user...

                    EnemigoUser::aniadirBajas($this->usuario->id,$enemigoSeleccionado['id'],$nuevasBajas);
                
                    
                    //Introduzco una nueva fila donde asocio user_id e enemigo_id en la tabla enemigo_user
                    //Idicando que el usuario ha conseguido uno o varias bajas nuevas que no figuraban
                    //en sus enemigos vencidos

                    
                    //$this->usuario->enemigos()->attach($enemigoSeleccionado, ['numero_bajas' => $nuevasBajas]);
                    //Attach es similar a usar insert_into(campos) values(valores)
                    
                
            }

            
            
       /* $this->bajas = EnemigoUser::getBajasUsuario($this->usuario->id)
        ->map(function ($enemigoBd) {
            return [
                'enemigoId' => $enemigoBd->enemigo_api_id,
                'nombre_enemigo' => $enemigoBd->nombre_enemigo,
                'tipo_monstruo' => $enemigoBd->tipo_monstruo,
                'especie' => $enemigoBd->especie,
                'numero_bajas' => $enemigoBd->numero_bajas
            ];
        });*/

       /* $this->bajas = EnemigoUser::getBajasUsuario($this->usuario->id)
        ->map(function ($enemigoBd) {
            return [
                'enemigoId' => $enemigoBd->enemigoId,
                'nombre_enemigo' => $enemigoBd->nombre_enemigo,
                'tipo_monstruo' => $enemigoBd->tipo_monstruo,
                'especie' => $enemigoBd->especie,
                'numero_bajas' => $numero_bajas
            ];
        });*/


           
        }

        $this->bajas=EnemigoUser::getBajasUsuario($this->usuario->id);// Refresco la colección de bajas para la vista
        $this->bajasTotales=EnemigoUser::calcularTotalBajasUsuario($this->usuario->id);
        session()->flash('aviso','se han importado las bajas correctamente.');
        Log::info('Bajas importadas con exito.');
        //Aviso al usuario del exito 
        return redirect()->route('bajas');//Refresco la pagina, ademas con esto evito duplicados o que se vuelva a enviar la peticion duplicada a la BD
    }

    public function volver(){
        
             $this->bajas = collect(); // Limpia las propiedades que Livewire podría hidratar
            $this->usuario = null;
            return redirect()->route('principal');
        
    }

    public function render()
    {
        return view('livewire.bajas');
    }
}

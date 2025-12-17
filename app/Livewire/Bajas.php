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

    public $bajas;
    public $usuario;

    public function mount(){

        $respuestaApi=Http::get('https://mhw-db.com/monsters');
        Log::info($respuestaApi);

        if(Auth::check()){
            $this->usuario=Auth::user();
           // $this->recuperarInventario();
            
            if(session()->has('aviso')){
                $this->aviso=session()->get('aviso');
            }

            $this->bajas=EnemigoUser::getBajasUsuario($this->usuario->id);
        }else{
            return redirect()->route('inicio');
        }

        
    }
    
    public function detalles($idEnemigo){
        session()->put('idSeleccionado',$idEnemigo);//Guardo el id del enemigo seleccionado en la session
        session()->put('isItem',false);//Como voy a mostrar el detalle de un enemigo y no un item,
        //declaro isItem a false en la sesion
        return redirect()->route('detalle');//Navego a detalle
    }


    public function importarBajas(){
        
       
        $tiposEnemigosVencidos=rand(1,Enemigo::contarEnemigos());
        //Los tipos de enemigo que puede haber vencido el jugador

        $repertorioEnemigos=DB::table('enemigos')->inRandomOrder()->limit($tiposEnemigosVencidos)
        ->pluck('id');
        //barajo todas la filas de la tabla enemigos, me quedo solo con un numero limitado de filas (en 
        //funcion del random generado, me quedare con mas o menos filas), extraigo la id unicamente de cada 
        // fila
        
        

        foreach($repertorioEnemigos as $enemigoSeleccionado){
            //Recorro todos los enemigos que he extraido de manera aleatoria de la tabla enemigos
           

            $nuevasBajas=rand(1,300);//Genero una nueva cantidad de bajs para el enemigo actual, 
            // comprendida entre 1 y 300

            $enemigoVencido=$this->usuario->enemigos()->where('enemigo_id',$enemigoSeleccionado)->first();
            //Hago una consulta a enemigo_user ya que estoy usuando los parentesis junto a enemigos
            //Lo cual quiere decir que esto NO devuelve una coleccion de los enemigos asociados al usuario
            //segun que requisitos, si no que estoy pidiendo en la tabla enemigos_users que me devuelva
            //la fila con la que coincidan el id del usuario logueado, y el id del enemigo actual 
            //sobre el que estoy iterando, si existe una fila con la combinacion actual user_id/enemigo_id,
            //en $enemigoVencido se almacena un objeto con la informacion de la fila

            

            if(!empty($enemigoVencido)){//Si la fila existe...

                if($nuevasBajas>$enemigoVencido->pivot->numero_bajas){
                    //Cuando necesito acceder a un campo de la tabla pivote y estoy accediendo
                    //usando un modelo que conforma la relacion N-N pero no es el modelo personalizado
                    //(El modelo personalizado es EnemigoUser, y yo estoy accediando a a su tabla pivote desde
                    //fuera con User) necesito usar pivot, para indicar a laravel que quiero acceder a uno
                    //de los campos de la tabla pivote que no esta relacionado con ninguna id o clave prima-
                    //ria o foranea

                    //Si no usase pivot para comprobar el numero de bajas en la fila que tengo guardada
                    //en enemigoVencido, recibiria un null, porque laravel no sabe a que campo me estoy
                    //refiriendo

                    /*Nota: pivot en realidad es un objeto que hace referencia a la tabla enemigo_user 
                    //la cual funciona como relacion entre enemigo y user, este objeto almacena los campos
                    //de la fila seleccionada, para los campos que tienen relacion directa con user
                    //enemigo (como las claves foraneas user_id o enemigo_id) no es necesario llamar a pivot
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
                    
                    $this->usuario->enemigos()->updateExistingPivot($enemigoVencido->id, ['numero_bajas' => $nuevasBajas]);
                    //Si es mayor que 0, actualizo la fila y con la  nueva cantidad

                    //Esto es como hacer un UPDATE item_user SET cantidad=':cantida'
                    //where use_id=:user_id AND enemigo_id=:enemigo_id
                
                }

                

            }else{//Si el enemigo no estaba asociado al usuario en la tabla item_user...

                    EnemigoUser::aniadirBajas($this->usuario->id,$enemigoSeleccionado,$nuevasBajas);
                
                    //$this->usuario->enemigos()->attach($enemigoSeleccionado, ['numero_bajas' => $nuevasBajas]);
                    //Introduzco una nueva fila donde asocio user_id e enemigo_id en la tabla enemigo_user
                    //Idicando que el usuario ha conseguido uno o varias bajas nuevas que no figuraban
                    //en sus enemigos vencidos

                    //Attach es similar a usar insert_into(campos) values(valores)
                    
                
            }

           
        }

        session()->flash('aviso','se han importado las bajas correctamente.');
        //Aviso al usuario del exito 
        return redirect()->route('bajas');//Refresco la pagina
    }

    public function volver(){
        return redirect()->route('principal');
    }

    public function render()
    {
        return view('livewire.bajas');
    }
}

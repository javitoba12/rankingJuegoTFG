<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Item;
use App\Models\MissionUser;
use App\Models\EnemigoUser;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;//Necesito importar este paquete cuando uso objetos con paginacion

class Administracion extends Component
{

    public $editar=false;
    public $paginacion=true;
    public $tipoEdicion;
    public $usuarioSeleccionado;
    public $nuevoNick;
    public $nuevaClave;
    public $itemsDisponibles;
    public $accionInventario='';//Mantener inicializado este atributo (aunque este vacio) 
    // para que se respete el option selected del select de acciones

    public $idItem='';//Mantener inicializado este atributo (aunque este vacio) 
    // para que se respete el option selected del select de items

    public $cantidadItem=0;

    public $nickBusqueda;

    use WithPagination;//Esto es necesario para que el componente pueda usar y gestionar el tema de la 
    //paginacion automaticamente, con ello consigo que livewire detecte la página actual de resultados
    //en la que me encuentro, usando una propiedad llamada $page (es una propiedad interna y no necesita ser 
    // declarada ni utilizada explícitamente en el componente, ya la maneja livewire de manera automatica),
    //  tambien detecta los enlaces de las paginaciones, guarda el cambio de pagina, cuando cambio a otro 
    // resultado, vuelve a renderizar la vista con la página de resultados actualizada.

    public function mount() {
        $this->itemsDisponibles=DB::table('items')->pluck('id','nombre');
    }

    public function crearUser(){
        redirect()->route('registro');
    }

    public function mostrarFormEditar($idUsuario,$tipoForm){

       // $this->idUsuarioEditado=$idUsuario;
       $this->paginacion=false;//oculto la tabla de paginacion de usuarios
        $this->editar=true;
        $this->tipoEdicion=$tipoForm;

        if(!empty($idUsuario) && $this->tipoEdicion!='busqueda'){
            $this->usuarioSeleccionado=User::find($idUsuario);
        }

    }

    public function mostrarMenuBorrarUser($tipoForm,$idUsuario){

        $this->paginacion=false;
        $this->editar=true;
        $this->tipoEdicion=$tipoForm;
        $this->usuarioSeleccionado=User::find($idUsuario);
    }

    public function verInventarioSeleccionado(){
        $idUsuario=$this->usuarioSeleccionado->id;
        session()->put('idUsuarioSeleccionado',$idUsuario);
        return redirect()->route('inventario');
    }

    

    public function modificarInventario(){

        if($this->cantidadItem>0){


        $itemEncontrado=$this->usuarioSeleccionado->items()->where('item_id',$this->idItem)->first();
        //Busco un item el cual tenga una combinacion user_id/item_id igual al id del usuario al que quiero 
        // modificar y el id del item seleccionado, y si existe se guarda en esta variable

        if(!empty($itemEncontrado)){//Si se ha encontrado un item que cumpla con los requisitos...

            if($this->accionInventario=='aniadir' || $this->accionInventario == 'actualizar'){
                //Miro si la accion seleccionada en el select es la accion añadir o actualizar
            // session()->flash('accion','AÑADIENDO');

            if(( !isset($this->cantidadItem) || !empty($this->cantidadItem)) && $this->cantidadItem>0){

                $cantidad;

                if($this->accionInventario=='aniadir'){//Si el usuario selecciona añadir...

                    $cantidad=$itemEncontrado->pivot->cantidad + $this->cantidadItem;
                    //La cantidad sera igual a la cantidad actual del item, mas la cantidad introducida por el
                    //usuario
                    session()->flash('accion','añadidos ' . $this->cantidadItem . ' ' .  $itemEncontrado->nombre . 
                    ' al inventario de ' . $this->usuarioSeleccionado->nick);
                    
                
                    }else{//Si el usuario selecciona actualizar
                    $cantidad=$this->cantidadItem;//La cantidad sera igual unicamente, a la cantidad introduci
                    //da por el usuario

                    session()->flash('accion','actualizada la cantidad de ' .  $itemEncontrado->nombre . ' a ' .  $this->cantidadItem .  
                    ' en el inventario de ' . $this->usuarioSeleccionado->nick);
                }

                    $this->usuarioSeleccionado->items()->updateExistingPivot(
                        $itemEncontrado->id, ['cantidad' =>$cantidad]);//Actualizo la fila con la nueva cantidad, si la accion es añadir se sumara la nueva
                    //cantidad a la cantidad actual, si es actualizar, se sobreescribe la cantidad actual por
                    //la nueva cantidad

                $this->refrescar();

            }else{
                session()->flash('aviso','Los datos introducidos no son correctos, revise el item seleccionado y su cantidad');
            }

            }else{//En caso de que el usuario no seleccione ni añadir ni actualizar, significa que ha
                //seleccionado eliminar

                $cantidad=$itemEncontrado->pivot->cantidad - $this->cantidadItem;
                //Contando con que hemos encontrado un item que cumple los requisitos, resto a la cantidad
                //actual del item, la nueva cantidad introducida por el usuario

                if($cantidad>=1){//Si tras la resta la cantidad sigue siendo mayor o igual a 1...
                    $this->usuarioSeleccionado->items()->updateExistingPivot(
                        $itemEncontrado->id, ['cantidad' =>$cantidad]);
                        //Actualizo la fila con la cantidad restante tras la resta

                if($this->cantidadItem>0){

                        session()->flash('accion','eliminados ' . $this->cantidadItem . ' ' .  $itemEncontrado->nombre . 
                 ' en el inventario de ' . $this->usuarioSeleccionado->nick);

                 $this->refrescar();

                }else{

                    session()->flash('accion','No se ha eliminado ningun item del inventario de ' . $this->usuarioSeleccionado->nick);

                }
                        
                }else{//En caso de que la cantidad sea menor que 1
                    $this->usuarioSeleccionado->items()->detach($itemEncontrado->id);
                    //Elimino la fila de tabla item_user

                    session()->flash('accion','eliminados todos los items ' .  $itemEncontrado->nombre . 
                 ' en el inventario de ' . $this->usuarioSeleccionado->nick);
                }

            }

    }else{//El caso de que la fila con la id de usuario y la id del item no existan previamente en la tabla... 
        
        if($this->accionInventario=='aniadir'){//Compruebo si la accion seleccionada por el usuario es añadir

            session()->flash('accion','nuevo item añadido en el inventario de ' . $this->usuarioSeleccionado->nick);
            $this->usuarioSeleccionado->items()->attach($this->idItem ,['cantidad'=> $this->cantidadItem]);
            //Añado una nueva fila con el id del usuario y el id del item para asociar dicho item al usuario,
            //junto con la cantidad introducida por el usuario

            $this->refrescar();

        }else{//En caso de que el usuario haya seleccionado una opcion distinta a añadir...

            session()->flash('aviso','El usuario no posee el item seleccionado, o el campo cantidad esta vacio');
            //Aviso al usuario de que el item que intenta modificar no esta asociado al jugador seleccionado
        }
    }

    

}else{
    session()->flash('aviso','La cantidad debe ser mayor a 0');
}

    }

    public function actualizarUser(){

        $this->validate([
            'nuevoNick' => 'required|min:5',
            'nuevaClave' => 'required|min:4',
        ],
        [
            'nuevoNick.required' => 'El nick introducido no es valido o el campo esta vacio',//si no se cumple
            //la regla required|email anterior, se añadira este mensaje a $errors

            'nuevoNick.min' => 'El nick introducido debe tener al menos 5 caracteres',//si no se cumple
            //la regla min:5 anterior de nick, se añadira este mensaje a $errors

            'nuevaClave.required' => 'Debe rellenar el campo de contraseña',//si no se cumple
            //la regla required anterior de password, se añadira este mensaje a $errors

            'nuevaClave.min' => 'La contraseña debe contener al menos 4 caracteres',//si no se cumple
            //la regla min:4 anterior de password, se añadira este mensaje a $errors
        ]);


        if(!User::isNickRepetido($this->usuarioSeleccionado->id,$this->nuevoNick)){//Compruebo si hay un usuario
            //con distinta id al usuario actual, cuyo nick sea igual al nuevo nick introducido por el
            //usuario, o lo que es lo mismo, busco si hay alguien que tenga el mismo nick que el nick
            //nuevo al que quiere cambiar el usuario

            $exito=User::actualizarUsuario($this->usuarioSeleccionado->id,$this->nuevoNick,$this->nuevaClave);
            //Si el nuevo nick del usuario no esta repetido, actualizo la informacion del usuario
            //actual
            
            if(!$exito){
                //Mensaje de error
                session()->flash('aviso','No se ha podido cambiar la informacion del usuario'); 
            }else{
                session()->flash('aviso','Se ha actualizado la informacion del usuario');
            }
            
        }else{
            session()->flash('aviso','Ya existe el nick seleccionado'); 
        }

        $this->refrescar();

    }

    public function EliminarUser($idUsuario){
        $exito=User::borrarUsuario($idUsuario);
        //Busco y borro el usuario seleccionado con la funcion borrarUsuario del modelo User, esta
        //funcion devuelve un booleano que indica el exito

        if($exito){//Aviso al admin si hubo exito
            session()->flash('aviso','Usuario borrado correctamente');
        }else{//Aviso si no se pudo borrar el usuario
            session()->flash('aviso','No se ha podido eliminar al usuario');
        }
        return redirect()->route('Administracion.dashboard');//Recargo la pagina
    }

    public function reiniciarEstadisticas($idUsuario){

        $exitoPuntuaciones=MissionUser::eliminarPuntuacionesUsuario($idUsuario);//Llamo a el metodo
        //eliminar puntuaciones de MissionUser, que se encarga de ejecutar un DELETE FROM WHERE user_id =
        //idUSuario
        $exitoBajas=EnemigoUser::eliminarBajasUsuario($idUsuario);
        //De la misma manera, tambien elimino las bajas del usuario seleccionado

        //Ambas funciones devuelven un booleano que indican el exito o fracaso de la consulta

        if($exitoPuntuaciones && $exitoBajas){//si tanto bajas como puntuaciones se reinician
            session()->flash('aviso','las estadisticas se han reiniciado correctamente');
            
        }elseif($exitoPuntuaciones  && !$exitoBajas){//Si se reinician las puntuaciones solo
            session()->flash('aviso','Se reiniciaron las puntuaciones, pero no las bajas');
            
        
        }elseif(!$exitoPuntuaciones  && $exitoBajas){//Si se reinician solo las bajas
            session()->flash('aviso','Se reiniciaron las bajas, pero no las puntuaciones');
            
        }
        
        else{//Si no se pudo reiniciar niguna de las dos
            session()->flash('aviso','No se pudo reinciar las estadisticas, o el usuario no tiene aun estadisticas');
            
        }

        $this->refrescar();//Independientemente del resultado, refresco la pagina
    }


    public function buscarUser(){

        $usuarioBuscado=User::buscarUsuario($this->nickBusqueda);
        //Busco al usuario por su nick
        
        if(empty($usuarioBuscado)){//Si el usuario no existe, aviso al admin
            session()->flash('aviso','No se ha encontrado al usuario');
        }else{//En caso de existir, lo guardo temporalmente para poder modificarlo mas tarde
            session()->flash('usuarioBuscado',$usuarioBuscado);
        }
    }

    public function buscarUser2(){
        $usuariosCoincidentes=User::buscarUsuariosCoincidentes($this->nickBusqueda);

        if(empty($usuariosCoincidentes) && $usuariosCoincidentes == null){

            session()->flash('aviso','No se ha encontrado ningun usuario');

        }else{

            session()->flash('usuariosCoincidentes',$usuariosCoincidentes);

        }
    }

    public function seleccionarUsuario($usuarioSeleccionado){
        if(!empty($usuarioSeleccionado)){

        
            session()->flash('usuarioBuscado',User::buscarUsuario($usuarioSeleccionado));

        }
    }

    public function refrescar(){
       
        redirect()->route('Administracion.dashboard');
    }

   /* public function updatingPage()
    {
        $this->reiniciarFormularios();
    }*/

    

    public function reiniciarFormularios(){
        $this->editar = false;
        $this->tipoEdicion = '';
        $this->reset(['nuevoNick', 'nuevaClave', 'accionInventario', 'idItem', 'cantidadItem']);
        session()->forget('usuarioBuscado');
    }

    public function render()
    {
        $usuarios=User::extraerClientes();//Debo almacenar en una varaiable local en el metodo render
        //el objeto que lleva extraido filas de 10 usuarios, porque es un objeto demasiado complejo
        //como para que las propiedades publicas propias de livewire o laravel sepan manejarlos,
        //ya que este objeto tambien contiene funciones ademas de atributos

        /*Las propiedades publicas de laravel o livewire no pueden manejar este objeto porque
        hablamos de un objeto que no solo tiene atributos que guardan valores, tambien usa funciones
        como atributos, y las funciones no se pueden parsear a JSON para poder llevarlas al fronent
        facilmente(en este caso hablamos de la vista), que es lo que hacen livewire y laravel automaticamente 
        en sus atributos publicos, de ahi que esté usando una variable local y mas tarde compact*/

        return view('livewire.admin.administracion',compact('usuarios'))->layout(\App\View\Components\Layouts\App::class);
        //uso compact para pasar el objeto que tiene almacenado $usuarios, compact es un array asociativo
        //que se comparte entre el componente y su vista, como una manera de compartir los datos entre ambos
        //usando compact no necesito serializar nada, simplemente paso $usuarios a la vista para poder 
        //manipularlo mas tarde, y mostrar la informacion que contiene, compact es un array asociativo
        //que transporta datos entre componente y vista
    }
}

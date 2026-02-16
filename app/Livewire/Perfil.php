<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;//Para el manejo de subida de archivos
use Illuminate\Support\Facades\Storage;//Para el manejo del directorio storage
use App\Models\User;
use App\Models\Item;
use App\Models\Mission;
use App\Models\MissionUser;
use App\Models\EnemigoUser;
use App\Http\Traits\colorTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Perfil extends Component
{
    use WithFileUploads;
    use colorTrait;

    public $usuario;
    public $actualizar=false;//Cuando pase a true, se mostrara su formulario respectivo
    public $borrar=false;//Cuando pase a true, se mostrara su formulario respectivo
    public $nuevoNick,$nuevaPassword,$aviso,$avatar;
    public $tema='oscuro';
    public $colorSeleccionado='';

    public function mount(){
        if(Auth::check()){//Compruebo que haya un usuario logueado

            $this->usuario=Auth::user();//guardo el usuario como atributo local de livewire
            //para que sea mas manejable

            $this->tema=$this->aplicarColor();//llamo a la funcion aplicar color del trait

            if(session()->has('perfilSeleccionado')){
                $this->mostrarPerfilSeleccionado(session()->get('perfilSeleccionado'));
            }

            if(session()->has('aviso')){//Si existe algun aviso en la sesion...

                $this->aviso=session()->get('aviso');//Lo guardo para mostrarlo mas tarde
                session()->forget('aviso');//elimino dicho mensaje de la sesion para que no se
                //repita
            }


        }else{//Si el usuario no esta logueado, lo envio a inicio
            return redirect()->route('inicio');
        }
    }

    

    public function refrescar(){
        return redirect()->route('perfil');
    }

    public function volver(){
        session()->forget('aviso');
        if(session()->has('perfilSeleccionado')){
            session()->forget('perfilSeleccionado');
        }
        return redirect()->route('principal');
    }

    public function mostrarPerfilSeleccionado($idUsuario){
        $this->usuario=User::find($idUsuario);
    }

    public function editarPerfil(){

        $this->resetearAviso();

         $this->resetErrorBag();//para limpiar los errores del array errors antes de volver a validar
        
        $this->validate([//valido que el campo de nick este relleno
            //y la contraseña contenga al menos 4 caracteres
            'nuevoNick' => 'required|min:5|alpha_dash|max:20',
            'nuevaPassword' => 'required|min:4',
        ],[
            'nuevoNick.required' => 'El campo nick no puede estar vacio',
            
            'nuevoNick.min' => 'El nick introducido debe tener al menos 5 caracteres',

            'nuevoNick.alpha_dash' => 'El nick introducido solo puede tener letras, numeros, o guiones',

            'nuevoNick.max' => 'el nick introducido no puede pasar de los 20 caracteres',

            'nuevaPassword.required' => 'El campo clave no puede estar vacio',
            
            'nuevaPassword.min' => 'La contraseña debe tener al mendos 4 caracteres',
        ]);

        

        if(!User::isNickRepetido($this->usuario->id,$this->nuevoNick)){//Compruebo si hay un usuario
            //con distinta id al usuario actual, cuyo nick sea igual al nuevo nick introducido por el
            //usuario, o lo que es lo mismo, busco si hay alguien que tenga el mismo nick que el nick
            //nuevo al que quiere cambiar el usuario

            $exito=User::actualizarUsuario($this->usuario->id,$this->nuevoNick,$this->nuevaPassword);
            //Si el nuevo nick del usuario no esta repetido, actualizo la informacion del usuario
            //actual
            
            if(!$exito){
                //Mensaje de error
                session()->put('aviso','No se ha podido cambiar la informacion del usuario'); 
            }else{
                session()->put('aviso','Se ha actualizado la informacion del usuario');
            }
            
        }else{
            session()->put('aviso','Ya existe el nick seleccionado'); 
        }

        $this->refrescar();

       // $this->actualizar=false;
    }


    public function subirAvatar(){//Esta funcion se llama updatedAvatar, para que cuando livewire detecte que se ha modificado la propiedad publica de 
        //avatar mediante wire:model en el input file, se dispare esta funcion automaticamente.
        //Para que se dispare una funcion automaticamente, debo de enlazar una propiedad publica del componente, a un elemento html en la vista.
        //Como segundo paso, la funcion debe llamarse siempre updated + el nombre de la propiedad, de lo contrario livewire no podra encontrar la funcion para
        //ejecutarla automaticamente. 

        $this->resetearAviso();
        
        $this->resetErrorBag();//para limpiar los errores del array errors antes de volver a validar


        //this->validate lo proporciona livewire

        $this->validate([//Valido y compruebo que el avatar solo sea explicitamente una imagen y no cualquier otro archivo.
    //y con un tamaño que no supere los 2 mb(2048kb), para evitar imagenes demasiado grandes.
        'avatar' => 'required|image|mimes:jpg,jpeg,png,gif,svg,webp|max:2048', //Con mimes especifico los formatos de imagen que permito
    ],[

        'avatar.max' => 'La imagen no puede pesar mas de 2mb',
        'avatar.image' => 'El archivo debe ser una imagen válida',
        'avatar.required' => 'Debes seleccionar una imagen',
        'avatar.mimes' => 'Formato de imagen no permitido'

    ]);


        if($this->usuario->avatar && Storage::disk('s3')->exists($this->usuario->avatar)){
            Storage::disk('s3')->delete($this->usuario->avatar);
        }

        $nombreAvatar=$this->usuario->nick . '_avatar'. time() . '.' . $this->avatar->getClientOriginalExtension();
        //El avatar de cada usuario se identificara, con el nombre del usuario seguido de _avatar, y despues del punto,
        //Uso time para usar la hora actual como parte del nombre de la imagen, de esta manera , el navegador detecta que la imagen
        //ha cambiado, al tener un nombre un poco diferente al anterior. El navegador detecta el cambio y vuelve a pintar la imagen de nuevo.
        //En la parte final del nombre, declaro tipo de extension que usa la imagen que el usuario ha subido como avatar, con la funcion de laravel
        //getClientOriginalExtension


      // $rutaAvatar = $this->avatar->store('avatars', 'public');//En esta linea livewire hace lo siguiente:

      $rutaAvatar=$this->avatar->storeAs('avatarsUsuarios', $nombreAvatar, ['disk' => 's3','visibility' => 'public']);//En esta linea livewire hace lo siguiente:

      //$rutaAvatar=$this->avatar->storePubliclyAs('avatarsUsuarios', $nombreAvatar, 's3');
       /*
       
       Recojo el archivo aavatar, que ahora mismo es un objeto tipo uploadFile para que livewire pueda manejarlo
       - storeAs() Es un método de Laravel que guarda el archivo en el disco configurado y, devuelve la ruta relativa del archivo (partiendo desde public, el espacio virtual) 
       dentro del disco como una cadena de texto.
       - avatars indica la carpeta donde se va a guardar la imagen dentro del disco (esta carpeta se encuentra en storage, ruta por defecto para archivos o imagenes),
       si la carpeta avatars no existia antes, se creara en el momento de subir la imagen, para poder almacenar la imagen correctamente.

       -avatars es la carpeta donde se va a guardar el avatar

       -nombreAvatar, es el nombre especifico con el que quiero guardar el avatar del usuario.

       -Public indica el disco configurado en laravel para alamcenar archivos o imagenes.
       
       -El disco en laravel no se refiere e un disco duro, o usb etc.. Se refiere mas bien a un espacio virtual(o disco virtual) que laravel necesita y usa para 
       manejar archivos, de lo contrario seria mucho mas complicado almacenar las imagenes que suban los usuarios.
      
       -La direccion de public realmente apunta a la ruta /storage/app/public
        por lo cual cuando uso como parametros 'avatars' y 'public' estoy indicando que busco almacenar la nueva imagen en la carpeta avatars, que se ubica o
       ubicara en el espacio virtual de public, si la carpeta avatars no existe aun.

       La ruta completa sera /storage/app/public/avatars.

        
        avatars es una carpeta dentro de storage que he creado yo, podria llamarse fondos-de-perfil o avatares, no es una carpeta propia de laravel
       */

        $exito=User::cambiarAvatar($this->usuario->id,$rutaAvatar);//Hago una consulta update para actualizar el campo avatar del usuario

        if($exito){//Si la consulta ha tenido exito, y devuelve 1 o mas filas modificadas en la tabla

        $this->usuario = User::find($this->usuario->id);//Vuelvo a buscar al usuario logueado en la base de datos y lo recojo de nuevo 
        //en mi variable local de usuario en el componente, con esto consigo actualizar facilmente la informacion del usuario en la web, sin recargar la pagina
        //si este ha cambiado su avatar.(en el campo avatar de la tabla users, se guarda como ruta la carpeta avatars + el nombre del avatar)
        //para poder localizar el avatar facilmente desde el componente, cunado extraigo la informacion del usuario de la BD
       
        //session()->flash('aviso', 'Avatar actualizado.');

        $this->aviso='Avatar actualizado.';//aviso al usuario del exito
        }else{
          $this->aviso ='Se ha producido un error al actualizar el avatar.';  //aviso al usuario de que hubo un error
        }
    }

    public function updatedColorSeleccionado(){
        if(!empty($this->colorSeleccionado)){
           
            $exito = User::cambiarColor($this->usuario->id,$this->colorSeleccionado);

           if($exito){
                $this->usuario = User::find($this->usuario->id);
                $this->aviso='Se ha actualizado el tema de la web.';
                
                
           }else{
                $this->aviso ='Se ha producido un error al actualizar el tema.';
           }
        }

        $this->tema=$this->aplicarColor();//llamo a la funcion aplicar color del trait

        $this->dispatch('recargarPagina');

    }

    private function resetearAviso(){
        if(!empty($this->aviso)){
            $this->aviso='';
        }
    }

    public function cambiarVisibilidadForm($tipoForm){

        $this->nuevaPassword='';//Como ambos formularios enlazan la propiedad nueva clave, siempre me aseguro de limpiar
        //dicha propiedad si la visibiliad de alguno de los formularios cambia
        
        if($tipoForm == 'actualizar'){

            $this->actualizar = !$this->actualizar;// Si actualizar es true, cambia a false y viceversa, 
            // siempre valdra lo contrario a lo que vale actualmente
            if($this->borrar){
                $this->borrar=false;
            }

        }elseif($tipoForm == 'borrar'){
        
            $this->borrar = !$this->borrar;
            if($this->actualizar){

            
                $this->actualizar = false;

            }

        }

        
    }

    public function actualizarPuntuacion(){

        $this->resetearAviso();
        

        $this->misionesDisponibles = Mission::pluck('id')->toArray();//Usando pluck
        //extraigo solamente los id de cada mision de la tabla missions sin necesidad de extraer el resto
        //de columnas de cada fila,dicha coleccion de id, las paso a un array para poder recorrerlas
        //comodamente con un foreach

        shuffle($this->misionesDisponibles);//Barajo el array de ids
        //$this->numMisiones=Mission::contarMisiones();
        $misionesCompletadas=MissionUser::getMisionesUser($this->usuario->id);
        //Extraigo todas las puntuaciones cuyo user_id coincida con el id del usuario actual

        foreach($this->misionesDisponibles as $misionSeleccionada) { 
            //Recorro el array de ids de misiones
            
            $isCompletada=false;//Completada inicia siempre en false, indicando que aun no se si la mision
            //actual ya habia sido completada por el usuario

            $nuevaPuntuacion=rand(1000, 7000);//genero una puntuacion random, contando con los bonus
            $nuevaMarca=rand(5,60);//En minutos, genero una marca de tiempo random
            
            foreach($misionesCompletadas as $completada){//Recorro el array de misiones que contienen
                //el id del usuario que extraje anteriormente de la tabla mission_users

                if($completada->id == $misionSeleccionada){
                    //Si la id de la mision seleccionada coincide con el id de una de las misiones
                    //completadas por el usuario en la tabla mission_users....
                    
                    $isCompletada=true;//isCompletada pasa a ser true, porque se que esta mision fue 
                    //completada por el usuario en algun momento.

                    if($nuevaPuntuacion > $completada->puntuacion){//Si la nueva puntuacion generada
                        //supera a la puntuacion registrada en la mision completada del usuario...

                        MissionUser::actualizarPuntuacion($this->usuario->id,$completada->id,
                        $nuevaPuntuacion,$nuevaMarca);//Actualizo la fila de la mision completada
                        //con la nueva puntuacion, ya que dicha puntuacion es un nuevo record respecto
                        //a la puntuacion anterior
                        
                        
                    }
                   
                    break;//Ya que he encontrado una mision completada que coincidia con la mision
                    //seleccionada, rompo el bucle anidado
                }
            }

            if(!$isCompletada){//En caso de que la mision seleccionada no haya sido completada hasta
                //ahora por el usuario...

                MissionUser::aniadirPuntuacion($this->usuario->id,$misionSeleccionada,
                $nuevaPuntuacion,$nuevaMarca);//Añado dicha mision como mision completada a la tabla
                //mission_users junto con la puntuacion y marca generadas aleatoriamente
                
            }

            
        }

        $horasInvertidas=rand(1,3) + $this->usuario->tiempo_juego;

        User::actualizarHorasJuego($this->usuario->id,$horasInvertidas);

        $this->aviso='Se han actualizado las puntuaciones';
        
        //session()->flash('aviso','Se han actualizado las puntuaciones'); 

        $this->usuario=Auth::user();//Actualizo la informacion del usuario
       // $this->refrescar();
    }       

        public function mostrarPartidas(){
            return redirect()->route('partidasGuardadas');
        }

    public function borrarUsuario(){

        $this->resetearAviso();
        
        if( !empty($this->nuevaPassword) && Hash::check($this->nuevaPassword,$this->usuario->password)){
            //Compruebo que la contraseña introducida por el usuario, y la contraseña en la BD
            //sean la misma, antes de pasar a borrar al usuario.
            //Esto lo hace laravel hasheando o encriptando la contraseña introducida, con el mismo metodo que utilizo
            //al encriptar la contraseña del usuario en la BD, en el momento que se creo dicho usuario.
            //Tras ello compara ambas contraseñas para ver si coinciden

            $idParaBorrar=$this->usuario->id;
            Auth::logout();// Cierro la sesión del usuario,(equivalente a unset($_SESSION['usuario']))
            session()->flush();// Elimino los datos de la sesión
            User::borrarUsuario($idParaBorrar,$this->usuario->avatar);//Borro al usuario de la BD
            return redirect()->route('inicio');//Lo redirijo a inicio
        }else{
            session()->flash('error','la contraseña es incorrecta');
        }
    }

    public function rutaAdmin(){
        return redirect()->route('Administracion.dashboard');
    }

    public function render()
    {//Aviso: render se activa cada vez que cambia una propiedad publica. Por ejemplo cuando se cambian propiedades publicas a traves de wire model
    //Por lo cual render viene bien para actualizar la informacion de la web sin necesidad de recargarlo por completo
       
       
        return view('livewire.perfil', [

            //Recojo como propieda serializada, que pasare directamente en un array a la vista perfil, 
            // el avatar del usuario(en caso de que tenga el nombre del archivo de su avatar asociada a su fila en la tabla users)

            'avatarUrl' =>$this->usuario->avatar //Compruebo si el usuario contiene una ruta a su avatar en la BD (lo que significa que ya tiene un avatar propio)
        ? Storage::disk('s3')->temporaryUrl($this->usuario->avatar, now()->addMinutes(60)) //Como la visibilidad del contenido del bucket es privada
        //por limitaciones del plan gratuito, lo que hago es pedir a iDrive una url temporal con un certificado de 60 min para que el usuario propietario del avatar
        // o cualquier otro usuario que consulte el perfil del usuario actual,
        //  tenga tiempo de sobra para ver su imagen. Esta url se genera utilizando las variables de entorno en el fichero .env (local) , o en railway (produccion)
        //Laravel le pasa esta url ya certificada a iDrive, y le pide que verifique el certificado, para poder usarla en mi web, y que cualquiera pueda ver esa imagen
                 //Si el usuario resulta tener el nombre de su archivo de avatar en la BD, y ademas ese archivo existe en el bucket 
                 // pego dicho nombre a la ruta de storage donde se encuentran almacenados todos los avatares de todos los usuarios, y con esto conseguire localizar 
                 // su avatar concreto. 

                 //Con asset() consigo enlazar la direccion de mi web, con la ruta donde se encuentra el avatar dentro de mi web, ensamblando asi la ruta absoluta exacta
                 //hasta el avatar del usuario, por ejemplo si mi servidor está en https://miweb.com (o http://127.0.0.1:8000 si estoy en local)
                 //   asset('storage/' . $this->usuario->avatar ) (que se traduce  como /avatars/nombre_foto) hace que
                 //laravel ensambla o traduzca la ruta como https://miweb.com/avatars/nombre_foto, con lo cual ya tengo la ruta absoluta exacta al avatar del usuario
                
                : asset('images/avatares/avatar.jpg'), // Este sera el avatar por defecto si el usuario no tiene ninguno subido.
           
            'tema' => $this->tema
            

                
        ]);
    }

    // return view('livewire.perfil');
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Registro extends Component
{
    public $usuario;
    public $privilegios=false;
    public $rol='usuario';
    public $nick,$email,$password,$password2;



    public function mount(){
        if(Auth::check()){//Si un usuario logueado accede a registro...
            $this->usuario=Auth::user();//guardo dicho usuario

            if($this->usuario->rol == 'admin'){//Compruebo que el usuario que acabo de capturar sea admin...
                $this->privilegios=true;//Si es admin, le concedo privilegios y le dejo
                //continuar en registro
            }else{//En caso de que el usuario este logueado y no sea un admin...
                redirect()->route('principal');//lo devuelvo a la pagina principal
            }//Para evitar que cualquier usuario logueado en la pagina puede acceder libremente a registro.
            //Solo acceden los usuarios no logueados o los admin
        }
    }

    public function registro(){

        $this->resetErrorBag();//para limpiar los errores del array errors antes de volver a validar

       // $ruta='inicio';//La pagina a la que devuelvo al usuario una vez complete el formulario

        $validate = $this->validate([

            'nick' => 'required|min:5|alpha_dash|max:20',
            'email' => 'required|email',
            'password' => 'required|min:4',
            
        ],
        [

            'nick.required' => 'El nick introducido no es valido o el campo esta vacio',//si no se cumple
            //la regla required|email anterior, se añadira este mensaje a $errors

            'nick.min' => 'El nick introducido debe tener al menos 5 caracteres',//si no se cumple
            //la regla min:5 anterior de nick, se añadira este mensaje a $errors

            'nick.alpha_dash' => 'El nick introducido solo puede tener letras, numeros, o guiones',

            'nick.max' => 'el nick introducido no puede pasar de los 20 caracteres',

            'email.required' => 'El email introducido no es valido o el campo esta vacio',//si no se cumple
            //la regla required|email anterior, se añadira este mensaje a $errors

            'password.required' => 'Debe rellenar el campo de contraseña',//si no se cumple
            //la regla required anterior de password, se añadira este mensaje a $errors

            'password.min' => 'La contraseña debe contener al menos 4 caracteres',//si no se cumple
            //la regla min:4 anterior de password, se añadira este mensaje a $errors

            //Mas tarde en la parte de la vista puedo mostrar los errores del array errors en caso de que 
            //tenga algun error almacenado
        ]);

        
            if($this->password == $this->password2){//uso this, porque dentro del controlador, los campos
                //del usuario existen como atributos de un objeto, y la manera de acceder a ellos en el 
                //controlador es usando this
                if (!User::where('email',$this->email)->exists() && !User::where('nick',$this->nick)->exists()){
                    //Si no existe ya un usuario registrado
                    //con el mismo email y nick que los introducidos por el usuario...
                    
                    User::create([//Paso a ejecutar un insert via laravel/eloquent para
                        //para crear el nuevo usuario en la tabla users

                        'nick' => $this->nick,
                        'email' => $this->email,
                        'password' => Hash::make($this->password),
                        'fecha_alta' => now(),
                        'tiempo_juego' => rand(1,200),
                        'rol' => $this->rol
                    ]);

                    if($this->privilegios){//Si el usuario es admin
                       // $ruta='Administracion.dashboard';
                        //la ruta a la que sera dirigido es su pagina de administracion mas tarde
                        session()->flash('aviso','Nuevo ' . $this->rol  . ' ' . $this->nick .' creado correctamente');
                        //Aviso al admin del exito
                    }else{
                        session()->flash('aviso','Su usuario ha sido registrado correctamente: Gracias!');
                    }
                    
                    $this->volver();

                   // return redirect()->route($ruta);//Una vez creado el usuario, redirijo a la pagina de
                    //correspondiente
                   

                }else{//Si ya existia un usuario con el mismo email, aviso al usuario actual
                    session()->flash('error', 'El usuario ya existe');
                }
            }else{
                session()->flash('error', 'Las contraseñas no coinciden');
            }
        
            
        

    }

    public function volver(){
        $ruta='inicio';

        if($this->privilegios){
            $ruta='Administracion.dashboard';
        }

        return redirect()->route($ruta);
    }

    public function render()
    {
        return view('livewire.registro');
    }
}

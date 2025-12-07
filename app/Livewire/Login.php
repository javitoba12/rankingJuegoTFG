<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Login extends Component
{
    public $email,$password;//email y password vinculados de la vista
     

    public function login()
    {

       /* $this->addError('email','El email introducido no es valido o el campo esta vacio');
        $this->addError('password','La contraseña debe contener al menos 4 caracteres');*/

        //Cuando uso la funcion validate en livewire, debo proporcionar un array con las reglas
        //que quiero que se cumplan en cada campo,adicionalmente puedo proporcionar como parametro adicional
        //otro array con los mensajes de error para dichas reglas, estos errores pasaran a cargarse 
        //individualmente al array errors de laravel en el caso de que su respectiva regla no se cumpla.

        $validate = $this->validate([//Array con las reglas que se han de cumplir
            
            'email' => 'required|email',//valido que el campo de email este relleno
            'password' => 'required|min:4',//valido que la contraseña contenga al menos 4 caracteres
        ],
        
        [//Hay que tener en cuenta que validate([]) no devuelve un booleano si hay exito o algun
        //campo de los rellenados por el usuario no es valido, en su lugar lo que hace es lanzar una 
        //excepcion la cual se almacena en un array llamado $errors que laravel crea solo y proporciona 
        //a los desarrolladores, dicho esto en lugar de usar los mensajes de error que validate() lanza
        //por defecto, puedo personalizarlos y usar los mios propios de la siguiente manera



            'email.required' => 'El email introducido no es valido o el campo esta vacio',//si no se cumple
            //la regla required|email anterior, se añadira este mensaje a $errors

            'password.required' => 'Debe rellenar el campo de contraseña',//si no se cumple
            //la regla required anterior de password, se añadira este mensaje a $errors

            'password.min' => 'La contraseña debe contener al menos 4 caracteres',//si no se cumple
            //la regla min:4 anterior de password, se añadira este mensaje a $errors

            //Mas tarde en la parte de la vista puedo mostrar los errores del array errors en caso de que 
            //tenga algun error almacenado
        ]);

        

            if (Auth::attempt($validate)) {//Una vez validado email y password, compruebo que los datos 
                //introducidos por el usuario existan en la tabla Users usando la funcion Auth::attemp de
                //laravel

            return redirect()->route('principal');//Si los datos son correctos, redirijo al usuario a la 
            //siguiente pagina

            // session()->flash('status', 'exito!');
            } else {//En otro caso, aviso de un error al usuario
                session()->flash('error', 'Credenciales incorrectas');
                //Guarda un dato,texto,objeto etc. que solo se guarda temporalmente
                //Esto quiere decir que la proxima vez que se recargue la pagina o se navegue 
                //hacia otra pagina, este dato de la sesion se borrara automaticamente, esto es 
                //ideal entre otras cosas para guardar mensajes de aviso para el usuario
            }

        
    }

    public function volver(){
        return redirect()->route('inicio');
    }

    public function render()
    {
        return view('livewire.login');
    }
}




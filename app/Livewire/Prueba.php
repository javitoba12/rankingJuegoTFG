<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Registro extends Component
{
    public $nick,$email,$password,$password2;

    public function registro(){

        $validate = $this->validate([

            'nick' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:4',
            
        ]);

        if($validate){
            if($this->password == $this->password2){//uso this, porque dentro del controlador, los campos
                //del usuario existen como atributos de un objeto, y la manera de acceder a ellos en el 
                //controlador es usando this
                if (User::where('email',$this->email)->exists()) {
                    
                    session()->flash('status', 'exito!');

                }else{
                    session()->flash('error', 'El usuario ya existe');
                }
            }else{
                session()->flash('error', 'Las contraseñas no coinciden');
            }
        }
            
        

    }


    public function render()
    {
        return view('livewire.registro');
    }
}

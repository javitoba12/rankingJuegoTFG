<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\SavedGame;
use Illuminate\Support\Facades\Auth;

class PartidasGuardadas extends Component
{
    public $usuarioLogueado;
    public $partidasGuardadas;
    

    public function mount(){

        if(Auth::check()){//Compruebo que haya un usuario logueado

            $this->usuarioLogueado=Auth::user();
            $this->extraerPartidas();
        }else{//Si el usuario no esta logueado, lo envio a inicio
            return redirect()->route('inicio');
        }
    
}

    public function extraerPartidas(){
        $this->partidasGuardadas=SavedGame::obtenerPartidasUsuario($this->usuarioLogueado->id);
    }
    public function render()
    {
        return view('livewire.partidas-guardadas');
    }
}



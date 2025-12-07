<?php

namespace App\Livewire;

use Livewire\Component;

class Inicio extends Component
{
    public $mensaje;

    public function mount(){

        if(session()->has('aviso')){
            $this->mensaje=session()->get('aviso');
            //session()->forget('aviso');
        }

    }


    public function render()
    {
        return view('livewire.inicio');
    }
}

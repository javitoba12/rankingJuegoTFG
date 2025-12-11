<?php

namespace App\Traits;
use Illuminate\Support\Facades\Auth;


trait colorTrait {

    public function aplicarColor(){
        $tema;
        $colorDefecto='oscuro';
        

        if(Auth::check()){
            $usuario=Auth::user();
            

            $tema = [
                'bgColor'   => $usuario?->tema == $colorDefecto ? 'bg-dark' : 'container-claro',
                'textColor' => $usuario?->tema == $colorDefecto ? 'text-white' : 'text-dark',
                'navbarColor' => $usuario->tema == $colorDefecto ? 'navbar-dark' : 'navbar-light'
            ];
        }else{
            $tema = [
                'bgColor'   =>  'bg-dark',
                'textColor' =>  'text-white',
                'navbarColor' =>  'navbar-dark'
                ];
        }
    

        return $tema;
    }
}


?>
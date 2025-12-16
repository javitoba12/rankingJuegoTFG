<?php

namespace App\Http\Traits;//Esta es la ruta que defino, y con la que luego puedo buscar e importar esta clase en otros componentes o clases.
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
                'navbarColor' => $usuario->tema == $colorDefecto ? 'navbar-dark' : 'navbar-light',
                'tableColor' => $usuario->tema == $colorDefecto ? 'table-dark' : 'table-light'
            ];
        }else{
            $tema = [
                'bgColor'   =>  'bg-dark',
                'textColor' =>  'text-white',
                'navbarColor' =>  'navbar-dark',
                'tableColor' => 'table-dark'
                ];
        }
    

        return $tema;
    }
}


?>